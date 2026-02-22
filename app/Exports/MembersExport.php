<?php

namespace App\Exports;

use App\Models\TeamMember;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MembersExport implements FromCollection, WithHeadings, WithMapping
{
    protected $columns;
    protected $groupId;
    protected $teamId;

    public function __construct(array $columns, $groupId, $teamId)
    {
        $this->columns = $columns;
        $this->groupId = $groupId;
        $this->teamId = $teamId;
    }

    public function collection()
    {
        $query = TeamMember::with('user')->where('team_id', $this->teamId);

        if ($this->groupId === 'A') {
            $query->where('is_group_a', true);
        } elseif ($this->groupId === 'B') {
            $query->where('is_group_b', true);
        }

        return $query->get();
    }

    public function headings(): array
    {
        $headingMap = [
            'name' => 'Name',
            'academic_number' => 'Academic Number',
            'email' => 'Email',
            'phone_number' => 'Phone Number',
            'whatsapp_number' => 'WhatsApp Number',
            'national_id' => 'National ID',
            'address' => 'Address',
            'role' => 'Role'
        ];
        
        $headings = [];
        foreach ($this->columns as $col) {
            $headings[] = $headingMap[$col] ?? ucwords(str_replace('_', ' ', $col));
        }
        return $headings;
    }

    public function map($member): array
    {
        $row = [];
        foreach ($this->columns as $col) {
            if ($col === 'name') {
                $row[] = $member->user->name ?? 'N/A';
            } elseif ($col === 'academic_number') {
                $row[] = isset($member->user->email) ? explode('@', $member->user->email)[0] : 'N/A';
            } elseif ($col === 'national_id') {
                $row[] = $member->user->national_id ?? 'N/A';
            } elseif ($col === 'email') {
                $row[] = $member->user->email ?? 'N/A';
            } elseif ($col === 'phone_number') {
                $row[] = $member->user->phone_number ?? 'N/A';
            } elseif ($col === 'whatsapp_number') {
                $row[] = $member->user->whatsapp_number ?? 'N/A';
            } elseif ($col === 'address') {
                $row[] = $member->user->address ?? 'N/A';
            } elseif ($col === 'role') {
                $row[] = ucfirst(str_replace('_', ' ', $member->role));
            } else {
                $row[] = 'N/A';
            }
        }
        return $row;
    }
}
