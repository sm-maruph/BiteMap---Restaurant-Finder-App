








<?php
session_start();
include("../templete/db_connect.php");

$errors = array('email' => '');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $restaurantName = mysqli_real_escape_string($conn, $_POST['restaurant_name']);
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $cityCorporation = mysqli_real_escape_string($conn, $_POST['cityCorporation']);
    $policeStation = mysqli_real_escape_string($conn, $_POST['policeStation']);
    $detailsAddress = mysqli_real_escape_string($conn, $_POST['details_address']);
    $contactNumber1 = mysqli_real_escape_string($conn, $_POST['contact_number1']);
    $contactNumber1 = preg_replace("/[^0-9]/", "", $contactNumber1);
    $contactNumber2 = mysqli_real_escape_string($conn, $_POST['contact_number2']);
    $contactNumber2 = preg_replace("/[^0-9]/", "", $contactNumber2);
    $signupTime = date('y-m-d H:i:s');
    $restaurant_picture = $_FILES['restaurant_img'];

    // Check if the restaurant name already exists
    $checkRestaurantNameQuery = $conn->prepare("SELECT * FROM `restaurant` WHERE `restaurant_name` = ?");
    $checkRestaurantNameQuery->bind_param("s", $restaurantName);
    $checkRestaurantNameQuery->execute();
    $checkRestaurantNameResult = $checkRestaurantNameQuery->get_result();

    // Check if the email already exists
    $checkEmailQuery = $conn->prepare("SELECT * FROM `restaurant` WHERE `email` = ?");
    $checkEmailQuery->bind_param("s", $email);
    $checkEmailQuery->execute();
    $checkEmailResult = $checkEmailQuery->get_result();

    if (mysqli_num_rows($checkRestaurantNameResult) > 0 && mysqli_num_rows($checkEmailResult) > 0) {
        $errors['email'] = 'Restaurant Name and Email already exist.';
        $_SESSION['input_values'] = get_input_values();
    } elseif (mysqli_num_rows($checkRestaurantNameResult) > 0) {
        $errors['email'] = 'Restaurant name already exists.';
        $_SESSION['input_values'] = get_input_values();
    } elseif (mysqli_num_rows($checkEmailResult) > 0) {
        $errors['email'] = 'Email already exists.';
        $_SESSION['input_values'] = get_input_values();
    } else {
        // Handle image upload
        $uploadDir = '../image/restaurant/profile';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $uploadFile = $uploadDir . basename($restaurant_picture['name']);  // Use correct array key

        if (file_exists($uploadFile)) {
            $errors['file'] = 'File already exists.';
            $_SESSION['input_values'] = get_input_values();
        }

        if (move_uploaded_file($restaurant_picture['tmp_name'], $uploadFile)) {
            // Image uploaded successfully
            $result = substr($uploadFile, 3);
            $result2 = true;

            // Insert data into the database
            $stmt = $conn->prepare("INSERT INTO `restaurant`(`restaurant_name`, `email`, `password`, `type`, `r_image`, `cityCorporation`, `policeStaion`, `contactNumber1`, `contactNumber2`, `detailsAddress`, `signup_time`) VALUES (?, ?, ?, 'Restaurant', ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssssss", $restaurantName, $email, $hashedPassword, $result, $cityCorporation, $policeStation, $contactNumber1, $contactNumber2, $detailsAddress, $signupTime);

            if ($stmt === false) {
                die("Error in SQL preparation: " . $conn->error);
            }

            if ($stmt->execute()) {
                $restaurant_id = mysqli_insert_id($conn);

                // Insert features
                for ($i = 1; $i <= 63; $i++) {
                    $checkbox_name = "c" . $i;

                    if (isset($_POST[$checkbox_name])) {
                        $feature_value = $i;
                        $sql2 = "INSERT INTO `restaurant_features`(`restaurant_id`, `feature_id`) VALUES ('$restaurant_id','$feature_value')";
                        $result2 = mysqli_query($conn, $sql2);
                    }
                }

                if ($result2) {
                    unset($_SESSION['input_values']);
                    unset($_SESSION['signup_errors']);
                    $_SESSION['registration_success'] = 'Registration Successful';
                    header("Location: ../signuprestaurant.php");
                    exit();
                } else {
                    $errors['registration_success'] = 'Error during registration: ' . $stmt->error . ' ' . mysqli_error($conn);
                }

                $stmt->close();
            } else {
                $errors['registration_success'] = 'Error uploading image or executing query.';
            }
        }
    }

    // Display error messages
    $_SESSION['signup_errors'] = $errors;
    $checkRestaurantNameQuery->close();
    $checkEmailQuery->close();
    $conn->close();
}

function get_input_values() {
    return [
        'restaurant_name' => $_POST['restaurant_name'],
        'email' => $_POST['email'],
        'password' => $_POST['password'],
        'cityCorporation' => $_POST['cityCorporation'],
        'policeStation' => $_POST['policeStation'],
        'details_address' => $_POST['details_address'],
        'contact_number1' => $_POST['contact_number1'],
        'contact_number2' => $_POST['contact_number2']
        // Add other form fields as needed
    ];
}

header("Location: ../signuprestaurant.php");
?>
