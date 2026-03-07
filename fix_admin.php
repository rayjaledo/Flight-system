<?php
include 'db.php';

$username = 'admin';
$password = 'admin123'; // Kini ang mahimong bag-ong password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$sql = "UPDATE admins SET password = '$hashed_password' WHERE username = '$username'";

if (mysqli_query($conn, $sql)) {
    echo "<h1>SUCCESS!</h1>";
    echo "Ang admin password na-update na ngadto sa: <b>admin123</b><br>";
    echo "Palihug sulayi pag-log in sa <a href='admin_login.php'>Admin Login Page</a>.";
} else {
    echo "Error updating record: " . mysqli_error($conn);
}
?>