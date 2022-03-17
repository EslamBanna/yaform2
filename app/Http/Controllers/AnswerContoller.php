<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Form;
use App\Models\Question;
use App\Models\RightSolution;
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
            $form = Form::withCount(['Questions' => function ($q) {
                $q->where('type', '0')
                    ->where('question_type', '!=', '7')
                    ->where('question_type', '!=', '8')
                    ->where('question_type', '!=', '9');
            }])->find($request->form_id);
            if (!$form) {
                return $this->returnError('202', 'the form does not exist');
            }
            // return $form;
            $submit_id = Submit::insertGetId([
                'form_id' => $request->form_id
            ]);
            $output_msg = 'submit success';
            if ($form['is_quiz'] == true) {
                $score = 0;
                $marks = $form['questions_count'];
                foreach ($request->answers as $answer) {
                    // multiple answer
                    if (is_array($answer['answer'])) {
                        $iternal_score = 0;
                        $iternal_mark = RightSolution::where('question_id', $answer['question_id'])->count();
                        foreach ($answer['answer'] as $ans) {
                            Answer::create([
                                'submit_id' => $submit_id,
                                'question_id' => $answer['question_id'],
                                'answer' => $ans
                            ]);
                            $right_answer = RightSolution::where('question_id', $answer['question_id'])
                                ->where('solution', $ans)
                                ->count();
                            if ($right_answer > 0) {
                                $iternal_score++;
                            }
                        }
                        if ($iternal_score == $iternal_mark && $iternal_score != 0) {
                            $score++;
                        }
                    } else {
                        // one answer
                        Answer::create([
                            'submit_id' => $submit_id,
                            'question_id' => $answer['question_id'],
                            'answer' => $answer['answer']
                        ]);
                        $right_answer = RightSolution::where('question_id', $answer['question_id'])
                            ->where('solution', $answer['answer'])
                            ->get();
                        if (count($right_answer) > 0) {
                            $score++;
                        }
                    }
                }
                $submit = Submit::find($submit_id);
                $submit->update([
                    'score' => $score,
                    'marks' => $marks
                ]);
                $output_msg = "Your Score is " . $score . " from " . $marks;
            } else {
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
            }
            DB::commit();


            return $this->returnSuccessMessage($output_msg);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->returnError('201', $e->getMessage());
        }
    }

    public function uploadImage(Request $request)
    {
        // DB::beginTransaction();
        try {
            $file_name = '';
            if (!$request->hasFile('image')) {
                return $this->returnError(202, 'you must send the file');
            }
            $file_name = $this->saveImage($request->image, 'question_images');
            // DB::commit();

            // $actual_link = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/';
            // $full_path = ($file_name == null ? '' : $actual_link . 'images/question_images/' . $file_name);
            // $data = [
            //     'file_name' => $file_name,
            //     'full_path' => $full_path
            // ];
            return $this->returnData('data', $file_name);
        } catch (\Exception $e) {
            // DB::rollBack();
            return $this->returnError('201', $e->getMessage());
        }
    }

    public function getSummaryResponses($formId)
    {
        try {
            $form = Form::with(['Questions' => function ($q) {
                $q->with(['options', 'answers' => function ($k) {
                    $k->select('answer', 'submit_id', 'question_id')
                        ->selectRaw('count(answer) as repeat_count')
                        ->groupBy('answer')
                        // ->orderBy('qty', 'DESC')
                        ->get();
                }])
                    // ->has('options')
                    ->with('options')
                    ->withCount('answers as question_responses_count');
            }])
                ->withCount('submits as response_count')
                ->find($formId);
            if (!$form) {
                return $this->returnError(202, 'this form does not exist');
            }
            return $this->returnData('data', $form);
        } catch (\Exception $e) {
            return $this->returnError('201', $e->getMessage());
        }
    }

    public function getQuestionResponses($questionId)
    {
        try {
            $question = Question::with('answers')->find($questionId);
            if (!$question) {
                return $this->returnError(202, 'this question does not exist');
            }
            return $this->returnData('data', $question);
        } catch (\Exception $e) {
            return $this->returnError('201', $e->getMessage());
        }
    }

    public function getSubmitsForFrom($formId)
    {
        try {
            $form = Form::find($formId);
            if (!$form) {
                return $this->returnError(202, 'this form is not exist');
            }
            $subimts = Submit::where('form_id', $formId)->get();
            return $this->returnData('data', $subimts);
        } catch (\Exception $e) {
            return $this->returnError('201', $e->getMessage());
        }
    }

    public function getIndividualResponses($submitId)
    {
        try {
            $submit = Submit::with(['form.Questions.options', 'form.Questions.answers' => function ($q) use ($submitId) {
                $q->where('submit_id', $submitId);
            }])->find($submitId);
            if (!$submit) {
                return $this->returnError(202, 'this submit does not exist');
            }
            return $this->returnData('data', $submit);
        } catch (\Exception $e) {
            return $this->returnError('201', $e->getMessage());
        }
    }
}
