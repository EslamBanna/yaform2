<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    use HasFactory;
    protected $table = 'options';
    protected $fillable = [
        'question_id',
        'value',
        'text'
    ];
    public function getValueAttribute($value)
    {
        return $value ?? "";
    }
    public function getTextAttribute($value)
    {
        return $value ?? "";
    }
}
