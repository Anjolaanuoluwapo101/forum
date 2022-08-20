<?php
//start session that would to check if user is logged in.
session_name('ProgrammersHub');
session_start();
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
	require_once("class.php");
	
	
	/*
	This two variables are used to customize the link to that ensure us to comment on this post.
	Along with $post_time_
	*/
	$post_no=1;
	$post_title="PHP is an highly recommended programming language";
	$post_title_=urlencode($post_title);
	
	
	/*
	The first query in this block ensures that even if the get variables in the link is tampered with
	a confirmation query is done to ensure that,the post exists to prevent this page from crashing,If a post exists 
	a row is gotten if not,a http response is provided 
	It also uses bindParam to counter SQL injections 
	*/

	$sqlQuery="SELECT COUNT(*) FROM Posts WHERE `Post_no`=? AND `Post_title`=?";
	$dbh= $dbh->prepare($sqlQuery);
	$dbh->bindParam(1,$post_no,PDO::PARAM_INT);
	$dbh->bindParam(2,$post_title,PDO::PARAM_STR);
	$dbh->execute();
	$result=$dbh->fetchColumn();
	if($result == 0){
		/* if the post doesn't exist due to link tampering,a http 404 page is generated instead*/
		http_response_code(404);
	}else{
		
	/* If the post exists,the database connection link is reset by calling the singleton class method  again to return the database connection 
	Using a singleton class ensures that,a previous database connection is used and authentication (which may cause delay ) is eliminated*/
	/* Then the post details are fetched */

	$user=$_SESSION['Username'];
	$dbh = $instance->getConnection();
	$sqlQuery="SELECT * FROM Posts WHERE `Post_no`=? AND `Post_title`=?";
	$dbh= $dbh->prepare($sqlQuery);
	$dbh->bindParam(1,$post_no,PDO::PARAM_INT);
	$dbh->bindParam(2,$post_title,PDO::PARAM_STR);
	$dbh->execute();
	$postDetails=$dbh->fetch(PDO::FETCH_ASSOC);
	
	/*
	Populate variables that would be details to the post
	*/
	$post_time=gmdate("D \, H:i:s M Y",$postDetails['Post_time']+3600);
	$no_of_comments_for_post=$postDetails['Post_comments_count'];
	$no_of_likes_for_post= $postDetails['Post_Likes'];
  
  
  /*
  The nxt 3 variables are needed in the Post_Liker_Unliker.php script and also 
  they are used to customize the comment link for this post.
  */
  $post_no=1;
	$post_title="PHP is an highly recommended programming language";
	$post_title_=urlencode($post_title);
	$post_owner=$postDetails['Post_admin'];
  
  
	/*
	Populate variables that would be used to customize the link to comment on this post
	Along with post_no and post_title_
	*/
	$post_time_ =$postDetails['Post_time'];
	$category_of_post=urlencode($postDetails['Category']);

	/*
	check what the meaning of what the Ids and LikeConfirmer variable does 
	at the line 230 below...  It uses the same mechanism.Same as rhe reply.php script..CHECK LINE 230 HERE BELOW
	*/

	$id=$post_no."_B";
	$ID=$post_no."_A";

	$LikeConfirmer = $postDetails['LikeConfirmer'];
	echo $_SESSION['Username'];
	if(isset($_SESSION['Username']) && preg_match("/$user/",$LikeConfirmer)){
			$like="<i class='fa fa-thumbs-up'>Liked</i>";
	}else{
			$like="<i class='fa fa-thumbs-o-up'>Like</i>";
	}

	$data=<<<HTML
	<style>
	.OPBlock{
		width:90%;
		margin-left:5%;
		border:1px solid black;
		text-align:center;
		border-radius:20px;
		min-height:100px;
		}
	
	.OPBlock span{
		float:left;
		padding-left:5px;
		font-weight:200;
		font-size:12px;
		
	}
	.OPBlock #category{
	padding:5px;
	padding-left:10px;
		
	}
	
	.OPBlock h4{
		text-align:center;
	}
	.OPBlock h4 span{
		font-size:12px;
		font-weight:200;
		float:right;
		padding-right:20px;
	}
	
	.OPBlock p{
		padding:5px;
		width:80%;
		margin:auto;
		border:1px solid silver;
		border-radius:20px;
		font-weight:200;
		font-size:10px;
	}
	
	.OPBlock #dateTime{
	padding-right:10px;
	padding-bottom:5px;
	float:right;
	}
	
	.OPBlockOptions{
		padding:10px;
		display:flex;
		flex-direction:row;
		flex-wrap:nowrap;
		height:auto;
		margin-top:5px;
		width:100%;
	}
	
	.OPBlockOptions button{
		margin-right:2px;
		margin-bottom:2px;
		border:1px solid black;
		padding:5px;
	}
	
	</style>
	
	<div class="OPBlock" >
	<span id="category" >In category: <a href="" > {$postDetails['Category']} </a> </span>
	<br>
	
	<h4>
	{$postDetails['Post_title']} 
	<br>
	<span> by {$postDetails['Post_admin']} </span>
	</h4>
	
	<br>
	<br>
	<p>
	{$postDetails['Post_content']}
	
	</p>
	
	<br>
	<span id="dateTime">
	$post_time
	</span>
	<br>

	<div>
	<span> No of likes:</span>	<span id="$ID">{$postDetails['Post_Likes']} </span> <br>
	<span>No of comments : {$postDetails['Post_comments_count']}</span> 
	</div>
		
	
	<div class="OPBlockOptions" >
	<button id="$id" onclick="Post_Liker_Unliker(1)"> $like </button>
	<button><a href="modify.php?Post_no=$post_no&Post_title=$post_title_&Post_time=$post_time_&Category_of_post=$category_of_post&Post_admin=$post_owner&newComment=1">Comment</a></button>
	</div>
	</div>
HTML;
echo $data;
	
	}
	
}catch(Exception $e){
	/* any error faced is caught by the try block and an http response is displayed to mask the error */
	/*the error can be caused by database connection not going through due to overloaded site */
	//http_response_code(404);
	echo $e->getMessage();
}

$toAdmin=<<<HTML
<div class="EditPost">
Edit Post Here
</div>
HTML;
if(isset($_SESSION['Username'])){
if($_SESSION['Username'] == $postDetails['Post_admin']){
echo $toAdmin;
}
}
?>


<br>
<br>

<!-- 

	The next block is the comment block...it does this lost of things...
	Fetches a limited amount of comments(if they're plenty ) ,
	Fetches the no_of_replies,no,of likes for each comments and embeds a link in them to allow you 
	redirect tho a modify.php page where you can reply a comment.

	It shows if the current viewer(a person with a account of this forum)/ of a page has liked a post before......
	Pagnation is also employed so as to group the no_of_comment in batches...
-->
<div class="commentBlock">

<?php
try{
	//checks if post has any comments
if($no_of_comments_for_post != 0){
		
	/*
	next is to load the comments by resetting the connection link
	and running a fresh query on the Comments table
	It also implements pagnation too
	*/
	
		if(isset($_GET['page'])){
			$page=$_GET['page'];
		}else{
			$page=1;
		}
		$comment_per_page=15;
		$offset=($page-1)*$comment_per_page;
		
		$dbh = $instance->getConnection();
		$sqlQuery="SELECT * FROM Comments WHERE `Post_no`=? ORDER BY `Time` ASC LIMIT ?,? ";
		$dbh= $dbh->prepare($sqlQuery);
		$dbh->bindParam(1,$post_no,PDO::PARAM_INT);
		$dbh->bindParam(2,$offset,PDO::PARAM_INT);
		$dbh->bindParam(3,$comment_per_page,PDO::PARAM_INT);
		$dbh->execute();
		while($comments=$dbh->fetch(PDO::FETCH_ASSOC,PDO::FETCH_ORI_NEXT)){
		/*
		The session username...username of the currently logged in user is assigned to a variable for easy reference;
		*/
		
		$user=$_SESSION['Username'];
		
		/*
		The variables required for sending a reply to a comment is created ...
		*/
		$category_of_comment=urlencode($comments['Category']);
		$comment_username=$comments['Comment_username'];
		$comment_bind_replies_id=$comments['Comment_bind_replies_id'];
		$comment_time=$comments['Time'];		
		
		
		
		
		/*
		The ids are created to specifically identify an html elements,
		In this case...we need to specifically identify the like button and the 
		no_of_likes counter...
		So when this like button is clicked.... it innerHTML is changed and the 
		no of likes counter is either incrreased of decreased
		*/
		
		
		$ID=$comments['Comment_username']."|".$comments['Time']."|_A";
		$id=$comments['Comment_username']."|".$comments['Time']."|_B";
		
		/*
	if the name of the comment_username maches the current session name ,it means the owner 
	of the comment is the one viewing the post ...He/She cannot like his/her own post.
		*/
		if($comments['Comment_username']== $_SESSION['Username']){
			//load your comment
		$comment=<<<COMMENT
		<br>
		<div class="comments">
		
		<p>
		<b>{$comments['Comment_username']}</b> says
		</p>
		
		<div>
		Comment content....here...
		</div>
		
		<div 	style="font-size:8px;margin-left:3px;margin-bottom-2px" >
		 <span> No of likes: </span>  <span>{$comments['Comment_likes']} </span> <br>
		<span>No of comments : {$comments['Comment_replies_count']}</span> 
		</div>
		
		
		
		<div class="OPBlockOptions" >
		<button  > View </button>
		<button  > Edit Comment </button>
		
		</div>
		</div>
		
		
COMMENT;
echo $comment;
echo "<br>";
		}else{
	/*
		load another person's comment
		But first.....we create a mechanism that ensures that a user can  see if 
		he or she has already like a post.
		
			The next 5lines of code helps check for the occurence 
			of the  logged in person's username....If it's found in the 
			like confirmer variable...it means he or she has previously liked the code.
	
	*/
	
	$LikeConfirmer=$comments['LikeConfirmer'];
	if(isset($_SESSION['Username']) && preg_match("/$user/",$LikeConfirmer)){
			$like="<i class='fa fa-thumbs-up'>Liked</i>";
	}else{
			$like="<i class='fa fa-thumbs-o-up'>Like</i>";
	}

		
$comment=<<<COMMENT
		<br>
		<div class="comments">
		
		<p>
		<b>{$comments['Comment_username']}</b> says
		</p>
		
		<div>
	{$comments['Comment_content']}
		</div>
		
		<div	style="font-size:8px;margin-left:3px;margin-bottom-2px" >
		<span>No of likes:</span> <span id="$ID">{$comments['Comment_likes']} </span> <br>
		<span>No of comments : {$comments['Comment_replies_count']}</span> <br>
		</div>
		
		<div class="OPBlockOptions" >
		<button id="$id" onclick="CommentLiker_Unliker('$id')" >$like</button>
		<button ><a href='modify.php?newReply=1&Comment_owner=$comment_username&Category_of_comment=$category_of_comment&Comment_bind_replies_id=$comment_bind_replies_id&time=$comment_time' > Reply </a> </button>
		</div>

		</div>
		
		
COMMENT;
echo $comment;
echo "<br>";
	
		}
		
		
		}
	
	}else{
		throw new Exception("No comments found");
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
		
		
		$total_pages_for_the_post=ceil($no_of_comments_for_post/$comment_per_page);
		$pagLink="";
		/*
		for the prev button in pagnation...
		*/
		if($page>=2){   
			echo "<a href='displaypost.php?page=".($page-1)."'>Prev</a>";   
        }
        
        
        
    
    /*
    for the list of pages in pagnation
    */
    for ($i=1; $i<=$total_pages_for_the_post; $i++) {
    	if ($i == $page) {
    		$pagLink .= "<a class = 'active' href='displaypost.php?page=".$i."'>".$i." </a>";
    		
    	}else{
        $pagLink .= "<a href='displaypost.php?page=".$i."'>".$i." </a>";
    		}
    	};     
		echo $pagLink;
		
		/*
		for thr next button in pagnation
		*/
		if($page<$total_pages_for_the_post){
			echo "<a href='displaypost.php?page=".($page+1)."'>Next</a>";   
        }   
?>
	
</div>
<script>

/*
Javascript function to update the post like button and no of likes
*/
function Post_Liker_Unliker(check) {
var check = 1;
var xhttp = new XMLHttpRequest();
xhttp.onreadystatechange = function() {
if (this.readyState == 4 && this.status ==
200) {
document.getElementById("<?php echo $post_no."_B"; ?>").innerHTML=this.responseText;
alterPostLikes(this.responseText);
}
};
var link ="Post_Liker_Unliker.php?checker=".concat(check,"<?php $post_title_=urlencode($post_title); echo  '&Post_no='.$post_no.'&Post_title='.$post_title_.'&Post_owner='.$post_owner; ?>");
xhttp.open("GET",link, true);
xhttp.send();
}

function alterPostLikes(check){
	if(check == "<i class='fa fa-thumbs-up'>Liked</i>"){
	var post_likes=<?php echo $no_of_likes_for_post; ?>;
	document.getElementById("<?php echo $post_no."_A"; ?>").innerHTML = post_likes + 1;
	}else if(check == "<i class='fa fa-thumbs-o-up'>Like</i>"){
		var post_likes= document.getElementById("<?php echo $post_no."_A"; ?>").innerHTML;
	document.getElementById("<?php echo $post_no."_A"; ?>").innerHTML = post_likes - 1;
	}
}

/*
Javascript function to update a comments like button and no_of likes counter

It uses the PostComments and CommentRepliea liker and unliker script
*/


function CommentLiker_Unliker(id) {
var id=id;
var arr = id.split("|");
alert("function getting called");
var xhttp = new XMLHttpRequest();
xhttp.onreadystatechange = function() {
if (this.readyState == 4 && this.status ==
200) {
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
var link ="PostComments_CommentReplies_Liker_Unliker.php?commentlike_id=".concat(id,"<?php echo '&Post_no='.$post_no.'&Post_admin='.$post_owner; ?>");
xhttp.open("GET",link, true);
xhttp.send();
	}


</script>
</body>

</html>