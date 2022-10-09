<?php
  
  	if(isset($_GET['page'])){
			$page=$_GET['page'];
		}else{
			$page=1;
		}
		if(isset($no_of_replies)){
	  $replies_per_page=15;
		$offset=($page-1)*$replies_per_page;
		}else{$comment_per_page=15;
		$offset=($page-1)*$comment_per_page;
		}




function pagnation(){
  global $page;
  global $no_of_replies,$replies_per_page;
  global $no_of_comments_for_post,$comment_per_page;
  
if(preg_match("/replies/",$_SERVER['SCRIPT_FILENAME'])){
  global $category,$comment_bind_replies;
  $__href = "replies.php?Category=$category&Comment_bind_replies_id=$comment_bind_replies";
	$total_pages_for_the_post=ceil($no_of_replies/$replies_per_page);
		$pagLink="";
}elseif(preg_match("/displaypost/",$_SERVER['SCRIPT_FILENAME'])){
  global $post_no,$post_title_;
  $__href="displaypost.php?Post_no=$post_no&Post_title=$post_title_";
  	$total_pages_for_the_post=ceil($no_of_comments_for_post/$comment_per_page);
		$pagLink="";
}
		/*
		for the prev button in pagnation...
		*/
		if($page>=2){   
			echo "<a href='$__href&page=".($page-1)."'>Prev</a>";   
        }
        
        
        
    
    /*
    for the list of pages in pagnation
    */
    for ($i=1; $i<=$total_pages_for_the_post; $i++) {
    	if ($i == $page) {
    		$pagLink .= "<a class = 'active' href='$__href&page=".$i."'>".$i." </a>";
    		
    	}else{
        $pagLink .= "<a href='$__href&page=".$i."'>".$i." </a>";
    		}
    	};     
		echo $pagLink;
		
		/*
		for thr next button in pagnation
		*/
		if($page<$total_pages_for_the_post){
			echo "<a href='$__href&page=".($page+1)."'>Next</a>";   
        }   
}
?>