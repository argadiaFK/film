<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$settings = App\Models\Setting::whereIn('key', ['footer_text', 'donation_link', 'donation_text'])->get();
foreach ($settings as $s) {
    echo "KEY: " . $s->key . "\n";
    echo "VALUE: " . $s->value . "\n";
    echo "---\n";
}
