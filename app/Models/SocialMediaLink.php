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
            $this->attributes['type'] = '0';
        } elseif ($value == 'Twitter') {
            $this->attributes['type'] = '1';
        } elseif ($value == 'Instgram') {
            $this->attributes['type'] = '2';
        }
    }

    public function getTypeAttribute($value)
    {
        if ($value == '0') {
            return 'Facebook';
        } elseif ($value == '1') {
            return 'Twitter';
        } elseif ($value == '2') {
            return 'Instgram';
        } else {
            return "";
        }
    }
    public function getUrlAttribute($value)
    {
        return $value ?? "";
    }
}
