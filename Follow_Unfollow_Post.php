<?php
require_once('class.php');
if(isset($_GET['Post_no'])){
  session_name('ProgrammersHub');
  session_start();
  $username = $_SESSION['Username'];
  $post_no = $_GET['Post_no'];
  $post_no=trim($post_no);
}else{
  http_response_code(403);
  exit();
}
  $dbh= $dbh->prepare('SELECT `Post_Followers` FROM Posts WHERE `Post_no` =? ');
  $dbh->bindParam(1,$post_no,PDO::PARAM_INT);
  $dbh->execute();
  $postDetails = $dbh->fetchAll();
  $post_followers=$postDetails[0]['Post_Followers']; //since fetchAll fetches an array with a list of array inside (mulitdimensional )
	if(isset($_SESSION['Username']) && preg_match("/$username/",$post_followers)){
	  $post_followers_updated=str_ireplace("$username||","",$post_followers);
	  $dbh=$instance->getConnection();
	  $dbh=$dbh->prepare("UPDATE Posts SET `Post_Followers`= ? WHERE `Post_no`=? ");
	  $dbh->bindParam(1,$post_followers_updated,PDO::PARAM_STR);
	  $dbh->bindParam(2,$post_no,PDO::PARAM_STR);
	  $dbh->execute();
	  echo "Post Unfollowed";
	}else{
	  $dbh=$instance->getConnection();
	  $userName= "$username||";
	  $dbh=$dbh->prepare("UPDATE Posts SET `Post_Followers`= CONCAT(`Post_Followers`,?) WHERE `Post_no`=? ");
	  $dbh->bindParam(1,$userName,PDO::PARAM_STR);
	  $dbh->bindParam(2,$post_no,PDO::PARAM_STR);
	  $dbh->execute();
	  echo "Post Followed";
	  
	}


?>