.<?php
//this script an integral path of profile.php is populates the
// secondTab tht loads the users activity_logs in form of notification
//it isnt loaded immediately the profile of the user is display and this is to 
//prevent the slow loading of that script.Instead a js ajax request
//calls this script.
//the notification is stored in form of a string so this script takes care of that.
try {
  $username = $_GET['Username'];
  require_once('class.php');
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





$notificationString = $userDetails[0]['activity_logs'];

$seenUnseenNotifications = explode(">>>", $notificationString);
if ($seenUnseenNotifications[count($seenUnseenNotifications)-1] == "") {
  unset($seenUnseenNotifications[count($seenUnseenNotifications)-1]);
}
$unseenNotifications = array_pop($seenUnseenNotifications);

$unseenNotifications = explode("||", $unseenNotifications);
array_pop($unseenNotifications);
$unseenNotifications = array_reverse($unseenNotifications);
foreach ($unseenNotifications as $eachNotifwithTime) {
  static $notification = "";
  preg_match("/~\d{9,}/", $eachNotifwithTime, $matches);
  $timeString = $matches[0];
  $time = preg_replace("/~/","",$timeString);
  $gg= $time;
   $time = date("r", trim($time)-3600);
 // $Time = preg_replace("/\+(\d{3,})/", "", $time);
  $eachNotif = preg_replace(array('/~\d{9,}/'), array(""), $eachNotifwithTime);

  $notification .= <<<HTML
        <div class="w3-panel w3-border-top w3-border-bottom w3-border-grey w3-grey w3-opacity">
         $eachNotif
         <p> $time </p>
        </div>
        <br>
HTML;

}


$seenNotifications = $seenUnseenNotifications;



$seenNotificationsBackToString = "";
foreach ($seenNotifications as $seenNotifications_element) {
  $seenNotificationsBackToString .= $seenNotifications_element;
}

$seenNotificationsBackToArray = explode("||", $seenNotificationsBackToString);
unset($seenNotificationsBackToArray[count($seenNotificationsBackToArray)-1]);//same as array_pop()
$seenNotificationsBackToArray = array_reverse($seenNotificationsBackToArray);
$seenNotificationsBackToArray = array_reverse($seenNotificationsBackToArray);
foreach ($seenNotificationsBackToArray as $eachNotifwithTime) {
  preg_match("/~\d{9,}/", $eachNotifwithTime, $matches);
  $timeString = $matches[0];
  $time = preg_replace("/~/","",$timeString);
  
  $time = date("r", trim($time)-3600);

  $eachNotif = preg_replace(array('~/\d{9,}/'), array(""), $eachNotifwithTime);

  $notification .= <<<HTML
        <div class="w3-container w3-center w3-padding w3-border ">
         $eachNotif
         <p>$time </p>
        </div>
        <br>
HTML;

}



echo $notification;

}catch(Exception $e) {
  echo "Problem loading your notification.Try again later";
}




?>
