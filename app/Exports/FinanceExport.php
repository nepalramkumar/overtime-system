<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class FinanceExport implements FromCollection, WithHeadings
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
            $hours = $rec->total_hours ?? 0;
            $rate  = $rec->employee->position->ot_rate ?? 0;

            $rows[] = [
                'sn'            => $sn++,
                'employee_code' => $rec->employee->employee_code ?? '-',
                'name'          => $rec->employee->name ?? 'N/A',
                'position'      => $rec->employee->position->name ?? 'N/A',
                'event'         => $rec->event->event_name ?? 'सामान्य (General)',
                'hours'         => $hours,
                'rate'          => $rate,
                'total_amount'  => round($hours * $rate, 2),
                'tiffin'        => $rec->total_lunch ?? 0,
            ];
        }

        return collect($rows);
    }

    public function headings(): array
    {
        return ["सि.नं.", "कर्मचारी कोड", "कर्मचारी", "पद", "कार्यक्रम", "घण्टा", "OT रेट", "जम्मा रकम", "खाजा"];
    }
}