<?php

namespace App\Exports;

use App\Models\Team;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\HeaderFooterDrawing;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class TeamsExport implements FromView, WithEvents
{
    protected $teamIds;

    public function __construct($teamIds)
    {
        $this->teamIds = $teamIds;
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

        return view('exports.teams', compact('data'));
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // 1. إعدادات الصفحة A4 الرسمية
                $sheet->setRightToLeft(false);
                $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_PORTRAIT);
                $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);

                // توسيط محتوى الجدول
                $sheet->getPageSetup()->setHorizontalCentered(true);
                // 2.   تكبير عرض الأعمدة يدوياً
                // الأرقام دي تقديرية عشان تناسب ورقة A4 وتكفي العنوان الكبير
                $sheet->getColumnDimension('A')->setWidth(30); // اسم الطالب (عريض)
                $sheet->getColumnDimension('B')->setWidth(20); // الرقم الأكاديمي
                $sheet->getColumnDimension('C')->setWidth(15); // البوزيشن
                $sheet->getColumnDimension('D')->setWidth(10); // السنة

                // 2.  تظبيط الووتر مارك في نص الصفحة
                $drawing = new HeaderFooterDrawing();
                $drawing->setName('Watermark');

                // المسار للصورة (تأكد إنها النسخة الباهتة/الشفافة)
                $path = public_path('assets/it_logos.png');

                if (file_exists($path)) {
                    $drawing->setPath($path);
                    $drawing->setHeight(250); // كبرناه شوية عشان يملى نص الصفحة

                    // إضافة الصورة للهيدر
                    $sheet->getHeaderFooter()->addImage($drawing, \PhpOffice\PhpSpreadsheet\Worksheet\HeaderFooter::IMAGE_HEADER_CENTER);

                    // 👇 التريك هنا: بنضيف 20 سطر فاضي (\n) قبل الصورة (&G) عشان نزقها لتحت في النص
                    $sheet->getHeaderFooter()->setOddHeader('&C' . str_repeat("\n", 20) . '&G');
                }
            },
        ];
    }
}
