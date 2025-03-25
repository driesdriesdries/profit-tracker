<?php
/**
 * Context Generator Script (Plain Text Version)
 * 
 * Scans for specific PHP files, including partials, strips PHP tags,
 * and writes their combined contents into context.txt.
 */

$targetFiles = [
    'single-transactions.php',
    'profit-tracker.php',
    'archive-transactions.php'
];

// Dynamically pull all .php files from the partials folder
$partialsPath = 'partials/';
if (is_dir($partialsPath)) {
    $partialFiles = glob($partialsPath . '*.php');
    foreach ($partialFiles as $partial) {
        $targetFiles[] = $partial;
    }
}

$outputFile = 'context.txt';
$combinedContent = "Auto-generated Context File (Plain Text)\n";
$combinedContent .= "Contains: " . implode(', ', $targetFiles) . "\n\n";

foreach ($targetFiles as $file) {
    if (file_exists($file)) {
        $fileContent = file_get_contents($file);

        // Strip ALL PHP opening and closing tags
        $fileContent = preg_replace('/<\?php\s*/', '', $fileContent);
        $fileContent = preg_replace('/\?>\s*/', '', $fileContent);

        $combinedContent .= "========== Begin {$file} ==========\n";
        $combinedContent .= trim($fileContent) . "\n";
        $combinedContent .= "========== End {$file} ==========\n\n";
    } else {
        $combinedContent .= "========== {$file} not found ==========\n\n";
    }
}

if (file_put_contents($outputFile, $combinedContent) !== false) {
    echo "✅ context.txt generated successfully.\n";
} else {
    echo "❌ Failed to generate context.txt.\n";
}