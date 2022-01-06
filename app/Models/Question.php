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

    protected $casts = [
        'required' => 'boolean',
        'focus' => 'boolean',
        'display_video' => 'boolean'
    ];

    public function setTypeAttribute($value)
    {
        if ($value == 'question') {
            $this->attributes['type'] = '0';
        } elseif ($value == 'title') {
            $this->attributes['type'] = '1';
        } elseif ($value == 'image') {
            $this->attributes['type'] = '2';
        } elseif ($value == 'video') {
            $this->attributes['type'] = '3';
        }
    }
    public function setQuestionTypeAttribute($value)
    {
        if ($value == 'Short answer') {
            $this->attributes['question_type'] = '0';
        } elseif ($value == 'Paragraph') {
            $this->attributes['question_type'] = '1';
        } elseif ($value == 'Multiple choice') {
            $this->attributes['question_type'] = '2';
        } elseif ($value == 'Checkboxes') {
            $this->attributes['question_type'] = '3';
        } elseif ($value == 'Dropdown') {
            $this->attributes['question_type'] = '4';
        } elseif ($value == 'Date') {
            $this->attributes['question_type'] = '5';
        } elseif ($value == 'Time') {
            $this->attributes['question_type'] = '6';
        } elseif ($value == 'Phone number') {
            $this->attributes['question_type'] = '7';
        } elseif ($value == 'Email') {
            $this->attributes['question_type'] = '8';
        } elseif ($value == 'Name') {
            $this->attributes['question_type'] = '9';
        } elseif ($value == 'Number') {
            $this->attributes['question_type'] = '10';
        }
    }
    public function getTypeAttribute($value)
    {
        if ($value == '0') {
            return 'question';
        } elseif ($value == '1') {
            return 'title';
        } elseif ($value == '2') {
            return 'image';
        } elseif ($value == '3') {
            return 'video';
        } else {
            return "";
        }
    }
    public function getDescriptionAttribute($value)
    {

        if ($this->attributes['type'] == '2') {
            $actual_link = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/';
            return ($value == null ? '' : $actual_link . 'images/question_images/' . $value);
        } else {
            return $value ?? "";
        }
    }
    public function getQuestionTypeAttribute($value)
    {
        if ($value == '0') {
            return 'Short answer';
        } elseif ($value == '1') {
            return 'Paragraph';
        } elseif ($value == '2') {
            return 'Multiple choice';
        } elseif ($value == '3') {
            return 'Checkboxes';
        } elseif ($value == '4') {
            return 'Dropdown';
        } elseif ($value == '5') {
            return 'Date';
        } elseif ($value == '6') {
            return 'Time';
        } elseif ($value == '7') {
            return 'Phone number';
        } elseif ($value == '8') {
            return 'Email';
        } elseif ($value == '9') {
            return 'Name';
        } elseif ($value == '10') {
            return 'Number';
        } else {
            return "";
        }
    }
}
