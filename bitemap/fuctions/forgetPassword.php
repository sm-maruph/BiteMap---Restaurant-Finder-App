<!-- functions/forgot_password.php -->
<?php
session_start();
include("../templete/db_connect.php");

$errors = array('email' => '');

if (isset($_POST["reset_password"])) {
    $email = mysqli_real_escape_string($conn, $_POST["email"]);

    // Check if the email exists in the database
    $stmt = $conn->prepare("SELECT `user_id` FROM `user` WHERE `email` = ?");
    $stmt->bind_param("s", $email);

    if ($stmt->execute()) {
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Email exists, generate and store a reset token
            $token = bin2hex(random_bytes(32));
            $_SESSION['reset_token'] = $token;
            $_SESSION['reset_email'] = $email;

            // TODO: Store the token and email in the database (for production, you would store it securely)

            // Send a reset email to the user
            $reset_link = "http://yourwebsite.com/reset_password.php?token=$token";
            $subject = "Password Reset";
            $message = "Click the link below to reset your password:\n\n$reset_link";

            // TODO: Implement a function to send the email, for example, using PHPMailer

            header("Location: ../login.php");
            exit;
        } else {
            $errors['email'] = 'Email not found';
        }
    } else {
        $errors['email'] = 'Error executing the statement: ' . $stmt->error;
    }

    // Set errors in the session
    $_SESSION['forgot_password_errors'] = $errors;

    // Redirect back to the forgot password page
    header("Location: ../forgot_password.php");
    exit;
}
?>
