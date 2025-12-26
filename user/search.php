<?php
include("config.php");

if (isset($_GET['ajax_search']) && !empty($_GET['term'])) {
    $searchTerm = trim($_GET['term']);
    $suggestions = [];

    try {
        // Check if connection exists
        if (!$conn) {
            throw new Exception("Database connection failed");
        }

        $sql = "SELECT DISTINCT no_plat FROM user WHERE no_plat LIKE ? LIMIT 10";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            throw new Exception("SQL prepare failed: " . $conn->error);
        }

        $searchPattern = $searchTerm . '%';
        $stmt->bind_param("s", $searchPattern);

        if (!$stmt->execute()) {
            throw new Exception("SQL execute failed: " . $stmt->error);
        }

        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $suggestions[] = $row['no_plat'];
        }

        $stmt->close();
    } catch (Exception $e) {
        // Log error for debugging (in production, log to file)
        error_log("Search error: " . $e->getMessage());
    }

    header('Content-Type: application/json');
    echo json_encode($suggestions);
    exit;
}

// Handle AJAX update request
if (isset($_POST['ajax_update']) && $_POST['ajax_update'] === '1') {
    $response = ['success' => false, 'message' => ''];

    try {
        $currentPlate = trim($_POST['currentPlate']);
        $newPlate = trim($_POST['newPlate']);
        $icNumber = trim($_POST['icNumber']);

        // Validate inputs
        if (empty($currentPlate) || empty($newPlate) || empty($icNumber)) {
            throw new Exception("All fields are required");
        }

        // Check if IC number matches the current license plate owner
        $verifySQL = "SELECT no_kp FROM user WHERE no_plat = ? AND no_kp = ?";
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

        $verifyStmt->close();

        // Check if new plate number already exists
        $checkSQL = "SELECT no_plat FROM user WHERE no_plat = ? AND no_kp != ?";
        $checkStmt = $conn->prepare($checkSQL);

        if (!$checkStmt) {
            throw new Exception("Database error: " . $conn->error);
        }

        $checkStmt->bind_param("ss", $newPlate, $icNumber);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {
            throw new Exception("New license plate number already exists in the system");
        }

        $checkStmt->close();

        // Update the license plate
        $updateSQL = "UPDATE user SET no_plat = ? WHERE no_plat = ? AND no_kp = ?";
        $updateStmt = $conn->prepare($updateSQL);

        if (!$updateStmt) {
            throw new Exception("Database error: " . $conn->error);
        }

        $updateStmt->bind_param("sss", $newPlate, $currentPlate, $icNumber);

        if ($updateStmt->execute()) {
            if ($updateStmt->affected_rows > 0) {
                $response['success'] = true;
                $response['message'] = "License plate updated successfully from {$currentPlate} to {$newPlate}";

                // Log the update for audit trail
                error_log("License plate updated: {$currentPlate} -> {$newPlate} for IC: {$icNumber}");
            } else {
                throw new Exception("No records were updated. Please verify your information.");
            }
        } else {
            throw new Exception("Update failed: " . $updateStmt->error);
        }

        $updateStmt->close();

    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
        error_log("Update error: " . $e->getMessage());
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Initialize search variables
$searchResults = [];
$searchTerm = '';
$totalResults = 0;
$currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$resultsPerPage = 10;
$offset = ($currentPage - 1) * $resultsPerPage;

// Handle POST search (from search form)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simple_search'])) {
    $searchTerm = trim($_POST['searchTerm']);

    if (!empty($searchTerm) && $conn) {
        try {
            // TODO: Replace 'user' with your actual table name if different
            $sql = "SELECT * FROM user WHERE no_plat LIKE ? LIMIT ?, ?";
            $stmt = $conn->prepare($sql);

            if ($stmt) {
                $searchPattern = '%' . $searchTerm . '%';
                $stmt->bind_param("sii", $searchPattern, $offset, $resultsPerPage);
                $stmt->execute();
                $searchResults = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
                $stmt->close();

                // Count total results
                $countSql = "SELECT COUNT(*) as total FROM user WHERE no_plat LIKE ?";
                $countStmt = $conn->prepare($countSql);
                if ($countStmt) {
                    $countStmt->bind_param("s", $searchPattern);
                    $countStmt->execute();
                    $totalResults = $countStmt->get_result()->fetch_assoc()['total'];
                    $countStmt->close();
                }
            }
        } catch (Exception $e) {
            error_log("Search error: " . $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <title>KoVoS Portal - Search</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            position: relative;
            overflow-x: hidden;
        }

        /* Geometric background elements */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 60%;
            height: 100%;
            background: linear-gradient(135deg, rgba(108, 117, 125, 0.1) 0%, rgba(173, 181, 189, 0.05) 100%);
            clip-path: polygon(30% 0%, 100% 0%, 100% 100%, 0% 100%);
            z-index: -1;
        }

        body::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50%;
            height: 40%;
            background: linear-gradient(45deg, rgba(108, 117, 125, 0.08) 0%, transparent 70%);
            clip-path: polygon(0% 100%, 100% 100%, 0% 0%);
            z-index: -1;
        }

        .header {
            padding: 2rem 3rem;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-bottom: 4px solid rgba(0, 0, 0, 0.1);
        }

        .Logo {
            display: flex;
            align-items: center;
            gap: 80px;
            justify-content: center;
        }

        .Logo img {
            height: 90px;
            object-fit: contain;
            transition: transform 0.3s ease;
        }

        /* Make KoVoS and KVS logos 1.2x bigger */
        .logo-kovos,
        .logo-kvs {
            transform: scale(1.6);
        }

        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 4rem 2rem;
            position: relative;
        }

        .portal-title {
            font-size: 3.5rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 4rem;
            letter-spacing: -1px;
            text-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            animation: fadeInUp 1s ease-out;
            border-bottom: 3px solid rgba(22, 28, 34, 0.4);
            padding-bottom: 1rem;
            display: inline-block;
        }

        .search-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            padding: 3rem 4rem;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            max-width: 500px;
            width: 100%;
            animation: fadeInUp 1s ease-out 0.2s both;
        }

        .search-group {
            position: relative;
            margin-bottom: 2rem;
        }

        .search-input {
            width: 100%;
            padding: 1rem 3rem 1rem 1.5rem;
            font-size: 1rem;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.9);
            transition: all 0.3s ease;
            outline: none;
        }

        .search-input:focus {
            border-color: rgba(22, 28, 34, 0.4);
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
            background: white;
        }

        .search-input:hover {
            border-color: #6c757d;
        }

        .search-btn {
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
            background: linear-gradient(135deg, rgb(83, 93, 103), rgb(68, 72, 76));
            color: white;
            padding: 0.7rem 1rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(217, 221, 225, 0.3);
        }

        .search-btn:hover {
            transform: translateY(-50%) scale(1.05);
            box-shadow: 0 4px 12px rgba(217, 221, 225, 0.3);
        }

        .search-btn i {
            font-size: 18px;
        }

        /* Auto-complete dropdown styles */
        .autocomplete-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #e9ecef;
            border-top: none;
            border-radius: 0 0 10px 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
        }

        .autocomplete-dropdown.show {
            display: block;
        }

        .autocomplete-item {
            padding: 0.75rem 1.5rem;
            cursor: pointer;
            border-bottom: 1px solid #f8f9fa;
            transition: background-color 0.2s ease;
            font-size: 0.95rem;
            position: relative;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .autocomplete-item:hover,
        .autocomplete-item.highlighted {
            background-color: #f8f9fa;
        }

        .autocomplete-item:last-child {
            border-bottom: none;
        }

        .no-results {
            padding: 0.75rem 1.5rem;
            color: #6c757d;
            font-style: italic;
            text-align: center;
        }

        /* Update button styles */
        .update-button {
            background: linear-gradient(135deg, rgb(83, 93, 103), rgb(68, 72, 76));
            color: white;
            border: none;
            padding: 0.4rem 0.8rem;
            border-radius: 15px;
            font-size: 0.8rem;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 6px rgba(22, 28, 34, 0.4);
            white-space: nowrap;
        }

        .update-button:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 8px rgba(22, 28, 34, 0.4);
        }

        .update-button:active {
            transform: scale(0.95);
        }

        /* Modal styles for update form */
        .modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
        }

        .modal-content {
            background-color: white;
            margin: 8% auto;
            padding: 2rem;
            border-radius: 15px;
            width: 90%;
            max-width: 550px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            animation: modalFadeIn 0.3s ease-out;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e9ecef;
        }

        .modal-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #2c3e50;
        }

        .close-btn {
            background: none;
            border: none;
            font-size: 1.8rem;
            cursor: pointer;
            color: #6c757d;
            transition: color 0.3s ease;
        }

        .close-btn:hover {
            color: #dc3545;
        }

        .modal-form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .form-label {
            font-weight: 500;
            color: #495057;
        }

        .form-input {
            padding: 0.8rem;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: rgb(49, 54, 59);
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
        }

        .form-input[readonly] {
            background-color: #f8f9fa;
            color: #6c757d;
        }

        .security-notice {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 1rem;
            border-radius: 4px;
            margin: 1rem 0;
            font-size: 0.9rem;
            color: #1976d2;
        }

        .security-notice i {
            margin-right: 0.5rem;
        }

        .modal-buttons {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            margin-top: 1.5rem;
        }

        .btn-cancel {
            padding: 0.8rem 1.5rem;
            border: 2px solid #6c757d;
            background: transparent;
            color: #6c757d;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-cancel:hover {
            background: #6c757d;
            color: white;
        }

        .btn-update {
            padding: 0.8rem 1.5rem;
            border: none;
            background: linear-gradient(135deg, rgb(83, 93, 103), rgb(68, 72, 76));
            color: white;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-update:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(45, 50, 55, 0.4);
        }

        .btn-update:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .loading-spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid #007bff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-right: 0.5rem;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        @keyframes modalFadeIn {
            from {
                opacity: 0;
                transform: translateY(-50px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .register-link-container {
            margin-top: 2rem;
            display: flex;
            gap: 4rem;
            align-items: center;
        }

        .back-btn,
        .no-account-link {
            color: rgb(29, 34, 39);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
            cursor: pointer;
        }

        .back-btn:hover,
        .no-account-link:hover {
            color: rgb(102, 110, 118);
        }

        .footer {
            background: rgba(52, 58, 64, 0.95);
            color: rgba(255, 255, 255, 0.8);
            text-align: center;
            padding: 1.5rem;
            font-size: 0.9rem;
            backdrop-filter: blur(10px);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Logo placeholder styles */
        .logo-placeholder {
            width: 120px;
            height: 60px;
            background: linear-gradient(135deg, #e9ecef, #dee2e6);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
            font-size: 0.8rem;
            text-align: center;
            font-weight: 500;
        }

        /* Success/Error message styles */
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin: 1rem 0;
            font-weight: 500;
        }

        .alert-success {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }

        .alert-error {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
    </style>
</head>

<body>
    <header class="header">
        <div class="Logo">
            <img src="image/Logo_BPLTV.png" alt="Logo BPLTV"
                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
            <div class="logo-placeholder" style="display: none;">BPLTV Logo</div>

            <img src="image/Logo_KoVoS.png" alt="Logo KoVoS" class="logo-kovos"
                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
            <div class="logo-placeholder" style="display: none;">KoVoS Logo</div>

            <img src="image/Logo_KVS.png" alt="Kolej Vokasional" class="logo-kvs"
                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
            <div class="logo-placeholder" style="display: none;">KVS Logo</div>
        </div>
    </header>

    <main class="main-content">
        <h1 class="portal-title">KoVoS Portal</h1>

        <div class="search-container">
            <form class="search-form" method="POST">
                <div class="search-group">
                    <input type="text" id="search" name="searchTerm" class="search-input"
                        placeholder="Search number plat ......" autocomplete="off" required>
                    <button type="submit" name="simple_search" class="search-btn">
                        <i class="fas fa-search"></i>
                    </button>

                    <!-- Auto-complete dropdown -->
                    <div id="autocomplete-dropdown" class="autocomplete-dropdown"></div>
                </div>

                <div class="register-link-container">
                    <a href="http://localhost/KoVoS/" class="back-btn">
                        <i class="fas fa-arrow-left"></i>
                        Go Back to Homepage
                    </a>
                    <a href="register.php" class="no-account-link">Not yet register?</a>
                </div>
            </form>
        </div>
    </main>

    <!-- Update Modal -->
    <div id="updateModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title"><i class="fas fa-edit"></i> Update License Plate</h3>
                <button class="close-btn" onclick="closeUpdateModal()">&times;</button>
            </div>

            <div class="security-notice">
                <i class="fas fa-shield-alt"></i>
                <strong>Security Verification Required:</strong> You must enter your IC number to verify ownership
                before updating your license plate.
            </div>

            <form action="update_plat.php" class="modal-form" id="updateForm">
                <div class="form-group">
                    <label class="form-label">Current License Plate:</label>
                    <input type="text" id="currentPlate" class="form-input" readonly>
                </div>

                <div class="form-group">
                    <label class="form-label">IC Number (for verification):</label>
                    <input type="text" id="icNumber" class="form-input"
                        placeholder="Enter your IC number (e.g., 123456121234)">
                </div>

                <div class="form-group">
                    <label class="form-label">New License Plate:</label>
                    <input type="text" id="newPlate" class="form-input"
                        placeholder="Enter new license plate (e.g., ABC 1234)" required>
                </div>

                <div id="alertContainer"></div>

                <div class="modal-buttons">
                    <button type="button" class="btn-cancel" onclick="closeUpdateModal()">Cancel</button>
                    <button type="submit" class="btn-update" id="updateBtn">
                        <div class="loading-spinner" id="loadingSpinner"></div>
                        Update License Plate
                    </button>
                </div>
            </form>
        </div>
    </div>

    <footer class="footer">
        Copyright ©2025 KoVoS [406400-X]. All rights reserved.
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('search');
            const dropdown = document.getElementById('autocomplete-dropdown');
            const updateModal = document.getElementById('updateModal');
            const updateForm = document.getElementById('updateForm');
            const currentPlateInput = document.getElementById('currentPlate');
            const newPlateInput = document.getElementById('newPlate');
            const icNumberInput = document.getElementById('icNumber');
            const updateBtn = document.getElementById('updateBtn');
            const loadingSpinner = document.getElementById('loadingSpinner');
            const alertContainer = document.getElementById('alertContainer');
            let currentFocus = -1;
            let searchTimeout;

            // Function to show alert messages
            function showAlert(message, type) {
                const alertClass = type === 'success' ? 'alert-success' : 'alert-error';
                alertContainer.innerHTML = `<div class="alert ${alertClass}">${message}</div>`;

                // Auto-hide success messages after 5 seconds
                if (type === 'success') {
                    setTimeout(() => {
                        alertContainer.innerHTML = '';
                    }, 5000);
                }
            }

            // Function to clear alerts
            function clearAlerts() {
                alertContainer.innerHTML = '';
            }

            // Function to perform AJAX search
            function performSearch(term) {
                if (term.length === 0) {
                    hideDropdown();
                    return;
                }

                // Make AJAX request
                fetch(`?ajax_search=1&term=${encodeURIComponent(term)}`)
                    .then(response => response.json())
                    .then(data => {
                        showSuggestions(data);
                    })
                    .catch(error => {
                        console.error('Search error:', error);
                        hideDropdown();
                    });
            }

            // Function to show suggestions
            function showSuggestions(suggestions) {
                dropdown.innerHTML = '';

                if (suggestions.length === 0) {
                    dropdown.innerHTML = '<div class="no-results">No matching license plates found</div>';
                    dropdown.classList.add('show');
                    return;
                }

                suggestions.forEach((suggestion, index) => {
                    const item = document.createElement('div');
                    item.className = 'autocomplete-item';

                    // Create the plate number span
                    const plateSpan = document.createElement('span');
                    plateSpan.textContent = suggestion;
                    plateSpan.addEventListener('click', function () {
                        selectSuggestion(suggestion);
                    });

                    // Create the update button
                    const updateBtn = document.createElement('button');
                    updateBtn.className = 'update-button';
                    updateBtn.innerHTML = '<i class="fas fa-edit"></i> Update';
                    updateBtn.type = 'button';
                    updateBtn.addEventListener('click', function (e) {
                        e.stopPropagation();
                        showUpdateModal(suggestion);
                    });

                    item.appendChild(plateSpan);
                    item.appendChild(updateBtn);
                    dropdown.appendChild(item);
                });

                dropdown.classList.add('show');
                currentFocus = -1;
            }

            // Function to hide dropdown
            function hideDropdown() {
                dropdown.classList.remove('show');
                currentFocus = -1;
            }

            // Function to select a suggestion
            function selectSuggestion(value) {
                searchInput.value = value;
                hideDropdown();
                searchInput.focus();
            }

            // Function to show update modal
            function showUpdateModal(plateNumber) {
                currentPlateInput.value = plateNumber;
                newPlateInput.value = '';
                icNumberInput.value = '';
                clearAlerts();
                updateModal.style.display = 'block';
                hideDropdown();

                // Focus on IC number input
                setTimeout(() => {
                    icNumberInput.focus();
                }, 300);
            }

            // Function to close update modal
            window.closeUpdateModal = function () {
                updateModal.style.display = 'none';
                clearAlerts();
            }

            // Handle update form submission
            updateForm.addEventListener('submit', function (e) {
                e.preventDefault();
                clearAlerts();

                const currentPlate = currentPlateInput.value.trim();
                const newPlate = newPlateInput.value.trim();
                const icNumber = icNumberInput.value.trim();

                // Validate inputs
                if (!currentPlate || !newPlate || !icNumber) {
                    showAlert('All fields are required', 'error');
                    return;
                }

                if (currentPlate === newPlate) {
                    showAlert('New license plate must be different from current one', 'error');
                    return;
                }

                // Show loading state
                updateBtn.disabled = true;
                loadingSpinner.style.display = 'inline-block';

                // Create form data
                const formData = new FormData();
                formData.append('ajax_update', '1');
                formData.append('currentPlate', currentPlate);
                formData.append('newPlate', newPlate);
                formData.append('icNumber', icNumber);

                // Send AJAX request to update_plat.php
                fetch('update_plat.php', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showAlert(data.message, 'success');

                            // Clear form after successful update
                            setTimeout(() => {
                                closeUpdateModal();

                                // Refresh the search if there was a previous search
                                if (searchInput.value.trim()) {
                                    performSearch(searchInput.value.trim());
                                }
                            }, 2000);
                        } else {
                            showAlert(data.message, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Update error:', error);
                        showAlert('Network error occurred. Please try again.', 'error');
                    })
                    .finally(() => {
                        // Hide loading state
                        updateBtn.disabled = false;
                        loadingSpinner.style.display = 'none';
                    });
            });

            // Close modal when clicking outside
            updateModal.addEventListener('click', function (e) {
                if (e.target === updateModal) {
                    closeUpdateModal();
                }
            });

            // Handle keyboard navigation
            function handleKeyNavigation(e) {
                const items = dropdown.querySelectorAll('.autocomplete-item');

                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    currentFocus++;
                    if (currentFocus >= items.length) currentFocus = 0;
                    addActive(items);
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    currentFocus--;
                    if (currentFocus < 0) currentFocus = items.length - 1;
                    addActive(items);
                } else if (e.key === 'Enter') {
                    e.preventDefault();
                    if (currentFocus > -1 && items[currentFocus]) {
                        const plateText = items[currentFocus].querySelector('span').textContent;
                        selectSuggestion(plateText);
                    }
                } else if (e.key === 'Escape') {
                    hideDropdown();
                }
            }

            // Function to add active class to current item
            function addActive(items) {
                items.forEach(item => item.classList.remove('highlighted'));
                if (items[currentFocus]) {
                    items[currentFocus].classList.add('highlighted');
                }
            }

            // Event listeners
            searchInput.addEventListener('input', function () {
                const term = this.value.trim();

                // Clear previous timeout
                clearTimeout(searchTimeout);

                // Set a small delay to avoid too many requests
                searchTimeout = setTimeout(() => {
                    performSearch(term);
                }, 300);
            });

            searchInput.addEventListener('keydown', handleKeyNavigation);

            searchInput.addEventListener('focus', function () {
                if (this.value.trim().length > 0) {
                    performSearch(this.value.trim());
                }
            });

            // Hide dropdown when clicking outside
            document.addEventListener('click', function (e) {
                if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
                    hideDropdown();
                }
            });

            // Prevent form submission when selecting from dropdown
            dropdown.addEventListener('mousedown', function (e) {
                e.preventDefault();
            });

            // Close modal with Escape key
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && updateModal.style.display === 'block') {
                    closeUpdateModal();
                }
            });
        });
    </script>
</body>

</html>