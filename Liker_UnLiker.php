<?php
session_name('ProgrammersHub');
session_start();
$username=$_SESSION['Username'];

if(isset($_GET['Post_no'],$_GET['Post_title'])){
	require_once("class.php");
	try{
	$post_no=$_GET['Post_no'];
	$post_title=$_GET['Post_title'];
	
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
	}
	
		
	}catch(Exception $e){
		echo $e->getMessage();
	}
	
	
}

?>