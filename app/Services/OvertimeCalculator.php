<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\OvertimeRecord;
use App\Models\Event;

class OvertimeCalculator
{
    public function calculateAndSave(array $data, $employee, $officeStart = '08:00:00', $officeEnd = '17:00:00')
    {
        $otDate = $data['ot_date'];
        $event = null;

if (!empty($data['event_id'])) {
    $event = Event::find($data['event_id']);
}

$isEligible = $event ? (bool)$event->is_tiffin_eligible : false;
        //filter_var($data['is_tiffin_eligible'] ?? true, FILTER_VALIDATE_BOOLEAN);
        
        $from = Carbon::parse($otDate . ' ' . $data['from_time']);
        $to = Carbon::parse($otDate . ' ' . $data['to_time']);
        
        if ($to->lt($from)) {
            $to->addDay();
        }

        $totalMinutes = ($to->timestamp - $from->timestamp) / 60;

        $baseData = [
            'employee_id'          => $employee->id,
            'event_id'             => $data['event_id'] ?? null,
            'ot_date'              => $otDate,
            'designation_snapshot' => $employee->designation,
            'ot_rate_snapshot'     => $employee->ot_rate,
            'is_holiday'           => $data['is_holiday'] ?? false,
            'remarks'              => $data['remarks'] ?? null,
            'status'               => 'Pending'
        ];

        // १. Holiday को लागि
        if ($baseData['is_holiday']) {
            if ($totalMinutes < 60) {
                throw new \Exception("अतिरिक्त समय न्यूनतम १ घण्टा (६० मिनेट) हुनुपर्छ।");
            }
            $hours = $totalMinutes / 60;
            $tiffin = $this->calculateTiffin($hours, $isEligible);
            
            OvertimeRecord::create(array_merge($baseData, [
                'from_time'     => $from->format('H:i:s'),
                'to_time'       => $to->format('H:i:s'),
                'total_hours'   => $hours,
                'tiffin_amount' => $tiffin,
                'type'          => 'Holiday'
            ]));
            return;
        }

        // २. Regular कार्यदिनको लागि
        $officeStartTime = Carbon::parse($otDate . ' ' . $officeStart);
        $officeEndTime = Carbon::parse($otDate . ' ' . $officeEnd);

        $recordsToCreate = [];

        // Before Office
        if ($from->lt($officeStartTime)) {
            $beforeEnd = $to->lt($officeStartTime) ? $to : $officeStartTime;
            $minutesBefore = ($beforeEnd->timestamp - $from->timestamp) / 60;
            if ($minutesBefore > 0) {
                $recordsToCreate[] = [
                    'from_time' => $from->format('H:i:s'),
                    'to_time'   => $beforeEnd->format('H:i:s'),
                    'minutes'   => $minutesBefore,
                    'type'      => 'Before Office'
                ];
            }
        }

        // After Office
        if ($to->gt($officeEndTime)) {
            $afterStart = $from->gt($officeEndTime) ? $from : $officeEndTime;
            $minutesAfter = ($to->timestamp - $afterStart->timestamp) / 60;
            if ($minutesAfter > 0) {
                $recordsToCreate[] = [
                    'from_time' => $afterStart->format('H:i:s'),
                    'to_time'   => $to->format('H:i:s'),
                    'minutes'   => $minutesAfter,
                    'type'      => 'After Office'
                ];
            }
        }

        $validOtMinutes = array_sum(array_column($recordsToCreate, 'minutes'));

        if ($validOtMinutes < 60) {
            throw new \Exception("वैध ओभरटाइम न्यूनतम ६० मिनेट पुगेन।");
        }
        
        foreach ($recordsToCreate as $record) {
            $rowHours = $record['minutes'] / 60;
            $rowTiffinAmount = $this->calculateTiffin($rowHours, $isEligible);
            
            OvertimeRecord::create(array_merge($baseData, [
                'from_time'     => $record['from_time'],
                'to_time'       => $record['to_time'],
                'total_hours'   => $rowHours,
                'tiffin_amount' => $rowTiffinAmount,
                'type'          => $record['type']
            ]));
        }
    }

    private function calculateTiffin($hours, $isEligible)
    {
        if ($isEligible) {
            return 0;
        }

        if ($hours < 2) return 0;
        if ($hours >= 2 && $hours < 3) return 120;
        if ($hours >= 3 && $hours < 5) return 180;
        if ($hours >= 5 && $hours <= 9) return 300;
        
        return 420;
    }
}