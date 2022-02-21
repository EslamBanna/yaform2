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
        'msg',
        // 'deleted',
        // 'updated'
    ];

    protected $casts = [
        'is_quiz' => 'boolean',
        'is_template' => 'boolean',
        'accept_response' => 'boolean'
    ];

    public function setFormTypeAttribute($value)
    {
        if ($value == 'classic form') {
            $this->attributes['form_type'] = '0';
        } elseif ($value == 'card form') {
            $this->attributes['form_type'] = '1';
        }
    }

    public function getFormTypeAttribute($value)
    {
        $val = $value ?? "";
        if ($val == '0') {
            return 'classic form';
        } else if ($val == '1') {
            return 'card form';
        }
        return $val;
        // return ($value == null ? "" : ($value == "0" ? 'classic form' : 'card form'));
    }
    public function getImageHeaderAttribute($value)
    {
        $actual_link = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/';
        return ($value == null ? '' : $actual_link . 'images/images_header/' . $value);
    }
    public function getHeaderAttribute($value)
    {
        return $value ?? "";
    }
    public function getDescriptionAttribute($value)
    {
        return $value ?? "";
    }
    public function getLogoAttribute($value)
    {
        $actual_link = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/';
        return ($value == null ? '' : $actual_link . 'images/logos/' . $value);
    }
    public function getStyleThemeAttribute($value)
    {
        return $value ?? "";
    }
    public function getFontFamilyAttribute($value)
    {
        return $value ?? "";
    }
    public function getMsgAttribute($value)
    {
        return $value ?? "";
    }
    ####### relations ###########
    public function Questions()
    {
        return $this->hasMany(Question::class, 'form_id', 'id');
    }
    public function socialMedia()
    {
        return $this->hasMany(SocialMediaLink::class, 'form_id', 'id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function submits()
    {
        return $this->hasMany(Submit::class, 'form_id', 'id');
    }

    public static function boot()
    {
        parent::boot();
        self::deleting(function ($form) {
            $form->socialMedia()->each(function ($socialMedia) {
                $socialMedia->delete();
            });

            $form->Questions()->each(function ($questions) {
                $questions->delete();
            });

            $form->submits()->each(function ($submits) {
                $submits->delete();
            });
        });
    }
}
