<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;
    protected $table = 'answers';
    protected $fillable = [
        'submit_id',
        'answer'
    ];

    public function getAnswerAttribute($value)
    {
        return $value ?? "";
    }
}