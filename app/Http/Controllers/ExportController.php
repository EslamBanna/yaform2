<?php

namespace App\Http\Controllers;

use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use App\Exports\ResponsesExport;
use App\Models\Answer;
use App\Models\Form;
use App\Models\Question;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use JeroenDesloovere\VCard\VCard;
use JeroenDesloovere\VCard\Property\Name;
use JeroenDesloovere\VCard\Formatter\Formatter;
use JeroenDesloovere\VCard\Formatter\VcfFormatter;
use JeroenDesloovere\VCard\Property\Telephone;

// use Excel;
// use PDF;

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
            return Excel::download(new ResponsesExport($data['form_questions'], collect($data['responses'])), 'form_answers.xlsx');
        } catch (\Exception $e) {
            return $this->returnError(201, $e->getMessage());
        }
    }

    public function showPdf($formId)
    {
        try {
            $data = $this->prepareData($formId);
            $response = $data['responses'];
            $show_button = true;
            $form_questions = $data['form_questions'];
            return view('pdf', compact('response', 'form_questions', 'formId', 'show_button'));
        } catch (\Exception $e) {
            return $this->returnError(201, $e->getMessage());
        }
    }

    public function exportPdf($formId)
    {
        try {
            $data = $this->prepareData($formId);
            $response = $data['responses'];
            $form_questions = $data['form_questions'];
            $pdf = Pdf::loadView('pdf', compact('response', 'form_questions', 'formId'));
            return $pdf->download('pdf_file.pdf');
        } catch (\Exception $e) {
            return $this->returnError(201, $e->getMessage());
        }
    }

    public function exportVcf($formId)
    {
        try {
            $form = Form::find($formId);
            if (!$form) {
                return $this->returnError(202, 'this form is not availabale');
            }

            $q_name = Question::where('form_id', $formId)
                ->where('question_type', '9')
                ->select('id')
                ->first();

            $q_phone = Question::where('form_id', $formId)
                ->where('question_type', '10')
                ->select('id')
                ->first();

            $form = Form::with(['submits.answers' => function ($q) use ($q_name, $q_phone) {
                $q->where('question_id', $q_name['id'])
                    ->orWhere('question_id', $q_phone['id'])->get();
            }])->find($formId);

            $formatter = new Formatter(new VcfFormatter(), 'yaform-vcard-export '. $form['header']);
            foreach ($form['submits'] as $submit) {
                $vcard = null;
                $pointer = 0;
                $rand_name = rand(2, 100);
                $name = "yaform " . $rand_name;
                $phone = null;
                foreach ($submit['answers'] as $answer) {
                    if ($answer['question_id'] == $q_name['id']) {
                        // name
                        $name = $answer['answer'];
                        $pointer++;
                    } elseif ($answer['question_id'] == $q_phone['id']) {
                        // phone
                        $phone = $answer['answer'];
                        $pointer++;
                    }
                    if ($pointer == 2) {
                        $lastname = "";
                        $firstname = $name;
                        $additional = "";
                        $prefix = "";
                        $suffix = "";
                        $vcard = new VCard();
                        $vcard->add(new Telephone($phone));
                        $vcard->add(new Name($lastname, $firstname, $additional, $prefix, $suffix));
                        $formatter->addVCard($vcard);
                    }
                }
            }
            $formatter->download();
            // return $this->returnSuccessMessage('exported successfully');
        } catch (\Exception $e) {
            return $this->returnError(201, $e->getMessage());
        }
    }
}
