<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RightSolution extends Model
{
    use HasFactory;

    protected $table = 'right_solutions';
    protected $fillable = [
        'question_id',
        'solution'
    ];
    public function getSolutionAttribute($value)
    {
        return $value ?? "";
    }
}
