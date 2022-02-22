<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ResponsesExport implements FromCollection, WithHeadings
{

    private $form_answers;
    private $form_questions;
    public function __construct($form_questions, $form_answers)
    {
        $this->form_answers = $form_answers;
        $this->form_questions = $form_questions;
    }
    public function headings(): array
    {
        // $heads = ['#'];
        $heads = [];
        $ind = 1;
        foreach ($this->form_questions as $form_question) {
            $heads[$ind] = $form_question['question'];
            $ind++;
        }
        return [
            $heads
        ];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->form_answers;
    }
}
