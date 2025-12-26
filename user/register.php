<?php
include 'config.php';
require __DIR__ . '/../vendor/autoload.php'; // Adjust path to vendor/autoload.php as needed

use Picqer\Barcode\BarcodeGeneratorPNG;

if (isset($_POST['submit'])) {
    $nama = $_POST['nama'];
    $no_kp = $_POST['no_kp'];
    $no_plat = strtoupper(trim($_POST['no_plat']));  // uppercase & trimmed
    $status = $_POST['status'];
    $kenderaan = $_POST['kenderaan'];

    // Validate license plate characters
    if (!preg_match('/^[A-Z0-9 ]+$/', $no_plat)) {
        die("Invalid license plate format.");
    }

    // Create barcode image
    $generator = new BarcodeGeneratorPNG();
    $barcodeData = $generator->getBarcode($no_plat, $generator::TYPE_CODE_128);

    // Ensure directory exists
    $barcodeDir = __DIR__ . '/../admin/image/barcodes/';
    if (!is_dir($barcodeDir)) {
        mkdir($barcodeDir, 0755, true);
    }

    // Save the barcode image as PNG file
    $filename = 'barcode_' . preg_replace('/\s+/', '_', $no_plat) . '_' . time() . '.png';
    $filePath = $barcodeDir . $filename;
    file_put_contents($filePath, $barcodeData);

    // URL path for HTML image src and DB storage
    $barcode_img = 'admin/image/barcodes/' . $filename;

    // Insert into database
    $sql = "INSERT INTO user (nama, no_kp, no_plat, status, kenderaan, barcode_img)
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("ssssss", $nama, $no_kp, $no_plat, $status, $kenderaan, $barcode_img);

    if ($stmt->execute()) {
        // Show success alert and display the barcode image
        echo "<script>alert('Your Account Successfully Created!!');window.location.href='search.php';</script>";
    } else {
        echo "<script>alert('Failed to Create an Account!'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KoVoS Portal - Registration</title>
    <style>
        * {
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background: #fff;
        }

        .main-container {
            flex: 1;
            display: flex;
            min-height: calc(100vh - 120px);
        }

        .video-section {
            flex: 1;
            display: flex;
            align-items: left;
            justify-content: left;
            position: relative;
            overflow: hidden;
        }

        .video-section video {
            position: absolute;
            top: 0;
            left: 0;
            width: 90%;
            height: 100%;
            object-fit: cover;
            pointer-events: none;
        }

        .registration-section {
            flex: 1;
            padding: 2rem;
            background: rgba(255, 255, 255, 0.95);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .registration-container {
            width: 100%;
            max-width: 400px;
            background: white;
            padding: 3rem 2.5rem;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .registration-title {
            font-size: 1.8rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 2rem;
            text-align: center;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            font-size: 0.9rem;
            font-weight: 500;
            color: #555;
            margin-bottom: 0.5rem;
            display: block;
        }

        .form-input,
        .form-select {
            width: 100%;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            background: white;
            transition: all 0.3s ease;
            outline: none;
            box-sizing: border-box;
        }

        .form-input:focus,
        .form-select:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
        }

        .form-input:hover,
        .form-select:hover {
            border-color: #007bff;
        }

        .form-select {
            cursor: pointer;
        }

        .submit-container {
            display: flex;
            justify-content: flex-end;
            margin-top: 2rem;
        }

        .submit-btn {
            background: linear-gradient(135deg, rgb(83, 93, 103), rgb(68, 72, 76));
            color: white;
            padding: 0.8rem 2rem;
            font-size: 1rem;
            font-weight: 500;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(217, 221, 225, 0.3);
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 2px 8px rgba(217, 221, 225, 0.3);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .footer {
            background: rgba(255, 255, 255, 0.95);
            color: #6c757d;
            text-align: center;
            padding: 1rem;
            font-size: 0.9rem;
            border-top: 1px solid rgba(0, 0, 0, 0.1);
        }

        /* Accessibility */
        .submit-btn:focus {
            outline: 3px solid rgba(0, 123, 255, 0.5);
            outline-offset: 2px;
        }

        .logo-button:focus {
            outline: 2px solid rgba(0, 123, 255, 0.5);
            outline-offset: 2px;
        }
    </style>
</head>

<body>

    <div class="main-container">
        <section class="video-section">
            <video autoplay muted loop playsinline>
                <source src="image/reg_vid1.mp4" type="video/mp4">
            </video>
        </section>

        <section class="registration-section">
            <div class="registration-container">
                <h2 class="registration-title">Register Account</h2>

                <form action="" method="POST" class="registration-form">
                    <div class="form-group">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" id="name" name="nama" class="form-input" required>
                    </div>

                    <div class="form-group">
                        <label for="ic-number" class="form-label">IC Number</label>
                        <input type="text" id="ic-number" name="no_kp" class="form-input" required>
                    </div>

                    <div class="form-group">
                        <label for="plate-number" class="form-label">License Plate</label>
                        <input type="text" id="plate-number" name="no_plat" class="form-input" required>
                    </div>

                    <div class="form-group">
                        <label for="status" class="form-label">Status</label>
                        <select id="status" name="status" class="form-select" required>
                            <option value="">Select Status</option>
                            <option value="parent">Parent</option>
                            <option value="student">Student</option>
                            <option value="staff">Staff</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="plate-number" class="form-label">Vehicle Type</label>
                        <select id="vehicle-type" name="kenderaan" class="form-select" required>
                            <option value="">Select Vehicle Type</option>
                            <option value="car">Car</option>
                            <option value="motorcycle">Motorcycle</option>
                            <option value="van">Van</option>
                            <option value="bus">Bus</option>
                        </select>
                    </div>

                    <div class="submit-container">
                        <button type="submit" name="submit" class="submit-btn">Submit</button>
                    </div>
                </form>
            </div>
        </section>
    </div>

    <footer class="footer">
        Copyright ©2025 KoVoS [406400-X]. All rights reserved.
    </footer>

</body>

</html>