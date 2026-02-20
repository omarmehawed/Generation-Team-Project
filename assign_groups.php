<?php
use App\Models\Team;
$teams = Team::all();
foreach($teams as $team) {
    $members = $team->members()->orderBy('created_at', 'asc')->get();
    $count = 0;
    foreach($members as $m) {
        $m->auto_group = ($count < 30) ? 'A' : 'B';
        $m->save();
        $count++;
    }
}
echo "Retroactive assignment complete.\n";
