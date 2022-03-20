<?php 

 if($_SERVER['REQUEST_METHOD']=='POST'){

    $con = mysqli_connect("localhost","root","","shansocial") or die('Unable to Connect...');

    $ID = $_POST['ID'];

    $sql = "UPDATE messages SET `seen`=1 WHERE `ID`='$ID';";

    $res = mysqli_query($con, $sql);

    if ($res and mysqli_num_rows($res) > 0) {

        while ($row = mysqli_fetch_array($res)) {
            $flag[] = $row;
        }

        echo json_encode($flag);
    }

    mysqli_close($con);
 }
  
?>