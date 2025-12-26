<?php
include "config.php";

// Error handling
error_reporting(E_ALL);
ini_set('display_errors', 0); // Set to 1 for debugging

try {
    // Include database connection
    if (!isset($conn)) {
        include_once 'config.php';
    }

    // Check database connection
    if (!$conn) {
        throw new Exception("Database connection failed");
    }

    // Initialize variables
    $success_message = '';
    $error_message = '';

    // Handle form submissions
    if ($_POST) {
        if (isset($_POST['action']) && $_POST['action'] == 'add') {
            // Add new visitor with prepared statements
            $visitor_name = mysqli_real_escape_string($conn, $_POST['visitor_name']);
            $date_of_visit = mysqli_real_escape_string($conn, $_POST['date_of_visit']);
            $time_in = mysqli_real_escape_string($conn, $_POST['time_in']);
            $time_out = mysqli_real_escape_string($conn, $_POST['time_out']);
            $purpose_of_visit = mysqli_real_escape_string($conn, $_POST['purpose_of_visit']);
            $person_to_meet = mysqli_real_escape_string($conn, $_POST['person_to_meet']);
            $remarks = mysqli_real_escape_string($conn, $_POST['remarks']);

            $query = "INSERT INTO visitor (visitor_name, date_of_visit, time_in, time_out, purpose_of_visit, person_to_meet, remarks) VALUES ('$visitor_name', '$date_of_visit', '$time_in', '$time_out', '$purpose_of_visit', '$person_to_meet', '$remarks')";

            if (mysqli_query($conn, $query)) {
            } else {
                $error_message = "Error adding visitor: " . mysqli_error($conn);
            }
        }

        if (isset($_POST['action']) && $_POST['action'] == 'edit') {
            // Edit existing visitor with proper escaping
            $id = mysqli_real_escape_string($conn, $_POST['id']);
            $visitor_name = mysqli_real_escape_string($conn, $_POST['visitor_name']);
            $date_of_visit = mysqli_real_escape_string($conn, $_POST['date_of_visit']);
            $time_in = mysqli_real_escape_string($conn, $_POST['time_in']);
            $time_out = mysqli_real_escape_string($conn, $_POST['time_out']);
            $purpose_of_visit = mysqli_real_escape_string($conn, $_POST['purpose_of_visit']);
            $person_to_meet = mysqli_real_escape_string($conn, $_POST['person_to_meet']);
            $remarks = mysqli_real_escape_string($conn, $_POST['remarks']);

            $update_query = "UPDATE visitor SET 
                           visitor_name='$visitor_name', 
                           date_of_visit='$date_of_visit', 
                           time_in='$time_in', 
                           time_out='$time_out', 
                           purpose_of_visit='$purpose_of_visit', 
                           person_to_meet='$person_to_meet',
                           remarks='$remarks' 
                           WHERE id='$id'";
        }
    }

    // Handle delete via GET
    if (isset($_GET['delete_id'])) {
        $id = mysqli_real_escape_string($conn, $_GET['delete_id']);
        if (mysqli_query($conn, "DELETE FROM visitor WHERE id='$id'")) {
        } else {
            $error_message = "Error deleting visitor: " . mysqli_error($conn);
        }
    }

    // Prepare and execute query to get all visitors
    $query = "SELECT id, visitor_name, date_of_visit, time_in, time_out, purpose_of_visit, person_to_meet, remarks FROM visitor ORDER BY date_of_visit DESC, time_in DESC";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        throw new Exception("Query failed: " . mysqli_error($conn));
    }

    // Add modern CSS styles
    echo '<style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        .visitor-management-container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .controls {
            background: #f8f9fa;
            padding: 1.5rem 2rem;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
        }
        
        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 25px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
        }
        
        .btn-primary {
            background: color:rgba(108, 117, 125, 0.3);
            color: white;
            box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
        }
        
        .btn-success {
            background: color:rgba(108, 117, 125, 0.3);
            color: white;
        }
        
        .btn-warning {
    background-color: #ffc107;   /* Bright Warning Yellow */
    color: #212529;
    border: none;
    transition: 0.3s ease;
}

.btn-warning:hover {
    background-color: #e0a800;   /* Darker on hover */
}
        
        .btn-secondary {
            background: linear-gradient(135deg, #6c757d, #5a6268);
            color: white;
            box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3);
        }
        
        .btn:hover {
            transform: translateY(-2px);
            text-decoration: none;
        }
        
        .btn-primary:hover { box-shadow: 0 6px 20px rgba(0, 123, 255, 0.4); color: white; }
        .btn-success:hover { box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4); color: white; }
        .btn-warning:hover { box-shadow: 0 6px 20px rgba(255, 193, 7, 0.4); color: #212529; }
        .btn-danger:hover { box-shadow: 0 6px 20px rgba(220, 53, 69, 0.4); color: white; }
        .btn-secondary:hover { box-shadow: 0 6px 20px rgba(108, 117, 125, 0.4); color: white; }
        
        .table-container {
            padding: 2rem;
            overflow-x: auto;
        }
        
        .visitor-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        }
        
        .visitor-table thead {
            background: linear-gradient(135deg, rgb(83, 93, 103), rgb(68, 72, 76));
        }
        
        .visitor-table thead th {
            color: white;
            font-weight: 600;
            padding: 1.5rem 1rem;
            text-align: left;
            border: none;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.85rem;
        }
        
        .visitor-table tbody tr {
            border: none;
            transition: all 0.3s ease;
        }
        
        .visitor-table tbody tr:hover {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .visitor-table tbody td {
            padding: 1rem;
            border: none;
            vertical-align: middle;
            font-size: 0.9rem;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }
        
        .visitor-table tbody tr:last-child td {
            border-bottom: none;
        }
        
        .visitor-id {
            background: linear-gradient(135deg, rgb(83, 93, 103), rgb(68, 72, 76));
            color: white;
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .visitor-name {
            font-weight: 600;
            color: #2c3e50;
        }
        
        .date-badge {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            padding: 0.4rem 0.8rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
            display: inline-block;
        }
        
        .time-badge {
            background: linear-gradient(135deg, #27ae60, #219a52);
            color: white;
            padding: 0.3rem 0.6rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            margin: 0.1rem;
            display: inline-block;
        }
        
        .time-out {
            background: linear-gradient(135deg, #e67e22, #d35400);
        }
        
        .purpose-badge {
            background: linear-gradient(135deg, #9b59b6, #8e44ad);
            color: white;
            padding: 0.4rem 0.8rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .person-to-meet {
            font-weight: 600;
            color: #34495e;
        }
        
        .remarks {
            font-style: italic;
            color: #7f8c8d;
            font-size: 0.85rem;
        }
        
        .action-buttons {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        
        .action-btn {
            padding: 0.4rem 0.8rem;
            border-radius: 15px;
            text-decoration: none;
            font-size: 0.75rem;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        
        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 0;
            border-radius: 15px;
            width: 90%;
            max-width: 600px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        
        .modal-header {
            background: linear-gradient(135deg, rgb(83, 93, 103), rgb(68, 72, 76));
            color: white;
            padding: 1.5rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .modal-header h3 {
            margin: 0;
            font-size: 1.5rem;
        }
        
        .close {
            color: white;
            font-size: 2rem;
            font-weight: bold;
            cursor: pointer;
            background: none;
            border: none;
        }
        
        .close:hover {
            opacity: 0.7;
        }
        
        .modal-body {
            padding: 2rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #2c3e50;
        }
        
        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }
        
        .form-control:focus {
            outline: none;
            border-color: rgb(83, 93, 103);
            box-shadow: 0 0 0 3px rgba(83, 93, 103, 0.1);
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        
        .no-data {
            text-align: center;
            padding: 3rem;
            color: #6c757d;
            font-size: 1.1rem;
        }
        
        .no-data-icon {
            font-size: 4rem;
            opacity: 0.3;
            margin-bottom: 1rem;
        }
        
        @media print {
            body * {
                visibility: hidden;
            }
            .visitor-table, .visitor-table * {
                visibility: visible;
            }
            .visitor-table {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
            .controls, .action-buttons, .modal {
                display: none !important;
            }
        }
        
        @media (max-width: 768px) {
            .controls {
                flex-direction: column;
                align-items: stretch;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .visitor-table {
                font-size: 0.8rem;
            }
            
            .visitor-table thead th,
            .visitor-table tbody td {
                padding: 0.5rem;
            }
        }
    </style>';

    // JavaScript for modals and functionality
    echo '<script>
        // Track if form is being submitted to prevent double submission
        let isSubmitting = false;
        
        function openAddModal() {
            document.getElementById("addModal").style.display = "block";
            document.getElementById("addForm").reset();
        }
        
        function openEditModal(id, name, date, timeIn, timeOut, purpose, person, remarks) {
            document.getElementById("editModal").style.display = "block";
            document.getElementById("editId").value = id;
            document.getElementById("editVisitorName").value = name;
            document.getElementById("editDateOfVisit").value = date;
            document.getElementById("editTimeIn").value = timeIn;
            document.getElementById("editTimeOut").value = timeOut;
            document.getElementById("editPurposeOfVisit").value = purpose;
            document.getElementById("editPersonToMeet").value = person;
            document.getElementById("editRemarks").value = remarks;
        }
        
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = "none";
        }
        
        function deleteVisitor(id, name) {
            if (isSubmitting) return; // Prevent double submission
            
            if (confirm("Are you sure you want to delete the record for " + name + "? This action cannot be undone.")) {
                isSubmitting = true;
                window.location.href = "?" + "delete_id=" + id;
            }
        }
        
        function printTable() {
            window.print();
        }
        
        // Prevent form double submission
        document.addEventListener("DOMContentLoaded", function() {
            const forms = document.querySelectorAll("form");
            forms.forEach(function(form) {
                form.addEventListener("submit", function(e) {
                    if (isSubmitting) {
                        e.preventDefault();
                        return false;
                    }
                    isSubmitting = true;
                    
                    // Re-enable submission after 3 seconds as a safety measure
                    setTimeout(function() {
                        isSubmitting = false;
                    }, 3000);
                });
            });
        });
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            var addModal = document.getElementById("addModal");
            var editModal = document.getElementById("editModal");
            if (event.target == addModal) {
                addModal.style.display = "none";
            }
            if (event.target == editModal) {
                editModal.style.display = "none";
            }
        }
    </script>';

    // Display success/error messages
    if (!empty($success_message)) {
        echo '<div class="alert alert-success">' . htmlspecialchars($success_message) . '</div>';
    }
    if (!empty($error_message)) {
        echo '<div class="alert alert-danger">' . htmlspecialchars($error_message) . '</div>';
    }

    echo '<div class="visitor-management-container">';
    echo '<div class="controls">';
    echo '<div>';
    echo '<button class="btn btn-primary" onclick="openAddModal()">Add New Visitor</button>';
    echo '</div>';
    echo '<div>';
    echo '<button class="btn btn-success" onclick="printTable()">Print Report</button>';
    echo '</div>';
    echo '</div>';

    echo '<div class="table-container">';
    echo '<table class="visitor-table">';
    echo '<thead>';
    echo '<tr>';
    echo '<th>ID</th>';
    echo '<th>Visitor Name</th>';
    echo '<th>Date</th>';
    echo '<th>Time In/Out</th>';
    echo '<th>Purpose</th>';
    echo '<th>Meeting With</th>';
    echo '<th>Remarks</th>';
    echo '<th class="no-print">Actions</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo '<tr>';
            echo '<td><span class="visitor-id">' . htmlspecialchars($row['id'] ?? '0') . '</span></td>';
            echo '<td><div class="visitor-name">' . htmlspecialchars($row['visitor_name'] ?? 'N/A') . '</div></td>';
            echo '<td><span class="date-badge">' . htmlspecialchars($row['date_of_visit'] ?? 'N/A') . '</span></td>';
            echo '<td>';
            echo '<div class="time-badge">IN: ' . htmlspecialchars($row['time_in'] ?? 'N/A') . '</div>';
            echo '<div class="time-badge time-out">OUT: ' . htmlspecialchars($row['time_out'] ?? 'N/A') . '</div>';
            echo '</td>';
            echo '<td><span class="purpose-badge">' . htmlspecialchars($row['purpose_of_visit'] ?? 'N/A') . '</span></td>';
            echo '<td><div class="person-to-meet">' . htmlspecialchars($row['person_to_meet'] ?? 'N/A') . '</div></td>';
            echo '<td><div class="remarks">' . htmlspecialchars($row['remarks'] ?? '-') . '</div></td>';
            echo '<td class="no-print">';
            echo '<div class="action-buttons">';
            echo '<button class="action-btn btn-warning" onclick="openEditModal(\'' .
                htmlspecialchars($row['id'] ?? '0') . '\', \'' .
                htmlspecialchars(str_replace("'", "\\'", $row['visitor_name'] ?? '')) . '\', \'' .
                htmlspecialchars($row['date_of_visit'] ?? '') . '\', \'' .
                htmlspecialchars($row['time_in'] ?? '') . '\', \'' .
                htmlspecialchars($row['time_out'] ?? '') . '\', \'' .
                htmlspecialchars(str_replace("'", "\\'", $row['purpose_of_visit'] ?? '')) . '\', \'' .
                htmlspecialchars(str_replace("'", "\\'", $row['person_to_meet'] ?? '')) . '\', \'' .
                htmlspecialchars(str_replace("'", "\\'", $row['remarks'] ?? '')) . '\')">Edit</button>';
            echo '<button class="action-btn btn-danger" onclick="deleteVisitor(\'' .
                htmlspecialchars($row['id'] ?? '0') . '\', \'' .
                htmlspecialchars(str_replace("'", "\\'", $row['visitor_name'] ?? '')) . '\')">Delete</button>';
            echo '</div>';
            echo '</td>';
            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="8" class="no-data">';
        echo '<div class="no-data-icon">📋</div>';
        echo '<strong>No visitor records found</strong><br>';
        echo 'There are currently no visitors logged in the system.';
        echo '</td></tr>';
    }

    echo '</tbody>';
    echo '</table>';
    echo '</div>';
    echo '</div>';

    // Add Visitor Modal
    echo '<div id="addModal" class="modal">';
    echo '<div class="modal-content">';
    echo '<div class="modal-header">';
    echo '<h3>Add New Visitor</h3>';
    echo '<button class="close" onclick="closeModal(\'addModal\')">&times;</button>';
    echo '</div>';
    echo '<div class="modal-body">';
    echo '<form id="addForm" method="POST" action="">';
    echo '<input type="hidden" name="action" value="add">';

    echo '<div class="form-group">';
    echo '<label for="visitorName">Visitor Name *</label>';
    echo '<input type="text" class="form-control" name="visitor_name" required>';
    echo '</div>';

    echo '<div class="form-row">';
    echo '<div class="form-group">';
    echo '<label for="dateOfVisit">Date of Visit *</label>';
    echo '<input type="date" class="form-control" name="date_of_visit" required>';
    echo '</div>';
    echo '<div class="form-group">';
    echo '<label for="timeIn">Time In *</label>';
    echo '<input type="time" class="form-control" name="time_in" required>';
    echo '</div>';
    echo '</div>';

    echo '<div class="form-row">';
    echo '<div class="form-group">';
    echo '<label for="timeOut">Time Out</label>';
    echo '<input type="time" class="form-control" name="time_out">';
    echo '</div>';
    echo '<div class="form-group">';
    echo '<label for="purposeOfVisit">Purpose of Visit *</label>';
    echo '<input type="text" class="form-control" name="purpose_of_visit" required>';
    echo '</div>';
    echo '</div>';

    echo '<div class="form-group">';
    echo '<label for="personToMeet">Person to Meet</label>';
    echo '<input type="text" class="form-control" name="person_to_meet">';
    echo '</div>';

    echo '<div class="form-group">';
    echo '<label for="remarks">Remarks</label>';
    echo '<textarea class="form-control" name="remarks" rows="3"></textarea>';
    echo '</div>';

    echo '<div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem;">';
    echo '<button type="button" class="btn btn-secondary" onclick="closeModal(\'addModal\')">Cancel</button>';
    echo '<button type="submit" class="btn btn-primary">Add Visitor</button>';
    echo '</div>';

    echo '</form>';
    echo '</div>';
    echo '</div>';
    echo '</div>';

    // Edit Visitor Modal
    echo '<div id="editModal" class="modal">';
    echo '<div class="modal-content">';
    echo '<div class="modal-header">';
    echo '<h3>Edit Visitor</h3>';
    echo '<button class="close" onclick="closeModal(\'editModal\')">&times;</button>';
    echo '</div>';
    echo '<div class="modal-body">';
    echo '<form method="POST" action="">';
    echo '<input type="hidden" name="action" value="edit">';
    echo '<input type="hidden" id="editId" name="id">';

    echo '<div class="form-group">';
    echo '<label for="editVisitorName">Visitor Name *</label>';
    echo '<input type="text" class="form-control" id="editVisitorName" name="visitor_name" required>';
    echo '</div>';

    echo '<div class="form-row">';
    echo '<div class="form-group">';
    echo '<label for="editDateOfVisit">Date of Visit *</label>';
    echo '<input type="date" class="form-control" id="editDateOfVisit" name="date_of_visit" required>';
    echo '</div>';
    echo '<div class="form-group">';
    echo '<label for="editTimeIn">Time In *</label>';
    echo '<input type="time" class="form-control" id="editTimeIn" name="time_in" required>';
    echo '</div>';
    echo '</div>';

    echo '<div class="form-row">';
    echo '<div class="form-group">';
    echo '<label for="editTimeOut">Time Out</label>';
    echo '<input type="time" class="form-control" id="editTimeOut" name="time_out">';
    echo '</div>';
    echo '<div class="form-group">';
    echo '<label for="editPurposeOfVisit">Purpose of Visit *</label>';
    echo '<input type="text" class="form-control" id="editPurposeOfVisit" name="purpose_of_visit" required>';
    echo '</div>';
    echo '</div>';

    echo '<div class="form-group">';
    echo '<label for="editPersonToMeet">Person to Meet</label>';
    echo '<input type="text" class="form-control" id="editPersonToMeet" name="person_to_meet">';
    echo '</div>';

    echo '<div class="form-group">';
    echo '<label for="editRemarks">Remarks</label>';
    echo '<textarea class="form-control" id="editRemarks" name="remarks" rows="3"></textarea>';
    echo '</div>';

    echo '<div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem;">';
    echo '<button type="button" class="btn btn-secondary" onclick="closeModal(\'editModal\')">Cancel</button>';
    echo '<button type="submit" class="btn btn-warning">Update Visitor</button>';
    echo '</div>';

    echo '</form>';
    echo '</div>';
    echo '</div>';
    echo '</div>';

} catch (Exception $e) {
    // Handle errors gracefully
    echo '<div style="background: linear-gradient(135deg, #e74c3c, #c0392b); color: white; padding: 2rem; border-radius: 15px; margin: 2rem; box-shadow: 0 10px 25px rgba(231, 76, 60, 0.3);">';
    echo '<h4>System Error</h4>';
    echo '<p>Unable to load visitor data. Please try again later.</p>';
    if (ini_get('display_errors')) {
        echo '<small>Debug info: ' . htmlspecialchars($e->getMessage()) . '</small>';
    }
    echo '</div>';
}
?>