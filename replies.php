
<?php
session_name("ProgrammersHub");
session_start();
//echo $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']."?".$_SERVER['argv'][0];
?>
<!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<!--	<link rel="stylesheet" href="css/w3.css">-->
	<link rel="stylesheet" href="css/style.css">
	<link rel="stylesheet" href="css/font-awesome/css/font-awesome.min.css">
	
</head>
<body>

<?php
try{

//get variables from link;
$category="PHP";
$comment_bind_replies=1234567890;

//confirm that get request link was not tampered with...
require_once("class.php");
	

$sqlQuery="SELECT COUNT(*) FROM Comments WHERE `Category`=? AND `Comment_bind_replies_id`=?";
$dbh= $dbh->prepare($sqlQuery);
$dbh->bindParam(1,$category,PDO::PARAM_STR);
$dbh->bindParam(2,$comment_bind_replies,PDO::PARAM_INT);
$dbh->execute();
$check=$dbh->fetchColumn();
if($check == 0){
		/* if the comment doesn't exist due to link tampering,a http 404 page is generated instead*/
	http_response_code(404);
	die();
}else{
		/* If the comment exists,the database connection link is reset by calling the singleton class method  again to return the database connection 
		Using a singleton class ensures that,a previous database connection is used and authentication (which may cause delay ) is eliminated*/
		/* Then the post details are fetched */
	$dbh = $instance->getConnection();
	$sqlQuery="SELECT * FROM Comments WHERE `Category`=? AND `Comment_bind_replies_id`=?";
	$dbh= $dbh->prepare($sqlQuery);
	$dbh->bindParam(1,$category,PDO::PARAM_STR);
	$dbh->bindParam(2,$comment_bind_replies,PDO::PARAM_INT);
	$dbh->execute();
	
		/*
		Populate variables that would display details about that comment
		*/
	$commentDetails=$dbh->fetch(PDO::FETCH_ASSOC);
	$Comment_time=gmdate("D \, H:i:s M Y",$commentDetails['Time']+3600);
	$no_of_replies=$commentDetails['Comment_replies_count'];
	
	
		/*
		The ids are created to specifically identify an html elements,
		In this case...we need to specifically identify the like button tag and the 
		no_of_likes counter html span tag...
		So when this like button is clicked.... it innerHTML is changed and the 
		no of likes counter is either incrreased of decreased
		*/
		
		
	$ID=$commentDetails['Comment_username']."|".$commentDetails['Time']."|_A";
	$id=$commentDetails['Comment_username']."|".$commentDetails['Time']."|_B";
		
	
		/*
		We also neeed to populate the variables that would be sent to along with the modify link
		*/
	$comment_username=$commentDetails['Comment_username'];
	$comment_time=$commentDetails['Time'];
	$category_of_comment =$commentDetails['Category'];
	$comment_bind_replies_id=$commentDetails['Comment_bind_replies_id'];
	
	
	$LikeConfirmer=$commentDetails['LikeConfirmer'];
	
	if(isset($_SESSION['Username']) && preg_match("/$user/",$LikeConfirmer)){
			$like="<i class='fa fa-thumbs-up'>Liked</i>";
	}else{
			$like="<i class='fa fa-thumbs-o-up'>Like</i>";
	}
	
	$data=<<<HTML
	
	<div class="OPBlock" >
	<span id="category" >In category: <a href="" > {$commentDetails['Category']} </a> </span>
	<br>
	
	<h4>
	 {$commentDetails['Comment_username']} 's Reply.... 
	</h4>
	
	<br>
	<br>
	<p>
	{$commentDetails['Comment_content']}
	
	</p>
	
	<br>
	<span id="dateTime">
	$Comment_time
	</span>
	<br>
	<div style="font-size:8px;margin-left:3px;margin-bottom-2px" >
	<span>No of likes : </span> <span id='$ID'>{$commentDetails['Comment_likes']} </span> <br><br>
	<span>No of comments : {$commentDetails['Comment_replies_count']}</span> 
	</div>
	<br>
	<div class="OPBlockOptions" >
	<button id='$id'  onclick="CommentLiker_Unliker('$id')"> $like </button>
	<button> <a href="modify.php?Comment_owner=$comment_username&time=$comment_time&Category_of_comment=$category_of_comment&Comment_bind_replies_id=$comment_bind_replies_id&newReply=1">Reply</a></button>
	</div>
	</div>
HTML;
echo $data;
	
	
	}
}catch(Exception $e){
	echo "This reply doesn't exist";
}

$toCommentOwner=<<<HTML
<div class="EditPost">
<ins>Hey there,</ins> only you can see this 
<a>Click Here</a> to edit post
</div>
HTML;


if(isset($_SESSION['Username'])){
	if($_SESSION['Username'] == $commentDetails['Comment_username']){
		echo $toCommentOwner;
		}
	}
?>





<br>
<br>
<div class="commentBlock">

<?php
try{
	//checks if post has any comments
	if($no_of_replies != 0){
		
		/*
		next is to load the replies by resetting the connection link
		and running a fresh query on the Comments table
		It also implements pagnation too
		*/
		if(isset($_GET['page'])){
			$page=$_GET['page'];
		}else{
			$page=1;
		}
		$replies_per_page=15;
		$offset=($page-1)*$replies_per_page;
		
		$dbh = $instance->getConnection();
		$sqlQuery="SELECT * FROM Replies WHERE `Category`=? AND `Comment_bind_replies_id`=? ORDER BY `Time` ASC LIMIT ?,? ";
		$dbh= $dbh->prepare($sqlQuery);
		$dbh->bindParam(1,$category,PDO::PARAM_STR);
		$dbh->bindParam(2,$comment_bind_replies,PDO::PARAM_INT);
		$dbh->bindParam(3,$offset,PDO::PARAM_INT);
		$dbh->bindParam(4,$replies_per_page,PDO::PARAM_INT);
	//	echo $sqlQuery;
		$dbh->execute();
		
		/*
		The comment class [css] can be use to order the replies.
		*/
		while($replies=$dbh->fetch(PDO::FETCH_ASSOC,PDO::FETCH_ORI_NEXT)){

		$ID=$replies['Reply_username']."|".$replies['Time']."|_A";
		$id=$replies['Reply_username']."|".$replies['Time']."|_B";
		
		$user=$_SESSION['Username'];
		$urlcategory=urlencode($category);
					
		if($replies['Reply_username'] == $_SESSION['Username']){
		$replytime=$replies['Time'];
		$reply=<<<REPLY
		<br>
		<div class="comments">
		
		<p>
		<b>{$replies['Reply_username']}</b> says
		</p>
		
		<div>
		Replies content here ...
		</div>
		
		<div 	style="font-size:8px;margin-left:3px;margin-bottom-2px" >
		<span>No of likes:</span> <span> {$replies['Reply_likes']} </span> <br>
		</div>
		
		
		<div class="OPBlockOptions" >
		<button>	<a href="modify.php?userReply=$user&id=$comment_bind_replies&Replytime=$replytime">Edit Reply</a> </button>
		</div>
		
		<br>
		
		</div>
		
		
REPLY;
echo $reply;
echo "<br>";

	}else{
			
			/*the else blocks loaads every comment for othwr users that 
			where not posted by the the owner of the session */
			/*but first it confirms if the session_owner has liked the other person's post */
			
			/*
			Like confirmer helps check if user already likes another person's post .
			*/
		$LikeConfirmer=$replies['LikeConfirmer'];
	
	
	
	if((isset($_SESSION['Username']) && preg_match("/$user/",$LikeConfirmer))){
			$like="<i class='fa fa-thumbs-up'>Liked</i>";
	}else{
			$like="<i class='fa fa-thumbs-o-up'>Like</i>";
	}


$reply=<<<REPLY
		<br>
		<div class="comments">
		<p>
		<b>{$replies['Reply_username']}</b> says
		</p>
		
		<div>
		{$replies['Reply_content']}
		</div>
		<br>
		<div 	style="font-size:8px;margin-left:3px;margin-bottom-2px" >
		<span>No of likes : </span> <span id="$ID"> {$replies['Reply_likes']} </span> <br>
		</div>
		
		<div class="OPBlockOptions" >
		<button id="$id" onclick="Reply_Liker_Unliker('$id')">$like</button>
		<button> Reply</button>
		</div>
	
		</div>
		
		
REPLY;
echo $reply;
echo "<br>";
	
		}
		
		
		}
	
	}else{
		throw new Exception("No Replies found");
		
	}	
	
	
}catch(Exception $e){
	echo $e->getMessage();
}

?>
</div>
<br>
<br>
<div class="pagnation">
	<?php
		/*
		Implementing pagnation...
		*/
		
		
		$total_pages_for_the_post=ceil($no_of_replies/$replies_per_page);
		$pagLink="";
		/*
		for the prev button in pagnation...
		*/
		if($page>=2){   
			echo "<a href='replies.php?page=".($page-1)."'>Prev</a>";   
        }
        
        
        
    
    /*
    for the list of pages in pagnation
    */
    for ($i=1; $i<=$total_pages_for_the_post; $i++) {
    	if ($i == $page) {
    		$pagLink .= "<a class = 'active' href='replies.php?page=".$i."'>".$i." </a>";
    		
    	}else{
        $pagLink .= "<a href='replies.php?page=".$i."'>".$i." </a>";
    		}
    	};     
		echo $pagLink;
		
		/*
		for thr next button in pagnation
		*/
		if($page<$total_pages_for_the_post){
			echo "<a href='replies.php?page=".($page+1)."'>Next</a>";   
        }   
?>

<script>
/*
This alters the comment like and number of likes
*/
function CommentLiker_Unliker(id) {
var id=id;
var arr = id.split("|");
alert("function getting called");
//var check = 1;
var xhttp = new XMLHttpRequest();
xhttp.onreadystatechange = function() {
if (this.readyState == 4 && this.status ==
200) {
	alert(this.responseText);
document.getElementById(id).innerHTML=this.responseText;
var ID = arr[0].concat("|",arr[1],"|_A");
var value =document.getElementById(String(ID)).innerHTML;

if(this.responseText == "<i class='fa fa-thumbs-o-up'>Like</i>"){
document.getElementById(ID).innerHTML = parseInt(value) - 1;
}else if(this.responseText == "<i class='fa fa-thumbs-up'>Liked</i>"){
document.getElementById(ID).innerHTML = parseInt(value) + 1;
}

}
};
var link ="PostComments_CommentReplies_Liker_Unliker.php?commentlike_id=".concat(id,"<?php echo '&Post_no='.$post_no; ?>");
xhttp.open("GET",link, true);
xhttp.send();
}	




/*
Javascript function to update a replies like button and no_of likes counter

It uses the PostComments and CommentReplies liker and unliker script

*/
	
function Reply_Liker_Unliker(id) {
var id=id;
var arr = id.split("|");
alert("function getting called");
//var check = 1;
var xhttp = new XMLHttpRequest();
xhttp.onreadystatechange = function() {
if (this.readyState == 4 && this.status ==
200) {
	alert(this.responseText);
document.getElementById(id).innerHTML=this.responseText;
//alert(document.getElementById(id));
var ID = arr[0].concat("|",arr[1],"|_A");
var value =document.getElementById(String(ID)).innerHTML;

if(this.responseText == "<i class='fa fa-thumbs-o-up'>Like</i>"){
document.getElementById(ID).innerHTML = parseInt(value) - 1;
}else if(this.responseText == "<i class='fa fa-thumbs-up'>Liked</i>"){
document.getElementById(ID).innerHTML = parseInt(value) + 1;
}

}
};
var link ="PostComments_CommentReplies_Liker_Unliker.php?replylike_id=".concat(id,"<?php echo '&Comment_bind_replies_id='.$comment_bind_replies; ?>");
xhttp.open("GET",link, true);
xhttp.send();
}


	
</script>
</body>
</html>
