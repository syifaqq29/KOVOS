<?php
include('config.php');
$no_kp = $_GET['no_kp'];
$result = mysqli_query($conn, "DELETE FROM user WHERE no_kp='$no_kp'");
header("Location:dashboard.php");
?>