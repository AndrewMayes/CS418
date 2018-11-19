<?php
/*
References: https://www.youtube.com/watch?v=JNtZl9SMmLQ
			https://www.youtube.com/watch?v=y4GxrIa7MiE
			https://www.youtube.com/watch?v=jvnRFsFwiT8
*/
	include ('connection.php');

	session_start();
	
	if(!isset($_SESSION['email'])){
		header("Location: index.php?msg=" . urlencode('needs_to_log_in'));
	}

	if(isset($_GET['id'])) {
		$groupID = $_GET['id'];	
	} else {
		$groupID = "1";
	}

	//getUserID();
	//turn these into functions soon.
	//retrieve UserID from database
	$userEmail = $_SESSION['email'];
	$queryID = "SELECT id FROM users WHERE email = " . "'$userEmail';";
	$userEmail = $conn->query($queryID);

	if ($userEmail->num_rows > 0) { 
		// output data of each row
		while($row = $userEmail->fetch_assoc()) {
			$userID = $row['id'];  
		} 
	}

	$queryUserProfile = "SELECT * FROM users WHERE id ='" . $_GET['id']. "'";
	$result_profile = $conn->query($queryUserProfile);
	if($result_profile->num_rows > 0){	
		while($row_profile = $result_profile->fetch_assoc()) {
			$profileFname = $row_profile['fname'];
			$profileLname = $row_profile['lname'];
			$profileEmail = $row_profile['email'];
			$profilePassword = $row_profile['password'];
			$profileUsername = $row_profile['username'];
		}
	}

	//Finds the groups that a user is in
	$queryGroups = "SELECT groups.group_id,groups.group_name FROM users, groups, group_users WHERE users.id = group_users.user_id AND groups.group_id = group_users.group_id AND users.id = " . "'$userID';";
	$userGroups = $conn->query($queryGroups);

	if ($userGroups->num_rows > 0) { 
		// output data of each row
		while($row = $userGroups->fetch_assoc()) {
			$groupNames[] = $row['group_name'];
			$groupIDs[] = $row['group_id'];
		} 
	}

	$countNames = count($groupNames);
	$countIDs = count($groupIDs);


	if (isset($_POST['submit'])) {
		$message = mysqli_real_escape_string($conn, $_POST['message']);

		$query = "INSERT INTO `messages` (`msg_id`, `user_id`, `msg`, `post_time`, `group_id`) VALUES (NULL, '" . $userID . "', '" . $message . "', CURRENT_TIMESTAMP, '" . $groupID . "');";

		$conn->query($query);

		header("Location: home.php?id=" . $groupID . ""); 

		$conn->close();
	}
?>

<!doctype HTML>
<html>
	<head>
		<meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0">
		<title>Social Media Prototype Testing</title>
		<link rel="stylesheet" type="text/css" href="css/style.css">
		<link href="https://fonts.googleapis.com/css?family=Exo+2" rel="stylesheet">
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
		<script src="script/dropdown.js" type="text/javascript"></script>
	</head>
	<body>
	<div class="header">
			<?php 
				echo "<div id='logo'>";
					echo $_SESSION['username'];
				echo "</div>";
			?>

			<div class="menu">
				<ul>
					<li><a href="loggedout.php">Log Out</a></li>
				</ul>
			</div>
		</div>

		<div class="sidemenu">
			<ul>
				<li><a href="home.php">Home</a></li>
			</ul>

			<ul>
				<li><a href="profile.php">Profile</a></li>
				<li><a href="search_users.php">Search Users</a></li>
			</ul>

			<ul id="submenu">
				<li>
					<span>Groups</span>
					<ul>
						<?php
							for ($x = 1; $x < $countIDs; $x++) {
								echo "<li><a href='./home.php?id=" . $groupIDs[$x] ."'>" . $groupNames[$x] . "</a></li>";
							}
							if ($countIDs == 1) {
								echo "<li><a href='./home.php'>User is only in the global group</a></li>";
							}
						?>
					</ul>
				</li>
			</ul>

			 <ul>
				<li><a href="invite_groups.php">Groups Invites</a></li>
                <li><a href="create_groups.php">Create Groups</a></li>
				<li><a href="search_groups.php">Search Groups</a></li>
				<?php
                    if ($_SESSION['adminID'] == $userID) {
                        echo "<li><a href='groupadmin.php'>Group Administration</a></li>";
                    }
                ?>
                <?php
                    if ($_SESSION['adminID'] == $userID) {
                        echo "<li class='active'><a href='adminhelp.php'>Help</a></li>";
                    }
                    else{
                        echo "<li class='active'><a href='help.php'>Help</a></li>";
                    }
                ?>
            </ul>
		</div>
		

	</body>
</html>

