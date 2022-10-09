<?php
session_name('ProgrammersHub');
session_start();
if (isset($_SESSION['Username'])) {
  require_once('helperfiles/class.php');
  $username = $_SESSION['Username'];
} else {
  http_response_code(403);
  die();
}


if (isset($_POST['postText'])) {
  require_once("helperfiles/utilityfunctions.php");
  require_once("helperfiles/class.php");
  $postText = $_POST['postText'];
  $postTitle = "<b>".$username."</b>  shared a thought...";
  $categories = "Personal";
  $post_time = time();
  $postText = htmlentities($postText); //gets rid of html tag injection



  checkMediaCompatibility(1);

  $dbh = $dbh->prepare("INSERT INTO Posts(`Post_admin`,`Post_title`,`Post_content`,`image1`,`image2`,`image3`,`Post_time` ) VALUES(?,?,?,?,?,?,?)");
  $dbh->bindParam(1, $username, PDO::PARAM_STR);
  $dbh->bindParam(2, $postTitle, PDO::PARAM_STR);
  $dbh->bindParam(3, $postText, PDO::PARAM_STR);
  $dbh->bindParam(4, $imglink1, PDO::PARAM_STR);
  $dbh->bindParam(5, $imglink2, PDO::PARAM_STR);
  $dbh->bindParam(6, $imglink3, PDO::PARAM_STR);
  $dbh->bindParam(7, $post_time, PDO::PARAM_INT);
  $dbh->execute();

  //increase the no_of_posts made column in UserProfile
  $dbh = $instance->getConnection();
  $dbh = $dbh->prepare('UPDATE UserProfile SET `no_of_posts` = `no_of_posts`+1  WHERE `Username` =?');
  $dbh->bindParam(1, $username, PDO::PARAM_STR);
  $dbh->execute();
  echo "<script>alert(Post shared,check your profile to view personal thoughts.); history.back(); </script>";
  $dbh = $instance->getConnection(); //this resets the connection link
}
?>


<html>
<head>
  <title>News Feed</title>
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
      font-family:Arial;
      src: url(arial.ttf);
    }
    html, body, h1, h2, h3, h4, h5 div {
      font-family: Arial;
    }

    #leftContainer {
      top: 50px!important;
    }
  </style>

  <body>
    <!--Beginning of nav bar for all sizes  -->
    <?php
    require_once('nav_bar.php');
    ?>
    <!--End of nav bar for all screen sizes -->

    <!-- News Feed -->
    <div style="margin-top:30px" class='w3-container w3-opacity w3-left-align'>
      <h3 style='font-weight:900'>NEWS FEED</h3>
    </div>
    <!-- End of News Feed -->

    <!-- Page Container -->
    <div class="w3-container" style="max-width:1400px;margin-top:50px">
      <div class="w3-row">

        <!-- Left Container-->
        <?php
        require_once('Left_Container.php')
        ?>

        <!-- Middle container -->
        <div class="w3-col m5 l4">
          <!-- Create Post -->
          <div class="w3-container w3-padding-2px">
            <form enctype="multipart/form-data" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
              <textarea name="postText" onfocus="hide_show()" class="" style="width:100%;height:50px;" placeholder="Share a thought....." required></textarea>

              <!-- collapsible div tag that houses post media submission-->
              <div class="w3-container w3-hide" id='fileMedia'>
                <input type="file" name="files[]" class="w3-input" style="width:100%">
                <input type="file" name="files[]" class="w3-input" style="width:100%">
                <input type="file" name="files[]" class="w3-input" style="width:100%">
              </div>

              <button type="submit" class="w3-right w3-btn">Share</button>
            </form>
          </div>

          <!-- Other peoples post-->

          <?php

          $sqlQuery = "SELECT `Following_list` FROM UserProfile WHERE `Username`=? "; //obtains the list of people the current user is following..
          $dbh = $dbh->prepare($sqlQuery);
          $dbh->bindParam(1, $username, PDO::PARAM_STR);
          $errorCheck = $dbh->execute();
          $following_list = $dbh->fetchAll(PDO::FETCH_COLUMN);
          $following_list = $following_list[0]; //since fetchAll returns a mulitdimensional array.
          $following_list = explode('||', trim($following_list)); //this converts the text string(with the delimiter '||') of that column to an array.
          $array_length = count($following_list); //gets array length
          array_pop($following_list);//removes the empty last element from the array
          print_r($following_list);
          shuffle($following_list); //shuffle the array
          /*
the next db query fetches posts from the usernames which the current user is
following
*/

          require_once('helperfiles/generator.php');

          try {
            if ($array_length == 1 || $array_length > 1) {
              $dbh = $instance->getConnection();
              $sqlQuery = "SELECT * FROM Posts WHERE `Post_admin` = ?  OR `Post_Followers` LIKE ? ";
              $newsFeed = "";


              //   foreach (xrange(0,$array_length,1) as $val) {
              for ($val = 0; $val < $array_length-1; $val++) {
                $eachfollower = "%".$following_list[$val]."%";
                $dbh = $instance->getConnection();
                $dbh = $dbh->prepare($sqlQuery);
                $dbh->bindParam(1, $following_list[$val], PDO::PARAM_STR);
                $dbh->bindParam(2, $eachfollower, PDO::PARAM_STR);
                $dbh->execute();
                $result = $dbh->fetchAll(PDO::FETCH_ASSOC);


                if (count($result) == 1) {

                  $eachPost = $result[0];
            
                  require('helperfiles/nf_content_template.php');
                  echo $data;
                } elseif (count($result) > 1) {
                  $sub_array_length = count($result)-1;
                  foreach (xrange(0, $sub_array_length, 1) as $i) {
                  $eachPost = $result[$i];
                  require('helperfiles/nf_content_template.php');
                  $newsFeed .= $data;
                  }
                  echo $newsFeed;
                  $newsFeed="";
                }
              }
              //  echo $newsFeed;
            } else {
              echo "No followers yet";
            }
          }catch(PDOException $e) {
            echo "Couldn't connect to database";
            echo $e->getMessage();
          }

          ?>


        </div>

        <!--Right container -->
        <div id="rightContainer" class="w3-hide-small w3-col m4 l4  w3-container">

        </div>

        <!-- Right container for small screen devices-->
        <div class="w3-container w3-animate-right w3-medium-hide w3-large-hide" id="rightContainerCollapsible" style="position:fixed;z-index:1000;top:0;right:0;display:none;width:80%;height:100%;background-color:white!important;overflow-y:scroll;margin-top:50px">

        </div>


      </div>
    </div>
    <!--End of page container -->

    <!--script that controls side bar -->
    <script src="js/sidebar.js"></script>
    <script type="application/javascript" src="js/CheckUserLoggedIn.php"></script>
    <script src="js/delete.js"></script>

    <script>


      /*
Javascript function to update the post like button and no of likes
This works diffeerently from the Post_Liker_Unliker function in displaypost.
*/

      function Post_Liker_Unliker(id) {
        if (checkIfLoggedIn() == false) {
          return false;
        }
        var credentials = id.split("|"); //splits the id along the delimiter into an array of 4;
        var post_no = credentials[0];
        var post_title = credentials[1];
        var post_owner = credentials[2];
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status ==
            200) {
            var likeButton = post_no.concat("|", post_title, "|", post_owner, "|_B");
            var no_of_likes_for_post = post_no.concat("|", post_title, "|", post_owner, "|_A");
            document.getElementById(likeButton).innerHTML = this.responseText;

            if (this.responseText == "<i class='fa fa-thumbs-up'>Liked</i>") {
              document.getElementById(no_of_likes_for_post).innerHTML = post_likes + 1;
            } else if (this.responseText == "<i class='fa fa-thumbs-o-up'>Like</i>") {
              document.getElementById(no_of_likes_for_post).innerHTML = post_likes - 1;
            }
          }
        };
        var link = "helperfiles/Post_Liker_Unliker.php?".concat("Post_no=", post_no, "&Post_title=", post_title, "&Post_owner=", post_owner);
        xhttp.open("GET", link, true);
        xhttp.send();
      }

      //to hide or show input file type media
      function hide_show() {
        if (document.getElementById('fileMedia').className.indexOf("w3-hide") != -1) {
          document.getElementById('fileMedia').className += " w3-show";
        } else {
          document.getElementById('fileMedia').className += " w3-hide";
        }
      }


      //to load notification from database after newfeed as been loaded
      function updateNotification() {
        if (checkIfLoggedIn() == false) {
          return false;
        }
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status ==
            200) {
            document.getElementById("rightContainer").innerHTML += this.responseText;
            document.getElementById("rightContainerCollapsible").innerHTML += this.responseText;
          }
        };
        var link = "helperfiles/notification_activity_logs.php?Username=<?php echo $username; ?>";
        xhttp.open("GET", link, true);
        xhttp.send();
      }

      setTimeout(function() {
        updateNotification();
      }, 2000);

      function showNotifs() {
        //this funtion will only fire if site is on small screen window
        if (window.matchMedia("(max-width: 767px)").matches) {
          if (document.getElementById('rightContainerCollapsible').style.display == "none") {
            document.getElementById('rightContainerCollapsible').style.display = "block";
          } else {
            document.getElementById('rightContainerCollapsible').style.display = "none";
          }
        }
      }
      /*
      if (window.matchMedia("(max-width: 767px)").matches)
{
      // The viewport
alert("This is a mobile device.");
}
   */
    </script>
  </body>
</html>