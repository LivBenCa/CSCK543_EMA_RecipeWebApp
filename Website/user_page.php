<?php
// Start or resume a session
session_start();

// Check if user is not logged in, redirect to login page
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Initialize 'sort_by' session variable if not already set
if (!isset($_SESSION['sort_by'])) {
    $_SESSION['sort_by'] = "";
}

// Initialize 'selected_category_id' session variable if not already set, defaulting to 0 (All Categories)
if (!isset($_SESSION['selected_category_id'])) {
    $_SESSION['selected_category_id'] = 0;
}

// Include database configuration and establish a connection
require_once "../config/dbconfig.php";
$conn = getConnection();

// Determine the current script's directory path for URL construction
$current_script_path = pathinfo($_SERVER['SCRIPT_NAME'], PATHINFO_DIRNAME);
// Determine the protocol (HTTP or HTTPS) for URL construction
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
// Construct the base URL by combining protocol, host, and script path
$base_url = $protocol . $_SERVER['HTTP_HOST'] . $current_script_path . '/';

// Retrieve user ID from session to use in database queries
$user_id = $_SESSION['user_id'];

// Initialize an array to store the user's favorite recipes
$favoriteRecipes = [];

// Prepare a statement to call the stored procedure for fetching the user's favorite recipes
if ($stmt = $conn->prepare("CALL sp_get_user_favourite_recipes(?)")) {
    // Bind the user ID as a parameter to the statement
    $stmt->bind_param("i", $user_id);
    // Execute the prepared statement
    $stmt->execute();
    // Retrieve the result set from the statement
    $result = $stmt->get_result();
    // Fetch each row from the result set
    while ($row = $result->fetch_assoc()) {
        // Round the average rating of each recipe to the nearest whole number
        $row['average_rating'] = round($row['average_rating']);
        // Modify the picture URL to include the full path by prepending the base URL
        $row['picture_url'] = $base_url . ltrim($row['picture_url'], '/');
        // Add the modified row to the array of favorite recipes
        $favoriteRecipes[] = $row;
    }
    // Close the statement
    $stmt->close();
}

// Initialize variable to store user's full name, to be fetched from the database, to show on screen
$userFullName = '';

// Prepare a statement to call the stored procedure for fetching the user's full name
if ($stmt = $conn->prepare("CALL sp_get_user_full_name(?)")) {
    // Bind the user ID as a parameter to the statement
    $stmt->bind_param("i", $user_id);
    // Execute the prepared statement
    $stmt->execute();
    // Retrieve the result set from the statement
    $result = $stmt->get_result();
    // Fetch the row from the result set
    if ($row = $result->fetch_assoc()) {
        // Assign the full name from the row to the userFullName variable
        $userFullName = $row['full_name'];
    }
    // Close the statement
    $stmt->close();
}

// Close the database connection to free up resources
$conn->close();
?>

<!DOCTYPE html>
<!-- User page -->
<html lang="en">
  <!-- Head section -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User page</title>
    <!--link to show favicon int the browser url box-->
    <link rel="icon" type="image/x-icon" href="images/icons/favicon.ico">
</head>
<body>
<style>
    @import url("user_page.css");
</style>

	<header>
      <div id="logo">
        <a href="index.php"><img src="images/logo/logo.png" width="50" height="50" alt="FF logo"></a>
      </div>
      <nav>
        <ul>
          <?php if (isset($_SESSION['user_id'])): ?>
		  <div class="simpleSearch">
        <!-- Search bar in header with aria labels for accessibility-->
		  <form action="search_results.php" method="get" class="header-form">
      <input id="search-bar" type="text" placeholder="Search..." name="search" aria-label="Search">

                    <button id="search-button" type="submit">Search</button>
                </form>
			</div>
      <!--logic to show different items on the dropdown menu depending on whether the user is logged in-->
      <!-- list items with links to other pages-->
            <li><a href="search_results.php">All Recipes</a></li>
            <li class="dropdown">
              <a href="javascript:void(0)" class="dropbtn">
                <img src="images/icons/auth-icon.png" alt="authorization icon" width="30">
              </a>
              <div class="dropdown-content" id="myDropdown" aria-label="User Menu">
                <a href="logout.php">Log Out</a>
              </div>
            </li>
           <?php endif; ?>
        </ul>
      </nav>
    </header>


    <main>
        <div class="container">
            <section class="catalog">
<!-- User's name is displayed ensuring the user is logged in in their profile -->
                <header class="catalog-header">
                    <div class="catalog-title">
                        <h1>Hi, <?php echo htmlspecialchars($userFullName); ?>!</h1>
                        <h2>Your favorite recipes</h2>
                    </div>
                </header>
<!-- Here cards with chosen recipes are displayed -->
                <div class="catalog-content">
                    <ul class="goods-list">
                        <?php foreach ($favoriteRecipes as $recipe): ?>
                        <li class="goods-item">
                            <div class="product">
<!-- Photo of the dish -->
                                <div class="product-header">
                                    <img src="<?php echo htmlspecialchars($recipe['picture_url']); ?>" alt="<?php echo htmlspecialchars($recipe['title']); ?>">
                                </div>

                                <div class="product-content">
                                    <a href="recipe.php?id=<?php echo $recipe['recipe_id']; ?>" class="product-title"><?php echo htmlspecialchars($recipe['title']); ?></a>
<!-- Recipe's rating is displayed here -->
                                    <div class="product-btns">
                                        <div class="product-rating">
                                            <span><?php echo htmlspecialchars(round($recipe['average_rating'])); ?></span>
                                            <img src="images/user-page/star-icon.svg" alt="Rating">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </section>
        </div>
    </main>
                          <!-- Footer section -->
    <footer class="footer">

        <p>© 2024 Flavour Finds</p>
    </footer>
     <script>
        window.onload = function() {
            document.getElementById('search-input').focus();
        };
    </script>
</body>
</html>
