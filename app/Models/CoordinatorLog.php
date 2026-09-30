<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoordinatorLog extends Model
{
    protected $fillable = [
        'device_eui', 'tag_id', 'raw_payload', 'matched_student_id',
        'matched_attendance_id', 'status', 'received_at',
    ];

    protected $casts = [
        'raw_payload' => 'array',
        'received_at' => 'datetime',
    ];
}
