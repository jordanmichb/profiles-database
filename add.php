<?php
    require_once 'pdo.php';
    require_once 'util.php';
    require_once 'head.php';

    session_start();

    // Cannot use this page if not logged in
    if (!isset($_SESSION['user_id'])) {
        die('Not logged in');
        return;
    }
    // Cancel button is pressed
    if (isset($_POST['cancel'])) {
        header('Location: index.php');
        return;
    }


    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
    ** If an input error causes a redirect, retain old input values
    * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
    $add_data = array(
        'first_name' => '',
        'last_name' => '',
        'email' => '',
        'headline' => '',
        'summary' => '',
    );

    if (isset($_SESSION['add_data'])) {
        $add_data['first_name'] = $_SESSION['add_data']['first_name'];
        $add_data['last_name'] = $_SESSION['add_data']['last_name'];
        $add_data['email'] = $_SESSION['add_data']['email'];
        $add_data['headline'] = $_SESSION['add_data']['headline'];
        $add_data['summary'] = $_SESSION['add_data']['summary'];
        unset($_SESSION['add_data']);
    }


    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
    ** Validate input. Redirect if invalid, otherwise add to database
    * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
    if (isset($_POST['add'])) { // Add button is pressed
        // Validate input, function from util.php
        $msg = validateProfile();
        if ($msg !== true) {
                $_SESSION['add_data'] = $_POST;
                $_SESSION['error'] = $msg;
                header('Location: add.php');
                return;
        }

        // Validation passed, add new entry and redirect back to index.php
        $stmt = $pdo->prepare('INSERT INTO `profile` 
            (user_id, first_name, last_name, email, headline, summary)
            VALUES (:id, :fn, :ln, :em, :he, :su)');
        $stmt->execute(array(
            ':id' => $_SESSION['user_id'],
            ':fn' => $_POST['first_name'],
            ':ln' => $_POST['last_name'],
            ':em' => $_POST['email'],
            ':he' => $_POST['headline'],
            ':su' => $_POST['summary']
        ));
        // Insert position entries
        $profile_id = $pdo->lastInsertId();
        // Add education and positions, function from util.php
        addRemainingFields($pdo, $profile_id);

        $_SESSION['success'] = 'Profile added';
        unset($_SESSION['add_data']);
        header('Location: index.php');
        return;
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Jordan Ballard Resume Registry Profile Add</title>
    </head>
    <body>
        <header>
            <h1>Adding Profile</h1>
        </header>
        <?php 
            flashMessages(); // Check for error or success messages
        ?>
        <form class="add_form" method="post">
            <label for="first_name">First Name:</label>
            <input type="text" id="first_name" name="first_name" value=<?= htmlentities($add_data['first_name']) ?>>
            <label for="last_name">Last Name:</label>
            <input type="text" id="last_name" name="last_name" value=<?= htmlentities($add_data['last_name']) ?>>
            <label for="email">Email:</label>
            <input type="text" id="email" name="email" value=<?= htmlentities($add_data['email']) ?>>
            <label for="headline">Headline:</label>
            <input type="text" id="headline" name="headline" value=<?= htmlentities($add_data['headline']) ?>>
            <label for="summary">Summary:</label>
            <textarea id="summary" name="summary" rows="6"><?= htmlentities($add_data['summary']) ?></textarea>
            <p class="edu_header">Education: <button class="add_edu" type="button">+</button></p>
            <div class="educations"></div>
            <p class="pos_header">Positions: <button class="add_pos" type="button">+</button></p>
            <div class="positions"></div>
            <div class="form_action">
                <button type="submit" name="add">Add</button>
                <button type="submit" name="cancel">Cancel</button>
            </div>
        </form>
    </body>
</html>