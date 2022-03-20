<?php

$conn=mysqli_connect("localhost","root","", "shansocial") or die("Unable to connect");

if(mysqli_connect_error($conn)) {
	echo "Failed To Connect";
}

if(isset($_POST['loggedinuser']) and isset($_POST['loggedinid'])){

	$loggedinuser = $_POST['loggedinuser'];
	$loggedinid = $_POST['loggedinid'];

	$qry = "SELECT * FROM `contacts` WHERE `ID`!='$loggedinid'";

	$res = mysqli_query($conn, $qry);

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