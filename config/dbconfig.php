<?php
// dbconfig.php
// Define constants for database connection details.
define('DB_SERVER', '127.0.0.1'); // Database server address, localhost, hostname or IP address.
define('DB_USERNAME', 'flavourfinds'); // Username for database login as per the service account (Script 4 in database/scripts).
define('DB_PASSWORD', 'flavourfindsPassword(123)'); // Password for the database service account.
define('DB_DATABASE', 'flavour_finds'); // The name of the database to connect to.

/**
 * Establishes a connection to the MySQL database using mysqli extension.
 *
 * @return mysql connection object on success.
 */
function getConnection() {
    // Attempt to connect to the MySQL database using the defined constants.
    $connection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
    
    // Check if the connection was unsuccessful and output the error if true.
    if (mysqli_connect_errno()) {
        die("Failed to connect to MySQL: " . mysqli_connect_error());
    }
    
    // Return the successful database connection object.
    return $connection;
}
?>
