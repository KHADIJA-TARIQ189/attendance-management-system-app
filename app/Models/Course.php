<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = ['code', 'title', 'credit_hours', 'teacher_id'];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'enrollments', 'course_id', 'student_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }

    // Number of distinct class sessions (dates) held so far for this course
    public function totalSessions(): int
    {
        return $this->attendances()->distinct('date')->count('date');
    }

    // Attendance % for one student in this course
    public function attendancePercentFor(int $studentId): float
    {
        $total = $this->attendances()->where('student_id', $studentId)->count();
        if ($total === 0) return 0;
        $present = $this->attendances()
            ->where('student_id', $studentId)
            ->whereIn('status', ['present', 'late'])
            ->count();
        return round(($present / $total) * 100, 2);
    }
}
