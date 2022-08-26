<?php
session_name('ProgrammersHub');
  session_start();
   if (isset($_SESSION['Username'])) {
     require_once('class.php');
     $username = $_SESSION['Username'];
     $GLOBALS['check'] = $username;
     } else {
       http_response_code(403);
      die();
    }


if (isset($_POST['postText'])) {
  /* session_name('ProgrammersHub');
  session_start();
  if (isset($_SESSION['Username'])) {
    require_once('class.php');
    $username = $_SESSION['Username'];
  } else {
    http_response_code(403);
    die();
  } */


  require_once("utilityfunctions.php");
  require_once("class.php");
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
    <!--End of nav bar for all screen sizes -->

    <!-- News Feed -->
    <div style="margin-top:30px" class='w3-container w3-opacity w3-left-align'>
      <h3 style='font-weight:900'>NEWS FEED</h3>
    </div>
    <!-- End of News Feed -->

    <!-- Page Container -->
    <div class="w3-container" style="max-width:1400px;margin-top:20px">
      <div class="w3-row">
        <!-- Left container -->
        <div class="w3-hide-small w3-col m4 l4  w3-container">
          <button onclick="showAccordion('show1')" class="w3-btn w3-block w3-black w3-center"> Profile <i class="fa fa-profile"></i> </button>
          <div id="show1" class="w3-container w3-hide" style="width:100%">
            <a href="" class="w3-button"> <i class="fa fa-bell">&nbsp Notification</i></a>
            <p>
              
            </p>
          </div>
        <button onclick="showAccordion('show2')" class="w3-btn w3-block w3-black w3-center">Categories</button>
          <div id="show2" class="w3-container w3-hide" style="width:100%">
            <h4>Section 1</h4>
            <p>
              Some text..
            </p>
            <button onclick="showAccordion('show2a')" class="w3-btn w3-block w3-black w3-center"> Health Care <i class="fa fa-profile"></i> </button>
              <div id="show2a" class="w3-container w3-hide" >
               <a class="w3-button w3-tiny w3-center" style="width:100%;">Pharmacy</a>
              </div>
            <button onclick="showAccordion('show2b')" class="w3-btn w3-block w3-black w3-center"> Technology <i class="fa fa-profile"></i> </button>
              <div id="show2b" class="w3-container w3-hide ">
               <a class="w3-button  w3-tiny w3-center" style="width:100%;">Programming</a>
              </div>
          </div>

          <div class="" style="" >
          <a class="w3-button w3-left-align " style="width:100%">Categories</a>
          <a class="w3-button w3-left-align" style="width:100%">Some Text</a>
          <a class="w3-button w3-left-align" style="width:100%">About</a>
          </div>
          <!--End of left container-->
        </div>
        
        <!-- Container for small screen sized phones-->
          <div class="w3-container" id="leftContainer" style="position:fixed;display:none;width:80%;height:100%;padding-top:100px">
          <button onclick="showAccordion('show1')" class="w3-btn w3-block w3-black w3-center"> Profile <i class="fa fa-profile"></i> </button>
          <div id="show1" class="w3-container w3-hide" style="width:100%">
            <a href="" class="w3-button"> <i class="fa fa-bell">&nbsp Notification</i></a>
            <p>
              
            </p>
          </div>
        <button onclick="showAccordion('show2')" class="w3-btn w3-block w3-black w3-center">Categories</button>
          <div id="show2" class="w3-container w3-hide" style="width:100%">
            <h4>Section 1</h4>
            <p>
              Some text..
            </p>
            <button onclick="showAccordion('show2a')" class="w3-btn w3-block w3-black w3-center"> Health Care <i class="fa fa-profile"></i> </button>
              <div id="show2a" class="w3-container w3-hide" >
               <a class="w3-button w3-tiny w3-center" style="width:100%;">Pharmacy</a>
              </div>
            <button onclick="showAccordion('show2b')" class="w3-btn w3-block w3-black w3-center"> Technology <i class="fa fa-profile"></i> </button>
              <div id="show2b" class="w3-container w3-hide ">
               <a class="w3-button  w3-tiny w3-center" style="width:100%;">Programming</a>
              </div>
          </div>

          <div class="" style="" >
          <a class="w3-button w3-left-align " style="width:100%">Categories</a>
          <a class="w3-button w3-left-align" style="width:100%">Some Text</a>
          <a class="w3-button w3-left-align" style="width:100%">About</a>
          </div>
          </div>
        
        <!-- Middle container -->
        <div class="w3-col m6 l4">
          <!-- Create Post -->
          <div class="w3-container w3-padding-2px">
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
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
          unset($following_list[$array_length-1]); //removes the last element of the array since it would possess an empty value.
          $array_length = count($following_list); //get array length again
          shuffle($following_list); //shuffle the array

          $extra_following_list = array_splice($following_list, 5);
          $array_length = count($following_list); //array has be cut so we get the length again...
          /*
the next db query fetches posts from the usernames which the current user is
following
*/

          require_once('generator.php');

          try {
            if ($array_length == 1 || $array_length > 1) {
              $dbh = $instance->getConnection();
              $sqlQuery = "SELECT * FROM Posts WHERE `Post_admin` LIKE ?  OR `Post_Followers` LIKE ? ";
              foreach (xrange(0, $array_length-1, 1) as $val) {
                static $result = array();
                $eachfollower = "%".$following_list[$val]."%";
                $dbh = $dbh->prepare($sqlQuery);
                $dbh->execute(array($eachfollower, $eachfollower));
                $result[] = $dbh->fetchAll();
                $dbh = $instance->getConnection();

              }



              foreach (xrange(0, $array_length-1, 1) as $val) {
                static $newsFeedData = ""; //declare the variable that holds the newsfeed data
                $eachPost = $result[$val][0];
                if ($eachPost == '') {
                  //skips empty arrays....
                  continue;
                }
                //next if block check if post contains media
                if ($eachPost['image1'] != '') {
                  $image1 = "<div><img style='width:100%' src='".$eachPost['image1']."' ></div>";
                } else {
                  $image1 = "";
                }
                if ($eachPost['image2'] != '') {
                  $image2 = "<div><img style='width:100%' src='".$eachPost['image2']."' ></div>";
                } else {
                  $image2 = "";
                }
                if ($eachPost['image3'] != '') {
                  $image3 = "<div><img src='".$eachPost['image3']."' ></div>";
                } else {
                  $image3 = "";
                }

                $post_no = $eachPost['Post_no'];
                $post_title = $eachPost['Post_title'];
                $post_owner = $eachPost['Post_admin'];
                $id = $post_no."|".urlencode($post_title)."|".$post_owner."|_B"; /*specific id for an html span elements,that allows a js ajax callback  function identify it and update the no_of_likes receieved by the post.*/
                $ID = $post_no."|".urlencode($post_title)."|".$post_owner."|_A"; /*specific id for an html span elements,that allows a js ajax callback  function identify it and update the no_of_likes receieved by the post.*/
                $_ID = $post_no."_C"; /* specific id for the html follow button so it can be altered by js ajax callback function*/

                $LikeConfirmer = $eachPost['LikeConfirmer'];
                if (isset($_SESSION['Username']) && preg_match("/$username/", $LikeConfirmer)) {
                  $like = "<i class='fa fa-thumbs-up'>Liked</i>";
                } else {
                  $like = "<i class='fa fa-thumbs-o-up'>Like</i>";
                }


                //next if conditions helps process the time lapse of the post.(from a sec to days only)
                // $post_time = gmdate("H:i:s • D\,d M Y", $eachPost['Post_time']+3600);
                $post_time = time() - $eachPost['Post_time'];
                if ($post_time < 60) {
                  $post_time = "Less than ".$post_time." s";
                } elseif ($post_time <= 3600 && $post_time > 60) {
                  $post_time = round($post_time/60);
                  $post_time = $post_time." min ago";
                } elseif ($post_time > 3600 && $post_time <= 86400) {
                  $post_time = round($post_time/3600);
                  if ($post_time > 1) {
                    $unit = "hrs";
                  } else {
                    $unit = "hr";
                  }
                  $post_time = $post_time.$unit." ago ";
                } elseif ($post_time > 86400) {
                  $post_time = round($post_time/86400);
                  if ($post_time > 1) {
                    $unit = "days";
                  } else {
                    $unit = "day";
                  }
                  $post_time = $post_time.$unit." ago ";
                }

                if (strlen($eachPost['Post_content']) > 40) {
                  $post = substr($eachPost['Post_content'], 0, 30). "......<span style='color:silver;font-size:8px'>Read more>>></span.";
                } else {
                  $post = $eachPost['Post_content'];
                }

                $data = <<<POSTS
          <div class="w3-container w3-card w3-white w3-round w3-margin w3-small">
            <br>
            <img src="" alt="Avatar" class="w3-circle w3-left w3-circle w3-margin-right" style="width:60px">
            <span class="w3-tiny w3-right w3-opacity">$post_time</span>
            <h4>{$eachPost['Post_admin']}</h4><br>
            <hr class="w3-clear">
            <p class="w3-leftbar w3-border-grey">
             &nbsp &nbsp &nbsp $post
            </p>
            <div  style="display:flex;flex-direction:column;flex-wrap:nowrap;overflow-x:scroll;">
            $image1
            $image2
            $image3
            </div>
            <p id="$ID"></p>
            <br>
            <button onclick="Post_Liker_Unliker('$id')" id="$id" type="button" class="w3-button  w3-margin-bottom"> $like </button>
            <button type="button" class="w3-button  w3-margin-bottom"><i class="fa fa-comment"></i> &nbsp;Comment</button>
            <a href="displaypoststyled.php?Post_no=$post_no&Post_title=$post_title" target="_blank" ><button type="button" class="w3-button  w3-margin-bottom"><i class="fa fa-comment"></i> &nbsp;View</button></a>
          </div>

POSTS;
                $newsFeedData .= $data;
              }
              echo $newsFeedData;
            }
          }catch(PDOException $e) {
            echo "Couldn't connect to database";
            echo $e->getMessage();
          }

          ?>


        </div>
      </div>
    </div>
    <!--End of page container -->
    <script>

    let touchstartX = 0;
let touchendX = 0;
    
function checkDirection() {
  if (touchendX - touchstartX > 200){
     //document.getElementById('leftContainer').style.position = "fixed";
    // document.getElementById('leftContainer').style.top = "50";
     //document.getElementById('leftContainer').style.width = "80%";
     //document.getElementById('leftContainer').style.height = "100%";
     //document.getElementById('leftContainer').style.backgroundColor = "white";
     document.getElementById('leftContainer').className += document.getElementById('leftContainer').className.replace("w3-hide-small", "");
  }
  if (touchendX > touchstartX) {
    // document.getElementById('leftContainer').className += " w3-hide-small";
     /*document.getElementById('leftContainer').style.position = "relative";
     //document.getElementById('leftContainer').style.top = "";
     document.getElementById('leftContainer').style.width = "100%";
     document.getElementById('leftContainer').style.height = "100%";
     
    */
  };
}

document.addEventListener('touchstart', e => {
  touchstartX = e.changedTouches[0].screenX
})

document.addEventListener('touchend', e => {
  touchendX = e.changedTouches[0].screenX
  checkDirection();
})

      /*
Javascript function to update the post like button and no of likes
This works diffeerently from the Post_Liker_Unliker fution in displaypost.
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
        var link = "Post_Liker_Unliker.php?".concat("Post_no=", post_no, "&Post_title=", post_title, "&Post_owner=", post_owner);
        xhttp.open("GET", link, true);
        xhttp.send();
      }

      function hide_show() {
        if (document.getElementById('fileMedia').className.indexOf("w3-hide") != -1) {
          document.getElementById('fileMedia').className += " w3-show";
        } else {
          document.getElementById('fileMedia').className += " w3-hide";
        }
      }

      function changelocation(link) {
        window.open(link);
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

      function showAccordion(id) {
        var x = document.getElementById(id);
        if (x.className.indexOf("w3-show") == -1) {
          x.className += " w3-show";
        } else {
          x.className = x.className.replace(" w3-show", "");
        }
      }


    </script>
  </body>
</html>