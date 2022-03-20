<?php

$conn=mysqli_connect("localhost","root","", "shansocial") or die("Unable to connect");

if(mysqli_connect_error($conn)) {
	echo "Failed To Connect";
}

if($_SERVER['REQUEST_METHOD']=='POST' and isset($_POST['receiver'])){

    $loggedinid = $_POST['receiver'];

	$qry = "SELECT * FROM messages WHERE `receiver`='$loggedinid' AND `seen`=0 ORDER BY `secsince` DESC;";

    $res = mysqli_query($conn, $qry);
    
	if (mysqli_num_rows($res) > 0) {
		
		while ($row = mysqli_fetch_array($res)) {
			$flag[] = $row;
		}

	// returns tthe json data
	echo json_encode($flag);

	}

}


mysqli_close($conn);		

 
?>