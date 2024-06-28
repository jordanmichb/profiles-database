<?php
    require_once 'pdo.php';
    require_once 'util.php';
    require_once 'head.php';
    
    session_start();

    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
    ** Validate input. Redirect if invalid, otherwise add to database
    * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
    if (isset($_POST['login'])) { // If submit is pressed
        // Validate input
        if ($_POST['email'] == "" || $_POST['pass'] == "") {
            $_SESSION['error'] = 'Email and password are required';
            header('Location: login.php');
            return;
        }
        if (strpos($_POST['email'], '@') === false) {
            $_SESSION['error'] = 'Invalid email address';
            header('Location: login.php');
            return;
        }
        // Check that email and password are correct and log in if so
        $row = checkCredentials($pdo);
        if ($row) {
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['name'] = $row['name'];
            header('Location: index.php');
            return;
        } else {
            $_SESSION['error'] = 'Incorrect email or password';
            header('Location: login.php');
            return;
        }
    } 

    function checkCredentials($pdo) {
        $salt = 'XyZzy12*_';
        $check = hash('md5', $salt . $_POST['pass']); // Concat salt and input and create a hash
        $stmt = $pdo->prepare('SELECT user_id, `name` FROM users WHERE email=:em AND password=:pw'); // Try to get user with that hash
        $stmt->execute(array(':em' => $_POST['email'], ':pw'=>$check));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row; // If user found, returns true otherwise returns false
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Jordan Ballard Resume Registry Login</title>
    </head>
    <body>
        <header>
            <h1>Please Log In</h1>
        </header>
        <?php 
            flashMessages(); // Check for error or success messages
        ?>
        <form method="post">
            <label for="email">Email:</label>
            <input type="text" id="email" name="email">
            <label for="pass">Password:</label>
            <input type="password" id="pass" name="pass">
            <button class="form-action" type="submit" name="login" onclick="return validateLogin()">Log In</button>
        </form>
    </body>
</html>