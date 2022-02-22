<?php

namespace App\Http\Controllers;

use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use App\Exports\ResponsesExport;
use App\Models\Answer;
use App\Models\Form;
use App\Models\Question;
use Excel;

class ExportController extends Controller
{
    use GeneralTrait;
    public function exportExcel($formId)
    {
        try {

            $form_all = Form::with('submits.answers')->find($formId);
            $form_counted = Form::with(['submits.answers' => function ($q) {
                $q->select('*')->selectRaw('count(question_id) as answer_len')->groupBy(['question_id', 'submit_id']);
            }])->find($formId);
            if (!$form_all) {
                return $this->returnError(202, 'this form does not exist');
            }
            $response = [];
            $pointer = 0;
            foreach ($form_counted->submits as $ind => $submit) {
                $pointer = 0;
                foreach ($submit->answers as $index => $answer) {
                    $te = '';
                    if ($answer['answer_len'] > 1) {
                        for ($i = 0; $i < $answer['answer_len']; $i++) {
                            if ($i == 0) {
                                $te = $form_all['submits'][$ind]['answers'][$pointer]['answer'];
                            } else {
                                $te = $te . ',' . $form_all['submits'][$ind]['answers'][$pointer]['answer'];
                            }
                            $pointer++;
                            if ($i == $answer['answer_len'] - 1) {
                                $response[$ind]['test' . $index] = $te;
                            }
                        }
                    } else {
                        $pointer++;
                        $response[$ind]['test' . $index] = $answer['answer'];
                    }
                }
            }
            $form_questions = Question::select('id', 'question')->where('form_id', $formId)->get();
            return Excel::download(new ResponsesExport($form_questions, collect($response)), 'test.xlsx');
        } catch (\Exception $e) {
            return $this->returnError(201, $e->getMessage());
        }
    }
}
