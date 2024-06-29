<?php
    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
    ** Display message if it exists, then unset sso it does not persist
    * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
    function flashMessages() {
        if (isset($_SESSION['error'])) {
            echo "<p class='msg error'>" . $_SESSION['error'] . "</p>";
            unset($_SESSION['error']);
        } elseif (isset($_SESSION['success'])) {
            echo "<p class='msg success'>" . $_SESSION['success'] . "</p>";
            unset($_SESSION['success']);
        }
    }


    /* * * * * * * * * * * * * * * * *
    ** Validate input for add or edit
    * * * * * * * * * * * * * * * * */
    function validateProfile() {
        if ($_POST['first_name'] == "" || $_POST['last_name'] == "" || $_POST['email'] == "" ||
            $_POST['headline'] == "" || $_POST['summary'] == "") {
                return 'All fields are required';
        }
        if (strpos($_POST['email'], '@') === false) {
            return 'Invalid email address';
        }

        // Validate position and education entries if present
        foreach ($_POST as $key => $val) {
            if (strpos($key, 'edu_year') === 0) { // Find all education fields
                $id = substr($key, 8); // Get education number to get matching school
                $year = $val; // Year is current key's value
                $school = $_POST['edu_school' . $id]; // Get matching desc

                if ($year == "" || $school == "") {
                    return "All education fields are required";
                }
                // If year, make sure value is numeric
                if (!is_numeric($year)) {
                    return "Education year must be numeric"; 
                }
            } elseif (strpos($key, 'pos_year') === 0) { // Find position fields
                $id = substr($key, 8); // Get position number to get matching desc
                $year = $val; // Year is current key's value
                $desc = $_POST['pos_desc' . $id]; // Get matching desc

                if ($year == "" || $desc == "") {
                    return "All position fields are required";
                }
                // If year, make sure value is numeric
                if (!is_numeric($year)) {
                    return "Position year must be numeric"; 
                }
            } 
        }

        return true;
    }


    /* * * * * * * * * * * * * * * * * * * * * * * * *
    ** Add education and position fields into database
    * * * * * * * * * * * * * * * * * * * * * * * * */
    function addRemainingFields($pdo, $profile_id) {
        $pos_rank = 1; // Use rank to keep positions/education in order
        $edu_rank = 1;
        foreach ($_POST as $key => $val) {
            // Find all fields beginning with "year"
            if (strpos($key, 'edu_year') === 0) {
                $edu_num = substr($key, 8); // Get education number to get matching school
                $year = $val; // Year is current key's value
                $school = $_POST['edu_school' . $edu_num]; // Get matching desc

                // Check if school exists in institution db
                $institution_id = false; // id needed for education table insert
                $stmt = $pdo->prepare('SELECT institution_id FROM institution WHERE `name` = :nm');
                $stmt->execute(array(':nm' => $school));
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                // Institution exists
                if ($row !== false) $institution_id = $row['institution_id'];
                // Institution does not exist, add it
                if ($institution_id === false) {
                    $stmt = $pdo->prepare('INSERT INTO institution 
                        (`name`) VALUES (:nm)');
                    $stmt->execute(array(':nm' => $school));
                    $institution_id = $pdo->lastInsertId(); // Get id for education table insert
                }

                // Insert education entry
                $stmt = $pdo->prepare('INSERT INTO education
                    (profile_id, institution_id, rank, `year`)
                    VALUES (:pid, :iid, :rank, :yr)');
                $stmt->execute(array(
                    ':pid' => $profile_id,
                    ':iid' => $institution_id,
                    'rank' => $edu_rank,
                    ':yr' => $year
                ));
                $edu_rank++;
            } elseif (strpos($key, 'pos_year') === 0) {
                $pos_num = substr($key, 8); // Get position number to get matching desc
                $year = $val; // Year is current key's value
                $desc = $_POST['pos_desc' . $pos_num]; // Get matching desc

                $stmt = $pdo->prepare('INSERT INTO position 
                    (profile_id, rank, `year`, `description`) 
                    VALUES (:pid, :rank, :yr, :dsc)');

                $stmt->execute(array(
                    ':pid' => $profile_id,
                    ':rank' => $pos_rank,
                    ':yr' => $year,
                    ':dsc' => $desc
                ));
                $pos_rank++;
            } 
        } 
    }
?>