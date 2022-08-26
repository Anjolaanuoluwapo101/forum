<?php
  session_name('ProgrammersHub');
  session_start();
  $username=$_SESSION['Username'];
  $link = $_SERVER['HTTP_REFERER'];

if(isset($_GET['Post_no'],$_GET['Post_title'])){
	require_once("class.php");
	try{
	$post_no=$_GET['Post_no'];
	$post_title=urldecode($_GET['Post_title']);
	$post_owner =$_GET['Post_owner'];
	
	$sqlQuery="SELECT * FROM Posts WHERE `Post_no`=? AND `Post_title`=?";
	$dbh= $dbh->prepare($sqlQuery);
	$dbh->bindParam(1,$post_no,PDO::PARAM_INT);
	$dbh->bindParam(2,$post_title,PDO::PARAM_STR);
	$dbh->execute();
	$postDetails=$dbh->fetchAll();
	$LikeConfirmer= $postDetails[0]['LikeConfirmer'];
	if(isset($_SESSION['Username']) && preg_match('/'.$username.'/',$LikeConfirmer)){
	$sqlQuery="UPDATE Posts SET `LikeConfirmer` = ? WHERE `Post_no`=? AND `Post_title`=?";
	$userName=",".$username;
	$LikeConfirmer=str_replace($userName,"",$LikeConfirmer);
	
	
	$dbh = $instance->getConnection();
	$dbh= $dbh->prepare($sqlQuery);
	$dbh->bindParam(1,$LikeConfirmer,PDO::PARAM_STR);
	$dbh->bindParam(2,$post_no,PDO::PARAM_INT);
	$dbh->bindParam(3,$post_title,PDO::PARAM_STR);
	$dbh->execute();
	
	$sqlQuery="UPDATE Posts SET `Post_Likes` = Post_Likes -1 WHERE `Post_no`=? AND `Post_title`=?";
	$dbh = $instance->getConnection();
	$dbh= $dbh->prepare($sqlQuery);
	$dbh->bindParam(1,$post_no,PDO::PARAM_INT);
	$dbh->bindParam(2,$post_title,PDO::PARAM_STR);
	$dbh->execute();
	echo "<i class='fa fa-thumbs-o-up'>Like</i>";
	
	/*
	Since you unliked the post,it means the total number of likes ever gotten by
	the post_owner has dropped by 1.
	So we need to decrease that with the following query.
	*/
  
	$sqlQuery1 = "UPDATE UserProfile SET `no_of_likes_on_posts_and_comments` = `no_of_likes_on_posts_and_comments` - 1 WHERE `Username` =? ";

  
  $dbh = $instance->getConnection();
 	$dbh = $dbh->prepare($sqlQuery1);
 	$dbh->bindParam(1,$post_owner, PDO::PARAM_STR);   
 	$dbh->execute();
 	
 	
      		
	
	
	
	}elseif(isset($_SESSION['Username']) && !(preg_match('/'.$username.'/',$LikeConfirmer))){
	$sqlQuery="UPDATE Posts SET `LikeConfirmer` = CONCAT(`LikeConfirmer`,?) WHERE `Post_no`=? AND `Post_title`=?";
	$userName=",".$username;
	
	$dbh = $instance->getConnection();
	$dbh= $dbh->prepare($sqlQuery);
	$dbh->bindParam(1,$userName,PDO::PARAM_STR);
	$dbh->bindParam(2,$post_no,PDO::PARAM_INT);
	$dbh->bindParam(3,$post_title,PDO::PARAM_STR);
	$dbh->execute();
	
	
	$sqlQuery="UPDATE Posts SET `Post_Likes` = Post_Likes + 1 WHERE `Post_no`=? AND `Post_title`=?";
	$dbh = $instance->getConnection();
	$dbh= $dbh->prepare($sqlQuery);
	$dbh->bindParam(1,$post_no,PDO::PARAM_INT);
	$dbh->bindParam(2,$post_title,PDO::PARAM_STR);
	$dbh->execute();

	
	echo "<i class='fa fa-thumbs-up'>Liked</i>";
	
	/*
	Since you unliked the post,it means the total number of likes ever gotten by
	the post_owner has increased by 1.
	So we need to increase that with the following query.
	*/


	$sqlQuery1 = "UPDATE UserProfile SET `no_of_likes_on_posts_and_comments` = `no_of_likes_on_posts_and_comments` + 1, `activity_logs`=CONCAT(`activity_logs`,?) WHERE `Username` =? ";
  $timePostWasLiked = time();
  $activityLog_update =" <b> $username </b> liked your post ->   <a target='_blank' href='" .$link."' > View </a> ~ $timePostWasLiked ||";

  
  $dbh = $instance->getConnection();
 	$dbh = $dbh->prepare($sqlQuery1);
  $dbh->bindParam(1, $activityLog_update, PDO::PARAM_STR);
 	$dbh->bindParam(2,$post_owner, PDO::PARAM_STR);   
 	$dbh->execute();
 	

	
	}
	
		
	}catch(Exception $e){
		echo $e->getMessage();
	}
	
	
}

?>