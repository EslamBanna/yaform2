<?php

namespace App\Http\Controllers;

use App\Mail\FormMail;
use App\Models\Form;
use App\Models\Option;
use App\Models\Question;
use App\Models\SocialMediaLink;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class FormContoller extends Controller
{
    use GeneralTrait;
    public function createTemplate(Request $request)
    {
        DB::beginTransaction();
        try {
            // return $request;
            $rules = [
                'form_type' => 'required',
                'header' => 'required',
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }
            $image_header = null;
            if ($request->hasFile('image_header')) {
                $image_header = $this->saveImage($request->image_header, 'images_header');
            }
            $logo = null;
            if ($request->hasFile('logo')) {
                $logo = $this->saveImage($request->logo, 'logos');
            }
            $template_id = Form::insertGetId([
                'user_id' => Auth()->user()->id,
                'form_type' => ($request->form_type == "classic form" ? '0' : '1'),
                'image_header' => $image_header ?? "",
                'header' => $request->header,
                'is_quiz' => 0,
                'is_template' => 1,
                'description' => $request->description,
                'logo' => $logo,
                'style_theme' => $request->style_theme,
                'font_family' => $request->font_family,
                'msg' => $request->msg
            ]);
            if ($request->has('social_media')) {
                foreach ($request->social_media as $link) {
                    // return $link;
                    SocialMediaLink::create([
                        'form_id' => $template_id,
                        'type' => $link['type'],
                        'url' => $link['url']
                    ]);
                }
            }
            if ($request->has('questions')) {
                // return 'cc';
                foreach ($request->questions as $question) {
                    $desc = null;
                    if ($question['type'] == 'image' && $question->hasFile('description')) {
                        $desc = $this->saveImage($question->description, 'question_images');
                    }
                    $type = null;
                    if ($question['type'] == 'question') {
                        $type = '0';
                    } elseif ($question['type'] == 'title') {
                        $type = '1';
                    } elseif ($question['type'] == 'image') {
                        $type = '2';
                    } elseif ($question['type'] == 'video') {
                        $type = '3';
                    }
                    $q_type = null;
                    if ($question['question_type'] == "Short answer") {
                        $q_type = "0";
                    } elseif ($question['question_type'] == "Paragraph") {
                        $q_type = "1";
                    } elseif ($question['question_type'] == "Multiple choice") {
                        $q_type = "2";
                    } elseif ($question['question_type'] == "Checkboxes") {
                        $q_type = "3";
                    } elseif ($question['question_type'] == "Dropdown") {
                        $q_type = "4";
                    } elseif ($question['question_type'] == "Date") {
                        $q_type = "5";
                    } elseif ($question['question_type'] == "Time") {
                        $q_type = "6";
                    } elseif ($question['question_type'] == "Phone number") {
                        $q_type = "7";
                    } elseif ($question['question_type'] == "Email") {
                        $q_type = "8";
                    } elseif ($question['question_type'] == "Name") {
                        $q_type = "9";
                    } elseif ($question['question_type'] == "Number") {
                        $q_type = "10";
                    }
                    $question_id = Question::insertGetId([
                        'form_id' => $template_id,
                        'type' => $type,
                        'description' => $desc ?? $question['description'],
                        'question_type' => $q_type,
                        'required' => ($question['required'] == true ? 1 : 0),
                        'focus' => ($question['focus'] == true ? 1 : 0),
                        'display_video' => ($question['display_video'] == true ? 1 : 0)
                    ]);
                    if (isset($question['options'])) {
                        foreach ($question['options'] as $option)
                            Option::create([
                                'question_id' => $question_id,
                                'value' => $option['value'],
                                'text' => $option['text']
                            ]);
                    }
                }
            }
            DB::commit();
            return $this->returnSuccessMessage('inserted successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->returnError('201', $e->getMessage());
        }
    }
    public function getTemplates()
    {
        try {
            $templates = Form::with(['Questions.options', 'socialMedia'])->where('is_template', 1)->get();
            return $this->returnData('data', $templates);
        } catch (\Exception $e) {
            return $this->returnError('201', $e->getMessage());
        }
    }
    public function getTemplate($templateId)
    {
        try {
            $template = Form::with(['Questions.options', 'socialMedia'])->where('id', $templateId)
                ->where('is_template', 1)
                ->first();
            return $this->returnData('data', $template);
        } catch (\Exception $e) {
            return $this->returnError('201', $e->getMessage());
        }
    }
    public function acceptResponse($formId, Request $request)
    {
        try {
            if (!$request->has('accept_response')) {
                return $this->returnError('202', 'the accept_response field is required');
            }
            $form = Form::find($formId);
            if (!$form) {
                return $this->returnError('202', 'form not founded');
            }
            $form->update([
                'accept_response' => $request->accept_response
            ]);
            return $this->returnSuccessMessage('updated succesfully');
        } catch (\Exception $e) {
            return $this->returnError('201', $e->getMessage());
        }
    }
    public function formSetting($formId, Request $request)
    {
        try {
            if (!$request->has('msg')) {
                return $this->returnError('202', 'the msg field is required');
            }
            $form = Form::find($formId);
            if (!$form) {
                return $this->returnError('202', 'form not founded');
            }
            $form->update([
                'msg' => $request->msg
            ]);
            return $this->returnSuccessMessage('updated succesfully');
        } catch (\Exception $e) {
            return $this->returnError('201', $e->getMessage());
        }
    }

    public function createForm(Request $request)
    {
        DB::beginTransaction();
        try {
            $rules = [
                'form_type' => 'required',
                'header' => 'required',
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }
            $image_header = null;
            if ($request->hasFile('image_header')) {
                $image_header = $this->saveImage($request->image_header, 'images_header');
            }
            $logo = null;
            if ($request->hasFile('logo')) {
                $logo = $this->saveImage($request->logo, 'logos');
            }
            $template_id = Form::insertGetId([
                'user_id' => Auth()->user()->id,
                'form_type' => ($request->form_type == "classic form" ? '0' : '1'),
                'image_header' => $image_header ?? "",
                'header' => $request->header,
                'is_quiz' => 0,
                'is_template' => 0,
                'description' => $request->description,
                'logo' => $logo,
                'style_theme' => $request->style_theme,
                'font_family' => $request->font_family,
                'msg' => $request->msg
            ]);
            if ($request->has('social_media')) {
                foreach ($request->social_media as $link) {
                    // return $link;
                    SocialMediaLink::create([
                        'form_id' => $template_id,
                        'type' => $link['type'],
                        'url' => $link['url']
                    ]);
                }
            }
            if ($request->has('questions')) {
                // return 'cc';
                foreach ($request->questions as $question) {
                    $desc = null;
                    if ($question['type'] == 'image' && $question->hasFile('description')) {
                        $desc = $this->saveImage($question->description, 'question_images');
                    }
                    $type = null;
                    if ($question['type'] == 'question') {
                        $type = '0';
                    } elseif ($question['type'] == 'title') {
                        $type = '1';
                    } elseif ($question['type'] == 'image') {
                        $type = '2';
                    } elseif ($question['type'] == 'video') {
                        $type = '3';
                    }
                    $q_type = null;
                    if ($question['question_type'] == "Short answer") {
                        $q_type = "0";
                    } elseif ($question['question_type'] == "Paragraph") {
                        $q_type = "1";
                    } elseif ($question['question_type'] == "Multiple choice") {
                        $q_type = "2";
                    } elseif ($question['question_type'] == "Checkboxes") {
                        $q_type = "3";
                    } elseif ($question['question_type'] == "Dropdown") {
                        $q_type = "4";
                    } elseif ($question['question_type'] == "Date") {
                        $q_type = "5";
                    } elseif ($question['question_type'] == "Time") {
                        $q_type = "6";
                    } elseif ($question['question_type'] == "Phone number") {
                        $q_type = "7";
                    } elseif ($question['question_type'] == "Email") {
                        $q_type = "8";
                    } elseif ($question['question_type'] == "Name") {
                        $q_type = "9";
                    } elseif ($question['question_type'] == "Number") {
                        $q_type = "10";
                    }
                    $question_id = Question::insertGetId([
                        'form_id' => $template_id,
                        'type' => $type,
                        'description' => $desc ?? $question['description'],
                        'question_type' => $q_type,
                        'required' => ($question['required'] == true ? 1 : 0),
                        'focus' => ($question['focus'] == true ? 1 : 0),
                        'display_video' => ($question['display_video'] == true ? 1 : 0)
                    ]);
                    if (isset($question['options'])) {
                        foreach ($question['options'] as $option)
                            Option::create([
                                'question_id' => $question_id,
                                'value' => $option['value'],
                                'text' => $option['text']
                            ]);
                    }
                }
            }
            DB::commit();
            return $this->returnSuccessMessage('inserted successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->returnError('201', $e->getMessage());
        }
    }
    public function getForm($formId)
    {
        try {
            $form = Form::with(['Questions.options', 'socialMedia'])
                ->where('id', $formId)
                ->where('is_template', 0)
                ->first();
            if (!$form) {
                return $this->returnError('202', 'form not founded');
            }
            return $this->returnData('data', $form);
        } catch (\Exception $e) {
            return $this->returnError('201', $e->getMessage());
        }
    }

    public function getForms()
    {
        try {
            $forms = Form::select('id', 'logo', 'header', 'form_type', 'description', 'updated_at')
                ->where('user_id', Auth()->user()->id)
                ->get();
            return $this->returnData('data', $forms);
        } catch (\Exception $e) {
            return $this->returnError('201', $e->getMessage());
        }
    }

    public function sendForm(Request $request)
    {
        try {
            $rules = [
                'email' => 'required|email',
                'message' => 'required',
                'subject' => 'required',
                'link' => 'required'
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }
            Mail::to($request->email)->send(new FormMail($request->message, $request->subject, $request->link));
            return $this->returnSuccessMessage('mail send successfully');
        } catch (\Exception $e) {
            return $this->returnError('201', $e->getMessage());
        }
    }

    public function deleteForm($formId)
    {
        try {
            $form = Form::find($formId);
            if (!$form) {
                return $this->returnError('202', 'the form does not exist');
            }
            foreach ($form->Questions as $question) {
                foreach ($question->options as $option) {
                    $option->delete();
                }
                $question->delete();
            }
            foreach ($form->socialMedia as $link) {
                $link->delete();
            }
            $form->delete();
            return $this->returnSuccessMessage('deleted successfully');
        } catch (\Exception $e) {
            return $this->returnError('201', $e->getMessage());
        }
    }
}