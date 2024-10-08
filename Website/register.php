<?php

// Include the database configuration file to utilize the database connection settings
require_once "../config/dbconfig.php";

// Check if the request method is POST which indicates that form data has been sent
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and collect input data to prevent XSS and other vulnerabilities
    $name = filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
    $password = filter_input(INPUT_POST, "psw", FILTER_SANITIZE_STRING);

if (!empty($password)) {
    // Encrypt the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
  } else {
    // Handle the case where the password is empty or null
    echo "<script>alert('Password cannot be empty.'); window.location.href='register.php';</script>";
    exit; // Exit the script to prevent further execution
}


    // Establish a database connection
    $conn = getConnection();

    // Prepare a SQL statement to call the stored procedure for user registration
    if ($stmt = $conn->prepare("CALL sp_register_user(?, ?, ?)")) {
        // Bind user input parameters to the prepared SQL statement
        $stmt->bind_param("sss", $name, $hashed_password, $email);
        // Execute the prepared statement
        $stmt->execute();
        // Retrieve the result set from the stored procedure
        $result = $stmt->get_result();

        // Check if there's any result returned from the stored procedure
        if ($result && $result->num_rows > 0) {
            // Fetch the associative array from the result
            $user = $result->fetch_assoc();
            // Check if the stored procedure returned an error message indicating the user exists
            if (isset($user['ErrorMessage'])) {
                // Alert the user that the account already exists and keep on the registration page to try again
                echo "<script>alert('User already exists. Please try again or Log In'); window.location.href='register.php';</script>";
            }
        } else {
            // If no result is returned, registration was successful but without an immediate login
            echo "<script>alert('Successfully registered. Please login.'); window.location.href='login.php';</script>";
        }
        // Close the statement
        $stmt->close();
    } else {
        // If the SQL statement fails to prepare, alert the user
        echo "<script>alert('Failed to prepare the registration statement.');</script>";
    }
    // Close the database connection
    $conn->close();
}
?>


<!DOCTYPE html> <!-- Declares the document type and version of HTML -->
<html lang="en"> <!-- Begins the HTML document with language set to English -->

<head>
	<meta charset="utf-8"> <!-- Sets the character encoding for the document -->
	<meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Ensures the site is responsive and scales correctly on different devices -->
	<title>Best recipes ever</title> <!-- Sets the title of the web page -->
	<link rel="icon" type="image/x-icon" href="images/icons/favicon.ico"> <!-- Defines the favicon for the web page -->
</head>

<body>
  <!-- Imports external CSS file for styling -->
<style>
		@import url("StylesheetRecipeRegisterLogin.css");
	</style>

<header> <!-- Header section of the web page -->
  <div id="logo">
    <a href="index.php"><img src="images/logo/logo.png" width="50" height="50" alt="FF logo">
    <span></span> <!-- Possibly for text or additional elements next to the logo -->
  </a>
  </div>

	<nav> <!-- Navigation bar -->
    <ul> <!-- Unordered list for navigation items -->
      <li class="dropdown"> <!-- Dropdown menu for login/register -->
          <a href="javascript:void(0)" class="dropbtn">
               <img src="images/icons/auth-icon.png" alt="authorization icon" width="30"> <!-- Dropdown trigger icon/image -->
          </a>
          <div class="dropdown-content" id="myDropdown" aria-label="User Menu"> <!-- Dropdown content -->
              <a href="login.php">Log in</a> <!-- Link to login page -->
              <a href="register.php">Register</a> <!-- Link to registration page -->
          </div>
      </li>
  </ul>
	</nav>
</header>

<div class="container"> <!-- Main container for content -->
<div class="main"> <!-- Main content area -->

  <section class="login-block"> <!-- Registration form section -->
  <h2>Create an account</h2>
  <form class="login-register-form" action="" method="post"> <!-- Registration form -->
      <label for="name"><b>Name</b></label>
      <input class="input" type="text" placeholder="Enter name" name="name" required> <!-- Field for name input -->

      <label for="email"><b>Email</b></label>
      <input class="input" type="email" placeholder="Enter email" name="email" required> <!-- Field for email input -->

      <label for="psw"><b>Password</b></label>
      <input class="input" type="password" placeholder="Enter Password" name="psw" required> <!-- Field for password input -->

      <button type="submit">Register</button> <!-- Submission button for the form -->

      <span class="login">Already have an account? <a href="login.php">Login here</a></span> <!-- Link to login for existing users -->
  </form>
</section>

<section class="landing-image"> <!-- Section for landing image -->
  <img src="images/landing-images/landing-dishes.png" alt="a neatly served table" class="image-main-landing"> <!-- Landing page image -->
</section>
</div>
</div>

<footer class="footer">
        <p>© 2024 Flavour Finds</p> <!-- Footer content -->
</footer>

<script src="main.js"></script> <!-- Link to external JavaScript file -->
</body>

</html>
