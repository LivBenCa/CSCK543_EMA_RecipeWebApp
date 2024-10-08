<?php
// Start the session to maintain a logged-in user's state
session_start();
// Include the database configuration to use for database connections
require_once "../config/dbconfig.php"; 

// Check if the recipeId is provided in the POST request
if(isset($_POST['recipeId'])) {
    $recipeId = $_POST['recipeId'];

    // Establish a connection to the database
    $conn = getConnection();
    // Initialize a response array to hold the success status and average rating
    $response = ['success' => false, 'averageRating' => 0];

    // Prepare the SQL call procedure statement
    if($stmt = $conn->prepare("CALL sp_get_average_rating(?)")) {
        // Bind the recipe ID parameter to the SQL statement
        $stmt->bind_param("i", $recipeId);
        // Execute the prepared statement
        if($stmt->execute()) {
            // Retrieve the result set from the statement execution
            $result = $stmt->get_result();
            // Fetch the associative array from the result set
            if ($row = $result->fetch_assoc()) {
                // Update the response array with success status and rounded average rating
                $response['success'] = true;
                // Round the average rating to the nearest half
                $response['averageRating'] = round($row["average_rating"] * 2) / 2;
            }
        }
        // Close the prepared statement
        $stmt->close();
    }
    // Close the database connection
    $conn->close();

    // Encode the response array as JSON and output it
    echo json_encode($response);
} else {
    // Return an error message if the recipeId parameter is missing
    echo json_encode(['success' => false, 'message' => 'Missing required parameter: recipeId']);
}
?>
