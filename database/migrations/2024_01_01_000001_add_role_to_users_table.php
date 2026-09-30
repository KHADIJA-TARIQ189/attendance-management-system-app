<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'teacher', 'student'])->default('student')->after('email');
            $table->string('roll_no')->nullable()->unique()->after('role');
            $table->string('lora_tag_id')->nullable()->unique()->after('roll_no'); // RFID/BLE tag ID carried by the student, read by the LoRaWAN end-node
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'roll_no', 'lora_tag_id']);
        });
    }
};
