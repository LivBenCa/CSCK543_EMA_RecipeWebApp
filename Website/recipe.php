<?php
// Start or resume the session, crucial for tracking logged-in users
session_start();

// Include the database configuration file for database connection setup
require_once "../config/dbconfig.php";

// Check if the user is already logged in by looking for a user ID in the session
if(isset($_SESSION['user_id'])) {
    // If logged in, retrieve the user's ID from the session
    $user_id = $_SESSION['user_id'];
} else {
    // If not logged in, redirect the user to the login page
    header('Location: login.php');
    exit;
}

// Check if the user is logged in by verifying the presence of a user ID in the session
$userLoggedIn = isset($_SESSION['user_id']);

// Initialize arrays to store details, ingredients, steps, and tips of a recipe
$recipeDetails = [];
$ingredients = [];
$steps = [];
$tips = [];

// Check if the recipe ID is present in the URL query string and is a valid number
if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
// Convert the recipe ID from the query string into an integer
    $recipeId = intval($_GET["id"]);

    // Establish a connection to the database
    $conn = getConnection();

    // Calculate the current script's directory path for URL construction
$current_script_path = pathinfo($_SERVER['SCRIPT_NAME'], PATHINFO_DIRNAME);
// Determine the protocol (HTTP or HTTPS) for the base URL
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
// Construct the base URL using the protocol, host, and script path
    $base_url = $protocol . $_SERVER['HTTP_HOST'] . $current_script_path . '/';

    // Execute a query to fetch the recipe's detailed information
    if ($stmt = $conn->prepare("CALL sp_get_recipe_details(?)")) {
        // Bind the recipe ID as a parameter to the query
        $stmt->bind_param("i", $recipeId);
        // Execute the query
        $stmt->execute();
// Retrieve the query results
        $result = $stmt->get_result();
// Fetch the recipe details as an associative array
        $recipeDetails = $result->fetch_assoc();
// Close the statement
        $stmt->close();
    }

    // Retrieve recipe ingredients using a stored procedure
    if ($stmt = $conn->prepare("CALL sp_get_recipe_ingredients(?)")) {
// Bind the recipe ID as an integer parameter to the query
        $stmt->bind_param("i", $recipeId);
// Execute the prepared statement
        $stmt->execute();
// Fetch the result set from the statement
        $result = $stmt->get_result();
// Loop through each row of the result set
        while ($row = $result->fetch_assoc()) {
// Add each ingredient to the ingredients array
            $ingredients[] = $row;
        }
// Close the prepared statement
        $stmt->close();
    }

    // Retrieve cooking steps for the recipe
    if ($stmt = $conn->prepare("CALL sp_get_recipe_steps(?)")) {
// Bind the recipe ID to the prepared statement
        $stmt->bind_param("i", $recipeId);
// Execute the statement
        $stmt->execute();
// Get the result set
        $result = $stmt->get_result();
// Iterate over each row in the result set
        while ($row = $result->fetch_assoc()) {
// Add each step to the steps array
            $steps[] = $row;
        }
// Close the statement to free up resources
        $stmt->close();
    }

    // Retrieve tips related to the recipe
    if ($stmt = $conn->prepare("CALL sp_get_recipe_tips(?)")) {
// Bind the recipe ID to the statement
        $stmt->bind_param("i", $recipeId);
// Execute the statement
        $stmt->execute();
// Collect the result set
        $result = $stmt->get_result();
// Loop through each row in the result set
        while ($row = $result->fetch_assoc()) {
// Append each tip to the tips array
            $tips[] = $row;
        }
// Close the prepared statement
        $stmt->close();
    }

    // Determine if the recipe is a user's favourite
    $isFavourite = false;
// Only proceed if both user ID and recipe ID are valid
    if ($user_id && $recipeId) {
        // Prepare the stored procedure call
        if ($stmt = $conn->prepare("CALL sp_get_user_favourite_recipe(?, ?)")) {
// Bind user ID and recipe ID to the statement
            $stmt->bind_param("ii", $user_id, $recipeId);
// Execute the statement
            $stmt->execute();
// Get the result set
            $result = $stmt->get_result();
// Check if any rows are returned
            if ($result->num_rows > 0) {
// If yes, the recipe is a favourite
                $isFavourite = true;
            }
// Close the statement
            $stmt->close();
        }
    }

    // Calculate the average rating for the recipe
    $averageRating = 0;
if ($stmt = $conn->prepare("CALL sp_get_average_rating(?)")) {
        // Bind the recipe ID to the statement
        $stmt->bind_param("i", $recipeId);
        // Execute the query
        $stmt->execute();
// Retrieve the result set
        $result = $stmt->get_result();
// Check if a row is returned
        if ($row = $result->fetch_assoc()) {
// Update the average rating based on the fetched value
            $averageRating = $row["average_rating"];
        }
// Close the statement
        $stmt->close();
    }
    // Round the average rating to the nearest half for display
    $averageRating = round($averageRating * 2) / 2;

// Initialize the variable to store the current user's rating
    $currentRating = 0;
    // Check if user ID and recipe ID are valid
    if ($user_id && $recipeId) {
        // Prepare the statement to retrieve the user's rating for the recipe
        if ($stmt = $conn->prepare("CALL sp_get_user_rating(?, ?)")) {
// Bind user ID and recipe ID to the statement
            $stmt->bind_param("ii", $user_id, $recipeId);
// Execute the statement
            $stmt->execute();
// Retrieve the result
            $result = $stmt->get_result();
// If a row is returned, update the current rating
            if ($row = $result->fetch_assoc()) {
                $currentRating = $row['rating'];
            }
// Close the statement
            $stmt->close();
        }
    }
    // Close the database connection to free up resources
    $conn->close();
} else {
// Inform the user if the recipe ID is not valid or missing
    echo "Invalid recipe ID.";
    // Exit the script to prevent further execution
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recipe App</title>
    <!--link to show favicon int the browser url box-->
<link rel="icon" type="image/x-icon" href="images/icons/favicon.ico">
    <!--link to style sheet-->
    <link rel="stylesheet" href="StylesheetRecipeRegisterLogin.css">
   </head>
<body data-user-id="<?= $user_id ?>" data-recipe-id="<?= $recipeId ?>">
<!--header element-->
  <header>
    <!--logo element-->
    <div id="logo">
      <!--link to logo image with alt text for screen readers-->
      <a href="index.php"><img src="images/logo/logo.png" width="50" height="50" alt="FF logo"></a>
    </div>

    <!--search bar in header-->
    <?php if (isset($_SESSION['user_id'])): ?>
    <div class="simpleSearch">
      <!--header search bar with aria label for accessibility-->
    <form id="search-form" action="search_results.php" role="search">
			<input id="search-bar" type="text" placeholder="Search..." name="search" aria-label="Search">

      <button type="submit">Search</button>
      </form>
    </div>

    <nav>    <!--logic to show different items on the dropdown menu depending on whether the user is logged in-->
    <!--nav items organised as an unordered list-->
      <ul>
          <!-- list items with links to other pages-->
            <li><a href="search_results.php">All Recipes</a></li>
        <li class="dropdown">
            <a href="javascript:void(0)" class="dropbtn">
                <img src="images/icons/auth-icon.png" alt="authorization icon" width="30">
            </a>
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
      <!-- Sets a container for the main content -->
  <div class="container">
    <!-- The main content of the page -->
      <div class="main">
        <!-- The recipe image: -->
        <div class="recipe-image">

            <img class="image-link" src="<?php echo $base_url .
                htmlspecialchars($recipeDetails["picture_url"]); ?>" alt="<?php echo htmlspecialchars($recipeDetails["title"]); ?>">

        </div>
        <!-- The recipe info rating: -->
         <section class="recipe-info">
            <h2 class="title"><?php echo htmlspecialchars(
                $recipeDetails["title"] ?? "Recipe Title"
            ); ?></h2>
            <div class="favourites" id="favouritesDiv">
              <!-- heart icon -->
                <img src="images/icons/<?php echo $isFavourite ? 'heart' : 'whiteheart'; ?>.png" alt="heart icon" id="favouriteIcon" data-user-id="<?php echo $_SESSION['user_id']; ?>" data-recipe-id="<?php echo $recipeId; ?>" data-is-favourite="<?php echo $isFavourite ? 'true' : 'false'; ?>" style="cursor:pointer;">
                <p id="favouritesText"><?php echo $isFavourite ? 'Remove from favourites' : 'Add to favourites'; ?></p>
            </div>
            <!-- in case no data is found, a messsage saying no data is avaialble -->
            <h5 class="description"><?php echo htmlspecialchars(
                $recipeDetails["description"] ??
                    "Recipe description not available."
            ); ?></h5>
            <!-- categories -->
            <h5 class="categories"><?php echo htmlspecialchars(
                implode(", ", $recipeDetails["categories"] ?? [])
            ); ?></h5>
            <div class="time-people">
                <div class="time">
                    <img src="images/icons/clock.png" alt="clock icon"> <!-- clock icon -->
                    <p class="num-minutes"><?php echo htmlspecialchars(
                        $recipeDetails["preparation_time"] ?? "0"
                    ); ?> </p>
                </div>
                <div class="people">
                    <img src="images/icons/man.png" alt="man icon"><!-- person icon -->
                    <p class="num-people"><?php echo htmlspecialchars(
                        $recipeDetails["nr_served"] ?? "N/A"
                    ); ?> people</p>
                </div>
              </div>

                    <!-- The average rating: -->
              <div class="average-rating">
                  <h2>Average Rating</h2>
                  <p class="star-rating">
                        <?php
                        // Display filled stars
                        for ($i = 0; $i < floor($averageRating); $i++) {
                            echo '<img class="star" src="images/icons/star.png" alt="Full Star">';
                        }
                        // Display half star where applicable
                        if (floor($averageRating) < $averageRating) {
                            echo '<img class="star" src="images/icons/halfstar.png" alt="Half Star">';
                            $i++; // Increment to avoid an extra empty star
                        }
                        // Display empty stars
                        for ($i; $i < 5; $i++) {
                            echo '<img class="star" src="images/icons/emptystar.png" alt="Empty Star">';
                        }
                        ?>
                  </p>
              </div>
                      </section>
                      <!-- The ingredients section: -->
            <section class="ingredients">
            <h2>Ingredients</h2>
            <?php
            // Organizing the ingredients by section.
            $ingredientsBySection = [];
            foreach ($ingredients as $ingredient) {
                $section = $ingredient["ingredient_section"] ?: "Main"; // Default to 'Main' if no ingredient section is available in db
                $ingredientsBySection[$section][] = $ingredient;
            }

            // Looping through each section to display its ingredients.
            foreach ($ingredientsBySection as $section => $ingredientsList) {
                // Display the section heading
                echo "<h3>" .
                    htmlspecialchars($section === "Main" ? "" : $section) .
                    "</h3>";
                echo "<ul>";
                foreach ($ingredientsList as $ingredient) {
                    echo "<li>" .
                        htmlspecialchars($ingredient["full_sentence"]) .
                        "</li>";
                }
                echo "</ul>";
            }
            ?>
        </section>


            <!-- The method section, steps taken from the database via a php script -->
         <section class="method">
            <h2>Method</h2>
            <ol class="steps">
                <?php foreach ($steps as $step): ?>
                    <li><?php echo htmlspecialchars(
                        $step["step_description"]
                    ); ?>
                        <?php if (
                            isset($step["minutes_needed"]) &&
                            $step["minutes_needed"] > 0
                        ): ?>
                            - Approx. <?php echo htmlspecialchars(
                                $step["minutes_needed"]
                            ); ?> mins
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ol>
                          </section>

    <!-- The rating section: -->
    <section class="rating-box">
              <h2>Rate this recipe!</h2>
              <p class="star-rating" id="userRating">
          <?php for($i = 1; $i <= 5; $i++): ?>
            <!-- user clicks on the amount of stars they would like to rate the recipe-->
          <img src="images/icons/<?= $i <= $currentRating ? 'star' : 'emptystar'; ?>.png" class="star" data-star="<?= $i ?>" alt="<?= $i ?> Star" style="cursor:pointer;">
                <?php endfor; ?>
            </p>
        </section>

        <section class="tips">
            <h2>Tips</h2>
            <!-- If there are no tips to display, the following message will be displayed: -->
            <?php if (empty($tips)): ?>
          <p>There are no tips to display!</p>
           <?php else: ?>
            <ul>
               <!-- Tips are drawn from the database via a php script -->
                <?php foreach ($tips as $tip): ?>
                    <li><?php echo htmlspecialchars(
                        $tip["tip_description"]
                    ); ?></li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>
                  </section>
      </div>

      <!-- footer - same on each page -->
      <footer class="footer">

        <p>© 2024 Flavour Finds</p>
    </footer>
  </div>
<!-- link to javascript page -->
  <script src="main.js"></script>
<script>
        window.onload = function() {
            document.getElementById('search-bar').focus();
        };
   </script>
</body>
</html>
