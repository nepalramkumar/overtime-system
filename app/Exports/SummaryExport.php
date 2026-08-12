<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SummaryExport implements FromCollection, WithHeadings
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        if (empty($this->data)) {
            return collect([['Error' => 'डेटा उपलब्ध छैन']]);
        }

        $rows = [];
        $sn = 1;

        foreach ($this->data as $rec) {
            $rows[] = [
                'sn'            => $sn++,
                'employee_code' => $rec->employee->employee_code ?? '-',
                'name'          => $rec->employee->name ?? 'N/A',
                'position'      => $rec->employee->position->name ?? 'N/A',
                'event'         => $rec->event->event_name ?? 'सामान्य (General)',
                'date_from'     => $rec->date_from ?? 'N/A',
                'date_to'       => $rec->date_to ?? 'N/A',
                'total_hours'   => $rec->total_hours ?? 0,
                'total_lunch'   => $rec->total_lunch ?? 0,
            ];
        }

        return collect($rows);
    }

    public function headings(): array
    {
        return ["सि.नं.", "कर्मचारी कोड", "कर्मचारी", "पद", "कार्यक्रम", "मिति (देखि)", "मिति (सम्म)", "जम्मा घण्टा", "जम्मा खाजा"];
    }
}