<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizMark extends Model
{
    protected $fillable = ['quiz_id', 'student_id', 'marks_obtained'];

    public function quiz()    { return $this->belongsTo(Quiz::class); }
    public function student() { return $this->belongsTo(User::class, 'student_id'); }
}
