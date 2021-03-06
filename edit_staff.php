<?php
 //this page was meant to be used to edit the logged in user's information
$page_title = 'Edit Information';
include ('includes/header.html');
echo '<h1>Edit Personal Information</h1>';


if ((isset($_SESSION['Staff_ID'])))//if logged in as Staff
{
    require('mysqli_connect.php');
    if ($_SERVER['REQUEST_METHOD'] == 'POST') //if post then modify
    {
        $errors = array();
        $staff_ID = $_SESSION['Staff_ID'];
        // Check for the email:
        if (!isset($_POST['Email'])) {
            $errors[] = 'You forgot to enter an email.';
        } else {
            $email = mysqli_real_escape_string($dbc, trim($_POST['Email']));
        }

        // Check for phone:
        if (!isset($_POST['Phone'])) {
            $errors[] = 'You forgot to enter the phone.';
        } else {
            $phone = mysqli_real_escape_string($dbc, trim($_POST['Phone']));
        }
        // Check for email:
        if (!isset($_POST['Street'])) {
            $errors[] = 'You forgot to enter the Street.';
        } else {
            $street = mysqli_real_escape_string($dbc, trim($_POST['Street']));
        }
        if (!isset($_POST['City'])) {
            $errors[] = 'You forgot to enter the City.';
        } else {
            $city = mysqli_real_escape_string($dbc, trim($_POST['City']));
        }
	    if (!isset($_POST['State'])) {
            $errors[] = 'You forgot to enter the State.';
        } else {
            $state = mysqli_real_escape_string($dbc, trim($_POST['State']));
        }
        if (!isset($_POST['ZIP'])) {
            $errors[] = 'You forgot to enter the postal code.';
        } else {
            $zip = mysqli_real_escape_string($dbc, trim($_POST['ZIP']));
        }

        if (empty($errors)) { // If everything's OK.
	
            //  Test for unique email address:
            $q = "SELECT LOGIN.ID_Login, STAFF_ID.Staff
			FROM (LOGIN INNER JOIN STAFF_ID ON LOGIN.Staff_ID=STAFF_ID.Staff)
			WHERE LOGIN.Email='$email' AND STAFF_ID.Staff != $staff_id";
            $r = @mysqli_query($dbc, $q);
            if (mysqli_num_rows($r) == 0) {
                $user_id = $_SESSION['ID_Login'];
                mysqli_free_result($r);

                // update LOGIN
                $q = "UPDATE LOGIN
				SET Email='$email', Phone='$phone', Street='$street', City='$city', State='$state', ZIP='$zip', Phone='$phone'
				WHERE ID_Login=$user_id
				LIMIT 1";
                $r = @mysqli_query($dbc, $q);
                if (mysql_affected_rows($dbc) == 0 || mysqli_affected_rows($dbc) == 1) { // if no row updated, or only 1 row
                    // Print a message:
                    echo '<p>The user has been edited.</p>';

                } else { // If it did not run OK.
                    echo '<p class="error">The user could not be edited due to a system error. We apologize for any inconvenience.</p>'; // Public message.
                    if ($_SESSION['Is_Admin']) {
                        echo '<p>' . mysqli_error($dbc) . '<br />Query: ' . $q . '</p>'; // Debugging message.
                    }
                }

                mysqli_free_result($r);
            } else { // Already registered.
                echo '<p class="error">The email address has already been registered.</p>';
            }

        } else { // Report the errors.
            echo '<p class="error">The following error(s) occurred:<br />';
            foreach ($errors as $msg) { // Print each error.
                echo " - $msg<br />\n";
            }
            echo '</p><p>Please try again.</p>';
        }
    }
    else //display change form
    {


        $staff_ID = $_SESSION['Staff_ID'];
        $q = "SELECT a.Staff, a.User, b.ID_Login, b.Email, b.First_Name, b.Last_Name, DATE_FORMAT(b.Date_Of_Birth, '%M %d, %Y') AS DOB, 
        b.Phone, b.Street, b.City, b.State, b.ZIP
                      FROM STAFF_ID as a, LOGIN as b
                      WHERE a.Staff=$staff_ID && a.User=b.ID_Login && b.Deleted=0";

        $r = @mysqli_query($dbc, $q);
        $row = mysqli_fetch_array($r, MYSQLI_ASSOC);
        echo '<form action="edit_staff.php" method="post">
            <p>Email: <input type="text" name="Email" size="15" maxlength="15" value="' . $row['Email'] . '" /></p>
            <p>Phone: <input type="text" name="Phone" size="15" maxlength="15" value="' . $row['Phone'] . '" /></p>
            <p>Street: <input type="text" name="Street" size="15" maxlength="30" value="' . $row['Street'] . '" /></p>
            <p>City: <input type="text" name="City" size="20" maxlength="60" value="' . $row['City'] . '"  /> </p>
            <p>State (Abbreviated): <input type="text" name="State" size="2" maxlength="60" value="' . $row['State'] . '"  /> </p>
            <p>ZIP: <input type="text" name="ZIP" size="20" maxlength="60" value="' . $row['ZIP'] . '"  /> </p>
        
        <p><input type="submit" name="submit" value="Submit" /></p>
        <input type="hidden" name="id" value="' . $row['ID_Login'] . '" />
        </form>';
        mysqli_free_result ($r);
    }
}
//free the result to start second query

mysqli_close($dbc);

include ('includes/footer.html');
?>
