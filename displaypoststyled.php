<?php
//start session that would to check if user is logged in.
session_name('ProgrammersHub');
session_start();
$username = $_SESSION['Username']; //this is used in the preg_match function.
require_once('utilityfunctions.php');
?>


<html>
<head>
  <title>View Topic</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="css/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="css/w3.css">
  <link rel="stylesheet" href="fonts/arial.ttf">
  <link rel="stylesheet" href="css/w3-theme-blue-grey.css">
  <!--<link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Open+Sans'>-->
  <!--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">-->
  <style>
    @font-face {
      font-family:arial;
      src: url(arial.ttf);
    }
    html, body, h1, h2, h3, h4, h5 div {
      font-family: Arial;
    }
  </style>

  <body>
    <!--Beginning of nav bar for all sizes  -->
    <div class="w3-top w3-white">
      <div class="w3-bar w3-large w3-left-align w3-opacity">
        <a class="w3-button w3-padding-large w3-bar-item"><i class=""></i></a>
        <a class="w3-button w3-padding-large w3-bar-item"><i class=""></i></a>
        <a class="w3-button w3-padding-large w3-bar-item"><i class="fa fa-bell"></i></a>
        <a class="w3-button w3-right w3-padding-large w3-bar-item"><i class="fa fa-bars"></i></a>

      </div>
    </div>
    <div class="w3-row" style="margin-top:50px">

      <!--Left container -->
      <div class="w3-container w3-content w3-col m3 l4 w3-hide-small">

      </div>

      <!--Middle container -->
      <div class="w3-container w3-content w3-col m6 l4">

        <?php
        try {
          require_once("class.php");


          /*
	This two variables are used to customize the link to that ensure us to comment on this post.
	Along with $post_time_
	*/
          $post_no = $_GET['Post_no'];
          $post_title = $_GET['Post_title'];
          $post_title_ = urlencode($post_title);


          /*
	The first query in this block ensures that even if the get variables in the link is tampered with
	a confirmation query is done to ensure that,the post exists to prevent this page from crashing,If a post exists
	a row is gotten if not,a http response is provided
	It also uses bindParam to counter SQL injections
	*/

          $sqlQuery = "SELECT COUNT(*) FROM Posts WHERE `Post_no`=? AND `Post_title`=?";
          $dbh = $dbh->prepare($sqlQuery);
          $dbh->bindParam(1, $post_no, PDO::PARAM_INT);
          $dbh->bindParam(2, $post_title, PDO::PARAM_STR);
          $dbh->execute();
          $result = $dbh->fetchColumn();
          if ($result == 0) {
            /* if the post doesn't exist due to link tampering,a http 404 page is generated instead*/
            http_response_code(404);
            die();
          } else {

            /* If the post exists,the database connection link is reset by calling the singleton class method  again to return the database connection
	Using a singleton class ensures that,a previous database connection is used and authentication (which may cause delay ) is eliminated*/
            /* Then the post details are fetched */

            $username = $_SESSION['Username'];
            $dbh = $instance->getConnection();
            $sqlQuery = "SELECT * FROM Posts WHERE `Post_no`=? AND `Post_title`=?";
            $dbh = $dbh->prepare($sqlQuery);
            $dbh->bindParam(1, $post_no, PDO::PARAM_INT);
            $dbh->bindParam(2, $post_title, PDO::PARAM_STR);
            $dbh->execute();
            $postDetails = $dbh->fetch(PDO::FETCH_ASSOC);

            /*
	Populate variables that would be details to the post
	*/
            $post_time = gmdate("D \, H:i:s M Y", $postDetails['Post_time']+3600);
            $no_of_comments_for_post = $postDetails['Post_comments_count'];
            $no_of_likes_for_post = $postDetails['Post_Likes'];


            /*
  The nxt 3 variables are needed in the Post_Liker_Unliker.php script and also
  they are sent via an ajax request by the Post_Liker_Unliker() js function....
  */
            $post_no = 1;
            $post_title = "PHP is an highly recommended programming language";
            $post_title_ = urlencode($post_title);
            $post_owner = $postDetails['Post_admin'];


      //next if block check if post contains media
            renderPostMedia();
                
                
//this evaluates the timelapse of the post
            $post_time = time() - $postDetails['Post_time'];
               if($post_time <  60){
                 $post_time = "Less than ".$post_time." s";
               }elseif($post_time <= 3600 && $post_time >60){
                 $post_time = round($post_time/60);
                 $post_time = $post_time." min ago";
               }elseif($post_time > 3600 && $post_time <= 86400){
                   $post_time= round($post_time/3600);
                if($post_time > 1){
                  $unit="hrs";
                }else{
                  $unit="hr";
                }
                $post_time= $post_time.$unit." ago ";
               }
               elseif($post_time > 86400){
                 $post_time= round($post_time/86400);
                if($post_time > 1){
                  $unit="days";
                }else{
                  $unit="day";
                }
                $post_time= $post_time.$unit." ago ";
               }
               
               
            /*
	Populate variables that would be used to customize the link to comment on this post
	Along with post_no and post_title_(post_no and post_title are found in the link referring to this script)
	*/
               
            $post_time_ = $postDetails['Post_time'];
            $category_of_post = urlencode($postDetails['Category']);
            if (isset($_GET['page'])) {
              $current_page = $_GET['page'];
            } else {
              $current_page = 1;
            }
            
            
            
            $ID = $post_no."_A"; /*specific id for an html span elements,that allows a js ajax callback  function identify it and update the no_of_likes receieved by the post.*/
            $id = $post_no."_B"; /*specific id for the like html button element,it allows js ajax callback identify this element an change it from like to liked /vice versa*/
            $_ID = $post_no."_C"; /* specific id for the html follow button so it can be altered by js ajax callback function*/
    
              /*
              Check if current user is already following the post.
             */ 
           $postfollowers = $postDetails['Post_Followers'];
            if(isset($_SESSION['Username']) && preg_match("/$username/",$postfollowers)){
              $follow ="Post Followed";
            }else{
              $follow="Follow post";
            }
             
             
            /*
        	check what the meaning of what the Ids and LikeConfirmer variable does
      	at the line 230 below...  It uses the same mechanism.Same as rhe reply.php script..CHECK LINE 230 HERE BELOW
        	*/

            $LikeConfirmer = $postDetails['LikeConfirmer'];
            if (isset($_SESSION['Username']) && preg_match("/$username/", $LikeConfirmer)) {
              $like = "<i class='fa fa-thumbs-up'>Liked</i>";
            } else {
              $like = "<i class='fa fa-thumbs-o-up'>Like</i>";
            }

            $data = <<<HTML
        <div class="w3-card-4 " style="width:100%">
          <p class="w3-opacity w3-tiny w3-padding-ver-16" style="font-family:cursive">
            In {$postDetails['Category']}
          </p>
          <!-- OP Name and time lapse of post-->
          <div class="w3-row w3-padding-small" style="width:100%">
            <p class="w3-center w3-cursive">{$postDetails['Post_title']}</p>
            <div class="w3-left w3-cell w3-small">
             <b>By {$postDetails['Post_admin']} </b>
            </div>
            <div class="w3-right w3-cell w3-tiny w3-opacity">
              $post_time
            </div>
          </div>
          <!-- Main content of post-->
          <div class="w3-container w3-padding-large w3-leftbar w3-border-grey">
          {$postDetails['Post_content']}
          <br>
          <br>
           <div style="display:flex;flex-direction:column;flex-wrap:nowrap;">
            $image1
            $image2
            $image3
           </div>
          </div>
          <footer class="" >
            <!-- Post statistics-->
            <div class="w3-bar w3-opacity " style="font-family:cursive">
              <a class="w3-bar-item w3-center" style="width:50%"> No of likes:<span id="$ID">$no_of_likes_for_post  </span></a>
              <a class="w3-bar-item w3-center" style="width:50%">No of comments:<span></span>$no_of_comments_for_post</a>
            </div>
            <!-- Post options-->
            <div class="w3-padding-small w3-tiny">
              <button id="$id" onclick="Post_Liker_Unliker(1)" class="w3-button">$like</button>
              <button  class="w3-button"><a class="w3-button" href="create_new_comment_and_reply_edit_existing_comment_and_reply.php?Post_no=$post_no&Post_title=$post_title_&Post_time=$post_time_&Category_of_post=$category_of_post&Post_admin=$post_owner&newComment=1&page=$current_page"><i class="fa fa-comment"></i>Comment</a></button>
              <button id="$_ID" onclick="Follow_Unfollow_Post()" class="w3-button">$follow</button>
            </div>
          </footer>
        </div>
HTML;
            echo $data;

          }

        }catch(Exception $e) {
          /* any error faced is caught by the try block and an http response is displayed to mask the error */
          /*the error can be caused by database connection not going through due to overloaded site */
          //http_response_code(404);
          echo $e->getMessage();
        }

$toAdmin = <<<HTML
<div class="w3-panel w3-leftbar w3-border-yellow w3-pale-yellow">
Hey there?,Only you can see this.Edit Post <a href="" >Here</a>
</div>
HTML;
        if (isset($_SESSION['Username'])) {
          if ($_SESSION['Username'] == $postDetails['Post_admin']) {
            echo $toAdmin;
          }
        }


        ?>
        <!--

        	The next block is the comment block...it does this lost of things...
        	Fetches a limited amount of comments(if they're plenty ) ,
        	Fetches the no_of_replies,no,of likes for each comments and embeds a link in them to allow you
        	redirect tho a create_new_comment_and_reply_edit_existing_comment_and_reply.php page where you can reply a comment.

        	It shows if the current viewer(a person with a account of this forum)/ of a page has liked a post before......
        	Pagnation is also employed so as to group the no_of_comment in batches...
        -->
        <div class="commentBlock">

          <?php

          require_once("pagnation.php");

          try {
            //checks if post has any comments
            if ($no_of_comments_for_post != 0) {

              /*
	next is to load the comments by resetting the connection link
	and running a fresh query on the Comments table
	It also implements pagnation too
	*/

              $dbh = $instance->getConnection();
              $sqlQuery = "SELECT * FROM Comments WHERE `Post_no`=? ORDER BY `Time` ASC LIMIT ?,? ";
              $dbh = $dbh->prepare($sqlQuery);
              $dbh->bindParam(1, $post_no, PDO::PARAM_INT);
              $dbh->bindParam(2, $offset, PDO::PARAM_INT);
              $dbh->bindParam(3, $comment_per_page, PDO::PARAM_INT);
              $dbh->execute();
              while ($comments = $dbh->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
                /*
		The session username...username of the currently logged in user is assigned to a variable for easy reference;
		*/

                $username = $_SESSION['Username'];

                /*
		The variables required for sending a reply to a comment is created ...
		*/
                $category_of_comment = urlencode($comments['Category']);
                $comment_username = $comments['Comment_username'];
                $comment_bind_replies_id = $comments['Comment_bind_replies_id'];
                $comment_time = $comments['Time'];

      //next if block check if post contains media
               renderCommentMedia();
             
//this evaluates the time lapse
               $post_time = time() - $comment_time;
               if($post_time <  60){
                 $post_time = "Less than ".$post_time." s";
               }elseif($post_time <= 3600 && $post_time >60){
                 $post_time = round($post_time/60);
                 $post_time = $post_time." min ago";
               }elseif($post_time > 3600 && $post_time <= 86400){
                   $post_time= round($post_time/3600);
                if($post_time > 1){
                  $unit="hrs";
                }else{
                  $unit="hr";
                }
                $post_time= $post_time.$unit." ago ";
               }
               elseif($post_time > 86400){
                 $post_time= round($post_time/86400);
                if($post_time > 1){
                  $unit="days";
                }else{
                  $unit="day";
                }
                $post_time= $post_time.$unit." ago ";
               }
               

                /*
		The ids are created to specifically identify an html elements,
		In this case...we need to specifically identify the like button and the
		no_of_likes counter...
		So when this like button is clicked.... it innerHTML is changed and the
		no of likes counter is either incrreased of decreased
		*/


                $ID = $comments['Comment_username']."|".$comments['Time']."|_A";
                $id = $comments['Comment_username']."|".$comments['Time']."|_B";

                /*
	if the name of the comment_username maches the current session name ,it means the owner
	of the comment is the one viewing the post ...He/She cannot like his/her own post.
		*/
                if ($comments['Comment_username'] == $_SESSION['Username']) {
                  //load your comment
                  $comment = <<<COMMENT
		<br>
		 <div class="w3-card-4 " style="width:100%">
          <!-- OP Name and time lapse of post-->
          <div class="w3-row w3-padding-small" style="width:100%">
            <div class="w3-small w3-left w3-cell w3-border-bottom w3-padding-large" style="width:100%">
              <b>{$comments['Comment_username']}</b> says
            </div>
            <div class="w3-right w3-cell w3-opacity w3-tiny">
             $post_time 
            </div>
          </div>
          <!-- Main content of post-->
          <div class="w3-container w3-padding-large w3-leftbar w3-border-grey">
          {$comments['Comment_content']}
          <br>
          <br>
           <div style="display:flex;flex-direction:column;flex-wrap:nowrap;;">
            $image1
            $image2
            $image3
           </div>
         
          </div>
          <footer class="" >
            <!-- Post statistics-->
            <div class="w3-bar w3-opacity " style="font-family:cursive">
              <a class="w3-bar-item w3-center" style="width:50%"> No of likes:<span>{$comments['Comment_likes']} </span></a>
              <a href="replies.php?Category=$category_of_comment&Comment_bind_replies_id=$comment_bind_replies_id" class="w3-bar-item w3-center" style="width:50%">No of comments:<span>{$comments['Comment_replies_count']}</span></a>
            </div>
            <!-- Post options-->
            <div class="w3-padding-small">
             <a class="w3-button" href="replies.php?Category=$category_of_comment&Comment_bind_replies_id=$comment_bind_replies_id" >  <button class="w3-button w3-tiny">View</button></a>
             <a class="w3-button" href="create_new_comment_and_reply_edit_existing_comment_and_reply.php?userComment=$comment_username&Post_no=$post_no&CommentTime=$comment_time" ><button class="w3-button w3-tiny">Edit Comment</button></a>
            </div>
          </footer>
        </div>
COMMENT;
                  echo $comment;
                  echo "<br>";
                } else {
                  /*
		load another person's comment
		But first.....we create a mechanism that ensures that a user can  see if
		he or she has already like a post.
			The next 5lines of code helps check for the occurence
			of the  logged in person's username....If it's found in the
			like confirmer variable...it means he or she has previously liked the code.
	*/

                  $LikeConfirmer = $comments['LikeConfirmer'];
                  if (isset($_SESSION['Username']) && preg_match("/$username/", $LikeConfirmer)) {
                    $like = "<i class='fa fa-thumbs-up'>Liked</i>";
                  } else {
                    $like = "<i class='fa fa-thumbs-o-up'>Like</i>";
                  }


                  $comment = <<<COMMENT
			 <div class="w3-card-4 " style="width:100%">
          <!-- OP Name and time lapse of post-->
          <div class="w3-row w3-padding-small" style="width:100%">
            <div class="w3-small w3-left w3-cell w3-border-bottom w3-padding-large" style="width:100%">
              <b>{$comments['Comment_username']}</b> says
            </div>
            <div class="w3-right w3-cell w3-opacity w3-tiny">
             $post_time 
            </div>
          </div>
          <!-- Main content of post-->
          <div class="w3-container w3-padding-large w3-leftbar w3-border-grey">
          {$comments['Comment_content']}
            <div style="display:flex;flex-direction:column;flex-wrap:nowrap;">
            $image1
            $image2
            $image3
           </div>
          </div>
          <footer class="" >
            <!-- Post statistics-->
            <div class="w3-bar w3-opacity " style="font-family:cursive">
              <a class="w3-bar-item w3-center" style="width:50%"> No of likes:<span id="$ID">{$comments['Comment_likes']} </span></a>
            <a href="replies.php?Category=$category_of_comment&Comment_bind_replies_id=$comment_bind_replies_id" class="w3-bar-item w3-center" style="width:50%">No of replies:<span>{$comments['Comment_replies_count']}</span></a>
            </div>
            <!-- Post options-->
            <div class="w3-padding-small">
              <button  id="$id" onclick="CommentLiker_Unliker('$id')" class="w3-button w3-tiny">$like</button>
              <button class="w3-button w3-tiny"><a class="w3-button" href='create_new_comment_and_reply_edit_existing_comment_and_reply.php?newReply=1&Comment_owner=$comment_username&Category_of_comment=$category_of_comment&Comment_bind_replies_id=$comment_bind_replies_id&time=$comment_time' ><i class="fa fa-comment"></i> Reply </a></button>
            </div>
          </footer>
        </div>

		</div>
COMMENT;
                  echo $comment;
                  echo "<br>";

                }


              }

            } else {
              throw new Exception("No comments found");
            }


          }catch(Exception $e) {
            echo $e->getMessage();
          }
          ?>

        </div>
        <!-- End of middle container-->

        <!--Right container -->
        <div class="w3-container w3-content w3-col m3 l4  w3-hide-small">

        </div>


      </div>
<script>
        /*
Javascript function to update the post like button and no of likes
*/

function Post_Liker_Unliker(check) {
  if (checkIfLoggedIn() == false) {
    return false;
  };

  var check = 1;
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status ==
      200) {
      document.getElementById("<?php echo $post_no."_B"; ?>").innerHTML = this.responseText;
      alterPostLikes(this.responseText);

    }
  };
  var link = "Post_Liker_Unliker.php?checker=".concat(check, "<?php $post_title_=urlencode($post_title); echo  '&Post_no='.$post_no.'&Post_title='.$post_title_.'&Post_owner='.$post_owner; ?>");
  xhttp.open("GET", link, true);
  xhttp.send();
  }

function alterPostLikes(check){
  //var post_likes = document.getElementById("<?php echo $post_no.'_A'; ?>").innerHTML ;
  if (check == "<i class='fa fa-thumbs-up'>Liked</i>") {
    document.getElementById("<?php echo $post_no.'_A'; ?>").innerHTML = parseInt(document.getElementById("<?php echo $post_no.'_A'; ?>").innerHTML) + 1;
  } else if (check == "<i class='fa fa-thumbs-o-up'>Like</i>") {
    document.getElementById("<?php echo $post_no.'_A'; ?>").innerHTML = parseInt(document.getElementById("<?php echo $post_no.'_A'; ?>").innerHTML) - 1;
  }
  }


/*
Javascript function to update a comments like button and no_of likes counter
It uses the PostComments and CommentReplies liker and unliker script
*/


function CommentLiker_Unliker(id) {
  if (checkIfLoggedIn() == false) {
    return false;
  };
  var arr = id.split("|");
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status ==
      200) {
      document.getElementById(id).innerHTML = this.responseText;
      var ID = arr[0].concat("|", arr[1], "|_A");
      var value = document.getElementById(String(ID)).innerHTML;

      if (this.responseText == "<i class='fa fa-thumbs-o-up'>Like</i>") {
        document.getElementById(ID).innerHTML = parseInt(value) - 1;
      } else if (this.responseText == "<i class='fa fa-thumbs-up'>Liked</i>") {
        document.getElementById(ID).innerHTML = parseInt(value) + 1;
      }

    }
  };
  var link = "PostComments_CommentReplies_Liker_Unliker.php?commentlike_id=".concat(id, "<?php echo '&Post_no='.$post_no.'&Post_admin='.$post_owner; ?>");
  xhttp.open("GET", link, true);
  xhttp.send();
  }


/*
Js function that runs to follow or unfollow  post
*/

function Follow_Unfollow_Post(){
  if (checkIfLoggedIn() == false) {
    return false;
  };
  var xhr = new XMLHttpRequest();
  xhr.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      if (this.responseText == "Post Unfollowed") {
        document.getElementById('<?php echo $_ID; ?>').innerHTML = this.responseText;

      } else if (this.responseText == "Post Followed") {
        document.getElementById('<?php echo $_ID; ?>').innerHTML = this.responseText;

      }

    }

  }
  var url = 'Follow_Unfollow_Post.php<?php echo "?Post_no=".$post_no?>';
  xhr.open("GET", url, true);
  xhr.send();
  }

function checkIfLoggedIn(){
  var user = '<?php echo $username ?>';
  if (user == "") {
    alert('Please Login');
    return false
  } else {
    return true;
  }
  }
</script>
    </body>
  </html>