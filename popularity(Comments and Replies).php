<?php
	/*
	This script ensures that the no_of_posts_and_comments column in the userprofile
	table and the no_of_likes gotten by posts and comments(with replies) column
	is also updated.

	These columns are needed to rate the activity and engagement of a user on the forum .
	*/

require_once("class.php");
$sqlQuery1 = "UPDATE UserProfile SET `no_of_made_posts_comments_and_replies` = `no_of_made_posts_comments_and_replies` + 1 WHERE `Username` =?";
$sqlQuery2 = "UPDATE UserProfile SET `no_of_recieved_comments_and_replies` = `no_of_recieved_comments_and_replies` + 1 WHERE `Username` = ?";
$sqlQueryactivityLog = "UPDATE UserProfile SET `activityLog` = CONCAT(`activityLog`,?) WHERE `Username` =?";



if (isset($_GET['newReply'])) {
	$activityLog="||<a href=''> $username replied $comment_owner at $reply_time </a> || ";
	/*
	The updates the column (no_of_made_posts_comments_and_replies) of the person sending the reply.
	*/

	$dbh = $instance->getConnection();
	$dbh = $dbh->prepare($sqlQuery1);
	$dbh->bindParam(1, $username, PDO::PARAM_STR);
	$dbh->execute();

	/*
	Reset the database connection link and sends a query to update the activityLog .
	*/
	/*
	$dbh = $instance->getConnection();
	$dbh = $dbh->prepare($sqlQueryactivityLog);
	$dbh->bindParam(1, $activityLog, PDO::PARAM_STR);
	$dbh->execute();
	
	*/



	/*
	Reset the dbh connection link;
	Then execute a query that updates the no_of_recieved_comments_and_replies column of the username of the person you are
	replying or send a new comment.
	*/

	$dbh = $instance->getConnection();
	$dbh = $dbh->prepare($sqlQuery2);
	$dbh->bindParam(1, $comment_owner, PDO::PARAM_STR);
	$dbh->execute();
	echo "The owner of this article engagement has increased thanks to you.";
	echo "<br>";

}elseif (isset($_GET['newComment'])) {
	/*
	Since the initial get variables for making a comment does not come with the username of the post owner ,
	we obtain that with a quick query.
	*/

	$dbh = $instance->getConnection();
	$dbh = $dbh->prepare("SELECT `Post_admin` FROM Posts WHERE `Post_no` = $post_no");
	$dbh->execute();
	$fectchedUsername = $dbh->fetchColumn();

	/*
	The next thing is to update the no_of_recieved_comments_and_replies
	*/

	$dbh = $instance->getConnection();
	$dbh = $dbh->prepare($sqlQuery2);
	$dbh->bindParam(1, $fectchedUsername, PDO::PARAM_STR);
	$dbh->execute();
	echo "The owner of this article engagement has increased thanks to you.";
	echo "<br>";


}


?>