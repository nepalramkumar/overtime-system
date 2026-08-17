<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RepairExpenseExport implements FromCollection, WithHeadings
{
    protected $rows;

    public function __construct($rows)
    {
        $this->rows = $rows;
    }

    public function collection()
    {
        if ($this->rows->isEmpty()) {
            return collect([['Error' => 'डेटा उपलब्ध छैन']]);
        }

        $out = [];
        $sn = 1;

        foreach ($this->rows as $row) {
            $out[] = [
                'sn'            => $sn++,
                'employee_code' => $row['employee']->employee_code ?? '-',
                'name'          => $row['employee']->name ?? 'N/A',
                'position'      => $row['employee']->position->name ?? 'N/A',
                'vehicle_no'    => $row['employee']->vehicle_no ?? '-',
                'fy_year'       => $row['fy_year'],
                'bs_month'      => $row['bs_month'],
                'bs_date'       => $row['bs_date'],
                'description'   => $row['description'],
                'amount'        => $row['amount'],
            ];
        }

        return collect($out);
    }

    public function headings(): array
    {
        return ["सि.नं.", "कर्मचारी कोड", "कर्मचारी", "पद", "Vehicle No", "FY Year", "Month", "मिति (BS)", "Description", "रकम"];
    }
}