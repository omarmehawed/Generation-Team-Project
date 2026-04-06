<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JoinRequestQuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $questions = [
            [
                'question_text' => 'عندك خبرة في اي',
                'question_type' => 'radio',
                'options' => ['Embedded Systems / Microcontrollers', 'Robotics & Actuators', 'Mechanical Design & 3D Printing', 'Power Systems & Battery Management', 'Chemistry / Materials Science'],
                'is_required' => true,
                'order_priority' => 1,
            ],
            [
                'question_text' => 'هل لديك أي تجربة في العمل ضمن فريق كبير (أكثر من 10 أفراد)؟',
                'question_type' => 'radio',
                'options' => ['Yes', 'No'],
                'is_required' => true,
                'order_priority' => 2,
            ],
            [
                'question_text' => 'في حال تم قبولك، متى يمكنك البدء بشكل فعلي للعمل على المشروع (بعد الميد مباشرة)؟',
                'question_type' => 'date',
                'is_required' => true,
                'order_priority' => 3,
            ],
            [
                'question_text' => 'ما هي الفترة الزمنية التي يمكنك تخصيصها للعمل على هذا المشروع أسبوعيًا؟',
                'question_type' => 'radio',
                'options' => ['أقل من 5 ساعات', '5 - 10 ساعات', '10 - 20 ساعة', 'أكثر من 20 ساعة'],
                'is_required' => true,
                'order_priority' => 4,
            ],
            [
                'question_text' => 'اذكر أبرز مشروع سابق عملته',
                'question_type' => 'textarea',
                'is_required' => true,
                'order_priority' => 5,
            ],
            [
                'question_text' => 'على مقياس من 1 إلى 5، ما مدى ثقتك بقدرتك على إحداث فرق كبير في المشروع؟',
                'question_type' => 'scale',
                'options' => ['min' => 1, 'max' => 5],
                'is_required' => true,
                'order_priority' => 6,
            ],
            [
                'question_text' => 'يرجى تحديد مدى إتقانك للعمل ضمن فريق في الجوانب التالية:',
                'question_type' => 'matrix',
                'options' => [
                    'rows' => ['Communication', 'Team Problem Solving', 'Meeting Deadlines'],
                    'cols' => ['ضعيف', 'مقبول', 'جيد', 'ممتاز']
                ],
                'is_required' => true,
                'order_priority' => 7,
            ],
            [
                'question_text' => 'ما هي اللغة البرمجية التي تتقنها وتستخدمها بشكل أساسي في مجال خبرتك المذكورة؟',
                'question_type' => 'select',
                'options' => ['C/C++', 'Python', 'Java', 'MATLAB/Simulink', 'Other'],
                'is_required' => true,
                'order_priority' => 8,
            ],
            [
                'question_text' => 'يرجى تحديد مدى إتقانك للمهارات التالية المتعلقة بتصنيع النماذج الأولية (Prototyping):',
                'question_type' => 'matrix',
                'options' => [
                    'rows' => ['3D Printing', 'Soldering', 'PCB Design', 'Electrical Testing'],
                    'cols' => ['ضعيف', 'متوسط', 'جيد', 'ممتاز', 'لا اعرف']
                ],
                'is_required' => true,
                'order_priority' => 9,
            ],
            [
                'question_text' => 'هل لديك أي تجربة سابقة في تأمين تمويل أو التعامل مع مستثمرين لمشاريعك؟',
                'question_type' => 'radio',
                'options' => ['Yes', 'No'],
                'is_required' => true,
                'order_priority' => 10,
            ],
            [
                'question_text' => 'أي من الأدوات أو البرامج التالية تستخدمها بشكل متكرر في مجال خبرتك؟',
                'question_type' => 'matrix',
                'options' => [
                    'rows' => ['SolidWorks / Fusion 360', 'Arduino / Raspberry Pi IDE', 'TensorFlow / PyTorch', 'Altium Designer / Eagle'],
                    'cols' => ['قليلًا', 'أحياناً', 'بشكل متكرر']
                ],
                'is_required' => true,
                'order_priority' => 11,
            ],
            [
                'question_text' => 'هل لديك خبرة في تطوير مساعدات صوتية (Voice Assistants) تتفاعل بشكل لحظي (Real-time)؟',
                'question_type' => 'radio',
                'options' => ['Yes', 'No'],
                'is_required' => true,
                'order_priority' => 12,
            ],
            [
                'question_text' => 'ما هي درجة أهمية المشروع بالنسبة لأهدافك المهنية والشخصية؟',
                'question_type' => 'scale',
                'options' => ['min' => 1, 'max' => 5],
                'is_required' => true,
                'order_priority' => 13,
            ],
            [
                'question_text' => 'في حال وجود ضغط أو تحديات غير متوقعة في المشروع، كيف تتعامل معها؟',
                'question_type' => 'textarea',
                'is_required' => true,
                'order_priority' => 14,
            ],
        ];

        foreach ($questions as $q) {
            \App\Models\JoinRequestQuestion::create($q);
        }
    }
}
