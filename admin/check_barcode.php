<?php
include "config.php";

if (isset($_POST['barcode'])) {
    $barcode = trim($_POST['barcode']);

    $stmt = $conn->prepare("SELECT * FROM user WHERE no_plat = ?");
    $stmt->bind_param("s", $barcode);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();

        // Build response string (you can make JSON if you want)
        echo "User Found:
        Nama: " . $data['nama'] . "
        IC: " . $data['no_kp'] . "
        Kenderaan: " . $data['kenderaan'] . "
        Status: " . $data['status'] . "
        Plate: " . $data['no_plat'];

    } else {
        echo "No user found with plate: $barcode";
    }
}
?>