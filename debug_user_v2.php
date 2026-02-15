<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\TeamMember;
use App\Models\Team;

$debugEmail = '2420873@batechu.com'; // From screenshot

echo "\n--- DEBUGGING FOR USER: $debugEmail ---\n";

$user = User::where('email', $debugEmail)->first();

if (!$user) {
    echo "❌ User not found with email: $debugEmail\n";
    // Try fuzzy search
    $fuzzy = User::where('name', 'like', '%Fares%')->get();
    if ($fuzzy->count() > 0) {
        echo "Found potential matches:\n";
        foreach ($fuzzy as $u) {
            echo "- {$u->name} ({$u->email}) [ID: {$u->id}]\n";
        }
    }
    exit;
}

echo "✅ User Found: {$user->name} (ID: {$user->id})\n";
echo "Role: {$user->role}\n";

// Check Team Memberships
$memberships = TeamMember::where('user_id', $user->id)->get();
echo "Found " . $memberships->count() . " team memberships.\n";

foreach ($memberships as $mem) {
    echo "\n--------------------------------------------------\n";
    $team = Team::find($mem->team_id);
    
    if (!$team) {
        echo "❌ Membership exists for Team ID {$mem->team_id}, but TEAM NOT FOUND in DB!\n";
        continue;
    }

    echo "Team: {$team->name} (ID: {$team->id})\n";
    
    if ($team->project) {
        echo "✅ Linked Project:\n";
        echo "   - Title: {$team->project->title}\n";
        echo "   - Type: {$team->project->type}\n";
        echo "   - ID: {$team->project->id}\n";
    } else {
        echo "❌ NO PROJECT LINKED!\n";
        echo "   - Team project_id column: " . ($team->project_id ?? 'NULL') . "\n";
        if ($team->project_id) {
            $proj = \App\Models\Project::find($team->project_id);
            if (!$proj) echo "   - Project ID {$team->project_id} does not exist in 'projects' table.\n";
        }
    }
}
echo "\n--------------------------------------------------\n";
