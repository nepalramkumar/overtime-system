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

        foreach ($this->data as $rec) {
            $hours = $rec->total_hours ?? 0;
            $rate  = $rec->ot_rate_snapshot ?? 0;

            $rows[] = [
                'name'        => $rec->employee->name ?? 'N/A',
                'position'    => $rec->designation_snapshot ?? 'N/A',
                'event'       => $rec->event->event_name ?? 'सामान्य (General)',
                'date'        => $rec->ot_date ?? 'N/A',
                'hours'       => $hours,
                'rate'        => $rate,
                'total_amount'=> round($hours * $rate, 2),
                'tiffin'      => $rec->tiffin_amount ?? 0,
            ];
        }

        return collect($rows);
    }

    public function headings(): array
    {
        return ["कर्मचारी", "पद", "कार्यक्रम", "मिति", "घण्टा", "OT रेट", "जम्मा रकम", "खाजा"];
    }
}