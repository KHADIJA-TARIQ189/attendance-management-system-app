<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->string('title');
            $table->unsignedSmallInteger('total_marks')->default(10);
            $table->date('quiz_date')->nullable();
            $table->timestamps();
        });

        Schema::create('quiz_marks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained('quizzes')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('marks_obtained', 5, 2)->default(0);
            $table->timestamps();
            $table->unique(['quiz_id', 'student_id']);
        });

        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->string('title');
            $table->unsignedSmallInteger('total_marks')->default(20);
            $table->date('due_date')->nullable();
            $table->timestamps();
        });

        Schema::create('assignment_marks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained('assignments')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('marks_obtained', 5, 2)->default(0);
            $table->timestamps();
            $table->unique(['assignment_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignment_marks');
        Schema::dropIfExists('assignments');
        Schema::dropIfExists('quiz_marks');
        Schema::dropIfExists('quizzes');
    }
};
