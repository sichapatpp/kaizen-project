<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KaizenTypeExport implements FromArray, WithHeadings, WithStyles
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        $rows = [];
        $i = 1;

        foreach ($this->data['projects'] as $p) {
            $rows[] = [
                $i++,
                $p['title'] ?? '(ไม่มีชื่อ)',
                $p['user'],
                $p['before'],
                $p['after'],
                $p['net']
            ];
        }

        // Summary row
        $rows[] = [
            '',
            'รวมทั้งหมด',
            '',
            $this->data['summary']['before'],
            $this->data['summary']['after'],
            $this->data['summary']['net']
        ];

        return $rows;
    }

    public function headings(): array
    {
        return [
            ['รายงานกิจกรรม Kaizen - ประเภท: ' . $this->data['typeName'] . ' (ปีงบประมาณ ' . $this->data['selectedYear'] . ')'],
            ['ลำดับ', 'ชื่อกิจกรรม', 'ผู้ยื่น', 'ก่อน', 'หลัง', 'ผลต่าง']
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = count($this->data['projects']) + 3; 

        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1:F2')->getFont()->setBold(true);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A2:F2')->getAlignment()->setHorizontal('center');

        // Style for summary row
        $sheet->getStyle("A{$lastRow}:F{$lastRow}")->getFont()->setBold(true);
        $sheet->getStyle("A{$lastRow}:F{$lastRow}")->getFill()
              ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
              ->getStartColor()->setARGB('FFF0F0F0');

        return [];
    }
}
