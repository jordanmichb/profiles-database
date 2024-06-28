<?php
    require_once 'pdo.php';
    require_once 'util.php';
    require_once 'head.php';

    session_start();

    $loggedIn = false;
    if (isset($_SESSION['user_id'])) {
        $loggedIn = true;
    }
    
    /* * * * * * * * * * * * * * * * * * * * * * *
    ** Get clicked user's info to place into view
    * * * * * * * * * * * * * * * * * * * * * * */
    $stmt = $pdo->prepare('SELECT profile_id, first_name, last_name, headline FROM `profile`');
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Jordan Ballard Resume Registry Home</title>
    </head>
    <body>
        <header>
            <h1>Resume Registry</h1>
            <?php 
                echo $loggedIn ? '<a href="logout.php">Logout</a>'
                               : '<a href="login.php">Please log in</a>';
            ?>
        </header>
        <?php 
            flashMessages(); // Check for error or success messages
            if ($loggedIn) echo '<a class="add" href="add.php">Add New Entry</a>';
        ?>

        <table>
            <thead>
                <tr>
                    <th scope="col">Name</th>
                    <th scope="col">Headline</th>
                    <?php if ($loggedIn) echo '<th scope="col">Action</th>' ?>
                </tr>
            </thead>
            <tbody>
                <?php
                    if ($rows == false) {
                        echo '<tr><td colspan=100%>No Rows Found</td></tr>';
                    } else {
                        foreach ($rows as $row) {
                            echo '<tr>';
                            echo '<td><a href="view.php?profile_id=' . $row['profile_id'] . '">' . htmlentities($row['first_name'] . ' ' . $row['last_name']) . '</a></td>';
                            echo '<td>' . htmlentities($row['headline']) . '</td>';
                            if ($loggedIn) {
                                echo '<td><a href="edit.php?profile_id=' . $row['profile_id'] . '">Edit</a>';
                                echo ' | ';
                                echo '<a href="delete.php?profile_id=' . $row['profile_id'] . '">Delete</a></td>';
                            }
                            echo '</tr>';
                        }
                    }
                ?>
            </tbody>
        </table>

    </body>
</html>