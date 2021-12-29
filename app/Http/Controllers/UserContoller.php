<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
                'phone' => 'required|unique:users,phone',
                'password' => 'required',
                'name' => 'required',
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
                'business_category' => $request->business_category,
                'year_dob' => $request->year_dob,
                'month_dob' => $request->month_dob,
                'day_dob' => $request->day_dob
            ]);
            return $this->returnSuccessMessage('success');
        } catch (\Exception $e) {
            return $this->returnError('201', 'fail');
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
            $token = Auth::guard('api-user')->attempt($cardintions);
            if (!$token) {
                return $this->returnError('E001', 'fail');
            }
            $user = Auth::guard('api-user')->user();
            $user->token = $token;
            return $this->returnSuccessMessage($user);
        } catch (\Exception $e) {
            return $this->returnError('201', 'fail');
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
            return $this->returnError('E205', 'fail');
        }
    }
}
