<?php

namespace App\Http\Controllers;

use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use App\Exports\ResponsesExport;
use App\Models\Answer;
use App\Models\Form;
use App\Models\Question;
use Excel;
use PDF;

class ExportController extends Controller
{
    use GeneralTrait;

    private function prepareData($formId)
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
            $data = [
                'responses' => $response,
                'form_questions' => $form_questions
            ];
            return $data;
        } catch (\Exception $e) {
            return $this->returnError(201, $e->getMessage());
        }
    }
    public function exportExcel($formId)
    {
        try {
            $data = $this->prepareData($formId);
            return Excel::download(new ResponsesExport($data['form_questions'], collect($data['response'])), 'form_answers.xlsx');
        } catch (\Exception $e) {
            return $this->returnError(201, $e->getMessage());
        }
    }

    public function showPdf($formId)
    {
        try {
            $data = $this->prepareData($formId);
            $response = $data['responses'];
            $form_questions = $data['form_questions'];
            return view('pdf', compact('response', 'form_questions'));
        } catch (\Exception $e) {
            return $this->returnError(201, $e->getMessage());
        }
    }

    public function exportPdf($formId = 1)
    {
        try {
            $data = $this->prepareData($formId);
            // $data = Answer::all();
            // $data = Employee::all();
            // share data to view
            $response = $data['responses'];
            $form_questions = $data['form_questions'];
            // view()->share('pdf', $data);
            $pdf = PDF::loadView('pdf', compact('response','form_questions'));
            // download PDF file with download method
            return $pdf->download('pdf_file.pdf');
        } catch (\Exception $e) {
            return $this->returnError(201, $e->getMessage());
        }
    }
}
