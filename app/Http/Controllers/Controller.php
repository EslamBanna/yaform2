<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\Question;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function test()
    {
        $test = Question::get();
        return $test;
    }
    public function testT(Request $request)
    {
        Form::create([
            'user_id' => $request->user_id,
            'is_quiz' => $request->is_quiz,
            'is_template' => $request->is_template,
            "form_type" => $request->form_type
        ]);
        return 'ss';
    }
}
