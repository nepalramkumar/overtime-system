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
    return $this->data->map(function($item, $index) {
        $rate = $item->employee->ot_rate ?? 0;
        return [
            $item-> $index + 1,
            $item->employee->name ?? 'N/A',
            $item->employee->designation ?? 'N/A',
            $item->event->event_name ?? 'N/A',
            $item->date_from,
            $item->date_to,
            $item->total_hours,
            $rate,
            $item->total_hours * $rate, // OT Amount
            $item->total_lunch,
        ];
    });
}

public function headings(): array
{
    return ["SN","Name", "Designation", "Event", "Date From", "Date To", "Hours", "Rate", "OT Amount", "Lunch"];
}

}