<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Safe save handlers for attendance, quiz marks and assignment marks.
 *
 *  - Attendance: update the existing row for (student, course, date) instead of
 *    inserting a duplicate  ->  fixes "UNIQUE constraint failed: attendances...".
 *  - Marks: blank fields are skipped and existing marks are updated
 *    ->  fixes "NOT NULL constraint failed: quiz_marks.marks_obtained".
 *
 * Input names are detected automatically, so the existing Blade forms keep working.
 */
class ResultSaveController extends Controller
{
    private const STATUSES = ['present', 'absent', 'late', 'excused'];

    /* ---------------------------------------------------------------- */
    /*  ATTENDANCE                                                      */
    /* ---------------------------------------------------------------- */
    public function saveAttendance(Request $request, $course)
    {
        $courseRow = DB::table('courses')->where('id', $course)->first();
        abort_unless($courseRow, 404);
        $this->authorizeCourse($courseRow);

        try {
            $date = Carbon::parse($request->input('date', now()))->startOfDay();
        } catch (\Throwable $e) {
            return back()->withErrors(['date' => 'Invalid date.'])->withInput();
        }
        $dateValue = $date->format('Y-m-d H:i:s');

        $rows  = $this->extractStudentValues($request, ['attendance', 'status', 'statuses'], function ($v) {
            $v = strtolower(trim((string) $v));
            return $v === '' || in_array($v, self::STATUSES, true);
        });

        $saved = 0;
        $now   = now();

        DB::transaction(function () use ($rows, $courseRow, $date, $dateValue, $now, &$saved) {
            foreach ($rows as $studentId => $status) {
                $status = strtolower(trim((string) $status));
                if (!in_array($status, self::STATUSES, true)) {
                    continue;
                }

                $existing = DB::table('attendances')
                    ->where('student_id', $studentId)
                    ->where('course_id', $courseRow->id)
                    ->whereDate('date', $date->toDateString())
                    ->first();

                $payload = [
                    'status'     => $status,
                    'marked_by'  => Auth::id(),
                    'source'     => 'manual',
                    'updated_at' => $now,
                ];

                if ($existing) {
                    DB::table('attendances')->where('id', $existing->id)->update($payload);
                } else {
                    DB::table('attendances')->insert($payload + [
                        'student_id' => $studentId,
                        'course_id'  => $courseRow->id,
                        'date'       => $dateValue,
                        'created_at' => $now,
                    ]);
                }
                $saved++;
            }
        });

        return back()->with('status', "Attendance saved for {$saved} student(s).");
    }

    /* ---------------------------------------------------------------- */
    /*  QUIZ MARKS                                                      */
    /* ---------------------------------------------------------------- */
    public function saveQuizMarks(Request $request, $quiz)
    {
        return $this->saveMarks($request, 'quizzes', 'quiz_marks', 'quiz_id', $quiz);
    }

    /* ---------------------------------------------------------------- */
    /*  ASSIGNMENT MARKS                                                */
    /* ---------------------------------------------------------------- */
    public function saveAssignmentMarks(Request $request, $assignment)
    {
        return $this->saveMarks($request, 'assignments', 'assignment_marks', 'assignment_id', $assignment);
    }

    /* ---------------------------------------------------------------- */
    /*  Shared marks logic                                              */
    /* ---------------------------------------------------------------- */
    private function saveMarks(Request $request, string $parentTable, string $marksTable, string $fk, $parentId)
    {
        $parent = DB::table($parentTable)->where('id', $parentId)->first();
        abort_unless($parent, 404);

        if (isset($parent->course_id)) {
            $courseRow = DB::table('courses')->where('id', $parent->course_id)->first();
            if ($courseRow) {
                $this->authorizeCourse($courseRow);
            }
        }

        abort_unless(Schema::hasTable($marksTable), 500, "Table {$marksTable} not found.");

        $column = Schema::hasColumn($marksTable, 'marks_obtained') ? 'marks_obtained' : 'marks';

        // Maximum allowed mark, if the parent table stores one.
        $max = null;
        foreach (['total_marks', 'max_marks', 'marks', 'total'] as $col) {
            if (isset($parent->$col) && is_numeric($parent->$col)) {
                $max = (float) $parent->$col;
                break;
            }
        }

        $rows = $this->extractStudentValues($request, ['marks', 'marks_obtained', 'mark', 'scores'], function ($v) {
            return $v === null || $v === '' || is_numeric($v);
        });

        $saved   = 0;
        $skipped = 0;
        $errors  = [];
        $now     = now();

        DB::transaction(function () use ($rows, $marksTable, $fk, $parentId, $column, $max, $now, &$saved, &$skipped, &$errors) {
            foreach ($rows as $studentId => $mark) {
                // Blank = not graded yet: do not insert NULL into a NOT NULL column.
                if ($mark === null || trim((string) $mark) === '') {
                    $skipped++;
                    continue;
                }
                if (!is_numeric($mark) || (float) $mark < 0) {
                    $errors[] = "Invalid mark for student #{$studentId}.";
                    continue;
                }
                if ($max !== null && (float) $mark > $max) {
                    $errors[] = "Mark for student #{$studentId} exceeds the maximum of {$max}.";
                    continue;
                }

                $existing = DB::table($marksTable)
                    ->where($fk, $parentId)
                    ->where('student_id', $studentId)
                    ->first();

                if ($existing) {
                    DB::table($marksTable)->where('id', $existing->id)->update([
                        $column      => $mark,
                        'updated_at' => $now,
                    ]);
                } else {
                    DB::table($marksTable)->insert([
                        $fk          => $parentId,
                        'student_id' => $studentId,
                        $column      => $mark,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
                $saved++;
            }
        });

        $redirect = back()->with('status', "Marks saved for {$saved} student(s)" . ($skipped ? ", {$skipped} left blank." : '.'));

        return $errors ? $redirect->withErrors($errors) : $redirect;
    }

    /* ---------------------------------------------------------------- */
    /*  Helpers                                                         */
    /* ---------------------------------------------------------------- */

    /** Admins may touch any course; teachers only their own. */
    private function authorizeCourse($course): void
    {
        $user = Auth::user();
        if ($user && method_exists($user, 'isAdmin') && $user->isAdmin()) {
            return;
        }
        if (isset($course->teacher_id) && $course->teacher_id !== null) {
            abort_unless((int) $course->teacher_id === (int) Auth::id(), 403);
        }
    }

    /**
     * Finds the [student_id => value] array in the request.
     * Tries the preferred field names first, then any array keyed by numeric
     * student ids. Supports both  name[7]=x  and  name[7][status]=x / [mark]=x.
     */
    private function extractStudentValues(Request $request, array $preferred, callable $valid): array
    {
        $candidates = [];
        foreach ($preferred as $name) {
            if (is_array($request->input($name))) {
                $candidates[] = $request->input($name);
            }
        }
        foreach ($request->except(['_token', '_method', 'date']) as $value) {
            if (is_array($value)) {
                $candidates[] = $value;
            }
        }

        foreach ($candidates as $arr) {
            $flat = [];
            foreach ($arr as $studentId => $value) {
                if (!is_numeric($studentId)) {
                    continue 2; // not keyed by student id
                }
                if (is_array($value)) {
                    $value = $value['status'] ?? $value['marks_obtained'] ?? $value['marks'] ?? $value['mark'] ?? null;
                }
                $flat[(int) $studentId] = $value;
            }
            if (!$flat) {
                continue;
            }
            $allValid = true;
            foreach ($flat as $v) {
                if (!$valid($v)) {
                    $allValid = false;
                    break;
                }
            }
            if ($allValid) {
                return $flat;
            }
        }

        return [];
    }
}
