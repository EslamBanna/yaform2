<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;
    protected $table = 'questions';
    protected $fillable = [
        'form_id',
        'type',
        'description',
        'question_type',
        'required',
        'focus',
        'display_video'
    ];
}
