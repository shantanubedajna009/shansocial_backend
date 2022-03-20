<?php

// Api for sendFriend, cancel Friend, Un Friend, accept Friend
$app->post('/app/performaction',function($request){

     	include __DIR__ .'/../app/helpers/dbhelper.php';

		 $userId = $request->getParsedBody()['userId'];
		 $profileId = $request->getParsedBody()['profileid'];
		 $operationType = $request->getParsedBody()['operationType'];

		
		 if($operationType==1){
		 		unFriend($userId,$profileId);
		 }else if($operationType==2){
		 		cancelRequest($userId,$profileId);
		 }else if($operationType==3){
		 		acceptRequest($userId,$profileId);
		 }else if($operationType==4){
		 		sentRequest($userId,$profileId);
		 }
		
				
});


 


?>