<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\HomeSection;

echo "=== All Home Sections ===\n";
$sections = HomeSection::all();
foreach ($sections as $section) {
    echo "ID: {$section->id} | Type: {$section->type} | Title: {$section->title} | Active: " . ($section->is_active ? 'Yes' : 'No') . " | Order: {$section->display_order}\n";
}

echo "\n=== Active Sections ===\n";
$activeSections = HomeSection::where('is_active', true)->orderBy('display_order')->get();
foreach ($activeSections as $section) {
    echo "ID: {$section->id} | Type: {$section->type} | Title: {$section->title} | Order: {$section->display_order}\n";
}

echo "\n=== Checking for duplicates by type ===\n";
$types = HomeSection::groupBy('type')->selectRaw('type, count(*) as count')->get();
foreach ($types as $type) {
    echo "Type: {$type->type} | Count: {$type->count}\n";
    if ($type->count > 1) {
        echo "  WARNING: Duplicate type found!\n";
        $duplicates = HomeSection::where('type', $type->type)->get();
        foreach ($duplicates as $dup) {
            echo "    - ID: {$dup->id} | Title: {$dup->title} | Created: {$dup->created_at}\n";
        }
    }
}
