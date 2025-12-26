<?php
include('config.php');
$no_kp = $_GET['no_kp'];

// First, get the barcode image path before deleting the user
$select_query = "SELECT barcode_img FROM user WHERE no_kp='$no_kp'";
$select_result = mysqli_query($conn, $select_query);

if ($select_result && mysqli_num_rows($select_result) > 0) {
    $row = mysqli_fetch_assoc($select_result);
    $barcode_path = $row['barcode_img'];

    // Delete the user from database
    $delete_query = "DELETE FROM user WHERE no_kp='$no_kp'";
    $delete_result = mysqli_query($conn, $delete_query);

    if ($delete_result) {
        // If database deletion was successful, delete the barcode file
        if (!empty($barcode_path)) {
            // Use the same method as your working code
            $documentRoot = $_SERVER['DOCUMENT_ROOT'];
            $full_path = $documentRoot . '/KoVoS/' . $barcode_path;

            if (file_exists($full_path)) {
                if (unlink($full_path)) {
                    echo "<script>alert('File deleted successfully!'); window.location.href='dashboard.php';</script>";
                } else {
                    echo "<script>alert('Fail to delete file!'); window.location.href='dashboard.php';</script>";
                }
            } else {
                echo "File does not exist at: " . $full_path . "<br>";
            }

            // Uncomment the line below after testing
            // header("Location:dashboard.php");
            exit; // Stop execution to see debug output
        }
    }
}

?>