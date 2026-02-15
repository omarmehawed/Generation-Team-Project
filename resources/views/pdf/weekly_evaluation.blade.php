<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta charset="UTF-8">
    <title>Weekly Evaluation</title>
    <style>
        @page {
            margin: 10mm;
            size: A4;
        }

        body {
            font-family: 'Amiri', sans-serif;
            font-size: 12px;
            color: #000;
            line-height: 1.3;
        }

        .container {
            width: 100%;
            border: 2px solid #000;
            padding: 5mm;
            box-sizing: border-box;
        }

        .header-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-bottom: 5mm;
        }

        .header-table td {
            vertical-align: top;
            padding: 2px;
        }

        .info-row {
            margin-bottom: 3mm;
        }

        .info-label {
            display: inline-block;
            width: 80px;
            font-weight: bold;
        }

        .info-value {
            border: 1px solid #000;
            padding: 2px 5px;
            display: inline-block;
            width: 250px;
            min-height: 18px;
        }

        .logo-container {
            text-align: right;
            margin-bottom: 5mm;
        }

        .student-photo {
            width: 100px;
            height: 120px;
            border: 1px solid #000;
            object-fit: cover;
            display: block;
            margin-left: auto;
            margin-right: 0;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 2mm;
            border-bottom: 2px solid #000;
            padding-bottom: 1mm;
            margin-top: 5mm;
        }

        .content-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border: 1px solid #000;
            margin-bottom: 0;
        }

        .content-table th {
            background-color: #f0f0f0;
            border: 1px solid #000;
            padding: 5px;
            font-size: 11px;
            font-weight: bold;
            text-align: center;
        }

        .content-table td {
            border: 1px solid #000;
            padding: 4px;
            vertical-align: middle;
        }

        .checkbox-span {
            font-family: DejaVu Sans, sans-serif;
            font-size: 16px;
        }

        .notes-box {
            border: 1px solid #000;
            padding: 3mm;
            height: 25mm;
            font-size: 11px;
            margin-bottom: 5mm;
            overflow: hidden;
        }

        .comm-flat-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-bottom: 5mm;
        }

        .comm-flat-table td,
        .comm-flat-table th {
            border: 1px solid #000;
            text-align: center;
            padding: 4px;
            height: 8mm;
        }

        .comm-flat-table th {
            background-color: #f0f0f0;
            font-size: 11px;
        }

        .comm-spacer {
            border: none !important;
            background: none !important;
            width: 4%;
        }
    </style>
</head>

<body>
    <div class="container">

        {{-- Header --}}
        <table class="header-table">
            <tr>
                <td width="65%">
                    <div style="font-size: 20px; font-weight: bold; color: #00a4e4; margin-bottom: 5mm;">GENERATION TEAM
                    </div>
                    <div class="info-row"><span class="info-label">Name</span>
                        <div class="info-value">{{ $evaluation->student->name }}</div>
                    </div>
                    <div class="info-row"><span class="info-label">ID</span>
                        <div class="info-value">{{ $evaluation->student->national_id ?? $evaluation->student->id }}
                        </div>
                    </div>
                    <div class="info-row"><span class="info-label">Phone</span>
                        <div class="info-value">{{ $evaluation->student->phone_number }}</div>
                    </div>
                    <div class="info-row"><span class="info-label">National ID</span>
                        <div class="info-value">{{ $evaluation->student->national_id }}</div>
                    </div>
                    <div class="info-row"><span class="info-label">Address</span>
                        <div class="info-value">{{ $evaluation->student->address }}</div>
                    </div>
                </td>
                <td width="35%" style="text-align: right; vertical-align: top;">
                    <div class="logo-container">
                        <div style="text-align: center; margin-bottom: 5mm;">
                            @if(file_exists(public_path('assets/images/logo.png')))
                                <img src="{{ public_path('assets/images/logo.png') }}" style="width: 80px; height: auto;"
                                    alt="Logo">
                            @else
                                <div style="font-size: 16px; font-weight: bold;">FACULTY OF COMPUTERS & ARTIFICIAL
                                    INTELLIGENCE</div>
                            @endif
                            <div style="font-size: 9px; margin-top: 2px;">Faculty of Computers & AI</div>
                            <div style="font-size: 9px;">Benha University</div>
                        </div>
                    </div>

                    @php
                        $photoUrl = null;
                        if ($evaluation->student->profile_photo_path) {
                            $basePath = storage_path('app/public/' . $evaluation->student->profile_photo_path);
                            if (file_exists($basePath)) {
                                $photoUrl = $basePath;
                            } else {
                                $publicPath = public_path('storage/' . $evaluation->student->profile_photo_path);
                                if (file_exists($publicPath)) {
                                    $photoUrl = $publicPath;
                                }
                            }
                        }
                    @endphp
                    <div style="text-align: center;">
                        @if($photoUrl)
                            <img src="{{ $photoUrl }}" class="student-photo" alt="Student Photo">
                        @else
                            <div class="student-photo"
                                style="background: #eee; line-height: 120px; text-align: center; color: #999;">No Photo
                            </div>
                        @endif
                    </div>
                </td>
            </tr>
        </table>

        {{-- Commitment Section (Flat Table) --}}
        <div style="width: 100%; overflow: hidden; margin-bottom: 2px;">
            <div
                style="float: left; width: 48%; border-bottom: 2px solid #000; font-weight: bold; text-transform: uppercase; font-size: 14px;">
                Commitment Level</div>
            <div style="float: left; width: 4%;"></div>
            <div
                style="float: left; width: 48%; border-bottom: 2px solid #000; font-weight: bold; text-transform: uppercase; font-size: 14px;">
                Team Satisfaction</div>
        </div>

        <table class="comm-flat-table">
            <tr>
                @for($i = 1; $i <= 5; $i++)
                <th width="9.6%">{{ $i }}</th> @endfor
                <td class="comm-spacer" width="4%"></td>
                @for($i = 1; $i <= 5; $i++)
                <th width="9.6%">{{ $i }}</th> @endfor
            </tr>
            <tr>
                @for($i = 1; $i <= 5; $i++)
                    <td>
                        @if($evaluation->commitment_level == $i) <span class="checkbox-span">&#9745;</span> @else <span
                        class="checkbox-span">&#9744;</span> @endif
                    </td>
                @endfor
                <td class="comm-spacer"></td>
                @for($i = 1; $i <= 5; $i++)
                    <td>
                        @if($evaluation->satisfaction_level == $i) <span class="checkbox-span">&#9745;</span> @else <span
                        class="checkbox-span">&#9744;</span> @endif
                    </td>
                @endfor
            </tr>
        </table>

        {{-- Tasks --}}
        <div class="section-title">Tasks</div>
        <table class="content-table">
            <thead>
                <tr>
                    <th width="40%" style="text-align: left;">Task Title</th>
                    <th width="10%">Poor</th>
                    <th width="10%">Avg</th>
                    <th width="10%">Good</th>
                    <th width="10%">Exc</th>
                    <th width="20%">Note</th>
                </tr>
            </thead>
            <tbody>
                @foreach($evaluation->items->where('type', 'task')->sortBy('order') as $item)
                    <tr>
                        <td>{{ $item->title }}</td>
                        <td align="center">@if($item->rating == 'poor') <span class="checkbox-span">&#9745;</span> @else
                        <span class="checkbox-span">&#9744;</span> @endif</td>
                        <td align="center">@if($item->rating == 'average') <span class="checkbox-span">&#9745;</span> @else
                        <span class="checkbox-span">&#9744;</span> @endif</td>
                        <td align="center">@if($item->rating == 'good') <span class="checkbox-span">&#9745;</span> @else
                        <span class="checkbox-span">&#9744;</span> @endif</td>
                        <td align="center">@if($item->rating == 'excellent') <span class="checkbox-span">&#9745;</span>
                        @else <span class="checkbox-span">&#9744;</span> @endif</td>
                        <td>{{ $item->note }}</td>
                    </tr>
                @endforeach
                @if($evaluation->items->where('type', 'task')->count() == 0)
                    <tr>
                        <td colspan="6" align="center">No tasks recorded.</td>
                    </tr>
                @endif
            </tbody>
        </table>

        {{-- General Notes --}}
        <div class="section-title">General Notes</div>
        <div class="notes-box">
            {{ $evaluation->general_notes ?? 'No notes provided.' }}
        </div>

        {{-- Quizzes --}}
        <div class="section-title">Quizzes</div>
        <table class="content-table">
            <thead>
                <tr>
                    <th width="40%" style="text-align: left;">Quiz Title</th>
                    <th width="10%">Poor</th>
                    <th width="10%">Avg</th>
                    <th width="10%">Good</th>
                    <th width="10%">Exc</th>
                    <th width="20%">Note</th>
                </tr>
            </thead>
            <tbody>
                @foreach($evaluation->items->where('type', 'quiz')->sortBy('order') as $item)
                    <tr>
                        <td>{{ $item->title }}</td>
                        <td align="center">@if($item->rating == 'poor') <span class="checkbox-span">&#9745;</span> @else
                        <span class="checkbox-span">&#9744;</span> @endif</td>
                        <td align="center">@if($item->rating == 'average') <span class="checkbox-span">&#9745;</span> @else
                        <span class="checkbox-span">&#9744;</span> @endif</td>
                        <td align="center">@if($item->rating == 'good') <span class="checkbox-span">&#9745;</span> @else
                        <span class="checkbox-span">&#9744;</span> @endif</td>
                        <td align="center">@if($item->rating == 'excellent') <span class="checkbox-span">&#9745;</span>
                        @else <span class="checkbox-span">&#9744;</span> @endif</td>
                        <td>{{ $item->note }}</td>
                    </tr>
                @endforeach
                @if($evaluation->items->where('type', 'quiz')->count() == 0)
                    <tr>
                        <td colspan="6" align="center">No quizzes recorded.</td>
                    </tr>
                @endif
            </tbody>
        </table>

        {{-- Meetings --}}
        <div class="section-title">Meetings</div>
        <table class="content-table">
            <thead>
                <tr>
                    <th width="60%" style="text-align: left;">Meeting Title</th>
                    <th width="10%">Attended</th>
                    <th width="10%">Absence</th>
                    <th width="20%">Note</th>
                </tr>
            </thead>
            <tbody>
                @foreach($evaluation->items->where('type', 'meeting')->sortBy('order') as $item)
                    <tr>
                        <td>{{ $item->title }}</td>
                        <td align="center">@if($item->rating == 'attended') <span class="checkbox-span">&#9745;</span> @else
                        <span class="checkbox-span">&#9744;</span> @endif</td>
                        <td align="center">@if($item->rating == 'absence') <span class="checkbox-span">&#9745;</span> @else
                        <span class="checkbox-span">&#9744;</span> @endif</td>
                        <td>{{ $item->note }}</td>
                    </tr>
                @endforeach
                @if($evaluation->items->where('type', 'meeting')->count() == 0)
                    <tr>
                        <td colspan="4" align="center">No meetings recorded.</td>
                    </tr>
                @endif
            </tbody>
        </table>

        {{-- Workshops --}}
        <div class="section-title">Workshops</div>
        <table class="content-table">
            <thead>
                <tr>
                    <th width="60%" style="text-align: left;">Workshop Title</th>
                    <th width="10%">Attended</th>
                    <th width="10%">Absence</th>
                    <th width="20%">Note</th>
                </tr>
            </thead>
            <tbody>
                @foreach($evaluation->items->where('type', 'workshop')->sortBy('order') as $item)
                    <tr>
                        <td>{{ $item->title }}</td>
                        <td align="center">@if($item->rating == 'attended') <span class="checkbox-span">&#9745;</span> @else
                        <span class="checkbox-span">&#9744;</span> @endif</td>
                        <td align="center">@if($item->rating == 'absence') <span class="checkbox-span">&#9745;</span> @else
                        <span class="checkbox-span">&#9744;</span> @endif</td>
                        <td>{{ $item->note }}</td>
                    </tr>
                @endforeach
                @if($evaluation->items->where('type', 'workshop')->count() == 0)
                    <tr>
                        <td colspan="4" align="center">No workshops recorded.</td>
                    </tr>
                @endif
            </tbody>
        </table>

        {{-- Activities --}}
        <div class="section-title">Activities</div>
        <table class="content-table">
            <thead>
                <tr>
                    <th width="40%" style="text-align: left;">Activity Title</th>
                    <th width="10%">Poor</th>
                    <th width="10%">Avg</th>
                    <th width="10%">Good</th>
                    <th width="10%">Exc</th>
                    <th width="20%">Note</th>
                </tr>
            </thead>
            <tbody>
                @foreach($evaluation->items->where('type', 'activity')->sortBy('order') as $item)
                    <tr>
                        <td>{{ $item->title }}</td>
                        <td align="center">@if($item->rating == 'poor') <span class="checkbox-span">&#9745;</span> @else
                        <span class="checkbox-span">&#9744;</span> @endif</td>
                        <td align="center">@if($item->rating == 'average') <span class="checkbox-span">&#9745;</span> @else
                        <span class="checkbox-span">&#9744;</span> @endif</td>
                        <td align="center">@if($item->rating == 'good') <span class="checkbox-span">&#9745;</span> @else
                        <span class="checkbox-span">&#9744;</span> @endif</td>
                        <td align="center">@if($item->rating == 'excellent') <span class="checkbox-span">&#9745;</span>
                        @else <span class="checkbox-span">&#9744;</span> @endif</td>
                        <td>{{ $item->note }}</td>
                    </tr>
                @endforeach
                @if($evaluation->items->where('type', 'activity')->count() == 0)
                    <tr>
                        <td colspan="6" align="center">No activities recorded.</td>
                    </tr>
                @endif
            </tbody>
        </table>

    </div>
</body>

</html>