<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    protected $fillable = ['course_id', 'title', 'total_marks', 'due_date'];

    public function course() { return $this->belongsTo(Course::class); }
    public function marks()  { return $this->hasMany(AssignmentMark::class); }
}
