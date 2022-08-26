<?php
$errorMsg = "";

//utility functions...
function retrieving_saved_comments_replies($value) {
  global $loadReply,
  $loadComment;
  $value = $value;
  if ($loadReply) {
    $errorMsg = "Couldn't retrieve saved reply at ".date("r")."\n";
    checkSQLErrors($value);
  } else if ($loadComment) {
    $errorMsg = "Couldn't retrieve saved comment at ".date(" r")."\n";
    checkSQLErrors($value);
  }
}

function checkSQLErrors($value) {
  if ($value != TRUE) {
    trigger_error($errorMsg);
  }
}

//check if a post has a media to display
function renderPostMedia() {
  global $image1, $image2,$image3,$postDetails;

  if ($postDetails['image1'] != '' && $postDetails['image1'] != "chatResources/imgs/" ) {
    $image1 = "<div><img style='width:100%' src='".$postDetails['image1']."' ></div>";
  } else {
    $image1 = "";
  }
  if ($postDetails['image2'] != '' && $postDetails['image2'] != "chatResources/imgs/" ) {
    $image2 = "<div><img style='width:100%' src='".$postDetails['image2']."' ></div>";
  } else {
    $image2 = "";
  }
  if ($postDetails['image3'] != '' && $postDetails['image3'] != "chatResources/imgs/" ) {
    $image3 = "<div><img src='".$postDetails['image3']."' ></div>";
  } else {
    $image3 = "";
  }

}

//checks if a comment has media
function renderCommentMedia() {
    global $image1, $image2,$image3,$comments;
  if ($comments['image1'] != '' && $comments['image1'] != "chatResources/imgs/" ) {
    $image1 = "<div><img style='width:100%' src='".$comments['image1']."' ></div>";
  } else {
    $image1 = "";
  }
  if ($comments['image2'] != '' && $comments['image2'] != "chatResources/imgs/" ) {
    $image2 = "<div><img style='width:100%' src='".$comments['image2']."' ></div>";
  } else {
    $image2 = "";
  }
  if ($comments['image3'] != '' && $comments['image3'] != "chatResources/imgs/" ) {
    $image3 = "<div><img src='".$comments['image3']."' ></div>";
  } else {
    $image3 = "";
  }
}


//checks if a reply has media 
function renderReplyMedia(){
  global $image1, $image2,$image3,$replies;
  if ($replies['image1'] != '' && $replies['image1'] != "chatResources/imgs/" ) {
    $image1 = "<div><img style='width:100%' src='".$replies['image1']."' ></div>";
  } else {
    $image1 = "";
  }
  if ($replies['image2'] != '' && $replies['image2'] != "chatResources/imgs/" ) {
    $image2 = "<div><img style='width:100%' src='".$replies['image2']."' ></div>";
  } else {
    $image2 = "";
  }
  if ($replies['image3'] != '' && $replies['image3'] != "chatResources/imgs/" ) {
    $image3 = "<div><img src='".$replies['image3']."' ></div>";
  } else {
    $image3 = "";
  }
  
}



//this function is called in the last two blocks to check if  the media providd by the user is valid.

function checkMediaCompatibility($type) {
  global $imglink1,
  $imglink2,
  $imglink3;
  @$imglink1 = "chatResources/imgs/".basename($_FILES['files']['name'][0]);
  @$imglink2 = "chatResources/imgs/".basename($_FILES['files']['name'][1]);
  @$imglink3 = "chatResources/imgs/".basename($_FILES['files']['name'][2]);

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
  if ($type == 1) {

    for ($i = 0; $i <= 2; $i++) {
      foreach ($_FILES['files']['error'][$i] as $error) {
        if ($error == 0) {
          $mediaName = $_FILES['files']['name'][$i];
          move_uploaded_file($_FILES['files']['tmp_name'][$i], "chatResources/imgs/".$mediaName);

        }

      }

    }

  } else if ($type == 2) {
    for ($i = 0; $i <= 2; $i++) {
      $mediaName = basename($_FILES['files']['name'][$i]);
      if ($_FILES['files']['error'][$i] == 0) {
        move_uploaded_file($_FILES['files']['tmp_name'][$i], 'chatResources/imgs/'.$mediaName);
        @unlink($_POST['filesInDB'][$i]);

      }
    }

  }

}



?>