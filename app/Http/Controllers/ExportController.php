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
            $form = Form::find($formId);
            if (!$form) {
                return $this->returnError(202, 'this form does not exist');
            }
            $form_questions = Question::where('form_id',$formId)->get();
            $form_answers = Answer::->get();
            return Excel::download(new ResponsesExport($form_questions, $form_answers), 'test.xlsx');
        } catch (\Exception $e) {
            return $this->returnError(201, $e->getMessage());
        }
    }
}
