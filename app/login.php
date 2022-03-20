
<?php 
 
 if($_SERVER['REQUEST_METHOD']=='POST'){
 
    if( isset($_POST['name']) and isset($_POST['password']) ){
    
        $con = mysqli_connect("localhost","root","","shansocial") or die('Unable to Connect...');

        $name = $_POST['name'];

        $password = hash('sha512', $_POST['password']).sha1("slimshadySalt");
        
        try{

            $sql = "SELECT `ID`, `name` FROM `contacts` WHERE `name`='$name' AND `password`='$password'";
            $res = mysqli_query($con, $sql);

            // do something with $res if you want here

            if ($res) {
	
                while ($row = mysqli_fetch_array($res)) {
                    $flag[] = $row;
                    echo json_encode($flag);
                }
            }

        }catch(Exception $e){

        } 
        
        mysqli_close($con);
    }else{

    }
 }
 
 
?>