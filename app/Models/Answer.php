<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use PhpParser\Node\Expr\BinaryOp\Equal;

class Answer extends Model
{
    use HasFactory;
    protected $table = 'answers';
    protected $fillable = [
        'submit_id',
        'question_id',
        'answer'
    ];

    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id', 'id');
    }

    public function getAnswerAttribute($value)
    {
        return $value ?? "";
    }

    public function relatedAnswers()
    {
        // return $this->hasMany(Answer::class, 'answer', 'answer')->where('question_id', '=' , $this->question_id);
        return $this->hasMany(Answer::class, 'answer', 'answer')
            ->select('id', 'question_id', 'answer', 'submit_id');
        // ->where('question_id', '=' , $this->question_id);
        // ->orWhere('question_id', '=' ,$this->question_id);
    }

    public function relatedAnswer()
    {
        return $this->belongsTo(Answer::class, 'answer', 'answer');
    }
}
