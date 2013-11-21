<?php include('includes/mydb-functions.php');

if(isset($_POST['hwname'], $_POST['due'])){
	connect_to_db();
	
	$hwname = mysqli_real_escape_string($link, $_POST['hwname']);
	$due = mysqli_real_escape_string($link, $_POST['due']);
	
	if(isset($_POST['desc'])){
		$desc = mysqli_real_escape_string($link, $_POST['desc']);
		$sql = "INSERT INTO detail (due,hwname,detail) VALUES ('".$due."','".$hwname."','".$desc."')";
	} 
	
	if(!query($sql)) 
	{
		header("location: index.php?insert=fail1");
	} else {
		
			$lastId = mysqli_insert_id ( $link );
			$sql3 = "select hwname from detail where hwid = ".$lastId;
			if($answer = query($sql3)) {
				$data = mysqli_fetch_array($answer);
				
				echo $data['hwname'];	
			}
	}
}
else
{
	header("Location: index.php?insert=failed");
}
?>