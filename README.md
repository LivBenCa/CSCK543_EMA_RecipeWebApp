# CSCK543_EMA_RecipeWebApp
Universtiy of Liverpool: CSCK543 Networks and Web Technology January 2024

# Description
* This project is a server-side web application, using PHP, HTML5/CSS and JavaScript targeting the most recent version of Google Chrome to design a recipe app. 
* It stores and accesses data in a MySQL relational database with server-side and client-side validation. The web application follows the latest accessibility 
recommendations and is in a responsive design. 
* Users are able to register, login, search, add/delete favorite recipes and rate recipes.
* Visit the [Recipe Web App]((https://flavour.naiva.co.za)) to explore our collection of recipes and start cooking today!


# Table of Contents
- [Installation](#installation)
- [Usage](#usage)
- [Contributing](#contributing)
- [License](#license)


# Installation

1. Download and Install XAMPP https://www.apachefriends.org/
2. Clone GitHub Repo from https://github.com/THarmse/End_of_Module_Assignment_Recipe_Web_App.git and place it here C:\xampp\htdocs\flavourfinds
3. Run Database/Scripts 1_Create_Database, 2_Create_Stored_Procedures, 3_Insert_Sample_Data, 4_Create_ServiceAccount
4. Start the Apache server via XAMPP control panel
5. Navigate to http://localhost/flavourfinds/website/login.php
6. Register a new account on the Website or log in using an existing Testing user: John Doe with Email john.doe@example.com and Password = Password1


# Usage

## 1. Landing page
- Upon opening the web app, users will land on the homepage where they can log in and browse through a variety of recipes.
- Users can search based on title or words in the description of a recipe and/or using specific categories.  
- Users can sort by recipe title, amount of people served or average rating. 
- Users can use the search bar in each page to find specific recipes, as available in the search_results.php, user_page.php and recipe.php pages. 

## 2. Viewing Recipe Details
- Clicking on a recipe title will take the user to the recipe details page.
- Here, users can view detailed information about the recipe, including ingredients, instructions, cooking time, serving size, and tip if available.

## 3. Interacting with Recipes
  - Users can interact with recipes in various ways:
    - They can save recipes to their profile for later reference.
    - They can rate recipes based on their experience.
    - Users can also share recipes via URL once on the selected recipe.

## 4. Accessibility and Responsive Design
- The web app is designed to be accessible to users of all abilities, with features such as keyboard navigation, alt text for images, and semantic HTML markup.
- The user interface is responsive and adapts to different screen sizes and devices, providing a seamless experience across desktop, tablet, and mobile platforms.

## 5. Enjoying the Culinary Journey!
- Finally, we encourage users to explore, cook, and share delicious recipes with friends and family, and to enjoy the culinary journey facilitated by our Recipe Web App.

## 6. Possile future features:
   
   a. Creating and Editing Recipes (for registered users)
    - To create a recipe with minimal to no typing, users can navigate to their profile page and access the "Create Recipe" feature.
    - Users can add new ingredients, instructions, cooking tips, and images to their recipes.
    - After creating a recipe, users can edit or delete it as needed.
    - Registered users have the ability to create and share their own recipes with the community.

   b. Managing Profile and Preferences
    - Users can create a profile to access additional features such as saving favorite recipes and viewing their recipe history.
    - Within their profile, users can update their personal information, change preferences (e.g., dietary restrictions), and manage saved recipes.

   c. Troubleshooting and Support
    - If users encounter any issues while using the web app or have suggestions for improvement, they can contact our support team via email or through the provided feedback form.
    - Common troubleshooting tips and FAQs are also available in the Help section of the web app.

# 7. Hosted Version of the Web App:
- The Web app is hosted and accessible from: https://flavour.naiva.co.za/
    -This Allows for accessing the site from anywhere
    -Enables the accessibility on tablets and mobile phones
    -Enables for the ability to verify the carbon footprint and accessibility with tools on the web

# Contributing
We welcome contributions from the community. To contribute to this project:

* Fork the repository.
* Create a new branch (git checkout -b feature/your-feature).
* Make your changes.
* Commit your changes (git commit -am 'Add some feature').
* Push to the branch (git push origin feature/your-feature).
* Create a new Pull Request.

# License

* This project is licensed under the MIT License - see the LICENSE file for details.
