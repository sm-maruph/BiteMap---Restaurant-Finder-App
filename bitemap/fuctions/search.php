<?php
//include("../templete/db_connect.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $location = mysqli_real_escape_string($conn, $_POST["policeStation"]);
    $restaurantName = mysqli_real_escape_string($conn, $_POST["restaurantName"]);

    // Define an array of checkbox categories
    $categories = [
        'traveler_type',
        'restaurant_type',
        'meal_type',
        'price',
        'dishes',
        'goodFor',
        'cuisin',
        'features',
        // Add other categories as needed
    ];
    //NEecho print_r($categories);

    // Initialize an array to store conditions for each category
    $conditions = [];

    // Loop through each category
    foreach ($categories as $category) {
        // Check if the category is set in the POST data
        if (isset($_POST[$category])) {
            // Get the selected values for the category
            $values = $_POST[$category];
            // Sanitize and prepare the values for the SQL query
            $values = array_map(function ($value) use ($conn) {
                return $conn->real_escape_string($value);
            }, $values);
            // Create a condition for the category
            $conditions[] = "f.typename IN ('" . implode("','", $values) . "')";
            
        }
    }
    

    // Build the SQL query
    $sql = "SELECT DISTINCT(r.restaurant_name), r.restaurant_id, r.email,r.r_image, r.cityCorporation, r.policeStaion, r.contactNumber1, r.contactNumber2, r.detailsAddress ,r.map
            FROM restaurant as r 
            JOIN restaurant_features rf ON r.restaurant_id = rf.restaurant_id
            JOIN features f ON rf.feature_id = f.id
            WHERE 1";

    // Add location condition if provided
    if (!empty($location) && empty($restaurantName)) {
        $sql .= " AND (r.policeStaion='$location')";
    } elseif (empty($location) && !empty($restaurantName)) {
        $sql .= " AND (restaurant_name='$restaurantName')";
    } elseif (!empty($location) && !empty($restaurantName)) {
        $sql .= " AND (r.policeStaion='$location' AND restaurant_name='$restaurantName')";
    }

    // Add conditions to the query if there are any
    if (!empty($conditions)) {
        $sql .= " AND (" . implode(" OR ", $conditions) . ")";
    }

 // Execute the query
 $result = $conn->query($sql);

 // Check for errors
 if (!$result) {
   $error_message = "Error executing query: " . $conn->error;
 }
}
$conn->close();
?>









