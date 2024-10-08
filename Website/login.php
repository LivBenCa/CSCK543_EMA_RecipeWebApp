<?php
// Start or resume a session
session_start();

// Include database configuration
require_once "../config/dbconfig.php";

// Check if the user is already logged in
if (isset($_SESSION["user_id"])) {
    // Redirect to search results page
    header("Location: search_results.php");
    exit;
}

// Variable to store error messages
$error = '';

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and retrieve email and password from POST data
    $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
    $password = filter_input(INPUT_POST, "psw", FILTER_SANITIZE_STRING);

    // Establish a database connection
    $conn = getConnection();

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if a prepared statement can be made with the SQL procedure call
    if ($stmt = $conn->prepare("CALL sp_login(?)")) {
        // Bind the input email to the prepared statement as a string parameter
        $stmt->bind_param("s", $email);
        // Execute the prepared SQL statement
        $stmt->execute();
        // Get the result set from the executed statement
        $result = $stmt->get_result();

        // Check if the result set is valid and a row can be fetched
        if ($result && ($row = $result->fetch_assoc())) {
            // password_verify to compare the input password with the hashed password from the database
            if (password_verify($password, $row['password'])) {
                // Set session variables for user ID and email upon successful login
                $_SESSION["user_id"] = $row["user_id"];
                $_SESSION["email"] = $email;
                // Redirect the user to the search results page
                header("Location: search_results.php");
                exit; // Ensure script execution ends after redirect
            } else {
                // Set an error message if the password does not match
                $error = "Login failed. Email or password is incorrect.";
            }
        } else {
            // Set an error message if no matching row was found
            $error = "Login failed. Email or password is incorrect.";
        }
        // Close the prepared statement
        $stmt->close();
    } else {
        // Set an error message if the SQL statement fails to prepare
        $error = "Failed to prepare the login statement.";
    }

    // Close the connection
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Best recipes ever</title>
    <!-- Link to favicon for the website -->
    <link rel="icon" type="image/x-icon" href="images/icons/favicon.ico">
    <!-- Importing external CSS stylesheet -->
    <style>
        @import url("StylesheetRecipeRegisterLogin.css");
    </style>
</head>
<body>

<!-- Header section containing logo and navigation bar -->
<header>
    <div id="logo">
        <!-- Link back to the homepage from the logo -->
        <a href="index.php"><img src="images/logo/logo.png" width="50" height="50" alt="FF logo"></a>
    </div>
    <nav>
        <ul>
            <!-- Dropdown menu for authentication options -->
            <li class="dropdown">
                <a href="javascript:void(0)" class="dropbtn">
                    <img src="images/icons/auth-icon.png" alt="authorization icon" width="30">
                </a>
                <!-- Dropdown content with login and registration links -->
                <div class="dropdown-content" id="myDropdown" aria-label="User Menu">
                    <a href="login.php">Log in</a>
                    <a href="register.php">Register</a>
                </div>
            </li>
        </ul>
    </nav>
</header>
<!-- Main container for the content of the page -->
<div class="container">
    <div class="main">
        <!-- Section for login functionality -->
        <section class="login-block">
            <h2>Log in to your account</h2>
            <!-- Error message display section -->
            <?php if ($error): ?>
            <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>
            <!-- Login form with email and password fields -->
            <form class="login-register-form" method="post">
                <label class="label" for="email"><b>Email</b></label>
                <input class="input" type="email" placeholder="Enter email" name="email" required>
                <label for="psw"><b>Password</b></label>
                <input class="input" type="password" placeholder="Enter Password" name="psw" required>
                <button type="submit">Login</button>
                <!-- Registration link for new users -->
                <br>
                <span class="psw"><a href="register.php">Register here</a></span>
            </form>
        </section>
        <!-- Section displaying an image related to the site's content -->
        <section class="landing-image">
            <img src="images/landing-images/landing-dishes-2.png" alt="a neatly served table">
        </section>
    </div>
</div>

<!-- Footer section with copyright information -->
<footer class="footer">
    <p>© 2024 Flavour Finds</p>
</footer>

<!-- Link to the JavaScript file for the website -->
<script src="main.js"></script>
</body>
</html>
