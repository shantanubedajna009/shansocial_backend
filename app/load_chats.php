<?php

$conn=mysqli_connect("localhost","root","", "shansocial") or die("Unable to connect");

if(mysqli_connect_error($conn)) {
	echo "Failed To Connect";
}

if($_SERVER['REQUEST_METHOD']=='POST' and isset($_POST['loggedinid'])){

    $loggedinid = $_POST['loggedinid'];

	//$qry = "SELECT * FROM contacts WHERE id IN (SELECT sender FROM chats WHERE sender=$loggedinid OR receiver=$loggedinid) OR id IN (SELECT receiver FROM chats WHERE sender=$loggedinid OR receiver=$loggedinid)";


	// proper query
	$qry = "SELECT co.ID, co.name, co.number, co.imagelink, ch.chatID FROM contacts co, chats ch WHERE (co.ID=ch.sender OR co.ID=ch.receiver) AND (ch.sender=$loggedinid OR ch.receiver=$loggedinid) ORDER BY ch.modified DESC;";

    $res = mysqli_query($conn, $qry);
    
	if ($res and mysqli_num_rows($res) > 0) {
		
		while ($row = mysqli_fetch_array($res)) {
			$flag[] = $row;
		}

	// returns tthe json data
	echo json_encode($flag);

	}

}


mysqli_close($conn);		

 
?>