<?php

namespace App\Http\Controllers;

use App\Mail\VerficationMail;
use App\Models\User;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserContoller extends Controller
{
    use GeneralTrait;

    public function signUp(Request $request)
    {
        try {
            $rules = [
                'email' => 'required|email|unique:users,email',
                'phone' => 'required',
                'password' => 'required',
                'name' => 'required',
                'type' => 'required|in:individual,business,admin'
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }
            $img_src = null;
            if ($request->hasFile('img_src')) {
                $img_src  = $this->saveImage($request->img_src, 'users');
            }
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => bcrypt($request->password),
                'num_of_employees' => $request->num_of_employees,
                'img_src' => $img_src ?? '',
                'url' => $request->url,
                'country' => $request->country,
                'gender' => $request->gender,
                'business_category' => $request->business_category,
                'year_dob' => $request->year_dob,
                'month_dob' => $request->month_dob,
                'day_dob' => $request->day_dob,
                'type' => $request->type
            ]);
            return $this->returnSuccessMessage('success');
        } catch (\Exception $e) {
            return $this->returnError('201', $e->getMessage());
        }
    }

    public function login(Request $request)
    {
        try {
            $rules = [
                'email' => 'required|email',
                'password' => 'required'
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }
            $cardintions = $request->only(['email', 'password']);
            //    return $cardintions;
            $token = Auth::guard('api-user')->attempt($cardintions);
            // return $token;
            if (!$token) {
                return $this->returnError('E001', 'fail');
            }
            $user = Auth::guard('api-user')->user();
            // $user->token = $token;
            $data['id'] = $user->id;
            $data['name'] = $user->name;
            $data['img_src'] = $user->img_src;
            $data['type'] = $user->type;
            $data['token'] = $token;
            return $this->returnSuccessMessage($data);
        } catch (\Exception $e) {
            return $this->returnError('201', $e->getMessage());
        }
    }
    public function me()
    {
        return response()->json(auth()->user());
    }

    public function logout(Request $request)
    {
        try {
            $token = $request->header('authToken');
            if ($token) {
                JWTAuth::setToken($token)->invalidate();
                return $this->returnSuccessMessage('success');
            } else {
                return $this->returnError('E205', 'fail');
            }
        } catch (\Exception $e) {
            return $this->returnError('201', $e->getMessage());
        }
    }

    public function updateMyInfo(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = User::find(Auth()->user()->id);
            if (!$user) {
                return $this->returnError('202', 'user not found');
            }
            // $img_src = null;
            $photo_len = strlen((isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/images/users/');
            $img_src = substr($user->img_src, $photo_len);
            if ($request->hasFile('img_src')) {
                if ($user->img_src) {
                    unlink('images/users/' . $img_src);
                }
                $img_src = $this->saveImage($request->img_src, 'users');
            }
            $user->update([
                'name' => $request->name ?? $user->name,
                'email' => $request->email ?? $user->email,
                'phone' => $request->phone ?? $user->phone,
                // 'password' => bcrypt($request->password) ?? $user->password,
                'num_of_employees' => $request->num_of_employees ?? $user->num_of_employees,
                'img_src' => $img_src ?? '',
                'url' => $request->url ?? $user->url,
                'country' => $request->country ?? $user->country,
                'gender' => $request->gender ?? $user->gender,
                'business_category' => $request->business_category ?? $user->business_category,
                'year_dob' => $request->year_dob ?? $user->year_dob,
                'month_dob' => $request->month_dob ?? $user->month_dob,
                'day_dob' => $request->day_dob ?? $user->day_dob
            ]);
            DB::commit();
            return $this->returnSuccessMessage('success');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->returnError('201', $e->getMessage());
        }
    }

    public function forgetPassword(Request $request)
    {
        try {
            $rules = [
                'email' => 'required|email'
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return $this->returnError('202', 'user not founded');
            }
            $code =  rand(100000, 999999);
            $user->update([
                'reset_password_code' => $code
            ]);
            // send mail
            $reset_link = "http://localhost:8080/reset-password/" . $user->id;
            Mail::to($request->email)->send(new VerficationMail($code, $user->name, $user->email, $reset_link));
            return $this->returnSuccessMessage('success');
        } catch (\Exception $e) {
            return $this->returnError('201', $e->getMessage());
        }
    }

    public function getResetPasswordCode(Request $request)
    {
        try {
            if (!$request->has('user_id')) {
                return $this->returnError('202', 'input user id');
            }
            $user = User::find($request->user_id);
            if (!$user) {
                return $this->returnError('202', 'user not founded');
            }
            return $this->returnData('data', $user->reset_password_code);
        } catch (\Exception $e) {
            return $this->returnError('201', $e->getMessage());
        }
    }

    public function updatePassword(Request $request)
    {
        try {
            if (!$request->has('user_id') || !$request->has('password')) {
                return $this->returnError('202', 'you must enter user id and new password');
            }
            $user = User::find($request->user_id);
            if (!$user) {
                return $this->returnError('202', 'user not founded');
            }
            $user->update([
                'password' => bcrypt($request->password)
            ]);
            return $this->returnSuccessMessage('success');
        } catch (\Exception $e) {
            return $this->returnError('201', $e->getMessage());
        }
    }
}