<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialMediaLink extends Model
{
    use HasFactory;
    protected $table = 'social_media_links';
    protected $fillable = [
        'form_id',
        'type',
        'url'
    ];

    public function setTypeAttribute($value)
    {
        if ($value == 'Facebook') {
            $this->attributes['form_type'] = '0';
        } elseif ($value == 'twitter') {
            $this->attributes['form_type'] = '1';
        } elseif ($value == 'instgram') {
            $this->attributes['form_type'] = '2';
        }
    }

    public function getTypeAttribute($value)
    {
        if ($value == '0') {
            return 'Facebook';
        } elseif ($value == '1') {
            return 'twitter';
        } elseif ($value == '2') {
            return 'instgram';
        } else {
            return "";
        }
    }
    public function getUrlAttribute($value)
    {
        return $value ?? "";
    }
}
