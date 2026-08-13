<?php

namespace App\Services;

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\Style\Font;

class OvertimeWordService
{
    // तपाईंको संस्थाको नाम — यहाँ बदल्नुहोस्
    private $orgName = 'संस्थाको नाम';

    protected function newDocument(): PhpWord
    {
        $phpWord = new PhpWord();
        $phpWord->setDefaultFontName('Kalimati'); // Nepali Unicode font (system मा नभए Mangal/Arial राख्नुहोस्)
        $phpWord->setDefaultFontSize(11);
        return $phpWord;
    }

    protected function addHeader($section)
    {
        $section->addText($this->orgName, ['bold' => true, 'size' => 14], ['alignment' => Jc::CENTER]);
        $section->addText('आन्तरिक मेमो', ['bold' => true, 'size' => 12], ['alignment' => Jc::CENTER]);
        $section->addText('अतिरिक्त समय काम गरेको प्रमाणित फारम', ['bold' => true, 'size' => 12], ['alignment' => Jc::CENTER]);
        $section->addTextBreak(1);
    }

    protected function addSignatureBlock($section)
    {
        $section->addTextBreak(1);
        $section->addText('पेश गर्ने:', ['bold' => true]);
        $section->addText('नाम: ___________________  पद: ___________________');
        $section->addText('हस्ताक्षर: ___________________  मिति: ___________________');
        $section->addTextBreak(1);

        $section->addText('स्वीकृत गर्ने:', ['bold' => true]);
        $section->addText('नाम: ___________________  पद: ___________________');
        $section->addText('हस्ताक्षर: ___________________  मिति: ___________________');
        $section->addTextBreak(1);

        $section->addText('आन्तरिक व्यवस्थापन शाखा:', ['bold' => true]);
        $section->addText('माथि उल्लेखित कर्मचारीले अतिरिक्त समय काम गरेको व्यहोरा दैनिक हाजिरीको अभिलेखमा भएको प्रमाणित गर्दछु।');
        $section->addText('नाम: ___________________  पद: ___________________');
        $section->addText('हस्ताक्षर: ___________________  मिति: ___________________');
        $section->addTextBreak(1);

        $section->addText('निर्देशनालय प्रमुख:', ['bold' => true]);
        $section->addText('नाम: ___________________');
        $section->addText('हस्ताक्षर: ___________________  मिति: ___________________');
    }

    public function generateIndividual($records, $employee)
{
    $phpWord = $this->newDocument();
    $section = $phpWord->addSection();

    $this->addHeader($section);

    // कर्मचारी जानकारी
    $section->addText('कर्मचारीको नाम: ' . ($employee->name ?? 'N/A') . '   दर्जा: ' . ($employee->position->name ?? 'N/A'));
    $section->addText('शाखा/विभाग: ' . ($employee->department ?? 'N/A'));
    $section->addTextBreak(1);

    // Table
    $tableStyle = ['borderSize' => 6, 'borderColor' => '000000', 'cellMargin' => 80];
    $table = $section->addTable($tableStyle);

    $table->addRow();
    $headers = ['मिति', 'देखि - सम्म', 'घण्टा', 'मिनेटलाई घण्टामा', 'खाजा', 'काम/Purpose'];
    foreach ($headers as $h) {
        $table->addCell(1400)->addText($h, ['bold' => true], ['alignment' => Jc::CENTER]);
    }

    $totalHours = 0;
    $totalTiffin = 0;

    foreach ($records as $rec) {
        $wholeHours = floor($rec->total_hours);
        $decimalPart = round($rec->total_hours - $wholeHours, 2);

        $table->addRow();
        $table->addCell(1400)->addText(adToBs($rec->ot_date));
        $table->addCell(1400)->addText(substr($rec->from_time, 0, 5) . ' - ' . substr($rec->to_time, 0, 5));
        $table->addCell(1400)->addText((string) $wholeHours, [], ['alignment' => Jc::CENTER]);
        $table->addCell(1400)->addText(number_format($decimalPart, 2), [], ['alignment' => Jc::CENTER]);
        $table->addCell(1400)->addText(number_format($rec->tiffin_amount, 2), [], ['alignment' => Jc::CENTER]);
        $table->addCell(1400)->addText($rec->purpose->name ?? ($rec->remarks ?: '-'));

        $totalHours += $rec->total_hours;
        $totalTiffin += $rec->tiffin_amount;
    }

    $table->addRow();
    $table->addCell(1400)->addText('जम्मा', ['bold' => true]);
    $table->addCell(1400);
    $table->addCell(2800, ['gridSpan' => 2])->addText(number_format($totalHours, 2), ['bold' => true], ['alignment' => Jc::CENTER]);
    $table->addCell(1400)->addText(number_format($totalTiffin, 2), ['bold' => true], ['alignment' => Jc::CENTER]);
    $table->addCell(1400);

    $this->addSignatureBlock($section);

    $filename = 'OT_Slip_' . str_replace(' ', '_', $employee->name) . '_' . date('Ymd') . '.docx';
    return $this->saveToDownload($phpWord, $filename);
}

    public function generateGroup($records, $title)
{
    $phpWord = $this->newDocument();
    $section = $phpWord->addSection();

    $this->addHeader($section);
    $section->addText('कार्यक्रम/प्रयोजन: ' . $title, ['bold' => true]);
    $section->addTextBreak(1);

    // Group by employee
    $employeeGroups = [];
    foreach ($records as $rec) {
        $empId = $rec->employee_id;
        if (!isset($employeeGroups[$empId])) {
            $employeeGroups[$empId] = [
                'employee' => $rec->employee,
                'records'  => [],
                'total_hours' => 0,
                'total_tiffin' => 0,
            ];
        }
        $employeeGroups[$empId]['records'][] = $rec;
        $employeeGroups[$empId]['total_hours'] += $rec->total_hours;
        $employeeGroups[$empId]['total_tiffin'] += $rec->tiffin_amount;
    }

    // Roster Table (Summary)
    $tableStyle = ['borderSize' => 6, 'borderColor' => '000000', 'cellMargin' => 80];
    $table = $section->addTable($tableStyle);
    $table->addRow();
    $headers = ['क्र.सं.', 'नाम', 'मिति (देखि-सम्म)', 'पद', 'जम्मा घण्टा', 'खाजा खर्च', 'कैफियत'];
    foreach ($headers as $h) {
        $table->addCell(1200)->addText($h, ['bold' => true], ['alignment' => Jc::CENTER]);
    }

    $sn = 1;
    $grandTotalHours = 0;
    $grandTotalTiffin = 0;

    foreach ($employeeGroups as $group) {
        $emp = $group['employee'];
        $dates = collect($group['records'])->pluck('ot_date')->sort()->values();
        $dateRange = adToBs($dates->first()) . ($dates->count() > 1 ? ' देखि ' . adToBs($dates->last()) . ' सम्म' : '');

        $table->addRow();
        $table->addCell(1200)->addText((string) $sn++, [], ['alignment' => Jc::CENTER]);
        $table->addCell(1200)->addText($emp->name ?? 'N/A');
        $table->addCell(1200)->addText($dateRange);
        $table->addCell(1200)->addText($emp->position->name ?? 'N/A');
        $table->addCell(1200)->addText(number_format($group['total_hours'], 2), [], ['alignment' => Jc::CENTER]);
        $table->addCell(1200)->addText(number_format($group['total_tiffin'], 2), [], ['alignment' => Jc::CENTER]);
        $table->addCell(1200)->addText($title);

        $grandTotalHours += $group['total_hours'];
        $grandTotalTiffin += $group['total_tiffin'];
    }

    $table->addRow();
    $table->addCell(3600, ['gridSpan' => 3])->addText('जम्मा', ['bold' => true]);
    $table->addCell(1200);
    $table->addCell(1200)->addText(number_format($grandTotalHours, 2), ['bold' => true], ['alignment' => Jc::CENTER]);
    $table->addCell(1200)->addText(number_format($grandTotalTiffin, 2), ['bold' => true], ['alignment' => Jc::CENTER]);
    $table->addCell(1200);

    $this->addSignatureBlock($section);

    // हरेक Employee को detailed breakdown (उही page मा, page break बिना)
    $section->addTextBreak(2);
    $section->addText('अतिरिक्त समय कार्यको विस्तृत विवरण', ['bold' => true, 'size' => 12], ['alignment' => Jc::CENTER]);
    $section->addTextBreak(1);

    $detailTable = $section->addTable($tableStyle);
    $detailTable->addRow();
    $dHeaders = ['नाम', 'मिति', 'देखि - सम्म', 'घण्टा', 'मिनेटलाई घण्टामा', 'खाजा'];
    foreach ($dHeaders as $h) {
        $detailTable->addCell(1500)->addText($h, ['bold' => true], ['alignment' => Jc::CENTER]);
    }

    foreach ($employeeGroups as $group) {
        foreach ($group['records'] as $rec) {
            $wholeHours = floor($rec->total_hours);
            $decimalPart = round($rec->total_hours - $wholeHours, 2);

            $detailTable->addRow();
            $detailTable->addCell(1500)->addText($group['employee']->name ?? 'N/A');
            $detailTable->addCell(1500)->addText(adToBs($rec->ot_date));
            $detailTable->addCell(1500)->addText(substr($rec->from_time, 0, 5) . ' - ' . substr($rec->to_time, 0, 5));
            $detailTable->addCell(1500)->addText((string) $wholeHours, [], ['alignment' => Jc::CENTER]);
            $detailTable->addCell(1500)->addText(number_format($decimalPart, 2), [], ['alignment' => Jc::CENTER]);
            $detailTable->addCell(1500)->addText(number_format($rec->tiffin_amount, 2), [], ['alignment' => Jc::CENTER]);
        }
    }

    $filename = 'OT_Group_' . str_replace(' ', '_', $title) . '_' . date('Ymd') . '.docx';
    return $this->saveToDownload($phpWord, $filename);
}

    protected function saveToDownload(PhpWord $phpWord, string $filename)
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'ot_') . '.docx';
        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($tempFile);

        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }
}