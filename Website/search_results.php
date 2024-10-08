<?php
// Start session to manage user state
session_start();

// Include database configuration for connection
require_once "../config/dbconfig.php";
// Include script to fetch categories for dropdown menu
require_once "get_categories.php";

// Check if user is logged in, redirect to login page if not
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Establish database connection
$conn = getConnection();

// Compute current script path for URL construction
$current_script_path = pathinfo($_SERVER['SCRIPT_NAME'], PATHINFO_DIRNAME);

// Determine protocol (HTTP or HTTPS) for URL construction
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';

// Construct base URL for resource paths
$base_url = $protocol . $_SERVER['HTTP_HOST'] . $current_script_path . '/';

// Sanitize and assign search term and category ID from GET request
$searchTerm = filter_input(INPUT_GET, "search", FILTER_SANITIZE_STRING);
$categoryId = filter_input(INPUT_GET, "category_id", FILTER_SANITIZE_NUMBER_INT);

// Store 'sort_by' parameter from GET request in session for persistence across pages
if (isset($_GET['sort_by'])) {
    $_SESSION['sort_by'] = $_GET['sort_by'];
}

// Retrieve 'sort_by' option from session or default to null if not set
$sortBy = isset($_SESSION['sort_by']) ? $_SESSION['sort_by'] : null;

// Determine last selected category for dropdown persistence, default to session value or 0
$selectedCategoryId = isset($_GET["category_id"]) ? filter_input(INPUT_GET, "category_id", FILTER_SANITIZE_NUMBER_INT) : (isset($_SESSION['selected_category_id']) ? $_SESSION['selected_category_id'] : 0);

// Update session with last selected category ID for persistence
$_SESSION['selected_category_id'] = $selectedCategoryId;

// Prepare category options HTML, dynamically marking the selected option based on the last search
$category_options = ""; // Initialize HTML string to hold category options
// Check if categories array is set and not empty
if (isset($categories) && !empty($categories)) {
    // Iterate through each category to build the option elements
    foreach ($categories as $category) {
        // Determine if the current category should be marked as selected after last search
        $selectedAttr = ($category['category_id'] == $selectedCategoryId) ? "selected" : "";
        // Append the option element to the category_options string, ensuring the category name is properly escaped
        $category_options .= "<option value='" . $category['category_id'] . "' " . $selectedAttr . ">" . htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8') . "</option>";
    }
} else {
    // Provide a default option in case there are no categories available
    $category_options = "<option value=''>No categories available</option>";
}

// Fetch and prepare recipes data, including processing of picture URLs
$recipes = []; // Initialize array to store recipe details
// Prepare a database statement to execute the stored procedure for fetching recipes
if ($stmt = $conn->prepare("CALL sp_get_recipes(?, ?, ?)")) {
    // Bind the input parameters (search term, category ID, and sort option) to the prepared statement
    $stmt->bind_param("sss", $searchTerm, $categoryId, $sortBy);
    // Execute the prepared statement
    $stmt->execute();
    // Retrieve the result set from the executed statement
    $result = $stmt->get_result();
    // Iterate through each row in the result set
    while ($row = $result->fetch_assoc()) {
        // Modify the picture URL to include the full path, using the base URL as prefix
        $row['picture_url'] = $base_url . ltrim($row['picture_url'], '/');
        // Add the modified recipe row to the recipes array
        $recipes[] = $row;
    }
    // Close the statement
    $stmt->close();
}

// Close the database connection to ensure resources are released
$conn->close();
?>


<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search page</title>
    <link rel="icon" type="image/x-icon" href="images/icons/favicon.ico">
</head>
<body>
<style>
    @import url("search_results.css");
</style>
<header>
    <div id="logo">
      <a href="index.php"><img src="images/logo/logo.png" width="50" height="50" alt="FF logo"></a>
    </div>

    <!--search bar in header-->
    <!-- <div class="simpleSearch">
    <form id="search-form" action="search_results.php" role="search">
			<input id="search-bar" type="text" placeholder="What do you want to eat today?" name="search" aria-label="Search">
      <button type="submit">Search</button>
      </form>
    </div> -->

    <nav>    <!--logic to show different items on the dropdown menu depending on whether the user is logged in-->
      <ul>
        <?php if (isset($_SESSION['user_id'])): ?>
            <li><a href="search_results.php">All Recipes</a></li>
        <li class="dropdown">
            <a href="javascript:void(0)" class="dropbtn">
                <img src="images/icons/auth-icon.png" alt="authorization icon" width="30">
            </a>
<!-- Dropdown menu items -->
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
        <div class="container">
            <section class="catalog">

                    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="get" class="header-form">
                    <input type="search" name="search" placeholder="What do you want to eat today?" id="search-input" value="<?php echo htmlspecialchars($searchTerm); ?>">
<!-- Select recipe by category -->
                  <div class="search-filter">
                    <div class="search-submit">
                      <select name="category_id" id="category-filter">
                          <option value="0" <?php echo $selectedCategoryId == 0 ? "selected" : ""; ?>>All Categories</option>
                          <?php echo isset($category_options) ? $category_options : ''; ?>
                      </select>
<!-- Search button to submit search -->
                      <button id="search-button" type="submit">Search</button>
                    </div>
<!-- Sort recipes -->
                   <select name="sort_by" id="sort-by">
                        <option value="">Sort By</option>
                        <option value="title_asc" <?php echo $sortBy == "title_asc" ? "selected" : ""; ?>>Title (ASC)</option>
                        <option value="title_desc" <?php echo $sortBy == "title_desc" ? "selected" : ""; ?>>Title (DESC)</option>
                        <option value="nr_served_asc" <?php echo $sortBy == "nr_served_asc" ? "selected" : ""; ?>>Nr Served (ASC)</option>
                        <option value="nr_served_desc" <?php echo $sortBy == "nr_served_desc" ? "selected" : ""; ?>>Nr Served (DESC)</option>
                        <option value="average_rating_asc" <?php echo $sortBy == "average_rating_asc" ? "selected" : ""; ?>>Average Rating (ASC)</option>
                        <option value="average_rating_desc" <?php echo $sortBy == "average_rating_desc" ? "selected" : ""; ?>>Average Rating (DESC)</option>
                    </select>
                 </form>
                </div>
                <br/>
<!-- Found recipes are displayed here -->
                <div class="catalog-content">
                    <ul class="goods-list">
                        <?php foreach ($recipes as $recipe): ?>
                        <li class="goods-item">
                            <div class="product">
                                <div class="product-header">
                                    <img src="<?php echo htmlspecialchars($recipe['picture_url']); ?>" alt="<?php echo htmlspecialchars($recipe['title']); ?>">
                                </div>

                                <div class="product-content">
                                    <a href="recipe.php?id=<?php echo $recipe['recipe_id']; ?>" class="product-title"><?php echo htmlspecialchars($recipe['title']); ?></a>
<!-- Recipe's rating -->
                                    <div class="product-btns">
                                        <div class="product-rating">
                                            <span><?php echo htmlspecialchars(round($recipe['average_rating'] * 2) / 2); ?></span>
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

    <footer class="footer">
        
        <p>© 2024 Flavour Finds</p>
    </footer>
    <script src="main.js"></script>
    <script>
        window.onload = function() {
            document.getElementById('search-input').focus();
        };
    </script>
</body>
</html>
