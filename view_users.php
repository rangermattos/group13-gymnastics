<?php #5
// This script retrieves all the records from the users table.
// This new version allows the results to be sorted in different ways.

$page_title = 'View the Current Users';
include ('includes/header.html');
echo '<h1>Registered Users</h1>';

require ('mysqli_connect.php');

// Number of records to show per page:
$display = 10;

// Determine how many pages there are...
if (isset($_GET['p']) && is_numeric($_GET['p'])) { // Already been determined.
	$pages = $_GET['p'];
} else { // Need to determine.
 	// Count the number of records:
	$q = "SELECT COUNT(ID_Login) FROM LOGIN";
	$r = @mysqli_query ($dbc, $q);
	$row = @mysqli_fetch_array ($r, MYSQLI_NUM);
	$records = $row[0];
	// Calculate the number of pages...
	if ($records > $display) { // More than 1 page.
		$pages = ceil ($records/$display);
	} else {
		$pages = 1;
	}
} // End of p IF.

// Determine where in the database to start returning results...
if (isset($_GET['s']) && is_numeric($_GET['s'])) {
	$start = $_GET['s'];
} else {
	$start = 0;
}

// Determine the sort...
// Default is by registration date.
$sort = (isset($_GET['sort'])) ? $_GET['sort'] : 'rd';

// Determine the sorting order:
switch ($sort) {
	case 'ln':
		$order_by = 'Last_Name ASC';
		break;
	case 'fn':
		$order_by = 'First_Name ASC';
		break;
	case 'rd':
		$order_by = 'Registration_Date ASC';
		break;
	default:
		$order_by = 'Registration_Date ASC'; // default is rd
		break;
}
	
// Define the query:
$q = "SELECT Last_Name, First_Name, DATE_FORMAT(Registration_Date, '%M %d, %Y') AS dr, ID_Login, COMPETITOR_ID.Competitor AS Competitor_ID
	FROM (LOGIN LEFT OUTER JOIN COMPETITOR_ID ON LOGIN.ID_Login=COMPETITOR_ID.User)
	ORDER BY $order_by
	LIMIT $start, $display";		
$r = @mysqli_query ($dbc, $q); // Run the query.

// Table header:
echo '<table>
<thead>';
	if ($_SESSION['Is_Admin']) {
		echo '<th>Edit</th>
	<th>Delete</th>';
	}
	echo '
	<th><a href="view_users.php?sort=ln">Last Name</a></th>
	<th><a href="view_users.php?sort=fn">First Name</a></th>
	<th><a href="view_users.php?sort=rd">Date Registered</a></th>
</thead>
';

// Fetch and print all the records....
 
while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
	echo '<tr>';
		if ($_SESSION['Is_Admin']) {
			echo '
		<td><a href="edit_user.php?id=' . $row['ID_Login'] . '">Edit</a></td>
		<td><a href="delete_user.php?id=' . $row['ID_Login'] . '">Delete</a></td>';
		}
		echo '
		<td>' . $row['Last_Name'] . '</td>
		<td>' . $row['First_Name'] . '</td>
		<td>' . $row['dr'] . '</td>';
		if (isset($row['Competitor_ID'])) {
			echo '<td><a href="view_competitor.php?id=' . $row['Competitor_ID'] . '">View this competitor</a></td>';
		}
	echo '
	</tr>
	';
} // End of WHILE loop.

echo '</table>';
mysqli_free_result ($r);
mysqli_close($dbc);

// Make the links to other pages, if necessary.
if ($pages > 1) {
	
	echo '<br /><p>';
	$current_page = ($start/$display) + 1;
	
	// If it's not the first page, make a Previous button:
	if ($current_page != 1) {
		echo '<a href="view_users.php?s=' . ($start - $display) . '&p=' . $pages . '&sort=' . $sort . '">Previous</a> ';
	}
	
	// Make all the numbered pages:
	for ($i = 1; $i <= $pages; $i++) {
		if ($i != $current_page) {
			echo '<a href="view_users.php?s=' . (($display * ($i - 1))) . '&p=' . $pages . '&sort=' . $sort . '">' . $i . '</a> ';
		} else {
			echo $i . ' ';
		}
	} // End of FOR loop.
	
	// If it's not the last page, make a Next button:
	if ($current_page != $pages) {
		echo '<a href="view_users.php?s=' . ($start + $display) . '&p=' . $pages . '&sort=' . $sort . '">Next</a>';
	}
	
	echo '</p>'; // Close the paragraph.
	
} // End of links section.
	
include ('includes/footer.html');
?>
