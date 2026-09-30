<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'student_id', 'course_id', 'date', 'status', 'marked_by', 'source',
    ];

    protected $casts = ['date' => 'date'];

    public function student() { return $this->belongsTo(User::class, 'student_id'); }
    public function course()  { return $this->belongsTo(Course::class); }
    public function marker()  { return $this->belongsTo(User::class, 'marked_by'); }
}
