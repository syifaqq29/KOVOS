<?php
// download_barcode.php
// This file handles secure barcode downloads

if (!isset($_GET['file']) || !isset($_GET['name'])) {
    die('Missing parameters');
}

$fileName = $_GET['file'];
$studentName = $_GET['name'];

// Define possible barcode directories (adjust these paths to match your setup)
$possiblePaths = [
    __DIR__ . '/' . $fileName,                    // File path is already complete from database
    __DIR__ . '/../' . $fileName,                 // Try parent directory
    'C:/xampp/htdocs/KoVoS/' . $fileName,        // Full absolute path
];

$filePath = null;

// Check which path exists
foreach ($possiblePaths as $path) {
    if (file_exists($path) && is_readable($path)) {
        $filePath = $path;
        break;
    }
}

// If file not found, show error
if (!$filePath) {
    http_response_code(404);
    die('Barcode file not found. Checked paths: ' . implode(', ', $possiblePaths));
}

// Get file info
$fileInfo = pathinfo($filePath);
$extension = strtolower($fileInfo['extension']);

// Validate file type (security measure)
$allowedExtensions = ['png', 'jpg', 'jpeg', 'gif', 'pdf'];
if (!in_array($extension, $allowedExtensions)) {
    http_response_code(403);
    die('File type not allowed');
}

// Set appropriate headers
$mimeTypes = [
    'png' => 'image/png',
    'jpg' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'gif' => 'image/gif',
    'pdf' => 'application/pdf'
];

$mimeType = $mimeTypes[$extension] ?? 'application/octet-stream';

// Clean the student name for filename
$cleanName = preg_replace('/[^a-zA-Z0-9]/', '_', $studentName);
$downloadName = 'Barcode_' . $cleanName . '.' . $extension;

// Send headers
header('Content-Type: ' . $mimeType);
header('Content-Disposition: attachment; filename="' . $downloadName . '"');
header('Content-Length: ' . filesize($filePath));
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');

// Output file
readfile($filePath);
exit;
?>