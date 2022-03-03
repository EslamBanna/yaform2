<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submit extends Model
{
    use HasFactory;
    protected $table = 'submits';
    protected $fillable = [
        // 'user_id',
        'form_id',
        'score',
        'mark'
    ];

    public function answers()
    {
        return $this->hasMany(Answer::class, 'submit_id', 'id');
    }

    public function form()
    {
        return $this->belongsTo(Form::class, 'form_id', 'id');
    }

    public static function boot()
    {
        parent::boot();
        self::deleting(function ($submit) {
            $submit->answers()->each(function ($answers) {
                $answers->delete();
            });
        });
    }
}
