<?php
include 'db.php';

// Siguroha nga ang 'pay_method' nag-match sa 'name' sa radio buttons sa payment.php
if (isset($_POST['booking_id']) && isset($_POST['pay_method'])) {
    
    $booking_id = mysqli_real_escape_string($conn, $_POST['booking_id']);
    $method = mysqli_real_escape_string($conn, $_POST['pay_method']);

    // I-update ang status ug payment_method sa database
    $sql = "UPDATE bookings SET 
            status = 'Confirmed', 
            payment_method = '$method' 
            WHERE id = '$booking_id'";

    if (mysqli_query($conn, $sql)) {
        // Human sa malampuson nga update, adto sa history page
        header("Location: my_bookings.php?status=success");
        exit();
    } else {
        echo "Error updating record: " . mysqli_error($conn);
    }
} else {
    // Balik sa home kung walay data nga nadawat
    header("Location: home.php");
    exit();
}
?>