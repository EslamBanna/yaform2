<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Form;
use App\Models\Submit;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AnswerContoller extends Controller
{
    use GeneralTrait;
    public function submitAnswer(Request $request)
    {
        DB::beginTransaction();
        try {
            // return $request;
            $rules = [
                'form_id' => 'required',
                'answers' => 'required|array',
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }
            $form = Form::find($request->form_id);
            if (!$form) {
                return $this->returnError('202', 'the form does not exist');
            }
            $submit_id = Submit::insertGetId([
                'form_id' => $request->form_id
            ]);
            foreach ($request->answers as $answer) {
                // multiple answer
                if (is_array($answer['answer'])) {
                    foreach ($answer['answer'] as $ans) {
                        Answer::create([
                            'submit_id' => $submit_id,
                            'question_id' => $answer['question_id'],
                            'answer' => $ans
                        ]);
                    }
                } else {
                    // one answer
                    Answer::create([
                        'submit_id' => $submit_id,
                        'question_id' => $answer['question_id'],
                        'answer' => $answer['answer']
                    ]);
                }
            }
            DB::commit();
            return $this->returnSuccessMessage('submit success');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->returnError('201', $e->getMessage());
        }
    }
}
