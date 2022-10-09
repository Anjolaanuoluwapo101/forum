<?php
session_name('ProgrammersHub');
session_start();
$username = $_SESSION['Username'];

require("class.php");

$link = $_SERVER['HTTP_REFERER'];

if (isset($_GET['commentlike_id'], $_GET['Post_no']) || isset($_GET['replylike_id'], $_GET['Comment_bind_replies_id'])) {
  try {
    if (isset($_GET['commentlike_id'])) {
      $comment_username_and_time_comment_was_made = explode("|", $_GET['commentlike_id']);
      $comment_username = $comment_username_and_time_comment_was_made[0];
      $Time = $comment_username_and_time_comment_was_made[1];
      $a = $comment_username;
      $b = $Time;
      $sqlQuery = "SELECT * FROM Comments WHERE `Comment_username`=? AND `Time`=?";

    } elseif (isset($_GET['replylike_id'])) {

      $reply_username_and_time_reply_was_made = explode("|", $_GET['replylike_id']);
      $reply_username = $reply_username_and_time_reply_was_made[0];
      $Time = $reply_username_and_time_reply_was_made[1];
      $a = $reply_username;
      $b = $Time;
      $sqlQuery = "SELECT * FROM Replies WHERE `Reply_username`=? AND `Time`=?";

    } else {
      throw new Exception('Invalid Access to script');
    }


    $dbh = $dbh->prepare($sqlQuery);
    $dbh->bindParam(1, $a, PDO::PARAM_STR);
    $dbh->bindParam(2, $b, PDO::PARAM_INT);
    $dbh->execute();
    $commentDetails = $dbh->fetchAll();
    $LikeConfirmer = $commentDetails[0]['LikeConfirmer'];

    if (isset($_SESSION['Username']) && preg_match('/'.$username.'/', $LikeConfirmer)) {
      if (isset($_GET['commentlike_id'])) {
        $sqlQueryA = "UPDATE Comments SET `LikeConfirmer` = ?, `Comment_likes`= `Comment_likes` -1  WHERE `Comment_username`=? AND `Time`=?";

      } elseif (isset($_GET['replylike_id'])) {
        $sqlQueryA = "UPDATE Replies SET `LikeConfirmer` = ?,`Reply_likes`=`Reply_likes`-1 WHERE `Reply_username`=? AND `Time`=?";

      } else {
        throw new Exception('Invalid Access to script');
      }


      $userName = ",".$username;

      $LikeConfirmer = str_ireplace($userName, "", $LikeConfirmer);
      $dbh = $instance->getConnection();
      $dbh = $dbh->prepare($sqlQueryA);
      $dbh->bindParam(1, $LikeConfirmer, PDO::PARAM_STR);
      $dbh->bindParam(2, $a, PDO::PARAM_STR);
      $dbh->bindParam(3, $b, PDO::PARAM_INT);
      $dbh->execute();

      echo "<i class='fa fa-thumbs-o-up'>Like</i>";
      
         /*
  	Since you unliked the post,it means the total number of likes ever gotten by
  	the post_owner has decreased by 1.
  	So we need to decreased that with the following query.
  	*/

      $sqlQuery1 = "UPDATE UserProfile SET `no_of_likes_on_posts_and_comments` = `no_of_likes_on_posts_and_comments` - 1 WHERE `Username` =? ";

      $dbh = $instance->getConnection();
      $dbh = $dbh->prepare($sqlQuery1);
      $dbh->bindParam(1, $a, PDO::PARAM_STR);
      $dbh->execute();

      
      
    } elseif (isset($_SESSION['Username']) && !(preg_match('/'.$username.'/', $LikeConfirmer))) {

      if (isset($_GET['commentlike_id'])) {
        $sqlQueryA = "UPDATE Comments SET `LikeConfirmer` = CONCAT(`LikeConfirmer`,?),`Comment_likes`= `Comment_likes`+1 WHERE `Comment_username`=? AND `Time`=?";

      } elseif (isset($_GET['replylike_id'])) {
        $sqlQueryA = "UPDATE Replies SET `LikeConfirmer` = CONCAT(`LikeConfirmer`,?) ,`Reply_likes`= `Reply_likes` +1  WHERE `Reply_username`=? AND `Time`=?";

      } else {
        throw new Exception('Invalid Access to script');
      }

    // usage of the comma delimiter to seperate usernames in the database under a colunn
      $userName = ",".$username;

      $dbh = $instance->getConnection();
      $dbh = $dbh->prepare($sqlQueryA);
      $dbh->bindParam(1, $userName, PDO::PARAM_STR);
      $dbh->bindParam(2, $a, PDO::PARAM_STR);
      $dbh->bindParam(3, $b, PDO::PARAM_INT);
      $dbh->execute();

      echo "<i class='fa fa-thumbs-up'>Liked</i>";

      /*
  	Since you unliked the post,it means the total number of likes ever gotten by
  	the post_owner has increased by 1.
  	So we need to increase that with the following query.
  	*/

      $sqlQuery1 = "UPDATE UserProfile SET `no_of_likes_on_posts_and_comments` = `no_of_likes_on_posts_and_comments` + 1, `activity_logs`=CONCAT(`activity_logs`,?) WHERE `Username` =? ";
      $timeComment_ReplyWasLiked = time();
      $activityLog_update = "<b> $username </b> liked your comment,your engagement has increased ->   <a target='_blank' href='" .$link."' > View </a> ~$timeComment_ReplyWasLiked ||";


      $dbh = $instance->getConnection();
      $dbh = $dbh->prepare($sqlQuery1);
      $dbh->bindParam(1, $activityLog_update, PDO::PARAM_STR);
      $dbh->bindParam(2, $a, PDO::PARAM_STR);
      $dbh->execute();

    }


  }catch(Exception $e) {
    http_response_code(404);
    die();
  }

}

?>