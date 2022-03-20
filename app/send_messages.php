
<?php 

ini_set('date.timezone', 'Asia/Kolkata');
 

 if($_SERVER['REQUEST_METHOD']=='POST'){

    $con = mysqli_connect("localhost","root","","shansocial") or die('Unable to Connect...');
 
    if(isset($_POST['message'])){
    
        $sender = $_POST['sender'];

        $receiver = $_POST['receiver'];
        
        $message = $_POST['message'];
        
        $date = date('Y-m-d-H-i-s');

        $numofmsg = $_POST['numofmsg'];

        $loggedinid = $_POST['loggedinid'];

        $chatID = $_POST['chatID'];

        $msgID = $_POST['msgID'];

        $secsince = time();

        try{

            $sql = "INSERT INTO `messages` (`ID`, `sender`, `receiver`, `message`, `created`, `secsince`, `chatID`) VALUES ('$msgID', '$sender', '$receiver', '$message', '$date', '$secsince', '$chatID');";

            $res = mysqli_query($con, $sql);

            $sql = "UPDATE `contacts` SET `numofmsg`='$numofmsg' WHERE `ID`='$loggedinid';";

            $res = mysqli_query($con, $sql);

            $sql = "UPDATE `chats` SET `modified`='$secsince' WHERE `chatID`='$chatID';";

            $res = mysqli_query($con, $sql);


        }catch(Exception $e){


        } 
        
    }

    mysqli_close($con);
 }
 
 
?>