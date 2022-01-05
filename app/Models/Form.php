<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    use HasFactory;
    protected $table = 'forms';
    protected $fillable = [
        'user_id',
        'form_type',
        'image_header',
        'header',
        'is_quiz',
        'is_template',
        'description',
        'logo',
        'style_theme',
        'font_family',
        'accept_response',
        'msg'
    ];
}
