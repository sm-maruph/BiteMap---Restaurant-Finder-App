<?php
session_start();
include("../templete/db_connect.php");
$errors = array('email' => '');
$first_name = mysqli_real_escape_string($conn,$_POST["first_name"]);
$last_name = mysqli_real_escape_string($conn,$_POST["last_name"]);
$email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
$email = filter_var($email, FILTER_SANITIZE_EMAIL);
$password = mysqli_real_escape_string($conn,$_POST["password"]);
$address = mysqli_real_escape_string($conn,$_POST["address"]);
$contact_num = mysqli_real_escape_string($conn,$_POST["contact_number"]);
$gender = mysqli_real_escape_string($conn,$_POST["gender"]);

// Hash the password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$profile_picture = $_FILES['profile_picture'];

// Use a prepared statement to prevent SQL injection
// Check if the email already exists
$checkEmailStmt = $conn->prepare("SELECT `user_id` FROM `user` WHERE `email` = ?");
$checkEmailStmt->bind_param("s", $email);

if ($checkEmailStmt->execute()) {
    $checkResult = $checkEmailStmt->get_result();

    if ($checkResult->num_rows > 0) {
        // Email already exists, handle the error (e.g., redirect back to the registration form with an error message)
        $errors['email'] = 'Email already exists';
        $_SESSION['input_values'] = [
            'first_name' => $_POST['first_name'],
            'last_name' => $_POST['last_name'],
            'email' => $_POST['email'],
            'password' => $_POST['password'],
            'address' => $_POST['address'],
            'contact_number' => $_POST['contact_number'],
            'gender' => $_POST['gender'],
            'profile_picture' => $_FILES['profile_picture']
            // Add other form fields as needed
            
        ];
    } else {
           // Handle image upload
    $uploadDir = '../image/customers/profile_picture'; // Specify the upload 
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);  // Create the directory if it doesn't exist
        echo "Maruph";
    }
    $uploadFile = $uploadDir . basename($_FILES['profile_picture']['name']);

    if (file_exists($uploadFile)) {
        $message['error'] = 'File already Exists';
        
    }

    if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $uploadFile)) {
        echo "Image uploaded successfully.";
    } else {
        echo "Error uploading image.";
        mysqli_close($conn);
        echo "Maruph";
        exit();
    }
    $result = substr($uploadFile, 3);

    // Prepare the SQL statement for insertion
    $stmt = $conn->prepare("INSERT INTO `user`(`first_name`, `last_name`, `email`, `address`, `type`, `password`, `phone_no`, `Gender`, `profile_picture`) VALUES ('$first_name','$last_name','$email','$address','Customer','$hashedPassword','$contact_num','$gender','$result')");


        // Check for successful preparation
        if ($stmt === false) {
            die("Error in SQL preparation: " . $conn->error);
        }

        // Check for successful execution
        if ($stmt->execute()) {
            // Success
            unset($_SESSION['input_values']);
            unset($_SESSION['signup_errors']); // Clear the errors after displaying

            // Set a success message
            $_SESSION['registration_success'] = 'Registration Successful';

            // Redirect back to the signup page
            header("Location: ../signupcustomer.php");
        } else {
            die("Error executing the statement: " . $stmt->error);
        }

        // Close the statement
        $stmt->close();
    }
} else {
    die("Error checking email existence: " . $checkEmailStmt->error);
}

// Set errors in the session
$_SESSION['signup_errors'] = $errors;

// Close the check statement
$checkEmailStmt->close();

// Close the database connection
$conn->close();
header("location: ../signupcustomer.php");
?>
