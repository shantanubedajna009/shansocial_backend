<?php 


 if($_SERVER['REQUEST_METHOD']=='POST'){

    $con = mysqli_connect("localhost","root","","shansocial") or die('Unable to Connect...');

    $sender = $_POST['sender'];

    $receiver = $_POST['receiver'];
    
    $sql = "SELECT * FROM `chats` WHERE `sender`='$sender' AND `receiver`='$receiver' OR `sender`='$receiver' AND `receiver`='$sender' ORDER BY `modified`;";

    $res = mysqli_query($con, $sql);

    if ($res and mysqli_num_rows($res) > 0) {

        while ($row = mysqli_fetch_array($res)) {
            $flag[] = $row;
        }

        echo json_encode($flag);
    }
    else{

        $secsince = time();

        $sql = "INSERT INTO `chats` (`sender`, `receiver`, `modified`) VALUES ('$sender', '$receiver', '$secsince');";

        $res = mysqli_query($con, $sql);

        $sql = "SELECT * FROM `chats` WHERE `sender`='$sender' AND `receiver`='$receiver' OR `sender`='$receiver' AND `receiver`='$sender' ORDER BY `modified`;";

        $res = mysqli_query($con, $sql);

        if ($res and mysqli_num_rows($res) > 0) {

            while ($row = mysqli_fetch_array($res)) {
                $flag[] = $row;
            }

            echo json_encode($flag);
        }

        

    }

    mysqli_close($con);
 }
 
 
?>