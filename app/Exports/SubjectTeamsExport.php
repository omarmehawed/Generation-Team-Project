<?php

namespace App\Exports;

use App\Models\Team;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\HeaderFooterDrawing;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class SubjectTeamsExport implements FromView, WithEvents
{
    protected $teamIds;
    protected $headerTitle;

    // بنستقبل العنوان هنا في الكونستركتور
    public function __construct($teamIds, $headerTitle)
    {
        $this->teamIds = $teamIds;
        $this->headerTitle = $headerTitle;
    }

    public function view(): View
    {
        $teams = Team::with(['members.user'])->whereIn('id', $this->teamIds)->get();

        $data = [];
        foreach ($teams as $team) {
            $membersData = [];
            foreach ($team->members as $member) {
                $emailParts = explode('@', $member->user->email);
                $academicId = $emailParts[0];

                $membersData[] = [
                    'name' => $member->user->name,
                    'academic_id' => $academicId,
                    'position' => ucfirst($member->role),
                    'year' => $member->user->academic_year ?? 'N/A',
                ];
            }
            $data[] = [
                'team_name' => $team->name,
                'members' => $membersData
            ];
        }

        // هننادي على فيو جديد خاص بالمواد
        return view('exports.subject_teams', compact('data') + ['title' => $this->headerTitle]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $sheet->setRightToLeft(false);
                $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_PORTRAIT);
                $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
                $sheet->getPageSetup()->setHorizontalCentered(true);

                $sheet->getColumnDimension('A')->setWidth(35);
                $sheet->getColumnDimension('B')->setWidth(25);
                $sheet->getColumnDimension('C')->setWidth(20);
                $sheet->getColumnDimension('D')->setWidth(15);

                $sheet->getStyle('A:D')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A:D')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

                $drawing = new HeaderFooterDrawing();
                $drawing->setName('Watermark');

                // تأكد من المسار
                $path = public_path('assets/it_logos.png');

                if (file_exists($path)) {
                    $drawing->setPath($path);
                    $drawing->setHeight(250);
                    $sheet->getHeaderFooter()->addImage($drawing, \PhpOffice\PhpSpreadsheet\Worksheet\HeaderFooter::IMAGE_HEADER_CENTER);
                    $sheet->getHeaderFooter()->setOddHeader('&C' . str_repeat("\n", 22) . '&G');
                }
            },
        ];
    }
}
