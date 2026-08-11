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
    return $this->data->flatten(1)->map(function($item, $index) {
        return [
            $index + 1,
            $item->employee->name ?? 'N/A',
            $item->employee->designation ?? 'N/A',
            $item->event->event_name ?? 'N/A',
            $item->date_from,
            $item->date_to,
            $item->total_hours,
            $item->total_lunch,
        ];
    });
}

public function headings(): array
{
    return ["SN","Name", "Designation", "Event", "Date From", "Date To", "Total Hours", "Lunch Total"];
}
}