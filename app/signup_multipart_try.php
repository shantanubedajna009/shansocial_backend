
<?php 
 
 $upload_path = "images/";
 
 if($_SERVER['REQUEST_METHOD']=='POST'){
 
    if(isset($_POST['name']) and isset($_POST['number']) and isset($_POST['password']) and isset($_FILES['image']['name'])){
    
        $con = mysqli_connect("localhost","root","","shansocial") or die('Unable to Connect...');
        
        $fileinfo = pathinfo($_FILES['image']['name']);
        
        $extension = $fileinfo['extension'];

        $name = $_POST['name'];

        $number = $_POST['number'];

        $password = hash('sha512', $_POST['password']).sha1("slimshadySalt");
        
        $file_url = 'http://1.0.0.2/android_pool/whatsapp/' . $upload_path . $name . '.' . $extension;
        
        $file_path = $upload_path . $name . '.'. $extension; 
        
        try{

            move_uploaded_file($_FILES['image']['tmp_name'],$file_path);
            $sql = "INSERT INTO `contacts` (`name`, `number`, `password`, `imagelink`) VALUES ('$name', '$number', '$password', '$file_url');";
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
    }
 }
 
 
?>