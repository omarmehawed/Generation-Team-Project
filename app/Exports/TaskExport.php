<?php

namespace App\Exports;

use App\Models\Task;
use App\Models\TeamMember;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TaskExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $teamId;

    public function __construct($teamId)
    {
        $this->teamId = $teamId;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $members = TeamMember::where('team_id', $this->teamId)
            ->whereIn('technical_role', ['software', 'hardware'])
            ->with(['user', 'user.tasks' => function($q) {
                $q->where('team_id', $this->teamId)->orderBy('created_at', 'desc');
            }])
            ->get();

        $rows = [];
        foreach ($members as $member) {
            $tasks = $member->user->tasks;
            if ($tasks->count() === 0) {
                $rows[] = [
                    'member' => $member,
                    'task' => null
                ];
            } else {
                foreach ($tasks as $task) {
                    $rows[] = [
                        'member' => $member,
                        'task' => $task
                    ];
                }
            }
        }

        return collect($rows);
    }

    public function headings(): array
    {
        return [
            'Member Name',
            'Academic Number',
            'Technical Role',
            'Task Name',
            'Creation Date',
            'Task Status / Result'
        ];
    }

    /**
     * @param array $row
     * @return array
     */
    public function map($row): array
    {
        $member = $row['member'];
        $task = $row['task'];

        $taskName = $task ? $task->title : 'N/A';
        $creationDate = $task ? $task->created_at->format('d M, Y') : 'N/A';
        $statusText = $task ? $this->calculateTaskStatus($task) : 'No Task Assigned';

        // Extract Academic Number from email (prefix before @)
        $emailPrefix = explode('@', $member->user->email)[0];
        $academicNumber = is_numeric($emailPrefix) ? $emailPrefix : 'N/A';

        return [
            $member->user->name,
            $academicNumber,
            ucfirst($member->technical_role),
            $taskName,
            $creationDate,
            $statusText
        ];
    }

    private function calculateTaskStatus($task)
    {
        $now = now();
        
        // 1. Never Submitted
        if ($task->status === 'pending' && !$task->submitted_at) {
            return "❌ Did not submit at all";
        }

        // 2. Final Rejection (twice rejected)
        if ($task->status === 'rejected' && empty($task->new_deadline)) {
            return "🚫 Rejected Twice (Final Rejection)";
        }

        // 3. Ignored Resubmission Deadline
        if ($task->status === 'rejected' && $task->new_deadline && $now->gt($task->new_deadline) && !$task->submitted_at) {
            return "⚠️ Rejected - Ignored Resubmission Deadline";
        }

        // 4. Rejected & Still within resubmission deadline
        if ($task->status === 'rejected' && $task->new_deadline && $now->lte($task->new_deadline)) {
            return "🔄 Rejected - Waiting for Resubmission (Changes Requested)";
        }

        // 5. In Review
        if ($task->status === 'reviewing') {
            $base = "📤 Submitted for Review";
            if ($task->deadline && $task->submitted_at && $task->submitted_at->gt($task->deadline)) {
                return $base . " (LATE)";
            }
            return $base . " (On Time)";
        }

        // 6. Completed (Approved)
        if ($task->status === 'completed') {
            $base = "✅ Approved";
            if ($task->deadline && $task->submitted_at && $task->submitted_at->gt($task->deadline)) {
                return $base . " (Submitted Late)";
            }
            return $base . " (Submitted On Time)";
        }

        return ucfirst($task->status);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}
