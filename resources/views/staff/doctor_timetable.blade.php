@extends('layouts.staff')

@section('content')

    <style>
        /* نفس المتغيرات الأساسية */
        :root {
            --primary-gradient: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            --shadow-soft: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.01);
        }

        .chic-card {
            background: #ffffff;
            border-radius: 24px;
            box-shadow: var(--shadow-soft);
            border: 1px solid rgba(0, 0, 0, 0.03);
            overflow: hidden;
        }

        /* تنسيق الأزرار (زي ما هي) */
        .fc-button-primary {
            background: white !important;
            border: 1px solid #e5e7eb !important;
            color: #4b5563 !important;
            font-weight: 600 !important;
            border-radius: 50rem !important;
            padding: 0.6rem 1.5rem !important;
        }

        .fc-button-active {
            background: var(--primary-gradient) !important;
            color: white !important;
            border: none !important;
        }

        /* تنسيق الحدث (المربع الملون) */
        .fc-event {
            border: none !important;
            /* ❌ شلنا الخلفية البيضاء عشان يقبل اللون من الداتا */
            border-radius: 8px !important;
            padding: 8px 12px !important;
            /* وسعنا الحشو شوية */
            margin-bottom: 6px !important;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;

            /* خصائص النص */
            color: white !important;
            /* النص أبيض */
            height: auto !important;
            white-space: normal !important;
        }

        .fc-event:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.2) !important;
            filter: brightness(1.1);
            /* تفتيح بسيط لما تقف عليه */
        }

        .fc-event-title {
            color: rgb(0, 0, 0) !important;
            /* تأكيد إن العنوان أبيض */
            font-weight: 700 !important;
            font-size: 0.9rem;
            /* كبرنا الخط سنة */
            white-space: normal !important;
            word-wrap: break-word !important;
            line-height: 1.4;
            margin-bottom: 4px;
        }

        .fc-event-time {
            color: rgb(0, 0, 0) !important;
            /* الوقت أبيض شفاف شوية */
            font-weight: 600;
            font-size: 0.75rem;
            display: block;
            margin-bottom: 2px;
            text-transform: uppercase;
        }

        .event-location {
            font-size: 0.75rem;
            color: rgba(255, 255, 255, 0.85);
            /* المكان أبيض شفاف */
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 5px;
            font-weight: 500;
            background: rgba(0, 0, 0, 0.1);
            /* خلفية خفيفة للمكان */
            padding: 2px 6px;
            border-radius: 4px;
            width: fit-content;
        }

        .fc-daygrid-day-number {
            color: #374151;
            font-weight: 700;
            padding: 1rem !important;
        }

        .fc-col-header-cell-cushion {
            color: #6b7280;
            text-transform: uppercase;
            padding: 1.5rem 0 !important;
        }

        .fc-header-toolbar {
            margin-bottom: 2rem !important;
            padding: 1rem;
        }
    </style>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 animate-fade-in-up">

        {{-- Header --}}
        <div class="flex flex-col md:flex-row justify-between items-end mb-10 gap-4">
            <div>
                <h1 class="text-3xl font-black text-gray-800 dark:text-gray-200 tracking-tight mb-2">Defense Calendar 📅</h1>
                <p class="text-gray-500 dark:text-gray-400 font-medium text-sm">Manage graduation project defense schedules.</p>
            </div>
            <div class="bg-white dark:bg-gray-800 px-5 py-3 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div>
                    <p class="text-xs uppercase text-gray-400 font-bold tracking-wider">Scheduled</p>
                    <p class="text-xl font-black text-gray-800 dark:text-gray-200">{{ count($scheduledTeams) }} <span
                            class="text-xs font-normal text-gray-400">Teams</span></p>
                </div>
            </div>
        </div>

        {{-- Calendar --}}
        <div class="chic-card p-4 md:p-6 relative">
            <div id='calendar' class="font-sans"></div>
        </div>
    </div>

    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var calendarEl = document.getElementById('calendar');
            var events = @json($scheduledTeams);

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
                },
                themeSystem: 'standard',
                height: 'auto',
                dayMaxEvents: 3,
                events: events,

                // تخصيص المحتوى
                eventContent: function (arg) {
                    let container = document.createElement('div');

                    // Time
                    if (arg.timeText) {
                        let time = document.createElement('span');
                        time.className = 'fc-event-time';
                        time.innerHTML = arg.timeText; // اللون هيتاخد من الـ CSS الأبيض
                        container.appendChild(time);
                    }

                    // Title
                    let title = document.createElement('div');
                    title.className = 'fc-event-title';
                    title.innerHTML = arg.event.title;
                    container.appendChild(title);

                    // Location
                    if (arg.event.extendedProps.location) {
                        let loc = document.createElement('div');
                        loc.className = 'event-location';
                        loc.innerHTML = '<i class="fas fa-map-marker-alt"></i> ' + arg.event.extendedProps.location;
                        container.appendChild(loc);
                    }

                    return { domNodes: [container] };
                },

                // 🎨 تلوين الخلفية بالكامل
                eventDidMount: function (info) {
                    // FullCalendar بيلون الخلفية تلقائي لو مفيش CSS مانع ده
                    // بس زيادة تأكيد:
                    if (info.event.backgroundColor) {
                        info.el.style.backgroundColor = info.event.backgroundColor;
                        info.el.style.borderColor = info.event.backgroundColor;
                    }
                },

                eventClick: function (info) {
                    info.jsEvent.preventDefault();
                    if (info.event.url) {
                        window.open(info.event.url, "_self");
                    }
                }
            });

            calendar.render();
        });
    </script>
@endsection