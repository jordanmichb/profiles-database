<?php
    require_once 'pdo.php';
    require_once 'head.php';
    
    session_start();

    // Cannot enter page without GET param
    if (!isset($_GET['profile_id'])) {
        $_SESSION['error'] = 'Missing profile_id';
        header("Location: index.php");
        return;
    }

    /* * * * * * * * * * * * * * * * * * * * * * *
    ** Get clicked user's info to place into view
    * * * * * * * * * * * * * * * * * * * * * * */
    // Profile info
    $stmt = $pdo->prepare('SELECT first_name, last_name, email, headline, summary 
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
    $stmt = $pdo->prepare('SELECT `name`, `year` FROM education
        JOIN institution ON education.institution_id = institution.institution_id
        WHERE profile_id=:id ORDER BY rank');
        
    $stmt->execute(array(':id' => $_GET['profile_id']));
    $educations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Position info
    $stmt = $pdo->prepare('SELECT `year`, `description` FROM position 
        WHERE profile_id=:id ORDER BY rank');

    $stmt->execute(array(':id' => $_GET['profile_id']));
    $positions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Jordan Ballard Resume Registry Profile View</title>
    </head>
    <body>
        <header>
            <h1>Profile Information</h1>
        </header>
        <div class="view_container">
            <?php
                echo '<div><p>First Name: </p>';
                echo '<span>' . htmlentities($row['first_name']) . '</span></div>';
                echo '<div><p>Last Name: </p>';
                echo '<span>' . htmlentities($row['last_name']) . '</span></div>';
                echo '<div><p>Email: </p>';
                echo '<span>' . htmlentities($row['email']) . '</span></div>';
                echo '<div><p>Headline: </p>';
                echo '<span>' . htmlentities($row['headline']) . '</span></div>';
                echo '<div><p>Summary: </p>';
                echo '<span>' . htmlentities($row['summary']) . '</span></div>';
                echo '<div><p>Education: </p>';
                foreach ($educations as $edu) {
                    echo '<div class="view_edu">';
                    echo '<span>' . htmlentities($edu['year']) . '</span>';
                    echo '<span>' . htmlentities($edu['name']) . '</span>';
                    echo '</div>';
                }
                echo '</div>';
                echo '<div><p>Positions: </p>';
                foreach ($positions as $pos) {
                    echo '<div class="view_pos">';
                    echo '<span>' . htmlentities($pos['year']) . '</span>';
                    echo '<span>' . htmlentities($pos['description']) . '</span>';
                    echo '</div>';
                }
                echo '</div>'
            ?>
        </div>
        <a href="index.php">Done</a>
    </body>
</html>