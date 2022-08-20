<?php
ob_start();
echo "<script>
function randomValueGen(){
 var randomValue='';
	for(var i=0;i<10;i++){
		let x = Math.floor((Math.random() * 9) + 1);
		randomValue += x;
	}
	return randomValue;
}
</script>";
$comment_bind_reply_id= "<script> document.write(randomValueGen());</script>";
echo $comment_bind_reply_id;

$id=ob_get_clean();
echo $id;
?>