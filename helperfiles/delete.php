<?php
/*
Deleting comments,replies and post doesnt alter the no of made posts comments abd replies column in UserProfile.
It doesnt delete the media associated to this posts,comment and media->for now 
*/

try {
  require_once('class.php');
  require_once('generator.php');
  if(isset($_GET['Post_no'],$_GET['Post_title'],$_GET['Post_admin'])){
    $post_no=$_GET['Post_no'];
    $post_title= urldecode($_GET['Post_title']);
    $post_admin =$_GET['Post_admin'];
    $sqlQuery = "DELETE FROM Posts WHERE `Post_no`=? AND `Post_title`=? AND `Post_admin`=?";
    
  }elseif(isset($_GET['Comment_username'],$_GET['Comment_time'])){
    $comment_time = trim($_GET['Comment_time']);
    $comment_username=trim($_GET['Comment_username']);
    $sqlQuery ="DELETE FROM Comments WHERE `Comment_username`=? AND `Time`=? ";
    
  }elseif(isset($_GET['Reply_username'],$_GET['Reply_time'])){
    $reply_time = trim($_GET['Reply_time']);
    $reply_username=trim($_GET['Reply_username']);
    $sqlQuery ="DELETE FROM Replies WHERE `Reply_username`=? AND `Time`=? ";
    
  }else{
    throw new Exception('Invalid Access to script');
  }
  
  //this major if block executes if a post is to be deleted only
  if(isset($_GET['Post_no'])){  
    
    
    //decrease total number of posts made by you
  $dbh = $dbh->prepare("UPDATE `Posts` INNER JOIN `UserProfile` ON Posts.Post_admin = UserProfile.Username SET UserProfile.no_of_made_posts_comments_and_replies=UserProfile.no_of_made_posts_comments_and_replies - 1 WHERE Posts.Post_no =? ");
  $dbh->bindParam(1, $post_no, PDO::PARAM_INT);
  $check = $dbh->execute();
  echo "Statistics adjusted";
  
  //this only works if post has both comments and comment contain replies
  $dbh = $dbh->prepare("DELETE `Posts`,`Comments`,`Replies` FROM (`Posts` INNER JOIN `Comments` ON Posts.Post_no = Comments.Post_no) INNER JOIN `Replies` ON Comments.Comment_bind_replies_id = Replies.Comment_bind_replies_id WHERE Posts.Post_no=?");
  $dbh->bindParam(1, $post_no, PDO::PARAM_INT);
  $check = $dbh->execute();
  echo "Post deleted";
  
 //this only works if post has only comments but comments have no replies...
  $dbh=$instance->getConnection();
  $dbh = $dbh->prepare("DELETE `Posts`,`Comments` FROM (`Posts` INNER JOIN `Comments` ON Posts.Post_no = Comments.Post_no) WHERE Posts.Post_no=?");
  $dbh->bindParam(1, $post_no, PDO::PARAM_INT);
  $check = $dbh->execute();
  echo "Post deleted";
 
   //works if post has no comment or replies
  $dbh=$instance->getConnection();
  $dbh = $dbh->prepare("DELETE FROM `Posts` WHERE Posts.Post_no=?");
  $dbh->bindParam(1, $post_no, PDO::PARAM_INT);
  $check = $dbh->execute();
  echo "Post deleted";
  
  
 
 
  //this if block runs if we are deleting a comment instead..
  //ofcourse we have to obtain the Comment_bind_replies_id of that comment 
  //in order to get the replies linked to tha comment.*/
}elseif(isset($_GET['Comment_username'])){
    
  //decrease total number of   comments made by you
  $dbh = $dbh->prepare("UPDATE `Comments` INNER JOIN `UserProfile` ON Comments.Comment_username = UserProfile.Username SET UserProfile.no_of_made_posts_comments_and_replies=UserProfile.no_of_made_posts_comments_and_replies - 1 WHERE Comments.Comment_username =? ");
  $dbh->bindParam(1, $comment_username, PDO::PARAM_STR);
  $check = $dbh->execute();
  echo "Statistics adjusted \n";
  
  //alter post_comment_count ....
  $dbh=$instance->getConnection();
  $dbh = $dbh->prepare("UPDATE `Comments` INNER JOIN `Posts` ON Comments.Post_no = Posts.Post_no SET Posts.Post_comments_count=Posts.Post_comments_count - 1 WHERE Comments.Comment_username =? ");
  $dbh->bindParam(1, $comment_username, PDO::PARAM_INT);
  $check = $dbh->execute();
  echo "Statistics adjusted \n";
 

//only works if comments have have replies
  $dbh=$instance->getConnection();
  $dbh = $dbh->prepare('DELETE `Comments`,`Replies` FROM (`Comments` INNER JOIN `Replies` ON Comments.Comment_bind_replies_id = Replies.Comment_bind_replies_id ) WHERE Comments.Comment_username = ? AND Comments.Time =?');
  $dbh->bindParam(1, $comment_username, PDO::PARAM_STR);
  $dbh->bindParam(2, $comment_time, PDO::PARAM_INT);
  $dbh->execute();
  echo "Comment deleted";
  
  //only works if comments have no replies
  $dbh=$instance->getConnection();
  $dbh = $dbh->prepare('DELETE FROM Comments WHERE Comments.Comment_username = ? AND Comments.Time =?');
  $dbh->bindParam(1, $comment_username, PDO::PARAM_STR);
  $dbh->bindParam(2, $comment_time, PDO::PARAM_INT);
  $dbh->execute();
  echo "Comment deleted";

  
  //delete a reply instead of a comment with this block
}elseif(isset($_GET['Reply_username'])){
  
  //decrease total number of replies made by you
   $dbh = $dbh->prepare("UPDATE `Replies` INNER JOIN `UserProfile` ON Replies.Reply_username = UserProfile.Username SET UserProfile.no_of_made_posts_comments_and_replies=UserProfile.no_of_made_posts_comments_and_replies - 1 WHERE Replies.Reply_username =? ");
  $dbh->bindParam(1, $reply_username, PDO::PARAM_STR);
  $check = $dbh->execute();
  echo "Statistics adjusted \n";
 
   //alter comment_replies_count ....
  $dbh=$instance->getConnection();
  $dbh = $dbh->prepare("UPDATE `Replies` INNER JOIN `Comments` ON Comments.Comment_bind_replies_id = Replies.Comment_bind_replies_id SET Comments.Comment_replies_count=Comment.Comment_replies_count - 1 WHERE Reply_username =? ");
  $dbh->bindParam(1, $reply_username, PDO::PARAM_INT);
  $check = $dbh->execute();
  echo "Statistics adjusted \n";
 
  
  
  
  $dbh=$instance->getConnection();//we delete the reply in question.
  $dbh=$dbh->prepare('DELETE FROM Replies WHERE `Reply_username`=? AND `Time`=?');
  $dbh->bindParam(1,$reply_username,PDO::PARAM_STR);
  $dbh->bindParam(2,$reply_time,PDO::PARAM_INT);
  $dbh->execute();
  echo "Reply deleted \n";
   
}



}catch(Exception $e) {
  echo $e->getMessage();
  echo $e->getLine();
}


?>


