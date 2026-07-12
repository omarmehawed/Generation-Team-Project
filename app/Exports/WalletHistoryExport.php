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

class WalletHistoryExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle, WithColumnFormatting
{
    protected Collection $transactions;
    protected array $memberTeamMap;

    /**
     * @param Collection $transactions  Pre-sorted wallet transactions with relations loaded.
     * @param array      $memberTeamMap Keyed by user_id => ['team_name' => ..., 'role' => ...]
     */
    public function __construct(Collection $transactions, array $memberTeamMap)
    {
        $this->transactions = $transactions;
        $this->memberTeamMap = $memberTeamMap;
    }

    public function collection(): Collection
    {
        return $this->transactions;
    }

    public function title(): string
    {
        return 'Wallet Transaction History';
    }

    public function headings(): array
    {
        return [
            'Reference #',
            'Member Name',
            'Academic ID',
            'Team Name',
            'Role',
            'Transaction Date & Time',
            'Transaction Type',
            'Transaction Amount (EGP)',
            'Balance After (EGP)',
            'Deposit Source',
            'Processed By',
            'Payment Proof',
            'Transaction Status',
            'Notes',
        ];
    }

    public function map($txn): array
    {
        $user = $txn->user;
        $admin = $txn->admin;
        $depositRequest = $txn->depositRequest;

        // Academic ID
        $academicId = $user
            ? Str::before($user->university_email ?? $user->email, '@')
            : 'N/A';

        // Team & Role from pre-built map
        $teamInfo = $this->memberTeamMap[$txn->user_id] ?? null;
        $teamName = $teamInfo['team_name'] ?? 'N/A';
        $role = $teamInfo['role'] ?? 'N/A';

        // Transaction Type (human-readable)
        $typeMap = [
            'deposit'    => 'Deposit',
            'withdrawal' => 'Withdrawal',
            'rejected'   => 'Rejected Deposit',
        ];
        $transactionType = $typeMap[$txn->type] ?? ucfirst($txn->type);

        // Deposit Source logic
        $depositSource = $this->resolveDepositSource($txn, $depositRequest);

        // Processed By
        $processedBy = 'System';
        if ($depositRequest && $depositRequest->processor) {
            $processedBy = $depositRequest->processor->name;
        } elseif ($admin) {
            $processedBy = $admin->name;
        }

        // Payment Proof
        $paymentProof = '';
        if ($depositRequest && $depositRequest->screenshot_path) {
            $paymentProof = $depositRequest->screenshot_url ?? $depositRequest->screenshot_path;
        }

        // Transaction Status
        $status = $this->resolveStatus($txn, $depositRequest);

        // Amount sign prefix
        $amountValue = $txn->amount;
        if ($txn->type === 'withdrawal') {
            $amountValue = -1 * abs($amountValue);
        }

        return [
            'TXN-' . str_pad($txn->id, 6, '0', STR_PAD_LEFT),
            $user->name ?? 'N/A',
            $academicId,
            $teamName,
            $role,
            $txn->created_at->format('Y-m-d H:i:s'),
            $transactionType,
            $amountValue,
            $txn->balance_after ?? 0,
            $depositSource,
            $processedBy,
            $paymentProof,
            $status,
            $txn->notes ?? '',
        ];
    }

    /**
     * Determine the deposit source for a transaction.
     */
    private function resolveDepositSource($txn, $depositRequest): string
    {
        if ($txn->type === 'withdrawal') {
            return 'N/A';
        }

        if ($depositRequest) {
            $methodMap = [
                'vodafone_cash' => 'Vodafone Cash',
                'instapay'      => 'InstaPay',
                'cash'          => 'Cash',
            ];
            $method = $methodMap[$depositRequest->payment_method] ?? ucfirst(str_replace('_', ' ', $depositRequest->payment_method));

            if ($txn->type === 'rejected') {
                return $method . ' (Rejected)';
            }

            return 'Deposit request approved — ' . $method;
        }

        // Manual transactions (no deposit request linked)
        $notes = strtolower($txn->notes ?? '');

        if (str_contains($notes, 'bulk')) {
            return 'Bulk operation by Admin/Leader';
        }

        if (str_contains($notes, 'fund deduction') || str_contains($notes, 'wallet deduction')) {
            return 'Fund Deduction';
        }

        return 'Added manually by ' . ($txn->admin->name ?? 'Admin/Leader');
    }

    /**
     * Determine the status for display.
     */
    private function resolveStatus($txn, $depositRequest): string
    {
        if ($txn->type === 'rejected') {
            return 'Rejected';
        }

        if ($depositRequest) {
            return ucfirst($depositRequest->status);
        }

        return 'Completed';
    }

    public function columnFormats(): array
    {
        return [
            'H' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'I' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $totalRows = $this->transactions->count();
        $lastRow = $totalRows + 1; // +1 for header
        $lastCol = 'N'; // 14 columns (A-N)

        // Header row styling
        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 11,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1F2937'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '374151'],
                ],
            ],
        ]);

        $sheet->getRowDimension(1)->setRowHeight(32);

        // Freeze the header row
        $sheet->freezePane('A2');

        if ($lastRow > 1) {
            // Data rows styling
            $sheet->getStyle("A2:{$lastCol}{$lastRow}")->applyFromArray([
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'E5E7EB'],
                    ],
                ],
            ]);

            // Center-align specific columns: Reference, Academic ID, Type, Status
            foreach (['A', 'C', 'G', 'M'] as $col) {
                $sheet->getStyle("{$col}2:{$col}{$lastRow}")->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
            }

            // Right-align amount and balance columns
            foreach (['H', 'I'] as $col) {
                $sheet->getStyle("{$col}2:{$col}{$lastRow}")->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    'font' => ['bold' => true],
                ]);
            }

            // Alternate row coloring
            for ($i = 2; $i <= $lastRow; $i++) {
                if ($i % 2 === 0) {
                    $sheet->getStyle("A{$i}:{$lastCol}{$i}")->applyFromArray([
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'F9FAFB'],
                        ],
                    ]);
                }
            }

            // Color-code transaction type cells
            for ($i = 2; $i <= $lastRow; $i++) {
                $typeValue = $sheet->getCell("G{$i}")->getValue();

                if ($typeValue === 'Deposit') {
                    $sheet->getStyle("G{$i}")->applyFromArray([
                        'font' => ['color' => ['rgb' => '059669'], 'bold' => true],
                    ]);
                    $sheet->getStyle("H{$i}")->applyFromArray([
                        'font' => ['color' => ['rgb' => '059669']],
                    ]);
                } elseif ($typeValue === 'Withdrawal') {
                    $sheet->getStyle("G{$i}")->applyFromArray([
                        'font' => ['color' => ['rgb' => 'DC2626'], 'bold' => true],
                    ]);
                    $sheet->getStyle("H{$i}")->applyFromArray([
                        'font' => ['color' => ['rgb' => 'DC2626']],
                    ]);
                } elseif (str_contains($typeValue ?? '', 'Rejected')) {
                    $sheet->getStyle("G{$i}")->applyFromArray([
                        'font' => ['color' => ['rgb' => 'EA580C'], 'bold' => true],
                    ]);
                    $sheet->getStyle("H{$i}")->applyFromArray([
                        'font' => ['color' => ['rgb' => 'EA580C']],
                    ]);
                }
            }
        }

        // Summary row
        $summaryRow = $lastRow + 2;
        $sheet->setCellValue("A{$summaryRow}", 'WALLET HISTORY EXPORT');
        $sheet->setCellValue("F{$summaryRow}", 'Generated: ' . now()->format('Y-m-d H:i:s'));
        $sheet->setCellValue("G{$summaryRow}", 'Total Transactions:');
        $sheet->setCellValue("H{$summaryRow}", $totalRows);

        $sheet->getStyle("A{$summaryRow}:N{$summaryRow}")->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 10,
                'color' => ['rgb' => '6B7280'],
            ],
            'borders' => [
                'top' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '374151'],
                ],
            ],
        ]);

        // Set auto-filter on headers
        $sheet->setAutoFilter("A1:{$lastCol}1");

        return [];
    }
}
