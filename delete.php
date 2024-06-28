<?php
    require_once 'pdo.php';
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
    $stmt = $pdo->prepare('SELECT profile_id, first_name, last_name FROM `profile` WHERE profile_id = :pid');
    $stmt->execute(array(':pid' => $_GET['profile_id']));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    // Cannot enter page with invalid id
    if ($row === false) {
        $_SESSION["error"] = "Profile does not exist";
        header("Location: index.php");
        return;
    }

    
    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
    ** Delete is pressed, delete user and redirect back to index.php
    * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
    if (isset($_POST['delete'])) {
        $stmt = $pdo->prepare('DELETE FROM `profile` WHERE profile_id = :pid');
        $stmt->execute(array(':pid' => $_POST['profile_id']));
        $_SESSION["success"] = "Profile deleted";
        header("Location: index.php");
        return;
    }

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Jordan Ballard Resume Registry Profile Delete</title>
    </head>
    <body>
        <header>
            <h1>Deleting Profile</h1>
        </header>
        
        <form method="post">
            <div class="view_container">
                <?php
                    echo '<div><p>First Name: </p>';
                    echo '<span>' . htmlentities($row['first_name']) . '</span></div>';
                    echo '<div><p>Last Name: </p>';
                    echo '<span>' . htmlentities($row['last_name']) . '</span></div>';
                ?>
                <!-- Hidden input so id can be sent with post -->
                <input type="hidden" name="profile_id" value="<?= $row["profile_id"] ?>">
                <div class="form_action">
                    <button type="submit" name="delete">Delete</button>
                    <button type="submit" name="cancel">Cancel</button>
                </div>
            </div>
        </form>
    
    </body>
</html>