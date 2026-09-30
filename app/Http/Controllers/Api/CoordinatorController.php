<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\CoordinatorLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * How this fits into the LoRaWAN side of the project:
 *
 * [Student RFID/BLE tag] --scan--> [LoRa end-node in classroom]
 *        --LoRa radio--> [Coordinator / Gateway]
 *        --internet--> [LoRaWAN Network Server, e.g. The Things Stack / ChirpStack]
 *        --HTTP webhook (this endpoint)--> Laravel app --marks Attendance row
 *
 * You do NOT talk to the radio hardware from PHP. The network server (TTN/ChirpStack)
 * already decodes the LoRa packet and forwards clean JSON to a webhook URL you
 * configure in its console. This controller IS that webhook.
 *
 * Route: POST /api/coordinator/attendance   (see routes/api.php)
 *
 * Expected JSON body (adjust to match your actual node's payload/decoder):
 * {
 *   "device_eui": "AABBCCDDEEFF0011",
 *   "tag_id": "S-2201",              // matches users.lora_tag_id
 *   "course_id": 3,                  // which class session this node is set for
 *   "timestamp": "2026-09-22T09:05:00Z"
 * }
 */
class CoordinatorController extends Controller
{
    public function receive(Request $request)
    {
        $data = $request->validate([
            'device_eui' => ['nullable', 'string'],
            'tag_id' => ['required', 'string'],
            'course_id' => ['required', 'integer', 'exists:courses,id'],
            'timestamp' => ['nullable', 'date'],
        ]);

        $when = $data['timestamp'] ?? now();
        $date = \Illuminate\Support\Carbon::parse($when)->toDateString();

        $student = User::where('lora_tag_id', $data['tag_id'])->where('role', 'student')->first();

        $log = CoordinatorLog::create([
            'device_eui' => $data['device_eui'] ?? null,
            'tag_id' => $data['tag_id'],
            'raw_payload' => $request->all(),
            'status' => $student ? 'matched' : 'unknown_tag',
            'received_at' => $when,
        ]);

        if (!$student) {
            Log::warning('Coordinator uplink with unknown tag_id: ' . $data['tag_id']);
            return response()->json(['status' => 'unknown_tag'], 202);
        }

        $attendance = Attendance::updateOrCreate(
            ['student_id' => $student->id, 'course_id' => $data['course_id'], 'date' => $date],
            ['status' => 'present', 'source' => 'lorawan']
        );

        $log->update(['matched_student_id' => $student->id, 'matched_attendance_id' => $attendance->id]);

        return response()->json(['status' => 'ok', 'student' => $student->name, 'attendance_id' => $attendance->id]);
    }
}
