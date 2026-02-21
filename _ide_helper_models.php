<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property int|null $causer_id
 * @property int|null $subject_id
 * @property string|null $subject_type
 * @property string $action
 * @property string|null $description
 * @property array<array-key, mixed>|null $changes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $team_id
 * @property int|null $target_user_id
 * @property-read \App\Models\User|null $causer
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent|null $subject
 * @property-read \App\Models\User|null $targetUser
 * @property-read \App\Models\Team|null $team
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereCauserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereChanges($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereSubjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereSubjectType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereTargetUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereUpdatedAt($value)
 */
	class ActivityLog extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $meeting_id
 * @property int $student_id
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Meeting $meeting
 * @property-read \App\Models\User $student
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereMeetingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereUpdatedAt($value)
 */
	class Attendance extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $code
 * @property string $icon_class
 * @property string $color
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $year_level
 * @property int $term
 * @property string $department
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Project> $projects
 * @property-read int|null $projects_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereDepartment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereIconClass($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereTerm($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereYearLevel($value)
 */
	class Course extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $weekly_evaluation_id
 * @property string $type
 * @property string $title
 * @property string|null $rating
 * @property int|null $mark
 * @property string|null $note
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\WeeklyEvaluation $evaluation
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EvaluationItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EvaluationItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EvaluationItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EvaluationItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EvaluationItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EvaluationItem whereMark($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EvaluationItem whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EvaluationItem whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EvaluationItem whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EvaluationItem whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EvaluationItem whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EvaluationItem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EvaluationItem whereWeeklyEvaluationId($value)
 */
	class EvaluationItem extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $project_expense_id
 * @property int $user_id
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ProjectExpense $expense
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpenseContribution newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpenseContribution newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpenseContribution query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpenseContribution whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpenseContribution whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpenseContribution whereProjectExpenseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpenseContribution whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpenseContribution whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpenseContribution whereUserId($value)
 */
	class ExpenseContribution extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $fund_id
 * @property int $user_id
 * @property string|null $status
 * @property \Illuminate\Support\Carbon|null $paid_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $payment_method
 * @property string|null $payment_proof
 * @property string|null $transaction_date
 * @property string|null $transaction_time
 * @property string|null $from_number
 * @property string|null $notes
 * @property string|null $rejection_reason
 * @property-read \App\Models\ProjectFund $fund
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FundContribution newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FundContribution newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FundContribution query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FundContribution whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FundContribution whereFromNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FundContribution whereFundId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FundContribution whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FundContribution whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FundContribution wherePaidAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FundContribution wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FundContribution wherePaymentProof($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FundContribution whereRejectionReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FundContribution whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FundContribution whereTransactionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FundContribution whereTransactionTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FundContribution whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FundContribution whereUserId($value)
 */
	class FundContribution extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $full_name
 * @property \Illuminate\Support\Carbon $date_of_birth
 * @property string $national_id
 * @property string $academic_id
 * @property string $group
 * @property string $phone_number
 * @property string $whatsapp_number
 * @property string|null $address
 * @property int $is_dorm
 * @property string|null $photo_path
 * @property array<array-key, mixed>|null $answers
 * @property string $status
 * @property int|null $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $approved_by
 * @property-read \App\Models\User|null $approver
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JoinRequest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JoinRequest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JoinRequest query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JoinRequest whereAcademicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JoinRequest whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JoinRequest whereAnswers($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JoinRequest whereApprovedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JoinRequest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JoinRequest whereDateOfBirth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JoinRequest whereFullName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JoinRequest whereGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JoinRequest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JoinRequest whereIsDorm($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JoinRequest whereNationalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JoinRequest wherePhoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JoinRequest wherePhotoPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JoinRequest whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JoinRequest whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JoinRequest whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JoinRequest whereWhatsappNumber($value)
 */
	class JoinRequest extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $team_id
 * @property string $topic
 * @property string|null $description
 * @property \Illuminate\Support\Carbon $meeting_date
 * @property string $mode
 * @property string|null $meeting_link
 * @property string|null $location
 * @property string $type
 * @property string|null $status
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\MeetingAttendance> $attendances
 * @property-read int|null $attendances_count
 * @property-read \App\Models\Team $team
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Meeting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Meeting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Meeting query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Meeting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Meeting whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Meeting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Meeting whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Meeting whereMeetingDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Meeting whereMeetingLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Meeting whereMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Meeting whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Meeting whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Meeting whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Meeting whereTopic($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Meeting whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Meeting whereUpdatedAt($value)
 */
	class Meeting extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $meeting_id
 * @property int $user_id
 * @property int $is_present
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MeetingAttendance newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MeetingAttendance newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MeetingAttendance query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MeetingAttendance whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MeetingAttendance whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MeetingAttendance whereIsPresent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MeetingAttendance whereMeetingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MeetingAttendance whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MeetingAttendance whereUserId($value)
 */
	class MeetingAttendance extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $team_id
 * @property int $reporter_id
 * @property int $reported_user_id
 * @property string $complaint
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Team $team
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberReport newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberReport newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberReport query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberReport whereComplaint($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberReport whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberReport whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberReport whereReportedUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberReport whereReporterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberReport whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberReport whereUpdatedAt($value)
 */
	class MemberReport extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $course_id
 * @property string $title
 * @property string|null $description
 * @property string $type
 * @property string|null $deadline
 * @property string|null $leave_team_deadline
 * @property int $max_members
 * @property int $max_score
 * @property int $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $status
 * @property string|null $pre_defense_date
 * @property string|null $doctor_feedback
 * @property string|null $final_book_file
 * @property string|null $presentation_file
 * @property string|null $defense_video_link
 * @property int $is_fully_submitted
 * @property int|null $ta_id
 * @property string $project_type
 * @property string|null $academic_year
 * @property-read \App\Models\Course|null $course
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProjectFund> $funds
 * @property-read int|null $funds_count
 * @property-read \App\Models\User|null $leader
 * @property-read \App\Models\User|null $supervisor
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Team> $teams
 * @property-read int|null $teams_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereAcademicYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereDeadline($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereDefenseVideoLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereDoctorFeedback($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereFinalBookFile($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereIsFullySubmitted($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereLeaveTeamDeadline($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereMaxMembers($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereMaxScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project wherePreDefenseDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project wherePresentationFile($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereProjectType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereTaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereUpdatedAt($value)
 */
	class Project extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $team_id
 * @property int|null $user_id
 * @property string $item
 * @property int $quantity
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $shop_name
 * @property numeric $amount
 * @property string|null $receipt_path
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ExpenseContribution> $contributions
 * @property-read int|null $contributions_count
 * @property-read \App\Models\Team $team
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectExpense newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectExpense newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectExpense query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectExpense whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectExpense whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectExpense whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectExpense whereItem($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectExpense whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectExpense whereReceiptPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectExpense whereShopName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectExpense whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectExpense whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectExpense whereUserId($value)
 */
	class ProjectExpense extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $team_id
 * @property string $title
 * @property numeric $amount_per_member
 * @property string $deadline
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FundContribution> $contributions
 * @property-read int|null $contributions_count
 * @property-read \App\Models\Team $team
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectFund newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectFund newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectFund query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectFund whereAmountPerMember($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectFund whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectFund whereDeadline($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectFund whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectFund whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectFund whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectFund whereUpdatedAt($value)
 */
	class ProjectFund extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $team_id
 * @property string $file_path
 * @property string $type
 * @property string|null $caption
 * @property int $uploaded_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectGallery newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectGallery newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectGallery query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectGallery whereCaption($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectGallery whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectGallery whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectGallery whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectGallery whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectGallery whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectGallery whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectGallery whereUploadedBy($value)
 */
	class ProjectGallery extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $team_id
 * @property int $reporter_id
 * @property int $reported_user_id
 * @property string $reason
 * @property string|null $link
 * @property string|null $file_path
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereReportedUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereReporterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereUpdatedAt($value)
 */
	class Report extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $key
 * @property string $value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereValue($value)
 */
	class Setting extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $team_id
 * @property int|null $user_id
 * @property int|null $creator_id
 * @property string $title
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $deadline
 * @property string $status
 * @property string|null $submission_type
 * @property string|null $submission_value
 * @property string|null $submission_file
 * @property string|null $submission_comment
 * @property \Illuminate\Support\Carbon|null $submitted_at
 * @property numeric|null $grade
 * @property string|null $feedback
 * @property \Illuminate\Support\Carbon|null $graded_at
 * @property int|null $graded_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $assigned_by
 * @property-read mixed $file_format
 * @property-read mixed $file_name
 * @property-read mixed $file_path
 * @property-read mixed $file_size
 * @property-read mixed $file_url
 * @property-read mixed $is_late
 * @property-read mixed $is_overdue
 * @property-read mixed $is_submitted_late
 * @property-read mixed $status_color
 * @property-read mixed $status_text
 * @property-read mixed $zip_code
 * @property-read \App\Models\User|null $grader
 * @property-read Task|null $task
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task graded()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task late()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task pending()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereAssignedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereCreatorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereDeadline($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereFeedback($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereGrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereGradedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereGradedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereSubmissionComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereSubmissionFile($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereSubmissionType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereSubmissionValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereSubmittedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereUserId($value)
 */
	class Task extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $project_id
 * @property string $name
 * @property string|null $logo
 * @property string|null $proposal_title
 * @property string|null $proposal_description
 * @property string|null $proposal_file
 * @property string $code
 * @property int $leader_id
 * @property string $status
 * @property string|null $submission_link
 * @property string|null $submission_path
 * @property string|null $submission_comment
 * @property int|null $grade
 * @property string|null $doctor_feedback
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $proposal_status
 * @property string|null $rejection_reason
 * @property string|null $project_phase
 * @property int|null $ta_id
 * @property numeric|null $grade_phase1
 * @property numeric|null $grade_midterm
 * @property numeric|null $grade_final
 * @property numeric|null $grade_total
 * @property string|null $defense_date
 * @property string|null $defense_location
 * @property string|null $final_book_file
 * @property string|null $presentation_file
 * @property string|null $defense_video_link
 * @property int $is_fully_submitted
 * @property string|null $submitted_at
 * @property int $is_grades_published
 * @property float|null $project_score
 * @property int|null $project_max_score
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProjectExpense> $expenses
 * @property-read int|null $expenses_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProjectGallery> $gallery
 * @property-read int|null $gallery_count
 * @property-read \App\Models\User $leader
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Meeting> $meetings
 * @property-read int|null $meetings_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\MemberReport> $memberReports
 * @property-read int|null $member_reports_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TeamMember> $members
 * @property-read int|null $members_count
 * @property-read \App\Models\Project $project
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\WeeklyReport> $reports
 * @property-read int|null $reports_count
 * @property-read \App\Models\User|null $ta
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Task> $tasks
 * @property-read int|null $tasks_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereDefenseDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereDefenseLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereDefenseVideoLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereDoctorFeedback($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereFinalBookFile($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereGrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereGradeFinal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereGradeMidterm($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereGradePhase1($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereGradeTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereIsFullySubmitted($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereIsGradesPublished($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereLeaderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team wherePresentationFile($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereProjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereProjectMaxScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereProjectPhase($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereProjectScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereProposalDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereProposalFile($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereProposalStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereProposalTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereRejectionReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereSubmissionComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereSubmissionLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereSubmissionPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereSubmittedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereTaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereUpdatedAt($value)
 */
	class Team extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $team_id
 * @property int $user_id
 * @property string|null $role
 * @property int $is_group_a
 * @property int $is_group_b
 * @property string $status
 * @property numeric|null $individual_score
 * @property string $technical_role
 * @property string|null $extra_role
 * @property string $joined_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $sub_team
 * @property int $is_vice_leader
 * @property-read \App\Models\Team $team
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamMember newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamMember newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamMember query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamMember whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamMember whereExtraRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamMember whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamMember whereIndividualScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamMember whereIsGroupA($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamMember whereIsGroupB($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamMember whereIsViceLeader($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamMember whereJoinedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamMember whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamMember whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamMember whereSubTeam($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamMember whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamMember whereTechnicalRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamMember whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamMember whereUserId($value)
 */
	class TeamMember extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $team_id
 * @property string $name
 * @property string $email
 * @property string|null $phone_number
 * @property string|null $whatsapp_number
 * @property string|null $date_of_birth
 * @property int $is_dorm
 * @property string|null $national_id
 * @property string|null $address
 * @property string|null $profile_photo_path
 * @property numeric $wallet_balance
 * @property string|null $university_email
 * @property string $role
 * @property array<array-key, mixed>|null $permissions
 * @property int $academic_year
 * @property string $department
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property int|null $created_by_id
 * @property int|null $deleted_by_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\JoinRequest> $approvedRequests
 * @property-read int|null $approved_requests_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Course> $courses
 * @property-read int|null $courses_count
 * @property-read User|null $creator
 * @property-read User|null $deleter
 * @property-read \App\Models\JoinRequest|null $joinRequest
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Task> $tasks
 * @property-read int|null $tasks_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\WalletTransaction> $walletTransactions
 * @property-read int|null $wallet_transactions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\WeeklyEvaluation> $weeklyEvaluations
 * @property-read int|null $weekly_evaluations_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAcademicYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedById($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDateOfBirth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDeletedById($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDepartment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsDorm($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereNationalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePermissions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePhoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereProfilePhotoPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUniversityEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereWalletBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereWhatsappNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutTrashed()
 */
	class User extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int $admin_id
 * @property string $type
 * @property numeric $amount
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $admin
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTransaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTransaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTransaction query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTransaction whereAdminId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTransaction whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTransaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTransaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTransaction whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTransaction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTransaction whereUserId($value)
 */
	class WalletTransaction extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $student_id
 * @property int $week_number
 * @property int $commitment_level
 * @property int $satisfaction_level
 * @property string|null $general_notes
 * @property string|null $pdf_path
 * @property int $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $creator
 * @property-read mixed $activities
 * @property-read mixed $meetings
 * @property-read mixed $quizzes
 * @property-read mixed $tasks
 * @property-read mixed $workshops
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\EvaluationItem> $items
 * @property-read int|null $items_count
 * @property-read \App\Models\User $student
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeeklyEvaluation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeeklyEvaluation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeeklyEvaluation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeeklyEvaluation whereCommitmentLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeeklyEvaluation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeeklyEvaluation whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeeklyEvaluation whereGeneralNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeeklyEvaluation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeeklyEvaluation wherePdfPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeeklyEvaluation whereSatisfactionLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeeklyEvaluation whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeeklyEvaluation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeeklyEvaluation whereWeekNumber($value)
 */
	class WeeklyEvaluation extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $team_id
 * @property int $user_id
 * @property int $week_number
 * @property string|null $report_date
 * @property string $achievements
 * @property string $plans
 * @property string|null $challenges
 * @property string|null $file_path
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $status
 * @property string|null $title
 * @property-read \App\Models\Team $team
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeeklyReport newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeeklyReport newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeeklyReport query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeeklyReport whereAchievements($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeeklyReport whereChallenges($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeeklyReport whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeeklyReport whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeeklyReport whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeeklyReport wherePlans($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeeklyReport whereReportDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeeklyReport whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeeklyReport whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeeklyReport whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeeklyReport whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeeklyReport whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeeklyReport whereWeekNumber($value)
 */
	class WeeklyReport extends \Eloquent {}
}

