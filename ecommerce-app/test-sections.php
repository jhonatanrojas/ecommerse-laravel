<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$service = app(\App\Services\HomeConfigurationService::class);
$config = $service->getCompleteConfiguration();

echo "Total active sections: " . count($config) . "\n\n";

$types = [];
foreach ($config as $section) {
    $type = $section['type'];
    if (!isset($types[$type])) {
        $types[$type] = 0;
    }
    $types[$type]++;
    echo "- {$section['type']} (order: {$section['display_order']}, title: {$section['title']})\n";
}

echo "\n\nSection types summary:\n";
foreach ($types as $type => $count) {
    echo "- $type: $count\n";
}

$allTypes = ['hero', 'featured_products', 'featured_categories', 'banners', 'testimonials', 'html_block'];
$missingTypes = array_diff($allTypes, array_keys($types));

if (empty($missingTypes)) {
    echo "\n✓ All 6 section types are present!\n";
} else {
    echo "\n✗ Missing section types: " . implode(', ', $missingTypes) . "\n";
}
