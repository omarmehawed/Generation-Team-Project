<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Support\Str;

class ActiveBalanceExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle, WithColumnFormatting
{
    protected Collection $users;

    public function __construct(Collection $users)
    {
        $this->users = $users;
    }

    public function collection(): Collection
    {
        return $this->users;
    }

    public function title(): string
    {
        return 'Active Balance Report';
    }

    public function headings(): array
    {
        return [
            'Full Name',
            'Academic ID',
            'Mobile Number',
            'WhatsApp Number',
            'Current Wallet Balance',
        ];
    }

    public function map($user): array
    {
        $academicId = $user->university_email
            ? Str::before($user->university_email, '@')
            : Str::before($user->email, '@');

        return [
            $user->name ?? 'N/A',
            $academicId,
            $user->phone_number ?? 'N/A',
            $user->whatsapp_number ?? 'N/A',
            $user->wallet_balance ?? 0,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // 2 decimal places for balance
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $this->users->count() + 1; // +1 for header

        // Header row styling
        $sheet->getStyle('A1:E1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1F2937'], // Dark gray
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '374151'],
                ],
            ],
        ]);

        // Set header row height
        $sheet->getRowDimension(1)->setRowHeight(30);

        // Data rows styling
        if ($lastRow > 1) {
            $sheet->getStyle("A2:E{$lastRow}")->applyFromArray([
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'E5E7EB'],
                    ],
                ],
            ]);

            // Right-align the balance column
            $sheet->getStyle("E2:E{$lastRow}")->applyFromArray([
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_RIGHT,
                ],
                'font' => [
                    'bold' => true,
                ],
            ]);

            // Center-align Academic ID, Mobile, WhatsApp columns
            $sheet->getStyle("B2:D{$lastRow}")->applyFromArray([
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ]);

            // Alternate row coloring for readability
            for ($i = 2; $i <= $lastRow; $i++) {
                if ($i % 2 === 0) {
                    $sheet->getStyle("A{$i}:E{$i}")->applyFromArray([
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'F9FAFB'],
                        ],
                    ]);
                }
            }
        }

        // Summary row at the bottom
        $summaryRow = $lastRow + 1;
        $sheet->setCellValue("D{$summaryRow}", 'Total Balance:');
        $sheet->setCellValue("E{$summaryRow}", $this->users->sum('wallet_balance'));

        $sheet->getStyle("D{$summaryRow}:E{$summaryRow}")->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => 'D97706'], // Amber
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_RIGHT,
            ],
            'borders' => [
                'top' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '374151'],
                ],
            ],
        ]);

        $sheet->getStyle("E{$summaryRow}")->getNumberFormat()
            ->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

        return [];
    }
}
