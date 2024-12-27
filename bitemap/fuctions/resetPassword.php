<!-- functions/reset_password.php -->
<?php
session_start();
include("../templete/db_connect.php");

$errors = array('password' => '');

if (isset($_POST["submit_reset"])) {
    $token = $_SESSION['reset_token'];
    $email = $_SESSION['reset_email'];
    $newPassword = $_POST['new_password'];

    // TODO: Validate the token and email against the stored values in the database
    // If they match, proceed with updating the password

    // Update the password in the database
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE `user` SET `password` = ? WHERE `email` = ?");
    $stmt->bind_param("ss", $hashedPassword, $email);

    if ($stmt->execute()) {
        // Password updated successfully
        unset($_SESSION['reset_token']);
        unset($_SESSION['reset_email']);

        header("Location: ../login.php");
        exit;
    } else {
        $errors['password'] = 'Error updating the password: ' . $stmt->error;
    }

    // Set errors in the session
    $_SESSION['reset_password_errors'] = $errors;

    // Redirect back to the reset password page
    header("Location: ../reset_password.php");
    exit;
}
?>
