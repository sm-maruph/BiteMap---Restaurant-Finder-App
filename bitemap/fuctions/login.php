<?php
session_start();
include("../templete/db_connect.php");

$errors = array('email' => '', 'password' => '');

if (isset($_POST["login"])) {
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $password = $_POST["password"];

    // Fetch hashed password and user type from the database based on the entered email
    $stmt = $conn->prepare("SELECT `user_id`, `password`, 'user' AS `user_type`, null AS `restaurant_name`, null AS `restaurant_id`, null AS `email`, null AS `r_image`, null AS `cityCorporation`, null AS `policeStaion`, null AS `contactNumber1`, null AS `contactNumber2`, null AS `detailsAddress`, null AS `signup_time` FROM `user` WHERE `email` = ? 
                           UNION 
                           SELECT `restaurant_id`, `password`, 'restaurant' AS `user_type`, `restaurant_name`, `restaurant_id`, `email`, `r_image`, `cityCorporation`, `policeStaion`, `contactNumber1`, `contactNumber2`, `detailsAddress`, `signup_time` FROM `restaurant` WHERE `email` = ?");

    if (!$stmt) {
        die('Error preparing statement: ' . $conn->error);
    }

    $stmt->bind_param("ss", $email, $email);

    if ($stmt->execute()) {
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $hashedPasswordFromDatabase = $row['password'];

            // Verify the entered password using password_verify
            if (password_verify($password, $hashedPasswordFromDatabase)) {
                // Password is correct, 
                $user = $row;
                $_SESSION["user"] = $user;
                
                // Check user type and redirect accordingly
                if ($row['user_type'] == 'user') {
                    //  fetch user details
                    
                    header("Location: ../bitemap.php");
                } elseif ($row['user_type'] == 'restaurant') {
                    header("Location: ../restaurantHeader.php");
                }

                exit;
            } else {
                $errors['password'] = 'Invalid password';
            }
        } else {
            $errors['email'] = 'Invalid email or password';
        }
    } else {
        $errors['email'] = 'Error executing the statement: ' . $stmt->error;
    }

    // Set errors in the session
    $_SESSION['login_errors'] = $errors;

    // Redirect back to the login page
    header("Location: ../login.php");
    exit;
}
?>