<?php
session_start();
include 'db.php';

if (isset($_POST['first_name'])) {
    $f_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $l_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $age = mysqli_real_escape_string($conn, $_POST['age']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $flight_id = mysqli_real_escape_string($conn, $_POST['flight_id']);
    $class = mysqli_real_escape_string($conn, $_POST['class']);

    // I-save ang details sa 'bookings' table
    $sql = "INSERT INTO bookings (flight_id, class, first_name, last_name, age, gender, status) 
            VALUES ('$flight_id', '$class', '$f_name', '$l_name', '$age', '$gender', 'Pending')";

    if (mysqli_query($conn, $sql)) {
        // Kuhaa ang ID sa bag-ong booking aron ma-pasa sa payment page
        $last_id = mysqli_insert_id($conn);
        
        // I-redirect sa payment.php dala ang ID
        header("Location: payment.php?booking_id=" . $last_id);
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
} else {
    header("Location: home.php");
    exit();
}
?>