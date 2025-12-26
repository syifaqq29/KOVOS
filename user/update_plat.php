<?php
// Turn off error display for AJAX requests
ini_set('display_errors', 0);
error_reporting(0);

include "config.php";

// Load Composer autoload and import class at the top (outside the if-block)
require_once 'vendor/autoload.php';
use Picqer\Barcode\BarcodeGeneratorPNG;

// Handle AJAX update request
if (isset($_POST['ajax_update']) && $_POST['ajax_update'] === '1') {
    $response = ['success' => false, 'message' => ''];

    try {
        // Clean any output buffer
        if (ob_get_level()) {
            ob_clean();
        }

        $currentPlate = trim($_POST['currentPlate']);
        $newPlate = trim($_POST['newPlate']);
        $icNumber = trim($_POST['icNumber']);

        // Validate inputs
        if (empty($currentPlate) || empty($newPlate) || empty($icNumber)) {
            throw new Exception("All fields are required");
        }

        // Check database connection
        if (!$conn) {
            throw new Exception("Database connection failed");
        }

        // Check if IC number matches the current license plate owner
        $verifySQL = "SELECT no_kp, barcode_img FROM user WHERE no_plat = ? AND no_kp = ?";
        $verifyStmt = $conn->prepare($verifySQL);
        if (!$verifyStmt) {
            throw new Exception("Database error: " . $conn->error);
        }
        $verifyStmt->bind_param("ss", $currentPlate, $icNumber);
        $verifyStmt->execute();
        $verifyResult = $verifyStmt->get_result();
        if ($verifyResult->num_rows === 0) {
            throw new Exception("IC number does not match the license plate owner. Access denied.");
        }

        // Get the old barcode path for deletion later
        $oldBarcodeData = $verifyResult->fetch_assoc();
        $oldBarcodePath = $oldBarcodeData['barcode_img'];

        // DEBUG: Log what we got from database
        error_log("DEBUG: Retrieved old barcode path from DB: '" . $oldBarcodePath . "'");
        error_log("DEBUG: Current working directory: " . getcwd());

        $verifyStmt->close();

        // Check if new plate number already exists for other users
        $checkSQL = "SELECT no_plat FROM user WHERE no_plat = ? AND no_kp != ?";
        $checkStmt = $conn->prepare($checkSQL);
        if (!$checkStmt) {
            throw new Exception("Database error: " . $conn->error);
        }
        $checkStmt->bind_param("ss", $newPlate, $icNumber);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        if ($checkResult->num_rows > 0) {
            throw new Exception("New license plate number already exists in the system.");
        }
        $checkStmt->close();

        // Generate new barcode
        $generator = new BarcodeGeneratorPNG();
        $barcodeData = $generator->getBarcode($newPlate, $generator::TYPE_CODE_128);

        // FIXED: Use absolute path to ensure we always reach the correct directory
        // Get the document root and build absolute path
        $documentRoot = $_SERVER['DOCUMENT_ROOT'];
        $barcodeDir = $documentRoot . '/KoVoS/admin/image/barcodes/';

        error_log("DEBUG: Document root: " . $documentRoot);
        error_log("DEBUG: Barcode directory: " . $barcodeDir);

        if (!is_dir($barcodeDir)) {
            if (!mkdir($barcodeDir, 0755, true)) {
                throw new Exception("Failed to create barcode directory");
            }
        }

        // Generate new barcode filename with timestamp
        $newBarcodeFilename = 'barcode_' . preg_replace('/\s+/', '_', $newPlate) . '_' . time() . '.png';
        $newBarcodeFilePath = $barcodeDir . $newBarcodeFilename;

        // Save new barcode file
        if (file_put_contents($newBarcodeFilePath, $barcodeData) === false) {
            throw new Exception("Failed to save new barcode file");
        }

        // FIXED: Database path should still reference admin/image/barcodes/ for consistency
        $newBarcodeDbPath = 'admin/image/barcodes/' . $newBarcodeFilename;

        // Update both license plate and barcode_img in database
        $updateSQL = "UPDATE user SET no_plat = ?, barcode_img = ? WHERE no_plat = ? AND no_kp = ?";
        $updateStmt = $conn->prepare($updateSQL);
        if (!$updateStmt) {
            throw new Exception("Database error: " . $conn->error);
        }

        $updateStmt->bind_param("ssss", $newPlate, $newBarcodeDbPath, $currentPlate, $icNumber);

        if ($updateStmt->execute()) {
            if ($updateStmt->affected_rows > 0) {
                // Database update successful, now delete old barcode file
                if (!empty($oldBarcodePath) && trim($oldBarcodePath) !== '') {
                    error_log("DEBUG: Starting old file deletion process");
                    error_log("DEBUG: Old barcode path from DB: '" . $oldBarcodePath . "'");

                    // Build absolute path for old file
                    $documentRoot = $_SERVER['DOCUMENT_ROOT'];
                    $absoluteOldPath = $documentRoot . '/KoVoS/' . $oldBarcodePath;

                    error_log("DEBUG: Absolute old file path: '" . $absoluteOldPath . "'");

                    if (file_exists($absoluteOldPath)) {
                        if (is_writable($absoluteOldPath)) {
                            if (unlink($absoluteOldPath)) {
                                error_log("SUCCESS: Old barcode file deleted: " . $absoluteOldPath);
                            } else {
                                error_log("ERROR: Failed to delete old barcode file: " . $absoluteOldPath);
                                error_log("ERROR: Last error: " . print_r(error_get_last(), true));
                            }
                        } else {
                            error_log("ERROR: Old barcode file is not writable: " . $absoluteOldPath);
                            $perms = fileperms($absoluteOldPath);
                            error_log("DEBUG: File permissions: " . decoct($perms & 0777));
                        }
                    } else {
                        error_log("WARNING: Old barcode file does not exist: " . $absoluteOldPath);

                        // List files in the barcode directory for debugging
                        $barcodeDir = $documentRoot . '/KoVoS/admin/image/barcodes/';
                        if (is_dir($barcodeDir)) {
                            error_log("DEBUG: Files in barcode directory:");
                            $files = scandir($barcodeDir);
                            foreach ($files as $file) {
                                if ($file !== '.' && $file !== '..') {
                                    error_log("DEBUG: - " . $file);
                                }
                            }
                        }
                    }
                } else {
                    error_log("DEBUG: No old barcode path to delete (empty, null, or whitespace only)");
                }

                $response['success'] = true;
                $response['message'] = "License plate updated successfully from {$currentPlate} to {$newPlate}. New barcode generated.";

                // Log the successful update
                error_log("License plate updated: {$currentPlate} -> {$newPlate} for IC: {$icNumber}");
                error_log("New barcode generated and saved to: $newBarcodeFilePath");
                error_log("Database barcode_img updated to: $newBarcodeDbPath");

            } else {
                // If database update failed, delete the newly created barcode file
                if (file_exists($newBarcodeFilePath)) {
                    unlink($newBarcodeFilePath);
                }
                throw new Exception("No records were updated. Please verify your information.");
            }
        } else {
            // If database update failed, delete the newly created barcode file
            if (file_exists($newBarcodeFilePath)) {
                unlink($newBarcodeFilePath);
            }
            throw new Exception("Update failed: " . $updateStmt->error);
        }
        $updateStmt->close();

    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
        error_log("Update error: " . $e->getMessage());
    } catch (Error $e) {
        $response['message'] = "System error occurred. Please try again.";
        error_log("Fatal error: " . $e->getMessage());
    }

    // Ensure clean output
    if (ob_get_level()) {
        ob_clean();
    }

    header('Content-Type: application/json');
    header('Cache-Control: no-cache, must-revalidate');
    echo json_encode($response);
    exit;
}
?>