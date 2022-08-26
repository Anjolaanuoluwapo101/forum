<?php
session_name("ProgrammersHub");
session_start();
$username = $_SESSION['Username'];
require_once('utilityfunctions.php');
?>

<!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <!--	<link rel="stylesheet" href="css/w3.css">-->
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="css/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="css/w3.css">
  <link rel="stylesheet" href="fonts/arial.ttf">
  <link rel="stylesheet" href="css/w3-theme-blue-grey.css">
  <style>
    html, body, h1, h2, h3, h4, h5 div {
      font-family: Arial;
    }
  </style>
</head>
<body>
    <div class="w3-row" style="margin-top:50px">
      
      <!-- Middle container-->
    <div class="w3-container w3-content w3-col m6 l4">
   
  <?php
  try {

    //get variables from link;
    $category = $_GET['Category'];
    $comment_bind_replies = $_GET['Comment_bind_replies_id'];

    //confirm that get request link was not tampered with...
    require_once("class.php");


    $sqlQuery = "SELECT COUNT(*) FROM Comments WHERE `Category`=? AND `Comment_bind_replies_id`=?";
    $dbh = $dbh->prepare($sqlQuery);
    $dbh->bindParam(1, $category, PDO::PARAM_STR);
    $dbh->bindParam(2, $comment_bind_replies, PDO::PARAM_INT);
    $dbh->execute();
    $check = $dbh->fetchColumn();
    if ($check == 0) {
      /* if the comment doesn't exist due to link tampering,a http 404 page is generated instead*/
      http_response_code(404);
      die();
    } else {
      /* If the comment exists,the database connection link is reset by calling the singleton class method  again to return the database connection
		Using a singleton class ensures that,a previous database connection is used and authentication (which may cause delay ) is eliminated*/
      /* Then the post details are fetched */
      $dbh = $instance->getConnection();
      $sqlQuery = "SELECT * FROM Comments WHERE `Category`=? AND `Comment_bind_replies_id`=?";
      $dbh = $dbh->prepare($sqlQuery);
      $dbh->bindParam(1, $category, PDO::PARAM_STR);
      $dbh->bindParam(2, $comment_bind_replies, PDO::PARAM_INT);
      $dbh->execute();

      /*
		Populate variables that would display details about that comment
		*/
      $comments = $dbh->fetch(PDO::FETCH_ASSOC);
      $Comment_time = gmdate("D \, H:i:s M Y", $comments['Time']+3600);
      $no_of_replies = $comments['Comment_replies_count'];
 
    //next if block check if post contains media
      renderCommentMedia();
   

      /*
		The ids are created to specifically identify an html elements,
		In this case...we need to specifically identify the like button tag and the
		no_of_likes counter html span tag...
		So when this like button is clicked.... it innerHTML is changed and the
		no of likes counter is either incrreased of decreased
		*/


      $ID = $comments['Comment_username']."|".$comments['Time']."|_A";
      $id = $comments['Comment_username']."|".$comments['Time']."|_B";
              
              
              /*
              Calculate timelapse for the comment
              */
                  $post_time = time() - $comments['Time'];
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
		We also neeed to populate the variables that would be sent to along with the create_new_comment_and_reply_edit_existing_comment_and_reply link
		*/
      $comment_username = $comments['Comment_username'];
      $comment_time = $comments['Time'];
      $category_of_comment = $comments['Category'];
      $comment_bind_replies_id = $comments['Comment_bind_replies_id'];


      $LikeConfirmer = $comments['LikeConfirmer'];

      if (isset($_SESSION['Username']) && preg_match("/$username/", $LikeConfirmer)) {
        $like = "<i class='fa fa-thumbs-up'>Liked</i>";
      } else {
        $like = "<i class='fa fa-thumbs-o-up'>Like</i>";
      }
      /* This is the comment from the OP's block*/
      $data = <<<HTML
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
          
           <div  style="display:flex;flex-direction:column;flex-wrap:nowrap;;">
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

	
HTML;
      echo $data;


    }
  }catch(Exception $e) {
    echo "Invalid access";
  }

 $toCommentOwner = <<<HTML
<div class="w3-panel w3-leftbar w3-border-yellow w3-pale-yellow">
<ins>Hey there,</ins> only you can see this
<a>Click Here</a> to edit post
</div>
HTML;


  if (isset($_SESSION['Username'])) {
    if ($_SESSION['Username'] == $comments['Comment_username']) {
      echo $toCommentOwner;
    }
  }
  ?>





  <br>
  <br>
  <div class="commentBlock">
    <?php

    require_once("pagnation.php");


    try {
      //checks if post has any comments
      if ($no_of_replies != 0) {
        $dbh = $instance->getConnection();
        $sqlQuery = "SELECT * FROM Replies WHERE `Category`=? AND `Comment_bind_replies_id`=? ORDER BY `Time` ASC LIMIT ?,? ";
        $dbh = $dbh->prepare($sqlQuery);
        $dbh->bindParam(1, $category, PDO::PARAM_STR);
        $dbh->bindParam(2, $comment_bind_replies, PDO::PARAM_INT);
        $dbh->bindParam(3, $offset, PDO::PARAM_INT);
        $dbh->bindParam(4, $replies_per_page, PDO::PARAM_INT);
        $dbh->execute();

        while ($replies = $dbh->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {

          $ID = $replies['Reply_username']."|".$replies['Time']."|_A";
          $id = $replies['Reply_username']."|".$replies['Time']."|_B";

              renderReplyMedia();
          
               $post_time = time() - $replies['Time'];
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
               
          if ($replies['Reply_username'] == $_SESSION['Username']) {
            $replytime = $replies['Time'];
            $reply = <<<REPLY
      	<div class="w3-card-4 " style="width:100%">
          <!-- OP Name and time lapse of post-->
          <div class="w3-row w3-padding-small" style="width:100%">
            <div class="w3-small w3-left w3-cell w3-border-bottom w3-padding-large" style="width:100%">
              <b>{$replies['Reply_username']}</b> replied
            </div>
            <div class="w3-right w3-cell w3-opacity w3-tiny">
             $post_time
            </div>
          </div>
          <!-- Main content of post-->
        <div class="w3-container w3-padding-large w3-leftbar w3-border-grey">
          {$replies['Reply_content']}
           <div style="display:flex;flex-direction:column;flex-wrap:nowrap;">
            $image1
            $image2
            $image3
           </div>
          </div>
          <footer class="" >
            <!-- Post statistics-->
            <div class="w3-bar w3-opacity " style="font-family:cursive">
              <a class="w3-bar-item w3-center" style="width:50%"> No of likes:<span id="$ID">{$replies['Reply_likes']} </span></a>
            </div>
            <!-- Post options-->
            <div class="w3-padding-small">
              <button class="w3-button w3-tiny"><a class="w3-button" href='create_new_comment_and_reply_edit_existing_comment_and_reply.php?newReply=1&Comment_owner=$comment_username&Category_of_comment=$category_of_comment&Comment_bind_replies_id=$comment_bind_replies_id&time=$comment_time' ><i class="fa fa-comment"></i> Reply </a></button>
            </div>
          </footer>
        </div>
		
REPLY;
            echo $reply;
            echo "<br>";

          } else {

            /*the else blocks loaads every comment for othwr users that
			where not posted by the the owner of the session */
            /*but first it confirms if the session_owner has liked the other person's post */

            /*
			Like confirmer helps check if user already likes another person's post .
			*/
            $LikeConfirmer = $replies['LikeConfirmer'];



            if ((isset($_SESSION['Username']) && preg_match("/$username/", $LikeConfirmer))) {
              $like = "<i class='fa fa-thumbs-up'>Liked</i>";
            } else {
              $like = "<i class='fa fa-thumbs-o-up'>Like</i>";
            }


            $reply = <<<REPLY
		   	<div class="w3-card-4 " style="width:100%">
          <!-- OP Name and time lapse of post-->
          <div class="w3-row w3-padding-small" style="width:100%">
            <div class="w3-small w3-left w3-cell w3-border-bottom w3-padding-large" style="width:100%">
              <b>{$replies['Reply_username']}</b> replied
            </div>
            <div class="w3-right w3-cell w3-opacity w3-tiny">
             $post_time
            </div>
          </div>
          <!-- Main content of post-->
          <div class="w3-container w3-padding-large w3-leftbar w3-border-grey">
          {$replies['Reply_content']}
            <div style="display:flex;flex-direction:column;flex-wrap:nowrap;">
            $image1
            $image2
            $image3
           </div>
          </div>
          <footer class="" >
            <!-- Post statistics-->
            <div class="w3-bar w3-opacity " style="font-family:cursive">
              <a class="w3-bar-item w3-center" style="width:50%"> No of likes:<span id="$ID">{$replies['Reply_likes']} </span></a>
            </div>
            <!-- Post options-->
            <div class="w3-padding-small">
            		<button class="w3-button w3-tiny" id="$id" onclick="Reply_Liker_Unliker('$id')">$like</button>
              <button class="w3-button w3-tiny"><a class="w3-button" href='create_new_comment_and_reply_edit_existing_comment_and_reply.php?newReply=1&Comment_owner=$comment_username&Category_of_comment=$category_of_comment&Comment_bind_replies_id=$comment_bind_replies_id&time=$comment_time' ><i class="fa fa-comment"></i> Reply </a></button>
            </div>
          </footer>
        </div>
REPLY;
            echo $reply;
            echo "<br>";

          }


        }

      } else {
        throw new Exception("No Replies found");

      }


    }catch(Exception $e) {
      echo $e->getMessage();
    }

    ?>
   <!-- This ends the comment block -->
   </div>
    <!-- This exits the middle container -->
   </div>
   
   <!--This div tag is the Tag reply -->
   <div style="display:none">
     
   </div>
   <!-- This exits the  w3-row class-->
  </div>
  
  
  <br>
  <br>
  <div class="pagnation">
    <?php
    /*
		Implementing pagnation check pagnation.php...
		*/

    pagnation();


    ?>

    <script>
    let touchstartX = 0
    let touchendX = 0
    
    function checkDirection() {
      if (touchendX < touchstartX) {
        alert('swiped left!');
      }
      if (touchendX > touchstartX) {
        alert('swiped right!');
    }
    }
    
    document.addEventListener('touchstart', e => {
      touchstartX = e.changedTouches[0].screenX
    })
    
    document.addEventListener('touchend', e => {
      touchendX = e.changedTouches[0].screenX;
      checkDirection();
    })
        
    
    
      /*
This alters the comment like and number of likes
*/
      function CommentLiker_Unliker(id) {
        if (checkIfLoggedIn() == false) {
          return false;
        }
        var id = id;
        var arr = id.split("|");
        //var check = 1;
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
        var link = "PostComments_CommentReplies_Liker_Unliker.php?commentlike_id=".concat(id, "<?php echo '&Post_no='.$post_no; ?>");
        xhttp.open("GET", link, true);
        xhttp.send();
      }




      /*
Javascript function to update a replies like button and no_of likes counter

It uses the PostComments and CommentReplies liker and unliker script

*/

      function Reply_Liker_Unliker(id) {
        if (checkIfLoggedIn() == false) {
          return false;
        }
        var id = id;
        var arr = id.split("|");
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status ==
            200) {
            alert(this.responseText);
            document.getElementById(id).innerHTML = this.responseText;
            //alert(document.getElementById(id));
            var ID = arr[0].concat("|", arr[1], "|_A");
            var value = document.getElementById(String(ID)).innerHTML;

            if (this.responseText == "<i class='fa fa-thumbs-o-up'>Like</i>") {
              document.getElementById(ID).innerHTML = parseInt(value) - 1;
            } else if (this.responseText == "<i class='fa fa-thumbs-up'>Liked</i>") {
              document.getElementById(ID).innerHTML = parseInt(value) + 1;
            }

          }
        };
        var link = "PostComments_CommentReplies_Liker_Unliker.php?replylike_id=".concat(id, "<?php echo '&Comment_bind_replies_id='.$comment_bind_replies; ?>");
        xhttp.open("GET", link, true);
        xhttp.send();
      }


      function checkIfLoggedIn() {
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