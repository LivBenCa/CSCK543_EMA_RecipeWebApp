<?php
// Include the database configuration script for database connection
require_once "../config/dbconfig.php";

// Check if the current request uses the GET method 
if ($_SERVER["REQUEST_METHOD"] == "GET") {
  // Establish a connection to the database
  $conn = getConnection();

  // Prepare the SQL statement for execution, calling the dedicated stored procedure
  if ($stmt = $conn->prepare("CALL sp_get_categories()")) {
    // Execute the prepared statement
    $stmt->execute();
    // Store the result set returned by the stored procedure
    $result = $stmt->get_result();

    // Verify if any categories are returned
    if ($result && $result->num_rows > 0) {
      // Initialize an array to hold category data for the recipes
      $categories = [];
      // Fetch each row of category data as an associative array
      while ($row = $result->fetch_assoc()) {
        // Add the category data to the categories array
        $categories[] = $row;
      }
    } else {
      // Alert the user if no categories are found in the database and using generic message should the database be empty
      echo "<script>alert('No Data found.');</script>";
    }
    // Close the prepared statement
    $stmt->close();
  } else {
    // Alert the user if there is an error preparing the SQL statement
    echo "<script>alert('Error preparing statement.');</script>";
  }
  // Close the database connection
  $conn->close();
}
?>
