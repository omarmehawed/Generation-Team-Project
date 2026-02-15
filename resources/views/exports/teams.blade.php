<table>
    {{--  العنوان الرئيسي في أول الصفحة  --}}
    <tr>
        <td colspan="4"
            style="font-size: 24px; font-weight: bold; text-align: center; height: 60px; vertical-align: middle;">
            Graduation Projects Teams
        </td>
    </tr>

    {{-- مسافة صغيرة بعد العنوان --}}
    <tr>
        <td colspan="4" height="20"></td>
    </tr>

    @foreach ($data as $team)
        <tr>
            <td colspan="4"
                style="background-color: #175c53; color: #ffffff; font-weight: bold; font-size: 14px; text-align: center; border: 2px solid #000000;">
                TEAM: {{ strtoupper($team['team_name']) }}
            </td>
        </tr>

        <tr>
            <td
                style="font-weight: bold; background-color: #e2e8f0; border: 1px solid #000000; width: 30px; text-align: center;">
                Student Name</td>
            <td
                style="font-weight: bold; background-color: #e2e8f0; border: 1px solid #000000; width: 20px; text-align: center;">
                Academic ID</td>
            <td
                style="font-weight: bold; background-color: #e2e8f0; border: 1px solid #000000; width: 15px; text-align: center;">
                Position</td>
            <td
                style="font-weight: bold; background-color: #e2e8f0; border: 1px solid #000000; width: 10px; text-align: center;">
                Year</td>
        </tr>

        @foreach ($team['members'] as $member)
            <tr>
                <td style="border: 1px solid #000000; text-align: center;">{{ $member['name'] }}</td>
                <td style="border: 1px solid #000000; text-align: center;">{{ $member['academic_id'] }}</td>
                <td
                    style="border: 1px solid #000000; text-align: center; color: {{ $member['position'] == 'Leader' ? '#D4AF37' : '#000000' }}; font-weight: {{ $member['position'] == 'Leader' ? 'bold' : 'normal' }}">
                    {{ $member['position'] }}
                </td>
                <td style="border: 1px solid #000000; text-align: center;">{{ $member['year'] }}</td>
            </tr>
        @endforeach

        <tr>
            <td colspan="4" height="25"></td>
        </tr>
    @endforeach
</table>
