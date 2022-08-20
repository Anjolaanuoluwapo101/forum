<?php

/*
Not the best way of obtaining the referer link.
*/
$link = $_SERVER['HTTP_REFERER'];

    /*
		This page modifies both reply or comment,helps add new replies and comment.
		
		A link containing the "userReply" variable referred to this page is intendedvto edit a particular reply 
		of  the current user.userReply is the username of the current user.
		
		
		
		A link containing "userComment" along with some other variables referred to this page supposed to edit a particular 
		comment of the current user.userComment is the username of the current comment.
		
		
		
		A link containing "newReply"  or "newcomment"  referred to this page is inteded for creating a reply to a comment or 
		a comment to a post.
		
		
		Checks if session exits...
		Remember that,the session confirms if a person of a particular username
		is logged.
		It compares the Username to the username in the link ....
		If they dont match,it's most likely a hacker forging a modify link but he won't be let through as an htttp
		response code would be generated.
		
		*/
if (isset($_GET['userReply'], $_GET['id'], $_GET['Replytime']) || isset($_GET['userComment'], $_GET['Post_no'], $_GET['CommentTime']) || isset($_GET['newReply'], $_GET['Comment_owner'], $_GET['Category_of_comment'], $_GET['Comment_bind_replies_id']) || isset($_GET['Post_time'],$_GET['Post_admin'], $_GET['Post_no'], $_GET['Post_title'], $_GET['newComment'], $_GET['Category_of_post'])) {
  try {
    session_name("ProgrammersHub");
    session_start();


    if (isset($_SESSION["Username"])) {
      if (isset($_GET['userReply']) && $_SESSION["Username"] == $_GET["userReply"]) {
        /*
			The link for editing a reply will contain a userReply variable...so it defines
			a different sql query.
			*/

        $user = $_GET["userReply"];
        $comment_bind_reply_id = $_GET['id'];
        $time = $_GET['Replytime'];
        $loadReply = 1;
        $sqlQuery = "SELECT * FROM Replies WHERE `Reply_username`=? AND `Time`=? AND `Comment_bind_replies_id`=? ";
      } elseif (isset($_GET['userComment']) && $_SESSION["Username"] == $_GET['userComment']) {
        /*
				A link for editing a comment will contain a userComment...so it defines
				a different sql query....
				*/
        $user = $_GET['userComment'];
        $post_no = $_GET['Post_no'];
        $time = $_GET['CommentTime'];
        $loadComment = 1;
        $sqlQuery = "SELECT * FROM Comments WHERE `Comment_username`=? AND `Time`=? AND `Post_no`=?";
      } elseif (isset($_SESSION['Username'], $_GET['newReply'])) {
        /*
			The link for creating a reply to a comment will contain the ->newReply variable....
			*/
        $comment_owner = $_GET['Comment_owner'];
        $comment_bind_reply_id = $_GET['Comment_bind_replies_id'];
        $category = $_GET['Category_of_comment'];
        $time = $_GET['time'];
      } elseif (isset($_SESSION['Username'], $_GET['newComment'])) {
        /*
			The link for creating a comment to a post.... will contain the ->newComment variable....
			*/
        $post_no = $_GET['Post_no'];
        $post_title = $_GET['Post_title'];
        $time = $_GET['Post_time'];
        $category = $_GET['Category_of_post'];
         $post_owner =$_GET['Post_admin'];
        /*
			Each comment made must have a uniqely generated number tjat binds
			itself along with the replies it may get.This function below,takes care
			of this.
			*/
        function make_seed() {
          list($usec, $sec) = explode(' ', microtime());
          return $sec + $usec * 1000000;
        }

        mt_srand(make_seed());
        $comment_bind_reply_id = mt_rand();

      } else {
        /*
			an error is thrown if a user is logged in but attempts to modify another person's comment
			*/
        throw new Exception("Invalid Link");
      }
    } else {
      //loads an http response code if user isnt logged in at all.
      throw new Exception("User not logged in");
    }
  }catch(Exception $e) {
    http_response_code(403);
    die();
  }
}


?>



<?php
try {
  
  /*
  This block only deals with retrieving reply or comment to edit.
  
  */


  //call the class method to startup a db connection and run either of the queries...
  require_once("class.php");

  /*
	The below code will run to fetch the reply or comment data(both the text content and the images) ...
	*/
  if ($loadReply == 1) {
    $dbh = $dbh->prepare($sqlQuery);
    $dbh->bindParam(1, $user, PDO::PARAM_STR);
    $dbh->bindParam(2, $time, PDO::PARAM_INT);
    $dbh->bindParam(3, $comment_bind_reply_id, PDO::PARAM_INT);
    $dbh->execute();
    $yourReply = $dbh->fetchAll();
    $replyContent = $yourReply[0]['Reply_content'];
    $image1 = $yourReply[0]['image1'];
    $image2 = $yourReply[0]['image2'];
    $image3 = $yourReply[0]['image3'];


  } elseif ($loadComment == 1) {
    $dbh = $dbh->prepare($sqlQuery);
    $dbh->bindParam(1, $user, PDO::PARAM_STR);
    $dbh->bindParam(2, $time, PDO::PARAM_INT);
    $dbh->bindParam(3, $post_no, PDO::PARAM_INT);
    $dbh->execute();
    $yourComment = $dbh->fetchAll();
    $commentContent = $yourComment[0]['Comment_content'];
    $image1 = $yourComment[0]['image1'];
    $image2 = $yourComment[0]['image2'];
    $image3 = $yourComment[0]['image3'];

  }

  /* This if block below runs after the previous else if blocks..
	This ensures that fetchAll actually return contents because it's not possible
	to send empty comment or replies so,a reply or comment in the databse must contain atleast a
	a character */
  if (isset($loadReply) || isset($loadComment)) {
    if ($replyContent = "" || $commentContent == "") {
      throw new Exception('False link');
    }
  }




}catch(Exception $e) {
  echo " Validation passed but failure in retrieving your data from Database";
}

?>






<!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <form enctype="multipart/form-data" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
    <style>

      form {
        width: 90%;
        margin-left: 5%;
      }


      form #textfield {
        height: 200px;
        width: 90%;
        margin-left: 5%;

      }

      button {}


    </style>



    <!--  The hidden inputs contain important values that would be used when submitting user input to  database-->
    <!--  Only paricular variables in the hidden input tags are populated depending on the variabkles sent to this script hence the "@" to subdue errors -->


    <!-- This is a specific variable to updating a  reply or creating a new reply-->
    <input type="hidden" value="<?php echo @$comment_bind_reply_id; ?>" name="id">

    <!--  The $user and $time variables are required for updating a reply/updating a comment -->
    <input type="hidden" value="<?php echo @$user; ?>" name="username">

    <!--  The $user and $time variables are required for updating a reply/updating a comment/creating a new reply/creating a comment -->
    <input type="hidden" value="<?php echo @$time; ?> " name="time">
    <input type="hidden" value="<?php echo @$link; ?> " name="link">

    <!--  These variables below are required for creating a new reply-->
    <input type="hidden" value="<?php echo @$comment_owner; ?>" name="Comment_owner">
    <input type="hidden" value="<?php echo @$category; ?>" name="Category">

    <!-- This is specific to updating a comment  or creating a comment-->
    <input type="hidden" value="<?php echo @$post_no; ?> " name="Post_no">

    <!-- This variable is specific for creating a comment -->
    <input type="hidden" value="<?php echo @$post_title; ?>" name="Post_title">
    <input type="hidden" value="<?php echo @$post_owner; ?>" name="Post_admin">



    <input name="<?php

    /*
$loadReply and $loadComment are vaariables that are created only when,its are
reply link or when it is a comment link
*/
    if (isset($loadReply)) {
      echo "updatedReply";
    } elseif (isset($loadComment)) {
      echo "updatedComment";
    } elseif (isset($_GET['newReply'])) {
      echo "newReply";
    } elseif (isset($_GET['newComment'])) {
      echo "newComment";
    }

    ?>"

    type="text" id="textfield" value="
    <?php
    /*
The value is attribute is used to populate the
input field with either replyContent or commentContent
*/
    if (isset($loadReply)) {
      echo $replyContent;
    } elseif (isset($loadComment)) {
      echo $commentContent;
    }
    ?>

    " required>

    <div>
      <h4> Upload Media (Maximum of 4 Multimedia each less than 1.5MB ) </h4>

      <input type="file" name="files[]" /><input type="hidden" name="filesInDB[]" value="<?php echo $image1; ?>"> <?php if (basename($image1) != "imgs") {
      echo basename($image1);
    }; ?>

    <input type="file" name="files[]"><input type="hidden" name="filesInDB[]" value="<?php echo $image2; ?>"><?php if (basename($image2) != "imgs") {
      echo basename($image2);
    }; ?>

    <input type="file" name="files[]"><input type="hidden" name="filesInDB[]" value="<?php echo $image3; ?>"><?php if (basename($image3) != "imgs") {
      echo basename($image3);
    }; ?>
    <!--<input type="file" name="files[]"  >-->


  </div>

  <br>
  <button type="submit">POST</button>
</form>


<div>
  <h4> Posting Tools:</h4>
  <button onclick="addPreTags()"> Add Code</button>
  <span> *Type only code within pre tags </span>

</div>



<script>
  function addPreTags() {
    document.getElementById("textfield").value += " \n <pre> \\* Type code here *\\ </pre>";
    document.getElementById("textfield").focus();


    var end = document.getElementById("textfield");
    var len = document.getElementById("textfield").value.length-7;
    if (end.setSelectionRange) {
      end.focus();
      end.setSelectionRange(len, len);

    } else if (end.createTextRange) {
      var t = end.createTextRange();
      t.collapse(true);
      t.moveEnd('character', len);
      t.moveStart('character', len);
      t.select();

    }

  }





</script>

</body>
</html>






<?php
        
        /*
        This block simply helps creates  a new comment or reply
        */
        
        
        /*
        All the variables from the hidden input tags are sent to this aspect of the script
        but some will bcarry a null value since they were not created  depending on the link 
        and some of the  if blocks were untouched.
        The post block  depends on if the only visible input tag carries a name called
        newReply or newComment or updatedReply or updatedComment
        */
      
        
        require_once("class.php");
        
          /*
        	The script below is for submission of new comments,replies and modification
        	of existing comment and replies
        	*/
        
        
        
        
          /*
        	This block below deals with creating a comment to a post or creating a reply to a comment
        	*/
        
        
        
        if (isset($_POST['newComment'], $_POST['Post_title'], $_POST['Post_no'],$_POST['Post_admin'], $_POST['id'], $_POST['time'], $_POST['Category']) || isset($_POST['newReply'], $_POST['Category'], $_POST['id'], $_POST['Comment_owner'])) {
        
        session_name("ProgrammersHub");
        session_start();
        $username = $_SESSION['Username'];
        
        
        try {
        if (isset($_POST['newReply'])) {
        
        $category_of_comment = urldecode($_POST['Category']);
        $comment_bind_reply_id = $_POST['id'];
        $comment_owner = $_POST['Comment_owner'];
        $comment_time = $_POST['time'];
        
        
        $user_reply_to_comment = $_POST['newReply'];
        $reply_time = time();
        $link = $_POST['link'];
        
        $sqlQueryA = "INSERT INTO Replies(`Reply_username`,`Reply_content`,Time,`Category`,`Comment_bind_replies_id`,`image1`,`image2`,`image3`) VALUES(?,?,?,?,?,?,?,?)";
        $sqlQueryB = "UPDATE Comments SET `Comment_replies_count`=`Comment_replies_count` + 1 WHERE `Time`=? AND `Comment_bind_replies_id` = ? AND `Category` = ? AND `Comment_username`= ? ";
        
        
        } elseif ($_POST['newComment']) {
        $new_comment_time = time();
        $comment_content = $_POST['newComment'];
        $comment_bind_reply_id = $_POST['id'];
        $comment_time = time();
        $link =$_POST['link'];
        
        $post_no = $_POST['Post_no'];
        $post_title = urldecode($_POST['Post_title']);
        $post_time = $_POST['Post_time'];
        $post_owner=$_POST['Post_admin'];
        $category = urldecode($_POST['Category']);
        
        $sqlQueryA = "INSERT INTO Comments(`Comment_username`,`Comment_content`,`Category`,`Time`,`Comment_bind_replies_id`,`image1`,`image2`,`image3`,`Post_no`) VALUES(?,?,?,?,?,?,?,?,?)";
        $sqlQueryB = "UPDATE Posts SET `Post_comments_count` = `Post_comments_count` + 1 WHERE `Post_no`=? AND `Post_title`=? AND `Post_time`=?  ";
        }
        
        @$imglink1 = "chatResources/imgs/".basename($_FILES['files']['name'][0]);
        @$imglink2 = "chatResources/imgs/".basename($_FILES['files']['name'][1]);
        @$imglink3 = "chatResources/imgs/".basename($_FILES['files']['name'][2]);
        
        
        
        foreach ($_FILES['files']['size'] as $imageSize) {
        static $count1 = 0;
        $count1++;
        if ($imageSize[$count1] > 1572864) {
        if ($_FILES['files']['error'] == 0) {
        throw new Exception("<script>	alert('Image $count1 size is larger than 1.5MB'); history.back(-1); </script>");
        }
        }
        };
        
        foreach ($_FILES['files']['type'] as $imageType) {
        static $count2 = 0;
        if ($imageType[$count2] != 'image/jpeg' || $imageType[$count2] != 'image/png') {
        if ($_FILES['files']['error'] == 0) {
        throw new Exception("<script>	alert('Image $count type is not supported'); history.back(-1); </script>");
        
        }
        
        }
        $count2++;
        
        }
        
        
        for ($i = 0; $i <= 2; $i++) {
        foreach ($_FILES['files']['error'][$i] as $error) {
        if ($error == 0) {
        $mediaName = $_FILES['files']['name'][$i];
        move_uploaded_file($_FILES['files']['tmp_name'][$i], "chatResources/imgs/".$mediaName);
        
        }
        
        }
        
        }
        
        if (isset($_POST['newReply'])) {
          
        /*
        The actual reply to a comment is saved with this query...
        */
        $dbh = $dbh->prepare($sqlQueryA);
        $dbh->bindParam(1, $username, PDO::PARAM_STR);
        $dbh->bindParam(2, $user_reply_to_comment, PDO::PARAM_STR);
        $dbh->bindParam(3, $reply_time, PDO::PARAM_INT);
        $dbh->bindParam(4, $category_of_comment, PDO::PARAM_STR);
        $dbh->bindParam(5, $comment_bind_reply_id, PDO::PARAM_INT);
        $dbh->bindParam(6, $imglink1, PDO::PARAM_STR);
        $dbh->bindParam(7, $imglink2, PDO::PARAM_STR);
        $dbh->bindParam(8, $imglink3, PDO::PARAM_STR);
        $dbh->execute();
        
        
        /*
         The next query updates the Comment replies count table in the Comments table.
        */
        
        $dbh = $instance->getConnection();
        $dbh = $dbh->prepare($sqlQueryB);
        $dbh->bindParam(1, $comment_time, PDO::PARAM_INT);
        $dbh->bindParam(2, $comment_bind_reply_id, PDO::PARAM_INT);
        $dbh->bindParam(3, $category_of_comment, PDO::PARAM_STR);
        $dbh->bindParam(4, $comment_owner, PDO::PARAM_STR);
        $dbh->execute();
        
        /*
        next thing is to update the number of comments and replies column under the
        UserProfile table.This is neccesary to work out the activities and engagements of a user
        */
        
        //the next two variables generates the exact location of where this modify link was referred from 
        $id_of_comment="$comment_owner|$comment_time|_A";
        $link=trim($link);
        $id_of_comment=str_ireplace(" ","", $id_of_comment);
        
        $timeReplyWasMade=time();
        $activityLog_update ="<a target='_blank' href='" .$link.'#'.$id_of_comment."' > View </a> ~ $timeReplyWasMade||";
     //   echo $activityLog_update;
       
       
       $sqlQuery1 = "UPDATE UserProfile SET  `no_of_recieved_comments_and_replies` = `no_of_recieved_comments_and_replies` + 1 , `activity_logs` = CONCAT(`activity_logs` ,?)  WHERE `Username` =?";
       $sqlQuery2 = "UPDATE UserProfile SET `no_of_made_posts_comments_and_replies` = `no_of_made_posts_comments_and_replies` + 1  WHERE `Username` = ?";
  

      /*
      This updates the column (no_of_made_posts_comments_and_replies) of the person sending the reply.
      */
        
      
      $dbh = $instance->getConnection();
      $dbh = $dbh->prepare($sqlQuery2);
     	$dbh->bindParam(1, $username, PDO::PARAM_STR);
     	$dbh->execute();
      echo "You have replied this article";
      echo "<br>";

	    
	
      	
        
        /*
        Reset the dbh connection link;
        Then execute a query that updates the no_of_recieved_comments_and_replies column of the username of the person you are 
        replying a comment.
        */
        

      	$dbh = $instance->getConnection();
      	$dbh = $dbh->prepare($sqlQuery1);
        $dbh->bindParam(1, $activityLog_update, PDO::PARAM_STR);
        $dbh->bindParam(2, $comment_owner, PDO::PARAM_STR);
        $dbh->execute();
      	echo "The owner of this article engagement has increased thanks to you.";
      	echo "<br>";
       
    
       // echo "<script>alert('New Reply Sent'); history.go(-2);</script>";
        //echo "Comment reply count updated";
        
        
        
       
        } elseif (isset($_POST['newComment'])) {
         /*
         This query saves the newComment created by a user for a particular post.
         */
        $dbh = $dbh->prepare($sqlQueryA);
        $dbh->bindParam(1, $username, PDO::PARAM_STR);
        $dbh->bindParam(2, $comment_content, PDO::PARAM_STR);
        $dbh->bindParam(3, $category, PDO::PARAM_STR);
        $dbh->bindParam(4, $new_comment_time, PDO::PARAM_INT);
        $dbh->bindParam(5, $comment_bind_reply_id, PDO::PARAM_INT);
        $dbh->bindParam(6, $imglink1, PDO::PARAM_STR);
        $dbh->bindParam(7, $imglink2, PDO::PARAM_STR);
        $dbh->bindParam(8, $imglink3, PDO::PARAM_STR);
        $dbh->bindParam(9, $post_no, PDO::PARAM_INT);
        $dbh->execute();
        
         /*
     		The next query updates the  Post comment count table on the Post table.
     		*/
        
        $dbh = $instance->getConnection();
        $dbh = $dbh->prepare($sqlQueryB);
        $dbh->bindParam(1, $post_no, PDO::PARAM_INT);
        $dbh->bindParam(2, $post_title, PDO::PARAM_STR);
        $dbh->bindParam(3, $post_time, PDO::PARAM_INT);
        $dbh->execute();
        
        /*
        next thing is to update the number of comments and replies column under the
        UserPruserprofile table.This is neccesary to work out the activities and engagements of a user
        */
        
       $timeCommentWasMade = time();
       $activityLog_update ="<a target='_blank' href='" .$link."' > View </a> ~ $timeCommentWasMade||";
       
       $sqlQuery1 = "UPDATE UserProfile SET `no_of_made_posts_comments_and_replies` = `no_of_made_posts_comments_and_replies` + 1 , `activity_logs` = CONCAT(`activity_logs` ,?)  WHERE `Username` =?";
       $sqlQuery2 = "UPDATE UserProfile SET `no_of_recieved_comments_and_replies` = `no_of_recieved_comments_and_replies` + 1 WHERE `Username` = ?";



      /*
      The updates the column (no_of_made_posts_comments_and_replies) of the person sending the reply.
      */
        
      
      $dbh = $instance->getConnection();
      $dbh = $dbh->prepare($sqlQuery1);
      $dbh->bindParam(1, $activityLog_update, PDO::PARAM_STR);
      $dbh->bindParam(2, $username, PDO::PARAM_STR);
      $dbh->execute();
      echo "You have replied this article";
      echo "<br>";

	
	
      	
      
      /*
      Reset the dbh connection link;
      Then execute a query that updates the no_of_recieved_comments_and_replies column of the username of the person you are 
       sending a new comment.
      */
      

      	$dbh = $instance->getConnection();
      	$dbh = $dbh->prepare($sqlQuery2);
      	$dbh->bindParam(1, $post_owner, PDO::PARAM_STR);
      	$dbh->execute();
      	echo "The owner of this article engagement has increased thanks to you.";
      	echo "<br>";
              
      //  echo "<script>alert('You replied this post');history.go(-2); </script>";
        
        }
        
        
        
        
        }catch(Exception $e) {
        echo $e->getMessage();
        echo $e->getLine();
        }
        
        
        }
        
        
  ?>
        
        
        
        
        
        
        
        
        
        
  <?php
        
        
          /*
        	This if block below deals with updating a reply or comment(editing them)
        	*/
        
        if (isset($_POST["updatedReply"], $_POST["time"], $_POST["id"], $_POST["username"]) || isset($_POST["updatedComment"], $_POST["time"], $_POST["Post_no"], $_POST["username"])) {
        try {
        
        if (isset($_POST["updatedReply"])) {
        $updatedReply = $_POST['updatedReply'];
        $username = $_POST['username'];
        $comment_bind_reply_id = $_POST['id'];
        $time = $_POST['time'];
        $modifiedtime = time();
        $sqlQuery = "UPDATE Replies SET `Reply_content`=? , `ModifiedTime`=?, `image1`=? ,`image2`=? ,`image3`=? WHERE `Comment_bind_replies_id`=? AND `Reply_username`=? AND `Time`=? ";
        } elseif (isset($_POST['updatedComment'])) {
        $updatedComment = $_POST['updatedComment'];
        $username = $_POST['username'];
        $postNo = $_POST['Post_no'];
        $time = $_POST['time'];
        $modifiedtime = time();
        $sqlQuery = "UPDATE Comments SET `Comment_content`=?,`ModifiedTime`=?, `image1`=?,`image2`=? ,`image3`=? WHERE `Post_no` =? AND `Comment_username`=? AND `Time`=? ";
        
        
        }
        
         /*
        	the new updated images link...
        */
        
        $imglink1 = "chatResources/imgs/".basename($_FILES['files']['name'][0]);
        $imglink2 = "chatResources/imgs/".basename($_FILES['files']['name'][1]);
        $imglink3 = "chatResources/imgs/".basename($_FILES['files']['name'][2]);
        

        
        foreach ($_FILES['files']['size'] as $imageSize) {
        static $count1 = 0;
        $count1++;
        if ($imageSize[$count1] > 1572864) {
        throw new Exception("<script>	alert('Image $count1 size is larger than 1.5MB'); history.back(-1); </script>");
        }
        };
        
        foreach ($_FILES['files']['type'] as $imageType) {
        static $count2 = 0;
        if ($imageType[$count2] != 'image/jpeg' || $imageType[$count2] != 'image/png') {
        if ($_FILES['files']['error'] == 0) {
        throw new Exception("<script>	alert('Image $count type is not supported'); history.back(-1); </script>");
        }
        }
        $count2++;
        }
        
        
        //this loops through the file variable and uploads the images to the new  path on the server
        for ($i = 0; $i <= 2; $i++) {
        $mediaName = basename($_FILES['files']['name'][$i]);
        if ($_FILES['files']['error'][$i] == 0) {
        move_uploaded_file($_FILES['files']['tmp_name'][$i], 'chatResources/imgs/'.$mediaName);
        @unlink($_POST['filesInDB'][$i]);
        
        }
        }
        
        if (isset($_POST['updatedReply'])) {
        $dbh = $dbh->prepare($sqlQuery);
        $dbh->bindParam(1, $updatedReply, PDO::PARAM_STR);
        $dbh->bindParam(2, $modifiedtime, PDO::PARAM_INT);
        $dbh->bindParam(3, $imglink1, PDO::PARAM_STR);
        $dbh->bindParam(4, $imglink2, PDO::PARAM_STR);
        $dbh->bindParam(5, $imglink3, PDO::PARAM_STR);
        $dbh->bindParam(6, $comment_bind_reply_id, PDO::PARAM_INT);
        $dbh->bindParam(7, $username, PDO::PARAM_STR);
        $dbh->bindParam(8, $time, PDO::PARAM_INT);
        $dbh->execute();
        echo "<script>alert('Reply modified'); history.go(-2);</script>";
        } elseif (isset($_POST['updatedComment'])) {
        $dbh = $dbh->prepare($sqlQuery);
        $dbh->bindParam(1, $updatedComment, PDO::PARAM_STR);
        $dbh->bindParam(2, $modifiedtime, PDO::PARAM_INT);
        $dbh->bindParam(3, $imglink1, PDO::PARAM_STR);
        $dbh->bindParam(4, $imglink2, PDO::PARAM_STR);
        $dbh->bindParam(5, $imglink3, PDO::PARAM_STR);
        $dbh->bindParam(6, $postNo, PDO::PARAM_INT);
        $dbh->bindParam(7, $username, PDO::PARAM_STR);
        $dbh->bindParam(8, $time, PDO::PARAM_INT);
        $dbh->execute();
        echo "<script>alert('Comment modified'); history.go(-2);</script>";
        }
        
        
        }catch(Exception $e) {
        echo $e->getMessage();
        }
        }
        
        
        
        ?>