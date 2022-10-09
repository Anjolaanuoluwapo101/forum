<?php
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_httponly', 1);


session_name('ProgrammersHub');
session_start();
if (isset($_SESSION['Username'])) {
  //site loads
} else {
  http_response_code(404);
  die();
}

try {
  $username = $_SESSION['Username'];
  require_once('../helperfiles/class.php');
  $dbh = $dbh->prepare("SELECT * FROM UserProfile WHERE `Username` = ?");
  $dbh->bindParam(1, $username, PDO::PARAM_STR);
  $dbh->execute();
  $usernameResult = $dbh->fetchColumn();
  if ($usernameResult < 1) {
    throw new Exception('User not logged in');
  } else {
    $dbh = $instance->getConnection();
    $dbh = $dbh->prepare("SELECT * FROM UserProfile WHERE `Username` =?");
    $dbh->bindParam(1, $username, PDO::PARAM_STR);
    $dbh->execute();
    $userDetails = $dbh->fetchAll();
    $profilepic = $userDetails[0]['profilepic'];
  }



}catch(Exception $e) {
  echo $e->getMessage(),
  $e->getLine();
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <!--	<link rel="stylesheet" href="../css/profile.css">-->
  <link rel="stylesheet" href="css/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="../css/w3.css">

  <style>

    .changedClass {
      background: rgba(231,222,214,0.5)!important;
    }

  </style>
</head>
<body onload="active()">

  <div class="w3-row" style="">

    <!--Left container -->
    <?php
    require_once('../Left_Container.php');
    ?>


    <!--Middle container -->
    <div class=" w3-container w3-col m5 l4">
      <div class="">
        <div class="w3-center">
          <img class="w3-circle" style="width:50%" src="<?php echo $profilepic ?>" alt="">
        </div>
        <div class="w3-center w3-padding">
          <?php echo $userDetails[0]['Username']; ?>
        </div>

      </div>


      <!-- This tag below takes care of the user informatiom-->
      <br>
      <br>
      <div class="w3-border">
        <div class="w3-cursive w3-center">
          About You
        </div>
        <div class="w3-center">
          <?php echo $userDetails[0]['about you']; ?>
        </div>

      </div>

      <br>
      <div class="w3-row">
        <div class="w3-half w3-container w3-grey w3-opacity" style="width:50%!important">
          Personal Info
        </div>
        <div class="w3-half w3-container" style="width:50%!important">
          Activity Logs
        </div>
      </div>

      <div id="firsTab" class="w3-container ">
        <div class="w3-row">
          <div class="w3-half w3-panel">
            <b>Joined</b>
          </div>
          <div class="w3-half w3-panel">
            <?php $joinDate = $userDetails[0]['JoinDate']; echo date("r", $joinDate); ?>
          </div>
        </div>
        <br>
        <div class="w3-row">
          <div class="w3-half w3-panel ">
            <b>Residence</b>
          </div>
          <div class="w3-half w3-panel">
            <?php echo $userDetails[0]['location']; ?>
          </div>
        </div>
        <h4 class="w3-container w3-center w3-cursive w3-leftbar w3-border-grey">Your Engagements</h4>
        <div class="w3-row">
          <div class="w3-half w3-panel ">
            <b>Number of Posts Made</b>
          </div>
          <div class="w3-half w3-panel">
            <?php echo $userDetails[0]['no_of_posts']; ?>
          </div>
        </div>
        <div class="w3-row">
          <div class="w3-half w3-panel ">
            <b>Number of Comments and Replies Made</b>
          </div>
          <div class="w3-half w3-panel">
            <?php echo $userDetails[0]['no_of_made_posts_comments_and_replies']; ?>
          </div>
        </div>
        <div class="w3-row">
          <div class="w3-half w3-panel ">
            <b>Popularity</b>
          </div>
          <div class="w3-half w3-panel">
            <?php ?>
          </div>
        </div>
      </div>




      <div id="notifs" class="w3-container w3-padding-large" style="height:500px;overflow-y:scroll">
        <h3 class="w3-container w3-center w3-cursive">Your Posts</h3>
       <?php
       $dbh=$instance->getConnection();
       $dbh=$dbh->prepare("SELECT * FROM Posts WHERE `Post_admin` = ?");
       $dbh->bindParam($username);
       $dbh-execute();
       $yourPosts=$dbh->fetchAll();
       print_r($yourPosts);
       
       ?>

      </div>
    </div>

    <script src="../js/sidebar.js"></script>
    <script>
      function active() {
        var currentClass = document.getElementById('personalInfo');
        currentClass.className = "changedClass";
      }

      function switchActiveTab() {}

    </script>


  </body>
</html>