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
    // Cannot enter page without GET param
    if (!isset($_GET['profile_id'])) {
        $_SESSION['error'] = 'Missing profile_id';
        header("Location: index.php");
        return;
    }
    // Cancel button is pressed
    if (isset($_POST['cancel'])) {
        header('Location: index.php');
        return;
    }


    /* * * * * * * * * * * * * * * * * * * * * * *
    ** Get clicked user's info to place into view
    * * * * * * * * * * * * * * * * * * * * * * */
    // Profile info
    $stmt = $pdo->prepare('SELECT profile_id, first_name, last_name, email, headline, summary 
        FROM `profile` WHERE profile_id=:id');

    $stmt->execute(array(':id' => $_GET['profile_id']));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    // Cannot enter page with invalid id
    if ($row === false) {
        $_SESSION["error"] = "Profile does not exist";
        header("Location: index.php");
        return;
    }

    // Education info
    $stmt = $pdo->prepare('SELECT `year`, rank, `name` FROM education
        JOIN institution ON education.institution_id = institution.institution_id
        WHERE profile_id = :id ORDER BY rank');

    $stmt->execute(array(':id' => $_GET['profile_id']));
    $educations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Position info
    $stmt = $pdo->prepare('SELECT rank, `year`, `description` FROM position 
        WHERE profile_id=:id ORDER BY rank');

    $stmt->execute(array(':id' => $_GET['profile_id']));
    $positions = $stmt->fetchAll(PDO::FETCH_ASSOC);



    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
    ** Validate input. Redirect if invalid, otherwise update database
    * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
    if (isset($_POST['save'])) { // If save button is pressed
        // Validate input, function form util.php
        $msg = validateProfile();
        if ($msg !== true) {
                $_SESSION['error'] = $msg;
                header('Location: edit.php?profile_id=' . $_POST['profile_id']);
                return;
        }

        // Validation passed, update the user and redirect back to index.php
        $stmt = $pdo->prepare('UPDATE `profile` SET
            first_name = :fn, last_name = :ln, email = :em, headline = :he, summary = :su
            WHERE profile_id = :pid');
        $stmt->execute(array(
            ':fn' => $_POST['first_name'],
            ':ln' => $_POST['last_name'],
            ':em' => $_POST['email'],
            ':he' => $_POST['headline'],
            ':su' => $_POST['summary'],
            ':pid' => $_POST['profile_id']
        ));

        // Remove current position/education fields and re-add them
        $stmt = $pdo->prepare('DELETE FROM position WHERE profile_id = :pid');
        $stmt->execute(array(':pid' => $_POST['profile_id']));

        $stmt = $pdo->prepare('DELETE FROM education WHERE profile_id = :pid');
        $stmt->execute(array(':pid' => $_POST['profile_id']));
        // Add education and positions, function from util.php
        addRemainingFields($pdo, $_POST['profile_id']);

        $_SESSION['success'] = 'Profile updated';
        header('Location: index.php');
        return;
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Jordan Ballard Resume Registry Profile Edit</title>
    </head>
    <body>
        <header>
            <h1>Editing Profile</h1>
        </header>
        <?php 
            flashMessages(); // Check for error or success messages
        ?>
        <form method="post">
            <label for="first_name">First Name:</label>
            <input type="text" id="first_name" name="first_name" value=<?= htmlentities($row['first_name']) ?>>
            <label for="last_name">Last Name:</label>
            <input type="text" id="last_name" name="last_name" value=<?= htmlentities($row['last_name']) ?>>
            <label for="email">Email:</label>
            <input type="text" id="email" name="email" value=<?= htmlentities($row['email']) ?>>
            <label for="headline">Headline:</label>
            <input type="text" id="headline" name="headline" value="<?= htmlentities($row['headline']) ?>">
            <label for="summary">Summary:</label>
            <textarea id="summary" name="summary" rows="6"><?= htmlentities($row['summary']) ?></textarea>
            <p class="edu_header">Education: <button class="add_edu" type="button">+</button></p>
            <div class="educations"><?php
                foreach ($educations as $edu) {
                    echo '<div class="education" id="education' . $edu['rank'] . '">';
                    echo '<label for="edu_year' . $edu['rank'] . '">Year: </label>';
                    echo '<input value="' . htmlentities($edu['year']) . '" type="text" id="edu_year' . $edu['rank'] . '" name="edu_year' . $edu['rank'] . '">';
                    echo '<label for="edu_school' . $edu['rank'] . '">School: </label>';
                    echo '<input type="text" id="edu_school' . $edu['rank'] . '" name="edu_school' . $edu['rank'] . '" class="school" value="' . htmlentities($edu['name']) .'">';
                    echo '<button id="del_edu' . $edu['rank'] . '" class="del_edu" type="button">Delete</button>';
                    //echo '<input type="hidden" name="position_id' . $pos['rank'] . '" value="' . $pos["position_id"] . '">';
                    echo '</div>';
                }
            ?></div>
            <p class="pos_header">Positions: <button class="add_pos" type="button">+</button></p>
            <div class="positions"><?php
                foreach ($positions as $pos) {
                    echo '<div class="position" id="position' . $pos['rank'] . '">';
                    echo '<label for="pos_year' . $pos['rank'] . '">Year: </label>';
                    echo '<input value="' . htmlentities($pos['year']) . '" type="text" id="pos_year' . $pos['rank'] . '" name="pos_year' . $pos['rank'] . '">';
                    echo '<label for="pos_desc' . $pos['rank'] . '">Description: </label>';
                    echo '<textarea id="pos_desc' . $pos['rank'] . '" name="pos_desc' . $pos['rank'] . '" rows="6">' . htmlentities($pos['description']) .'</textarea>';
                    echo '<button id="del_pos' . $pos['rank'] . '" class="del_pos" type="button">Delete</button>';
                    //echo '<input type="hidden" name="position_id' . $pos['rank'] . '" value="' . $pos["position_id"] . '">';
                    echo '</div>';
                }
            ?></div>
            <!-- Hidden input so id can be sent with post -->
            <input type="hidden" name="profile_id" value="<?= $row["profile_id"] ?>">
            <div class="form_action">
                <button type="submit" name="save">Save</button>
                <button type="submit" name="cancel">Cancel</button>
            </div>
        </form>
    </body>
</html>