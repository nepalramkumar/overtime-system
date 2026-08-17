<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PetrolBillExport implements FromCollection, WithHeadings
{
    protected $bills;

    public function __construct($bills)
    {
        $this->bills = $bills;
    }

    public function collection()
    {
        if ($this->bills->isEmpty()) {
            return collect([['Error' => 'डेटा उपलब्ध छैन']]);
        }

        $rows = [];
        $sn = 1;

        foreach ($this->bills as $bill) {
            $rows[] = [
                'sn'            => $sn++,
                'employee_code' => $bill->employee->employee_code ?? '-',
                'name'          => $bill->employee->name ?? 'N/A',
                'position'      => $bill->employee->position->name ?? 'N/A',
                'vehicle_no'    => $bill->employee->vehicle_no ?? '-',
                'month'         => ($bill->month->month ?? '') . ' - ' . ($bill->month->year ?? ''),
                'total_quantity'=> $bill->total_quantity,
                'total_amount'  => $bill->total_amount,
                'remarks'       => $bill->remarks ?? '-',
            ];
        }

        return collect($rows);
    }

    public function headings(): array
    {
        return ["सि.नं.", "कर्मचारी कोड", "कर्मचारी", "पद", "Vehicle No", "Month", "जम्मा परिमाण (L)", "जम्मा रकम", "कैफियत"];
    }
}