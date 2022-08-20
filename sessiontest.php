<?php


function make_seed()
{
  list($usec, $sec) = explode(' ', microtime());
  return $sec + $usec * 1000000;
}
mt_srand(make_seed());
$randval = mt_rand();
echo $randval;



/*
include "generateRandomSpecificId.php";
print($i);
*/
session_name("ProgrammersHub");
session_start();
//echo $_SESSION['Username'];
$_SESSION['Username']="Anjola101";
session_write_close();
echo "<br>";
/*
function myrandnum(){
$randomValue ="";
$numbers = range(1, 10);
shuffle($numbers);
foreach ($numbers as $number) {
    static $count = 0;
    $randomValue .= $number;
    $count++;
    if($count >= 10){
    	return intval($randomValue);
    	}
		}
	}
	
	echo "<br>";
	echo myrandnum();
	echo "<br>";
	
	print_r(shuffle(range(1,10)));
/*
$array="hey,hyyy,yoooo";
$array=explode(",",$array);
foreach($array as $element){
//	static $keeper=array();
//	$keeper[]=$element;
echo $element;
echo "<br>";
}
*/
?>