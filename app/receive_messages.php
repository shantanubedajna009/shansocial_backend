
<?php 

ini_set('date.timezone', 'Asia/Kolkata');
 

 if($_SERVER['REQUEST_METHOD']=='POST'){

    $con = mysqli_connect("localhost","root","","shansocial") or die('Unable to Connect...');


    $sender = $_POST['sender'];

    $receiver = $_POST['receiver'];

    $chatID = $_POST['chatID'];

    $loadCount = $_POST['loadCount'];

    $sql = "SELECT * FROM `messages` WHERE `chatID`='$chatID' ORDER BY `secsince` DESC LIMIT $loadCount;";

    $res = mysqli_query($con, $sql);

    if ($res) {

        while ($row = mysqli_fetch_array($res)) {
            $flag[] = $row;
        }

        $flag = array_reverse($flag);

        echo json_encode($flag);
    }
    

    mysqli_close($con);
 }
 
 
?>