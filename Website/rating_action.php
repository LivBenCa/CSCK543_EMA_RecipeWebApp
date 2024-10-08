<?php
// Start or resume a session
session_start();

// Include the database configuration file to set up a database connection
require_once "../config/dbconfig.php";

// Check if all required data (user ID, recipe ID, and rating) are provided in the POST request
if(isset($_POST['userId'], $_POST['recipeId'], $_POST['rating'])) {
    // Extract the user ID, recipe ID, and rating from the POST data
    $userId = $_POST['userId'];
    $recipeId = $_POST['recipeId'];
    $rating = $_POST['rating'];

    // Establish a connection to the database
    $conn = getConnection();
    // Prepare a response array with a default success status of false
    $response = ['success' => false];

    // Prepare the SQL call to the stored procedure that adds or updates a rating
    if($stmt = $conn->prepare("CALL sp_add_rating(?, ?, ?)")) {
        // Bind the input parameters (user ID, recipe ID, and rating) to the prepared SQL statement
        $stmt->bind_param("iii", $userId, $recipeId, $rating);
        // Execute the statement and update the response success status based on the operation result
        if($stmt->execute()) {
            $response['success'] = true;
        }
        // Close the prepared statement
        $stmt->close();
    }
    // Close the database connection
    $conn->close();

    // Encode the response array as JSON and output it
    echo json_encode($response);
} else {
    // If required parameters are missing, encode and output an error message in JSON format
    echo json_encode(['success' => false, 'message' => 'Missing required parameters.']);
}
?>
