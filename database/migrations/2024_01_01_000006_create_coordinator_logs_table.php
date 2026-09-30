<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Every uplink the LoRaWAN coordinator/gateway forwards is logged here,
        // whether or not it could be matched to a student, for auditing.
        Schema::create('coordinator_logs', function (Blueprint $table) {
            $table->id();
            $table->string('device_eui')->nullable();
            $table->string('tag_id')->nullable();
            $table->json('raw_payload')->nullable();
            $table->foreignId('matched_student_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('matched_attendance_id')->nullable()->constrained('attendances')->nullOnDelete();
            $table->enum('status', ['matched', 'unknown_tag', 'no_active_course'])->default('unknown_tag');
            $table->timestamp('received_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('coordinator_logs'); }
};
