<?php
      //next if block check if post contains media
                  if ($eachPost['image1'] != ''&&  $eachPost['image1'] != 'chatResources/imgs/') {
                    $image1 = "<div><img style='width:100%' src='".$eachPost['image1']."' ></div>";
                  } else {
                    $image1 = "";
                  }
                  if ($eachPost['image2'] != '' && $eachPost['image2'] != 'chatResources/imgs/') {
                    $image2 = "<div><img style='width:100%' src='".$eachPost['image2']."' ></div>";
                  } else {
                    $image2 = "";
                  }
                  if ($eachPost['image3'] != '' && $eachPost['image3'] != 'chatResources/imgs/') {
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
            <a href="dp.php?Post_no=$post_no&Post_title=$post_title" target="_blank" ><button type="button" class="w3-button  w3-margin-bottom"><i class="fa fa-comment"></i> &nbsp;View</button></a>
            <button onclick="delete_post_comment_reply('$id')" id="$id" type="button" class="w3-button  w3-margin-bottom"> Delete Post</button>
          </div>

POSTS;
?>