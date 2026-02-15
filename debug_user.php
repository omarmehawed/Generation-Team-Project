<?php

use App\Models\User;
use App\Models\TeamMember;

$email = '2420873@batechu.com'; // From screenshot
$user = User::where('email', $email)->first();

if (!$user) {
    echo "User not found with email: $email\n";
    // Try to find by name part if email is wrong (though it looks correct)
    $user = User::where('name', 'like', '%Fares%')->first();
    if ($user) {
        echo "Found user by name: {$user->name} ({$user->email})\n";
    } else {
        exit;
    }
}

echo "User ID: {$user->id}\n";
echo "Role: {$user->role}\n";

$memberships = TeamMember::where('user_id', $user->id)->with(['team.project'])->get();

echo "Team Memberships count: " . $memberships->count() . "\n";

foreach ($memberships as $mem) {
    echo "--------------------------------------------------\n";
    echo "Team ID: {$mem->team_id}\n";
    echo "Team Name: " . ($mem->team->name ?? 'N/A') . "\n";
    
    if ($mem->team && $mem->team->project) {
        echo "Project ID: {$mem->team->project->id}\n";
        echo "Project Title: {$mem->team->project->title}\n";
        echo "Project Type: {$mem->team->project->type}\n";
    } else {
        echo "NO PROJECT LINKED TO THIS TEAM!\n";
        if (!$mem->team) echo "  (Team is null)\n";
        elseif (!$mem->team->project) {
             echo "  (Team exists but project is null)\n";
             // Check if project_id exists on team
             echo "  Team->project_id: {$mem->team->project_id}\n";
        }
    }
}
