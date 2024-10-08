<?php
// Start a new session or resume the existing session
session_start();

// Include the database configuration file for database connection setup
require_once "../config/dbconfig.php";

// Check if the necessary POST parameters are set
if(isset($_POST['user_id'], $_POST['recipe_id'], $_POST['action'])) {
    // Retrieve the user ID, recipe ID, and action (add or remove) from the POST data
    $user_id = $_POST['user_id'];
    $recipe_id = $_POST['recipe_id'];
    $action = $_POST['action'];

    // Establish a database connection
    $conn = getConnection();

    // Determine the action to take: add to favorites or remove from favorites
    if($action == 'add') {
        // Prepare the SQL statement for adding a recipe to favorites
        $stmt = $conn->prepare("CALL sp_add_to_favourites(?, ?)");
    } else {
        // Prepare the SQL statement for removing a recipe from favorites
        $stmt = $conn->prepare("CALL sp_remove_from_favourites(?, ?)");
    }

    // Bind the user ID and recipe ID as parameters to the SQL statement
    $stmt->bind_param("ii", $user_id, $recipe_id);
    // Execute the prepared statement
    $stmt->execute();
    // Close the prepared statement
    $stmt->close();
    // Close the database connection
    $conn->close();
    // Output success message
    echo "Success";
}
?>
