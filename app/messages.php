<?php

$app->post('/app/sentmessage',function($request){
	include __DIR__ .'/../app/helpers/dbhelper.php';

		$sender = $request->getParsedBody()['sender'];
		$receiver = $request->getParsedBody()['receiver'];
		$msgText = $request->getParsedBody()['message'];


        try{
            $stmt = $pdo->prepare("INSERT INTO `messages` ( `fromUser`, `toUser`, `msgText`)
                VALUES ( :sender, :receiver, :msgText); ");

            $stmt->bindParam(':sender', $sender, PDO::PARAM_STR);
            $stmt->bindParam(':receiver', $receiver, PDO::PARAM_STR);
            $stmt->bindParam(':msgText', $msgText, PDO::PARAM_STR);
                    
            $stmt= $stmt->execute();

            if($stmt){
                echo true;
            }else{
                echo false;
            }	
        
        }catch(Exception $e) {
            echo 'Exception -> ';
            var_dump($e->getMessage());
        }
				
		


});

$app->post('/app/loadmessages',function($request){
	include __DIR__ .'/../app/helpers/dbhelper.php';

		$sender = $request->getParsedBody()['sender'];
		$receiver = $request->getParsedBody()['receiver'];


        try{
            $stmt = $pdo->prepare("SELECT * FROM `messages` WHERE (`fromUser` = :sender AND `toUser` = :receiver) OR (`fromUser` = :receiver AND `toUser` = :sender) ORDER BY `msgDate`;");

            $stmt->bindParam(':sender', $sender, PDO::PARAM_STR);
            $stmt->bindParam(':receiver', $receiver, PDO::PARAM_STR);
                    
            // $stmt= $stmt->execute();
            $stmt->execute();

            $row = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode($row);
            
        }catch(Exception $e) {
            echo 'Exception -> ';
            var_dump($e->getMessage());
        }
				
		


});



?>