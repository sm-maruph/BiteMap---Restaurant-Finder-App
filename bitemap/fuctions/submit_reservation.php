<?php

// Replace these database credentials with your actual values
include("../templete/db_connect.php");



// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Process the reservation form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $restaurantID = $_POST['restaurant'];
    $dateTime = $_POST['dateTime'];
    $guests = $_POST['guests'];
    $cardNumber = $_POST['cardNumber'];
    $expiryDate = $_POST['expiryDate'];
    $cvv = $_POST['cvv'];

    // Perform SQL query to insert reservation data into the database
    $sql = "INSERT INTO `reservations`( `UserID`, `RestaurantID`, `ReservationDateTime`, `NumberOfGuests`, `ReservationStatus`) VALUES ('NULL','$restaurantID','$dateTime','$guests','Pending')";

    if ($conn->query($sql) === TRUE) {
        // Reservation data inserted successfully
        $reservationID = $conn->insert_id;

        // Perform SQL query to insert payment data into the database
        $paymentSql = "INSERT INTO `payments`( `ReservationID`, `PaymentDateTime`, `Amount`, `PaymentStatus`) VALUES ('$reservationID',' NOW()','50.00','successful')";

        if ($conn->query($paymentSql) === TRUE) {
            // Payment data inserted successfully
            $response = ['message' => 'Reservation and payment submitted successfully'];
            header('Content-Type: application/json');
            echo json_encode($response);
        } else {
            // Error inserting payment data
            $response = ['message' => 'Error submitting payment. Please try again.'];
            header('Content-Type: application/json');
            echo json_encode($response);
        }
    } else {
        // Error inserting reservation data
        $response = ['message' => 'Error submitting reservation. Please try again.'];
        header('Content-Type: application/json');
        echo json_encode($response);
    }
} else {
    // Redirect to the index page if accessed directly
    header('Location: reservation.php');
}

// Close the database connection
$conn->close();
?>
