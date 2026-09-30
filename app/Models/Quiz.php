<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    protected $fillable = ['course_id', 'title', 'total_marks', 'quiz_date'];

    public function course() { return $this->belongsTo(Course::class); }
    public function marks()  { return $this->hasMany(QuizMark::class); }
}
