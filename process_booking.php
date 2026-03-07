<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $flight_id = $_POST['flight_id'];
    $name = mysqli_real_escape_string($conn, $_POST['passenger_name']);
    $class = $_POST['class'];

    $sql = "INSERT INTO bookings (user_id, flight_id, passenger_name, class, booking_date) 
            VALUES ('$user_id', '$flight_id', '$name', '$class', NOW())";
            
    if(mysqli_query($conn, $sql)) {
        header("Location: my_bookings.php"); // I-redirect sa page nga gi-paste nimo ganina
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>