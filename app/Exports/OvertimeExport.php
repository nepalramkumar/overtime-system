<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OvertimeExport implements FromCollection, WithHeadings
{
    protected $data;

    // यो कन्स्ट्रक्टर थप्नुहोस्, यसले कन्ट्रोलरबाट डेटा लिन्छ
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        // १. सुरक्षा चेक
        if (empty($this->data)) {
            return collect([['Error' => 'डेटा उपलब्ध छैन']]);
        }

        $flattened = [];

        // २. $this->data ग्रुप गरिएको कलेक्सन हो
        foreach ($this->data as $records) {
            
            // सुरक्षा: $records एरे वा अब्जेक्ट हो कि होइन चेक गर्ने
            if (!is_iterable($records)) continue;

            $totalHours = $records->sum('total_hours');
            $totalAmount = $records->sum('tiffin_amount');

            // लुप चलाएर डेटा फ्ल्याट बनाउने
            foreach ($records as $index => $rec) {
                $flattened[] = [
                    'date'         => $rec->ot_date ?? 'N/A',
                    'name'         => $rec->employee->name ?? 'N/A',
                    'event'        => $rec->event->event_name ?? 'N/A',
                    'time'         => ($rec->from_time ?? '0') . ' - ' . ($rec->to_time ?? '0'),
                    'hours'        => $rec->total_hours ?? 0,
                    'tiffin'       => $rec->tiffin_amount ?? 0,
                    'group_total_h'=> ($index === 0) ? $totalHours : '',
                    'group_total_a'=> ($index === 0) ? $totalAmount : '',
                ];
            }

            // ३. हरेक ग्रुप पछि एउटा खाली रो थप्ने
            $flattened[] = [
                'date' => '', 'name' => '', 'event' => '', 'time' => '', 
                'hours' => '', 'tiffin' => '', 'group_total_h' => '', 'group_total_a' => ''
            ];
        }
        
        return collect($flattened);
    }

    public function headings(): array
    {
        return ["मिति", "कर्मचारी", "कार्यक्रम", "समय", "घण्टा", "खाजा", "जम्मा घण्टा", "कुल खाजा"];
    }
}