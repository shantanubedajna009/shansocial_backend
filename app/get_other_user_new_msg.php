<?php

$conn=mysqli_connect("localhost","root","", "shady") or die("Unable to connect");

if(mysqli_connect_error($conn)) {
	echo "Failed To Connect";
}

if(isset($_POST['sender']) and isset($_POST['receiver'])){

    $sender = $_POST['sender'];
	$receiver = $_POST['receiver'];
	$chatID = $_POST['chatID'];
	//$secsince = $_POST['secsince'];

	$sql = "SELECT * FROM `messages` WHERE `chatID`='$chatID' ORDER BY `secsince` DESC LIMIT 1";
	$res = mysqli_query($conn, $sql);

	if ($res) {
		
		while ($row = mysqli_fetch_array($res)) {
			$flag[] = $row;
		}

		// returns tthe json data
	echo json_encode($flag);


	}
}


mysqli_close($conn);		

 
?>