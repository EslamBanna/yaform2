<?php

namespace App\Http\Controllers;

use App\Mail\FormMail;
use App\Models\Form;
use App\Models\Option;
use App\Models\Question;
use App\Models\RightSolution;
use App\Models\SocialMediaLink;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

use function PHPUnit\Framework\isEmpty;

class FormContoller extends Controller
{
    use GeneralTrait;
    public function createTemplate(Request $request)
    {
        // return $request;
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
                'is_template' => 1,
                'description' => $request->description,
                'logo' => $logo,
                'style_theme' => $request->style_theme,
                'font_family' => $request->font_family,
                'msg' => $request->msg
            ]);
            DB::commit();
            return $this->returnData('data', $template_id);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->returnError('201', $e->getMessage());
        }
    }

    public function appendTemplate(Request $request, $template_id)
    {
        DB::beginTransaction();
        try {
            $template = Form::find($template_id);
            if (!$template) {
                return $this->returnError(202, 'this tempalte is not exist');
            }
            if ($template->is_template == 0) {
                return $this->returnError(202, 'this tempalte is not template');
            }
            if ($request->has('social_media')) {
                foreach ($request->social_media as $link) {
                    SocialMediaLink::create([
                        'form_id' => $template_id,
                        'type' => $link['type'],
                        'url' => $link['url']
                    ]);
                }
            }
            if ($request->has('questions')) {
                foreach ($request->questions as $question) {
                    $desc = null;
                    // if ($question['type'] == 'image' && $question->hasFile('description')) {
                    //     $desc = $this->saveImage($question->description, 'question_images');
                    // }else{
                    //     $desc = $question['description'];
                    // }
                    $desc = $question['description'];
                    // return $desc;
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
                    if ($type == '1' || $type == '2' || $type == '3') {
                        $q_type = "11";
                    }
                    $display_video = 0;
                    if (isset($question['display_video'])) {
                        $display_video = ($question['display_video'] == true ? 1 : 0);
                    }
                    $question_id = Question::insertGetId([
                        'form_id' => $template_id,
                        'question' => $question['question'],
                        'type' => $type,
                        'description' => $desc,
                        'question_type' => $q_type,
                        'required' => ($question['required'] == true ? 1 : 0),
                        'focus' => ($question['focus'] == true ? 1 : 0),
                        'display_video' => $display_video
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
            return $this->returnSuccessMessage('success');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->returnError('201', $e->getMessage());
        }
    }
    public function getTemplates()
    {
        try {
            $templates = Form::with(['Questions.options', 'socialMedia'])
                ->where('is_template', 1)
                // ->where('deleted', 0)
                // ->where('updated', 0)
                ->orderBy('id', 'DESC')->get();
            // ->get();
            return $this->returnData('data', $templates);
        } catch (\Exception $e) {
            return $this->returnError('201', $e->getMessage());
        }
    }
    public function getTemplate($templateId)
    {
        try {
            $template = Form::with(['Questions.options', 'socialMedia'])
                ->where('id', $templateId)
                ->where('is_template', 1)
                // ->where('deleted', 0)
                // ->where('updated', 0)
                ->first();

            if (!$template) {
                return $this->returnError('201', 'this template not exist');
            }
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
                return $this->returnError('203', 'form not founded');
            }
            if ($form->user_id != Auth()->user()->id) {
                return $this->returnError('204', 'form not belongs to you');
            }
            // if ($form->deleted == 1) {
            //     return $this->returnError('205', 'this form is deleted before');
            // }
            // if ($form->updated == 1) {
            //     return $this->returnError('205', 'this form is updated before');
            // }
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
                return $this->returnError('203', 'form not founded');
            }
            if ($form->user_id != Auth()->user()->id) {
                return $this->returnError('204', 'form not belongs to you');
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
        // return $request;
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
            $form_id = Form::insertGetId([
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
            DB::commit();
            return $this->returnData('data', $form_id);
            // return $this->returnSuccessMessage('inserted successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->returnError('201', $e->getMessage());
        }
    }

    public function appendForm(Request $request, $form_id)
    {
        DB::beginTransaction();
        try {
            $form = Form::find($form_id);
            if (!$form) {
                return $this->returnError(202, 'this form is not exist');
            }
            if ($form->is_template == 1) {
                return $this->returnError(202, 'this tempalte is not form');
            }
            if ($request->has('social_media')) {
                foreach ($request->social_media as $link) {
                    SocialMediaLink::create([
                        'form_id' => $form_id,
                        'type' => $link['type'],
                        'url' => $link['url']
                    ]);
                }
            }
            if ($request->has('questions')) {
                foreach ($request->questions as $question) {
                    $desc = "";
                    // if ($question['type'] == 'image' && $question->hasFile('description')) {
                    //     $desc = $this->saveImage($question->description, 'question_images');
                    // }else{
                    //     $desc = $question['description'];
                    // }
                    $desc = $question['description'];
                    // return $desc;
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
                    if ($type == '1' || $type == '2' || $type == '3') {
                        $q_type = "11";
                    }
                    $display_video = 0;
                    if (isset($question['display_video'])) {
                        $display_video = ($question['display_video'] == true ? 1 : 0);
                    }
                    $question_id = Question::insertGetId([
                        'form_id' => $form_id,
                        'type' => $type,
                        'question' => $question['question'],
                        'description' => $desc,
                        'question_type' => $q_type,
                        'required' => ($question['required'] == true ? 1 : 0),
                        'focus' => ($question['focus'] == true ? 1 : 0),
                        'display_video' => $display_video
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
            return $this->returnSuccessMessage('success');
        } catch (\Exception $e) {
            DB::rollBack();
            return  $this->returnError(201, $e->getMessage());
        }
    }

    public function getForm($formId)
    {
        try {
            $form = Form::with(['Questions.options', 'socialMedia'])
                ->with('user', function ($q) {
                    $q->select('id', 'name');
                })
                ->where('id', $formId)
                // ->where('is_template', 0)
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
            $forms = Form::select('id', 'logo', 'header', 'form_type', 'description', 'updated_at', 'is_quiz', 'is_template')
                ->where('user_id', Auth()->user()->id)
                ->orderBy('id', 'DESC')->get();
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

    public function createQuiz(Request $request)
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
            $quiz_id = Form::insertGetId([
                'user_id' => Auth()->user()->id,
                'form_type' => ($request->form_type == "classic form" ? '0' : '1'),
                'image_header' => $image_header ?? "",
                'header' => $request->header,
                'is_quiz' => 1,
                'is_template' => 0,
                'description' => $request->description,
                'logo' => $logo,
                'style_theme' => $request->style_theme,
                'font_family' => $request->font_family,
                'msg' => $request->msg
            ]);
            DB::commit();
            return $this->returnData('data', $quiz_id);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->returnError('201', $e->getMessage());
        }
    }

    public function appendQuiz(Request $request, $quiz_id)
    {
        DB::beginTransaction();
        try {
            $quiz = Form::find($quiz_id);
            if (!$quiz) {
                return $this->returnError(202, 'this quiz is not exist');
            }
            if ($quiz->is_template == 1) {
                return $this->returnError(202, 'this tempalte is not quiz');
            }
            if ($request->has('social_media')) {
                foreach ($request->social_media as $link) {
                    SocialMediaLink::create([
                        'form_id' => $quiz_id,
                        'type' => $link['type'],
                        'url' => $link['url']
                    ]);
                }
            }
            if ($request->has('questions')) {
                foreach ($request->questions as $question) {
                    $desc = null;
                    // if ($question['type'] == 'image' && $question->hasFile('description')) {
                    //     $desc = $this->saveImage($question['description'], 'question_images');
                    // }else{
                    //     $desc = $question['description'];
                    // }
                    $desc = $question['description'];
                    // return $desc;
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
                    if ($type == '1' || $type == '2' || $type == '3') {
                        $q_type = "11";
                    }
                    $display_video = 0;
                    if (isset($question['display_video'])) {
                        $display_video = ($question['display_video'] == true ? 1 : 0);
                    }
                    $question_id = Question::insertGetId([
                        'form_id' => $quiz_id,
                        'type' => $type,
                        'question' => $question['question'],
                        'description' => $desc,
                        'question_type' => $q_type,
                        'required' => ($question['required'] == true ? 1 : 0),
                        'focus' => ($question['focus'] == true ? 1 : 0),
                        'display_video' => $display_video
                    ]);
                    if (isset($question['options'])) {
                        foreach ($question['options'] as $option)
                            Option::create([
                                'question_id' => $question_id,
                                'value' => $option['value'],
                                'text' => $option['text']
                            ]);
                    }
                    if (isset($question['default_answer'])) {
                        if (is_array($question['default_answer'])) {
                            foreach ($question['default_answer'] as $answer)
                                RightSolution::create([
                                    'question_id' => $question_id,
                                    'solution' => $answer,
                                ]);
                        } else {
                            RightSolution::create([
                                'question_id' => $question_id,
                                'solution' => $question['default_answer'],
                            ]);
                        }
                    }
                }
            }
            DB::commit();
            return $this->returnSuccessMessage('success');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->returnError('201', $e->getMessage());
        }
    }

    public function deleteFormOrQuiz($formId)
    {
        DB::beginTransaction();
        try {
            $form = Form::find($formId);
            if (!$form) {
                return $this->returnError('202', 'the form does not exist');
            }
            if ($form->user_id != Auth()->user()->id) {
                return $this->returnError('203', 'the form does not belongs to you');
            }
            $form->delete();
            DB::commit();
            return $this->returnSuccessMessage('deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->returnError('201', $e->getMessage());
        }
    }

    public function updateForm($formId, Request $request)
    {
        DB::beginTransaction();
        try {
            $form = Form::find($formId);
            if (!$form) {
                return $this->returnError('202', 'the form does not exist');
            }
            if ($form->user_id != Auth()->user()->id) {
                return $this->returnError('203', 'the form does not belongs to you');
            }
            if ($form->is_quiz == 1) {
                return $this->returnError('204', 'this is quiz not form');
            }
            if ($form->is_template == 1) {
                return $this->returnError('205', 'this is template not form');
            }
            $image_header = null;
            if ($request->hasFile('image_header')) {
                $image_header = $this->saveImage($request->image_header, 'images_header');
            }
            $logo = null;
            if ($request->hasFile('logo')) {
                $logo = $this->saveImage($request->logo, 'logos');
            }
            if ($image_header == null) {
                $photo_len = strlen((isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/images/images_header/');
                $image_header = substr($form->image_header, $photo_len);
            }
            if ($logo == null) {
                $photo_len = strlen((isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/images/logos/');
                $logo = substr($form->logo, $photo_len);
            }

            $form->update([
                // 'form_type' => ($request->form_type == "classic form" ? '0' : '1'),
                'image_header' => $image_header,
                'header' => $request->header ?? $form->header,
                'is_quiz' => 0,
                'is_template' => 0,
                'description' => $request->description ?? $form->description,
                'logo' => $logo,
                'style_theme' => $request->style_theme ?? $form->style_theme,
                'font_family' => $request->font_family ?? $form->font_family,
                'msg' => $request->msg ?? $form->msg
            ]);
            DB::commit();
            return $this->returnSuccessMessage('updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->returnError('201', $e->getMessage());
        }
    }

    public function appendUpdateForm(Request $request, $formId)
    {
        DB::beginTransaction();
        try {
            $form = Form::find($formId);
            if (!$form) {
                return $this->returnError('202', 'the form does not exist');
            }
            if ($form->user_id != Auth()->user()->id) {
                return $this->returnError('203', 'the form does not belongs to you');
            }
            if ($form->is_quiz == 1) {
                return $this->returnError('204', 'this is quiz not form');
            }
            if ($form->is_template == 1) {
                return $this->returnError('205', 'this is template not form');
            }

            if ($request->has('social_media')) {
                $form->socialMedia()->delete();
                foreach ($request->social_media as $link) {
                    SocialMediaLink::create([
                        'form_id' => $formId,
                        'type' => $link['type'],
                        'url' => $link['url']
                    ]);
                }
            }
            if ($request->has('questions')) {
                foreach ($request->questions as $question) {
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
                    if ($type == '1' || $type == '2' || $type == '3') {
                        $q_type = "11";
                    }

                    if (!isset($question['question_id'])) {
                        $display_video = 0;
                        if (isset($question['display_video'])) {
                            $display_video = ($question['display_video'] == true ? 1 : 0);
                        }
                        $question_id = Question::insertGetId([
                            'form_id' => $form->id,
                            'type' => $type,
                            'question' => $question['question'],
                            'description' => $question['description'],
                            'question_type' => $q_type,
                            'required' => ($question['required'] == true ? 1 : 0),
                            'focus' => ($question['focus'] == true ? 1 : 0),
                            'display_video' => $display_video
                        ]);
                        if (isset($question['options'])) {
                            foreach ($question['options'] as $option)
                                Option::create([
                                    'question_id' => $question_id,
                                    'value' => $option['value'],
                                    'text' => $option['text']
                                ]);
                        }
                    } else {
                        $question_find = Question::find($question['question_id']);
                        if (!$question_find) {
                            return $this->returnError(202, 'this question ' . $question['question_id'] . ' is not exist');
                            // return $this->returnError(202, 'this question is not exist');
                        }

                        $q_required = null;
                        if (isset($question['required'])) {
                            $q_required = ($question['required'] == true ? 1 : 0);
                        } else {
                            $q_required = ($question_find->required == false ? 0 : 1);
                        }

                        $q_focus = null;
                        if (isset($question['focus'])) {
                            $q_focus = ($question['focus'] == true ? 1 : 0);
                        } else {
                            $q_focus = ($question_find->focus == true ? 1 : 0);
                        }

                        $display_video = null;
                        if (isset($question['display_video'])) {
                            $display_video = ($question['display_video'] == true ? 1 : 0);
                        } else {
                            $display_video = ($question_find->display_video == true ? 1 : 0);
                        }
                        $question_find->update([
                            'type' => $type ?? $question_find->type,
                            'description' => $question['description'] ?? $question_find->description,
                            'question' => $question['question'] ?? $question_find->question,
                            'question_type' => $q_type ?? $question_find->question_type,
                            'required' => $q_required,
                            'focus' => $q_focus,
                            'display_video' => $display_video
                        ]);
                        if (isset($question['options'])) {
                            $question_find->options()->delete();
                            foreach ($question['options'] as $option) {
                                Option::create([
                                    'question_id' => $question_find['id'],
                                    'value' => $option['value'],
                                    'text' => $option['text']
                                ]);
                            }
                        }
                    }
                }
            }
            if ($request->has('deleted_questions_id')) {
                foreach ($request->deleted_questions_id as $deleted_question_id) {
                    $question_find = Question::find($deleted_question_id);
                    if (!$question_find) {
                        return $this->returnError(202, 'this question ' . $deleted_question_id . ' is not exist');
                    }
                    $question_find->delete();
                }
            }
            DB::commit();
            return $this->returnSuccessMessage('updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->returnError('201', $e->getMessage());
        }
    }

    public function updateQuiz($quizId, Request $request)
    {
        DB::beginTransaction();
        try {
            $quiz = Form::find($quizId);
            if (!$quiz) {
                return $this->returnError('202', 'the quiz does not exist');
            }
            if ($quiz->user_id != Auth()->user()->id) {
                return $this->returnError('203', 'the quiz does not belongs to you');
            }
            if ($quiz->is_quiz == 0) {
                return $this->returnError('204', 'this is not quiz');
            }
            if ($quiz->is_template == 1) {
                return $this->returnError('205', 'this is template not form');
            }
            $image_header = null;
            if ($request->hasFile('image_header')) {
                $image_header = $this->saveImage($request->image_header, 'images_header');
            }
            $logo = null;
            if ($request->hasFile('logo')) {
                $logo = $this->saveImage($request->logo, 'logos');
            }
            if ($image_header == null) {
                $photo_len = strlen((isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/images/images_header/');
                $image_header = substr($quiz->image_header, $photo_len);
            }
            if ($logo == null) {
                $photo_len = strlen((isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/images/logos/');
                $logo = substr($quiz->logo, $photo_len);
            }

            $quiz->update([
                'image_header' => $image_header,
                'header' => $request->header ?? $quiz->header,
                'description' => $request->description ?? $quiz->description,
                'logo' => $logo,
                'style_theme' => $request->style_theme ?? $quiz->style_theme,
                'font_family' => $request->font_family ?? $quiz->font_family,
                'msg' => $request->msg ?? $quiz->msg
            ]);
            DB::commit();
            return $this->returnSuccessMessage('updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->returnError('201', $e->getMessage());
        }
    }

    public function appendUpdateQuiz(Request $request, $quizId)
    {
        DB::beginTransaction();
        try {
            $quiz = Form::find($quizId);
            if (!$quiz) {
                return $this->returnError('202', 'the quiz does not exist');
            }
            if ($quiz->user_id != Auth()->user()->id) {
                return $this->returnError('203', 'the quiz does not belongs to you');
            }
            if ($quiz->is_quiz == 0) {
                return $this->returnError('204', 'this is not quiz');
            }
            if ($quiz->is_template == 1) {
                return $this->returnError('205', 'this is template not quiz');
            }

            if ($request->has('social_media')) {
                $quiz->socialMedia()->delete();
                foreach ($request->social_media as $link) {
                    SocialMediaLink::create([
                        'form_id' => $quizId,
                        'type' => $link['type'],
                        'url' => $link['url']
                    ]);
                }
            }
            if ($request->has('questions')) {
                foreach ($request->questions as $question) {
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
                    if ($type == '1' || $type == '2' || $type == '3') {
                        $q_type = "11";
                    }

                    if (!isset($question['question_id'])) {
                        $display_video = 0;
                        if (isset($question['display_video'])) {
                            $display_video = ($question['display_video'] == true ? 1 : 0);
                        }
                        $question_id = Question::insertGetId([
                            'form_id' => $quiz->id,
                            'type' => $type,
                            'question' => $question['question'],
                            'description' => $question['description'],
                            'question_type' => $q_type,
                            'required' => ($question['required'] == true ? 1 : 0),
                            'focus' => ($question['focus'] == true ? 1 : 0),
                            'display_video' => $display_video
                        ]);
                        if (isset($question['options'])) {
                            foreach ($question['options'] as $option)
                                Option::create([
                                    'question_id' => $question_id,
                                    'value' => $option['value'],
                                    'text' => $option['text']
                                ]);
                        }
                        if (isset($question['default_answer'])) {
                            if (is_array($question['default_answer'])) {
                                foreach ($question['default_answer'] as $answer)
                                    RightSolution::create([
                                        'question_id' => $question_id,
                                        'solution' => $answer,
                                    ]);
                            } else {
                                RightSolution::create([
                                    'question_id' => $question_id,
                                    'solution' => $question['default_answer'],
                                ]);
                            }
                        }
                    } else {
                        $question_find = Question::find($question['question_id']);
                        if (!$question_find) {
                            return $this->returnError(202, 'this question ' . $question['question_id'] . ' is not exist');
                        }

                        $q_required = null;
                        if (isset($question['required'])) {
                            $q_required = ($question['required'] == true ? 1 : 0);
                        } else {
                            $q_required = ($question_find->required == false ? 0 : 1);
                        }

                        $q_focus = null;
                        if (isset($question['focus'])) {
                            $q_focus = ($question['focus'] == true ? 1 : 0);
                        } else {
                            $q_focus = ($question_find->focus == true ? 1 : 0);
                        }

                        $display_video = null;
                        if (isset($question['display_video'])) {
                            $display_video = ($question['display_video'] == true ? 1 : 0);
                        } else {
                            $display_video = ($question_find->display_video == true ? 1 : 0);
                        }
                        $question_find->update([
                            'type' => $type ?? $question_find->type,
                            'description' => $question['description'] ?? $question_find->description,
                            'question' => $question['question'] ?? $question_find->question,
                            'question_type' => $q_type ?? $question_find->question_type,
                            'required' => $q_required,
                            'focus' => $q_focus,
                            'display_video' => $display_video
                        ]);
                        if (isset($question['options'])) {
                            $question_find->options()->delete();
                            foreach ($question['options'] as $option)
                                Option::create([
                                    'question_id' => $question_find['id'],
                                    'value' => $option['value'],
                                    'text' => $option['text']
                                ]);
                        }
                        if (isset($question['default_answer'])) {
                            $question_find->rightSolutions()->delete();
                            if (is_array($question['default_answer'])) {
                                foreach ($question['default_answer'] as $answer)
                                    RightSolution::create([
                                        'question_id' => $question_find['id'],
                                        'solution' => $answer,
                                    ]);
                            } else {
                                RightSolution::create([
                                    'question_id' => $question_find['id'],
                                    'solution' => $question['default_answer'],
                                ]);
                            }
                        }
                    }
                }
            }
            if ($request->has('deleted_questions_id')) {
                foreach ($request->deleted_questions_id as $deleted_question_id) {
                    $question_find = Question::find($deleted_question_id);
                    if (!$question_find) {
                        return $this->returnError(202, 'this question' . $deleted_question_id . ' is not exist');
                    }
                    $question_find->delete();
                }
            }
            DB::commit();
            return $this->returnSuccessMessage('updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->returnError('201', $e->getMessage());
        }
    }

    public function updateTemplate($templateId, Request $request)
    {
        DB::beginTransaction();
        try {
            $tempalte = Form::find($templateId);
            if (!$tempalte) {
                return $this->returnError('202', 'the tempalte does not exist');
            }
            if ($tempalte->user_id != Auth()->user()->id) {
                return $this->returnError('203', 'the tempalte does not belongs to you');
            }
            if ($tempalte->is_quiz == 1) {
                return $this->returnError('204', 'this is not tempalte');
            }
            if ($tempalte->is_template == 0) {
                return $this->returnError('205', 'this is not template ');
            }
            $image_header = null;
            if ($request->hasFile('image_header')) {
                $image_header = $this->saveImage($request->image_header, 'images_header');
            }
            $logo = null;
            if ($request->hasFile('logo')) {
                $logo = $this->saveImage($request->logo, 'logos');
            }
            if ($image_header == null) {
                $photo_len = strlen((isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/images/images_header/');
                $image_header = substr($tempalte->image_header, $photo_len);
            }
            if ($logo == null) {
                $photo_len = strlen((isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/images/logos/');
                $logo = substr($tempalte->logo, $photo_len);
            }

            $tempalte->update([
                // 'form_type' => ($request->form_type == "classic form" ? '0' : '1'),
                'image_header' => $image_header,
                'header' => $request->header ?? $tempalte->header,
                'is_quiz' => 0,
                'is_template' => 0,
                'description' => $request->description ?? $tempalte->description,
                'logo' => $logo,
                'style_theme' => $request->style_theme ?? $tempalte->style_theme,
                'font_family' => $request->font_family ?? $tempalte->font_family,
                'msg' => $request->msg ?? $tempalte->msg
            ]);
            DB::commit();
            return $this->returnSuccessMessage('updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->returnError('201', $e->getMessage());
        }
    }

    public function appendUpdateTemplate(Request $request, $templateId)
    {
        DB::beginTransaction();
        try {
            $tempalte = Form::find($templateId);
            if (!$tempalte) {
                return $this->returnError('202', 'the tempalte does not exist');
            }
            if ($tempalte->user_id != Auth()->user()->id) {
                return $this->returnError('203', 'the tempalte does not belongs to you');
            }
            if ($tempalte->is_quiz == 1) {
                return $this->returnError('204', 'this is not tempalte');
            }
            if ($tempalte->is_template == 0) {
                return $this->returnError('205', 'this is not template ');
            }

            if ($request->has('social_media')) {
                $tempalte->socialMedia()->delete();
                foreach ($request->social_media as $link) {
                    SocialMediaLink::create([
                        'form_id' => $templateId,
                        'type' => $link['type'],
                        'url' => $link['url']
                    ]);
                }
            }
            if ($request->has('questions')) {
                foreach ($request->questions as $question) {
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
                    if ($type == '1' || $type == '2' || $type == '3') {
                        $q_type = "11";
                    }

                    if (!isset($question['question_id'])) {
                        $display_video = 0;
                        if (isset($question['display_video'])) {
                            $display_video = ($question['display_video'] == true ? 1 : 0);
                        }
                        $question_id = Question::insertGetId([
                            'form_id' => $tempalte->id,
                            'type' => $type,
                            'question' => $question['question'],
                            'description' => $question['description'],
                            'question_type' => $q_type,
                            'required' => ($question['required'] == true ? 1 : 0),
                            'focus' => ($question['focus'] == true ? 1 : 0),
                            'display_video' => $display_video
                        ]);
                        if (isset($question['options'])) {
                            foreach ($question['options'] as $option)
                                Option::create([
                                    'question_id' => $question_id,
                                    'value' => $option['value'],
                                    'text' => $option['text']
                                ]);
                        }
                    } else {
                        $question_find = Question::find($question['question_id']);
                        if (!$question_find) {
                            return $this->returnError(202, 'this question ' . $question['question_id'] . ' is not exist');
                            // return $this->returnError(202, 'this question is not exist');
                        }

                        $q_required = null;
                        if (isset($question['required'])) {
                            $q_required = ($question['required'] == true ? 1 : 0);
                        } else {
                            $q_required = ($question_find->required == false ? 0 : 1);
                        }

                        $q_focus = null;
                        if (isset($question['focus'])) {
                            $q_focus = ($question['focus'] == true ? 1 : 0);
                        } else {
                            $q_focus = ($question_find->focus == true ? 1 : 0);
                        }

                        $display_video = null;
                        if (isset($question['display_video'])) {
                            $display_video = ($question['display_video'] == true ? 1 : 0);
                        } else {
                            $display_video = ($question_find->display_video == true ? 1 : 0);
                        }
                        $question_find->update([
                            'type' => $type ?? $question_find->type,
                            'description' => $question['description'] ?? $question_find->description,
                            'question' => $question['question'] ?? $question_find->question,
                            'question_type' => $q_type ?? $question_find->question_type,
                            'required' => $q_required,
                            'focus' => $q_focus,
                            'display_video' => $display_video
                        ]);
                        if (isset($question['options'])) {
                            $question_find->options()->delete();
                            foreach ($question['options'] as $option) {
                                Option::create([
                                    'question_id' => $question_find['id'],
                                    'value' => $option['value'],
                                    'text' => $option['text']
                                ]);
                            }
                        }
                    }
                }
            }
            if ($request->has('deleted_questions_id')) {
                foreach ($request->deleted_questions_id as $deleted_question_id) {
                    $question_find = Question::find($deleted_question_id);
                    if (!$question_find) {
                        return $this->returnError(202, 'this question ' . $deleted_question_id . ' is not exist');
                    }
                    $question_find->delete();
                }
            }
            DB::commit();
            return $this->returnSuccessMessage('updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->returnError('201', $e->getMessage());
        }
    }
}
