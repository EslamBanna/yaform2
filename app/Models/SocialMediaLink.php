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
}
