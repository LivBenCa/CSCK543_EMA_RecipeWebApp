<?php
// Begin the session or resume an existing one
session_start();

// Include the database configuration file
require_once "../config/dbconfig.php";

// Check if a user session exists indicating a logged-in user
if(isset($_SESSION['user_id'])) {
    // Assign the user ID from the session to a variable for later use
    $user_id = $_SESSION['user_id'];
}

// Determine if the user is logged in by checking the presence of the user ID in the session
$userLoggedIn = isset($_SESSION['user_id']);

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Best recipes ever</title>
  <!--link to show favicon int the browser url box-->
	<link rel="icon" type="image/x-icon" href="images/icons/favicon.ico">
</head>

<body>
  <!--link to style sheet-->
<style>
		@import url("landing_page.css");
</style>

<!-- Includes logo, login and registration links -->

    <header>
      <div id="logo">

        <a href="index.php"><img src="images/logo/logo.png" width="50" height="50" alt="FF logo"></a>
      </div>
      <nav>
        <ul>
        <!-- list items with links to other pages and aria labels for accessibility-->
          <?php if (isset($_SESSION['user_id'])): ?>
            <li><a href="search_results.php">All Recipes</a></li>
            <li class="dropdown">
              <a href="javascript:void(0)" class="dropbtn">
                <img src="images/icons/auth-icon.png" alt="authorization icon" width="30">
              </a>
              <!--logic to show different items on the dropdown menu depending on whether the user is logged in-->
              <div class="dropdown-content" id="myDropdown" aria-label="User Menu">
                <a href="user_page.php">Your Profile</a>
                <a href="logout.php">Log Out</a>
              </div>
            </li>
          <?php else: ?>
            <li><a href="login.php">Log in</a></li>
            <li><a href="register.php">Register</a></li>
          <?php endif; ?>
        </ul>
      </nav>
    </header>
    <main>
<!-- Info about the website -->
    <section class="landing-main-block" >
      <h1>WELCOME TO FLAVOURFINDS</h1>
      <p class="landing-text">Welcome to FlavourFinds, your ultimate destination for discovering delicious recipes! Whether you're a seasoned chef or a curious beginner, our app is designed to inspire your culinary journey. Dive into a world of flavours with thousands of recipes at your fingertips. Search by meal type, meal description or category to find the perfect dish for any occasion. From quick weeknight dinners to sumptuous feasts, FlavourFinds is here to guide you through every step of your cooking adventure. Let's embark on a flavourful journey together!
      </p>

    </section>
  </main>
<!-- Just a visual highlighting of the page end -->
<footer class="footer">

        <p>© 2024 Flavour Finds</p>
    </footer>
        <!-- link to javascript script -->
<script src="main.js"></script>
</body>

</html>
