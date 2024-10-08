// Function to toggle the display of the dropdown menu
function myFunction() {
    // Toggle the 'show' class on the dropdown menu to show or hide it
    document.getElementById("myDropdown").classList.toggle("show");
}

// Event listener for clicking outside the dropdown to close it
window.onclick = function (event) {
    // Check if the clicked area is not the dropdown button
    if (!event.target.matches('.dropbtn')) {
        // Get all elements with the class 'dropdown-content'
        var dropdowns = document.getElementsByClassName("dropdown-content");
        // Iterate through all dropdowns
        for (var i = 0; i < dropdowns.length; i++) {
            var openDropdown = dropdowns[i];
            // If dropdown is visible (has 'show' class), hide it
            if (openDropdown.classList.contains('show')) {
                openDropdown.classList.remove('show');
            }
        }
    }
}

// Function to toggle a recipe as favourite or not
function toggleFavourite(user_id, recipe_id) {
    // Get the favourite icon element
    var favouriteIcon = document.getElementById("favouriteIcon");
    // Determine if the recipe is currently marked as favourite
    var isFavourite = favouriteIcon.getAttribute("data-is-favourite") === "true";

    // Determine the action to take: add or remove from favourites
    var action = isFavourite ? 'remove' : 'add';
    // Create a new XMLHttpRequest object for the asynchronous request
    var xhr = new XMLHttpRequest();
    // Set up the request to 'favourite_action.php' with POST method
    xhr.open("POST", "favourite_action.php", true);
    // Set the content type header for sending data in the request body
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    // Define what happens on successful data submission
    xhr.onload = function () {
        // Check if the request was successful with status code 200
        if (this.status === 200) {
            // Get the element that displays favourite text
            var favouriteText = document.getElementById("favouritesText");
            // Update the icon and text based on the current action
            if (action === 'add') {
                favouriteIcon.src = "images/icons/heart.png"; // Change to filled heart icon
                favouriteText.innerText = "Remove from favourites"; // Update text
                favouriteIcon.setAttribute("data-is-favourite", "true"); // Update attribute to reflect new state
            } else {
                favouriteIcon.src = "images/icons/whiteheart.png"; // Change to empty heart icon
                favouriteText.innerText = "Add to favourites"; // Update text
                favouriteIcon.setAttribute("data-is-favourite", "false"); // Update attribute to reflect new state
            }
        } else {
            // Log an error if the request was not successful
            console.error("An error occurred during the AJAX request");
        }
    };
    // Send the request with the user_id, recipe_id, and action as data
    xhr.send("action=" + action + "&user_id=" + user_id + "&recipe_id=" + recipe_id);
}


// Function to update the user's rating for a recipe via an AJAX POST request
function updateUserRating(userId, recipeId, rating) {
    // Make an AJAX request using the Fetch API, specifying the method, headers, and body
    fetch('rating_action.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `userId=${userId}&recipeId=${recipeId}&rating=${rating}`
    })
        .then(response => response.json()) // Convert the response to JSON
        .then(data => {
            if (data.success) {
                // Log success if the rating update was successful
                console.log("Rating updated successfully");
            } else {
                // Log an error if the update failed
                console.error("Failed to update rating");
            }
        })
        .catch(error => console.error('Error updating rating:', error)); // Catch and log any errors that occur during the fetch
}

// Function to visually update the average rating display with stars
function updateAverageRatingDisplay(averageRating) {
    const averageRatingContainer = document.querySelector('.average-rating .star-rating');
    averageRatingContainer.innerHTML = ''; // Clear any existing stars from the container

    // Generate and append star icons based on the average rating
    for (let i = 1; i <= 5; i++) {
        if (i <= Math.floor(averageRating)) {
            // Append a full star for each whole number in the average rating
            averageRatingContainer.innerHTML += '<img class="star" src="images/icons/star.png" alt="Full Star">';
        } else if (i - 0.5 === averageRating) {
            // Append a half star if the average rating includes a half
            averageRatingContainer.innerHTML += '<img class="star" src="images/icons/halfstar.png" alt="Half Star">';
        } else {
            // Append an empty star for the remainder
            averageRatingContainer.innerHTML += '<img class="star" src="images/icons/emptystar.png" alt="Empty Star">';
        }
    }
}


// Execute when the DOM is fully loaded to ensure all elements are accessible
document.addEventListener('DOMContentLoaded', function () {
    // Retrieve the user and recipe IDs from the body's data attributes
    const userId = document.body.getAttribute('data-user-id');
    const recipeId = document.body.getAttribute('data-recipe-id');

    // Attempt to find the favourite icon in the document
    var favouriteIcon = document.getElementById("favouriteIcon");

    // If the favourite icon exists, set up a click event handler to toggle the favourite state
    if (favouriteIcon) {
        var user_id = favouriteIcon.getAttribute("data-user-id");
        var recipe_id = favouriteIcon.getAttribute("data-recipe-id");
        favouriteIcon.onclick = function () { toggleFavourite(user_id, recipe_id); };
    }

    // Attach click event listeners to each star for rating
    document.querySelectorAll('.rating-box .star').forEach(star => {
        star.addEventListener('click', function () {
            const rating = parseInt(this.getAttribute('data-star'));
            // Visually update the stars based on the user's selection
            updateUserRatingVisuals(rating);
            // Make a request to update the user's rating in the database
            updateUserRating(userId, recipeId, rating);
        });
    });


    // Visually update the star icons based on the user's rating
    function updateUserRatingVisuals(rating) {
        document.querySelectorAll('.rating-box .star').forEach((s, idx) => {
            // Set the star image based on whether the index is less than the rating
            if (idx < rating) {
                s.src = 'images/icons/star.png';
            } else {
                s.src = 'images/icons/emptystar.png';
            }
        });
    }

    // Function to make AJAX call to rating_action.php for updating the user's rating
    function updateUserRating(userId, recipeId, rating) {
        // Perform an AJAX request using the Fetch API to submit the user's rating
        fetch('rating_action.php', {
            method: 'POST', // Use the POST method for the request
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, // Set content type header for form data
            body: `userId=${userId}&recipeId=${recipeId}&rating=${rating}` // Send the userId, recipeId, and rating as request body
        })
            .then(response => response.json()) // Parse the JSON response from the server
            .then(data => {
                if (data.success) {
                    // Log a message to the console if the rating was successfully updated
                    console.log("Rating updated successfully");
                    // After updating the rating, make another AJAX call to retrieve the updated average rating
                    fetch('get_average_rating.php', {
                        method: 'POST', // Use POST method for the request
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, // Set content type header for form data
                        body: `recipeId=${recipeId}` // Send the recipeId to get its new average rating
                    })
                        .then(response => response.json()) // Parse the JSON response containing the new average rating
                        .then(data => {
                            if (data.success) {
                                // Update the display of the average rating on the page
                                updateAverageRatingDisplay(data.averageRating);
                            }
                        });
                } else {
                    // Log an error to the console if updating the rating failed
                    console.error("Failed to update rating: ", data.error);
                }
            })
            .catch(error => console.error('Error updating rating:', error)); // Catch and log any errors during the fetch operation
    }


    // Event listener for the sort-by select element
    document.getElementById('sort-by').addEventListener('change', function () {
        // Get the selected sorting option
        const sortBy = this.value;
        // Get the form element
        const form = document.querySelector('.header-form');
        // Update the form's action URL to include the selected sorting option
        form.action = updateQueryStringParameter(form.action, 'sort_by', sortBy);
        // Submit the form
        form.submit();
    });

    // Update or add a URL query string parameter
    function updateQueryStringParameter(uri, key, value) {
        // Regular expression to find the key in the URI
        const re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
        const separator = uri.indexOf('?') !== -1 ? "&" : "?";
        if (uri.match(re)) {
            // If the key exists, replace its value
            return uri.replace(re, '$1' + key + "=" + value + '$2');
        } else {
            // If the key does not exist, append it to the URI
            return uri + separator + key + "=" + value;
        }
    }


    // Check if the favourite icon element exists in the document
    var favouriteIcon = document.getElementById("favouriteIcon");
    // If the favourite icon is found
    if (favouriteIcon) {
        // Retrieve the user's ID from the data attribute of the favourite icon
        var user_id = favouriteIcon.getAttribute("data-user-id");
        // Retrieve the recipe's ID from the data attribute of the favourite icon
        var recipe_id = favouriteIcon.getAttribute("data-recipe-id");
        // Determine if the recipe is currently marked as a favourite by checking if the icon's source includes "heart.png"
        var isFavourite = favouriteIcon.src.includes("heart.png");
        // Assign an onclick event handler to the favourite icon
        favouriteIcon.onclick = function () {
            // When the favourite icon is clicked, toggle the favourite status of the recipe
            // Pass the user's ID, recipe's ID, and the current favourite status to the function
            toggleFavourite(user_id, recipe_id, isFavourite);
        };
    }

});
