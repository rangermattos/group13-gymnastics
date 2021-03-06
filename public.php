<?php
//Public's view of the page (what a person that is not logged in/logged in can see)

$page_title = 'View the Current Users';
include ('includes/header.html');
echo '<h1>Our Upcoming Events!</h1><br />';

require ('mysqli_connect.php');

$meet = (isset($_GET['meet'])) ? $_GET['meet'] : 'none';
if($meet=='none')
{
	$q = "SELECT ID,Location_Name, Street, City, State, ZIP, DATE_FORMAT(Date, '%M %d, %Y') AS Date, TIME_FORMAT(Time, '%H:%i') AS Time, Competition_Name 
	FROM MEET
	WHERE Date>=CURRENT_DATE()
	ORDER BY Date DESC";
	$r = @mysqli_query ($dbc, $q); // Run the query.
	echo '<table align="center">
	<thead>
		<th>Competition Name</th>
		<th>Venue</th>
		<th>Location</th>
		<th>Date</th>
		<th>Time</th>
	</thead>
	';
	 
	while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
		echo '<tr>
			<td><a href="public.php?meet=' . $row['ID'] . '">' . $row['Competition_Name'] . '</a></td>
			<td>' . $row['Location_Name'] . '</td>
			<td>' . $row['Street'] .' ' . $row['City'] .' , ' . $row['State'] .' ' . $row['ZIP'] .'</td>
	                 <!--add a link to another php page to view teams competing according to meet id -->
			<td>' . $row['Date'] . '</td>
			<td>' . $row['Time'] . '</td>';
			if (isset($_SESSION['Competitor_ID'])) { // if logged in as a competitor
				echo '<td><a href="pay_fee.php?meet=' . $row['ID'] . '">Sign Up</a></td>';
			}
		echo '</tr>
		';
	} // End of WHILE loop.
	echo '</table>';
}
else
{
    $q1= "SELECT Competition_Name FROM MEET WHERE ID=$meet";
    $q = "SELECT MEET.Competition_Name, TEAM.Team_Name, TEAM_COMPETES_AT.Team_ID, TEAM_COMPETES_AT.Meet_ID 
	FROM (MEET INNER JOIN TEAM_COMPETES_AT ON MEET.ID=TEAM_COMPETES_AT.Meet_ID INNER JOIN TEAM ON TEAM_COMPETES_AT.Team_ID=TEAM.Team_ID)
	WHERE TEAM_COMPETES_AT.Meet_ID=$meet
	ORDER BY MEET.Date DESC";
    $r = @mysqli_query ($dbc, $q1); // Run the query.
    $row = mysqli_fetch_array($r, MYSQLI_ASSOC);
    echo '<center><h2>Teams Competing in ' . $row['Competition_Name'] . ' Competition</h2></center>
	<table>
	<thead>
		<th>Team Name</th>
	</thead>
	';
    mysqli_free_result ($r);
    $r = @mysqli_query ($dbc, $q);

    while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
        echo '<tr>
			<td><center>' . $row['Team_Name'] .'</center></td>
		</tr>
		';
    } // End of WHILE loop.
    echo '</table>';
    echo'<br /><a href="public.php">Go Back to Upcoming Events</a>';
}

mysqli_free_result ($r);
mysqli_close($dbc);

include ('includes/footer.html');
?>
