<?php
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_httponly', 1);


session_name('ProgrammersHub');
session_start();
if (isset($_SESSION['Username'])) {
	//site loads
} else {
	http_response_code(404);
	die();
}

try {
	$username = $_SESSION['Username'];
	require_once('../class.php');
	$dbh = $dbh->prepare("SELECT * FROM UserProfile WHERE `Username` = ?");
	$dbh->bindParam(1, $username, PDO::PARAM_STR);
	$dbh->execute();
	$usernameResult = $dbh->fetchColumn();
	if ($usernameResult < 1) {
		throw new Exception('User not logged in');
	} else {
		$dbh = $instance->getConnection();
		$dbh = $dbh->prepare("SELECT * FROM UserProfile WHERE `Username` =?");
		$dbh->bindParam(1, $username, PDO::PARAM_STR);
		$dbh->execute();
		$userDetails = $dbh->fetchAll();
		//print_r($userDetails);
		//load user data to variables...
		$profilepic = $userDetails[0]['profilepic'];
		// $userDetails[0]['Username'];
	}



}catch(Exception $e) {
	echo $e->getMessage(),
	$e->getLine();
}
?>

<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="stylesheet" href="../css/style.css">
	<link rel="stylesheet" href="../css/profile.css">
	<style>
	
.changedClass{
	background:rgba(231,222,214,0.5)!important;
}

	</style>
</head>
<body onload="active()">

	<div class="header">
		<div id="imgThumbnail">
			<img src="<?php echo $profilepic ?>" alt="">
		</div>
		<br>
		<div>
			<?php echo $userDetails[0]['Username']; ?>
		</div>

	</div>


	<!-- This tag below takes care of the user informatiom-->

	<div class="infoDivs">
		<div>
			About You
		</div>
		<br>
		<div>
			<?php echo $userDetails[0]['about you']; ?>
		</div>

	</div>
	
	<div class="switchBetweenTabs">
		<div id='personalInfo' class="g" >Personal Info</div>
		<div id='activityLogs' class="">Activity Logs</div>
	</div>

	<div class="firstTab">
		<div class="infoDivs">
			<table>
				<tr>
					<td class="name">&nbsp &nbsp &nbsp Joined</td>
					<td class="value">	<?php $joinDate = $userDetails[0]['JoinDate']; echo date("r", $joinDate); ?></td>
				</tr>
				<br>
			</table>
		</div>

		<div class="infoDivs">
			<table>
				<tr>
					<td class="name">&nbsp &nbsp &nbsp Residence</td>
					<td class="value">	<?php echo $userDetails[0]['location']; ?></td>
				</tr>
				<br>
			</table>
		</div>

		<div class="infoDivs">
			<table>
				<tr>
					<td class="name">&nbsp &nbsp &nbsp Residence</td>
					<td class="value">	<?php echo $userDetails[0]['location']; ?></td>
				</tr>
				<br>
			</table>
		</div>

	</div>
	
	
	<div class="secondTab">
	<?php
	
	
	?>
		
	</div>

<script>
	function active(){
 var currentClass= document.getElementById('personalInfo');
 currentClass.className = "changedClass";
	}
	
	function switchActiveTab(){
		
	}
	
</script>


</body>
</html>