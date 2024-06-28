# University of Michigan via Coursera - JavaScript, jQuery, and JSON

## Profiles Database - Using jQuery, JSON, and CRUD

### Assignment Specifications

Tools used: PHP, SQL, jQuery, VSCode, MAMP, phpMyAdmin

The database for this assignment contains many-to-many and one-to-many relationships.

You must use the PHP PDO database layer for this assignment. Your program must be resistant to both HTML and SQL Injection attempts.

Please do not use HTML5 in-browser data validation (i.e. type="number") for the fields in this assignment as we want to make sure you can properly do server side data validation. And in general, even when you do client-side data validation, you should still validate data on the server in case the user is using a non-HTML5 browser.

You will need to create a database, a user to connect to the database and a password for that user. You will need to make a connection to that database in a PHP file.

When typing in a school, an autocomplete list should show up. It checks the database for previously entered schools and shows them in a list.

index.php - Will present a list of all profiles in the system with a link to a detailed view with view.php whether or not you are logged in. If you are not logged in, you will be given a link to login.php. If you are logged in you will see a link to add.php add a new resume and links to delete or edit any resumes that are owned by the logged in user.

login.php - will present the user the login screen with an email address and password to get the user to log in. If there is an error, redirect the user back to the login page with a message. If the login is successful, redirect the user back to index.php after setting up the session. In this assignment, you will need to store the user's hashed password in the users table as described below.

logout.php - Will log the user out by clearing data in the session and redirecting back to index.php.

add.php - Add a new Profile entry. You will need to have a section where the user can press a "+" button to add up to nine empty education entries and up to nine position entries.

edit.php - Edit an exsiting entry in the database. Will support the addition of new position or education entries, the deletion of any or all of the existing entries, and the modification of any of the existing entries. After the "Save" is done, the data in the database should match whatever positions and education entries were on the screen and in the same order as the positions on the screen.

view.php - Show the detail for a particular entry. This works even is the user is not logged in. 

delete.php delete an entry from the database. Do not do the delete in a GET - you must put up a verification screen and do the actual delete in a POST request, after which you redirect back to index.php with a success message. Before you do the delete, make sure the user is logged in, that the entry actually exists, and that the current logged in user owns the entry in the database. 



The script must redirect after every POST. It must never produce HTML output as a result of a POST operation. With a successful login, login.php must redirect to index.php and must pass the logged in user's name through the session. A GET parameter is not allowed.

All error messages must be passed between the POST and GET using the session and "flash message" pattern. 

In order to protect the database from being modified without the user properly logging in, on each page you must first check the session to see if the user's name is set and if the user's name is not set in the session the they must stop immediately using the PHP die() function.

In view.php if the Logout button is pressed the user should be redirected back to the logout.php page. The logout.php page should clear the session and immediately redirect back to index.php.
