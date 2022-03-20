<?php

// Add route callbacks
$app->post('/app/login', function ($request) {
    include __DIR__ .'/../app/helpers/dbhelper.php';
   
    $uid = $request->getParsedBody()["uid"];
    $name = $request->getParsedBody()["name"];
    $email = $request->getParsedBody()["email"];
    $profileUrl = $request->getParsedBody()["profileUrl"];
    $coverUrl = $request->getParsedBody()["coverUrl"];
    $userToken = $request->getParsedBody()["userToken"];
    // for get requests
    //$uid = $request->getQueryParam("uid");

    $checkstmt = $pdo->prepare("SELECT `uid` FROM `users` WHERE `uid` = :uid LIMIT 1;");
    $checkstmt->bindParam(":uid", $uid, PDO::PARAM_STR);
    $checkstmt->execute();
    $rowcount = $checkstmt->rowCount();

    // if exists update user token
    if ($rowcount == 1) {
        
        $updateStmt = $pdo->prepare("UPDATE `users` SET `userToken` = :userToken WHERE `uid` = :uid;");
        $updateStmt->bindParam(":userToken", $userToken, PDO::PARAM_STR);
        $updateStmt->bindParam(":uid", $uid, PDO::PARAM_STR);
        $updateStmt = $updateStmt->execute();

        if ($updateStmt) {
            // $error="ok";

            // echo json_encode(array("response"=>$error,"name"=>"Shantanu Bhaiya"));

            echo true;
        }else{
            echo false;
        }

    // when doesn't exist create the user
    }else{

        $SQL_STRING = "INSERT INTO `users` (`uid`, `name`, `email`, `profileUrl`, `coverUrl`, `userToken`)";
        $SQL_STRING.= "VALUES(:uid, :name, :email, :profileUrl, :coverUrl, :userToken)";

        $createStmt = $pdo->prepare($SQL_STRING);

        $createStmt->bindParam(":uid", $uid, PDO::PARAM_STR);
        $createStmt->bindParam(":name", $name, PDO::PARAM_STR);
        $createStmt->bindParam(":email", $email, PDO::PARAM_STR);
        $createStmt->bindParam(":profileUrl", $profileUrl, PDO::PARAM_STR);
        $createStmt->bindParam(":coverUrl", $coverUrl, PDO::PARAM_STR);
        $createStmt->bindParam(":userToken", $userToken, PDO::PARAM_STR);

        $createStmt = $createStmt->execute();

        if ($createStmt) {
            //$error="ok";

            //echo json_encode(array("response"=>$error,"name"=>"Shantanu Bhaiya"));
            echo true;
        }else{
            echo false;
        }

    }


});




//Api for showing user's profile timeline
$app->get('/app/profiletimeline',function($request){

	include __DIR__ . '/../app/helpers/dbhelper.php';


	$uid = $request->getQueryParam('uid');
	$limit = $request->getQueryParam('limit');
	$offset = $request->getQueryParam('offset');
	$current_state = $request->getQueryParam('current_state');

	$stmt =  $pdo->prepare("SELECT `name`,`profileUrl`,`userToken` from `users` WHERE `uid` = :uid LIMIT 1");
	$stmt->bindParam(':uid', $uid, PDO::PARAM_STR);
	$stmt->execute();	

	$userInfo =$stmt->fetch(PDO::FETCH_OBJ);

		  

	/*

	privacy flags representation

		0 - > Friends privacy level
		1 - > Only Me privacy level
		2 - > Public privacy level

	*/


	/*
		Relations between two accounts 

		1=  two people are friends 
		4 = people are unkown
		5 = own profile


	*/

  	if($current_state==5){


	  $stmt = "SELECT * FROM `posts` WHERE `postUserId` = :uid ORDER By statusTime DESC"; 

	  /*

		  -> our own profile,
		  -> can view only me, friends and public  privacy level post

	  */

	}else if($current_state==4){

		$stmt = "SELECT * FROM `posts` WHERE `postUserId` = :uid AND `privacy` = 2 ORDER By statusTime DESC"; 

		/*

			-> not friend account ( unknown profile ),
			-> can view public privacy level post

		*/
	}else if($current_state==1){

		$stmt = "SELECT * FROM `posts` WHERE `postUserId` = :uid AND ( `privacy` = 2 OR `privacy` = 0 ) ORDER By statusTime DESC"; 

		/*

			-> friends accoun
			-> can view fiends and public privacy level post

		*/
	}else{
		$stmt = "SELECT * FROM `posts` WHERE `postUserId` = :uid AND `privacy` = 2 ORDER By statusTime DESC"; 
		/*
			-> relation not known
			-> can view public
		*/
	}

	$stmt .=  '  LIMIT '.$limit. ' OFFSET '.$offset;


	$stmt = $pdo->prepare($stmt);

	$stmt->bindParam(':uid', $uid, PDO::PARAM_STR);		 
	$stmt->execute();

	$viewablePosts= $stmt->fetchAll(PDO::FETCH_OBJ);
  



	// adding extra column to every fetched post
	foreach ($viewablePosts as $key => $value) {

		$value->name        =  $userInfo->name;
		$value->profileUrl =   $userInfo->profileUrl;
		$value->userToken   = $userInfo->userToken;

		if(checkLike($uid,$value->postId)){
			$value->isLiked=true;
		}else{
			$value->isLiked=false;
	   }

	

	}
	
	echo json_encode($viewablePosts);	
	  
		  
});


//Api for personalized timeline
$app->get('/app/gettimelinepost',function($request){

	include __DIR__ . '/../app/helpers/dbhelper.php';


  
	$uid = $request->getQueryParam('uid');
	$limit = $request->getQueryParam('limit');
	$offset = $request->getQueryParam('offset');
  
	$stmt = $pdo->prepare("
						   SELECT 	 posts.*, users.name, users.profileUrl,users.userToken
						   from 	`timeline`
						   INNER JOIN `posts`
							   on timeline.postId = posts.postId
						   INNER JOIN `users`
							   on  posts.postUserId = users.uid
						   WHERE 	timeline.whoseTimeLine= :uid
						   ORDER By timeline.statusTime DESC
						   LIMIT $limit OFFSET $offset
						   "
					   );

	$stmt->bindParam(':uid', $uid, PDO::PARAM_STR);		 
	$stmt->execute();

	$timelinePosts= $stmt->fetchAll(PDO::FETCH_OBJ);


	foreach ($timelinePosts as $key => $value) {
		
		if(checkLike($uid,$value->postId)){
			 $value->isLiked=true;
		 }else{
			 $value->isLiked=false;
		}

	}
  
	echo json_encode($timelinePosts);	
	  
});




$app->get('/app/loadprofile', function($request){

    include __DIR__.'/../app/helpers/dbhelper.php';

    $user_id = $request->getQueryParam('user_id');

    $stmt  = $pdo->prepare("SELECT * FROM `users` WHERE `uid` = :user_id;");
    $stmt->bindParam("user_id", $user_id, PDO::PARAM_STR);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC); // fetch single row
	$row['state'] = 5;

    echo json_encode($row);




});



// Api for loading others profile 
$app->get('/app/loadotherprofile',function($request){

	include __DIR__ .'/../app/helpers/dbhelper.php';


	$user_id = $request->getQueryParam('userId');
	$profileId = $request->getQueryParam('profileId');

	$stmt = $pdo->prepare('SELECT * FROM `users` WHERE `uid` = :userId');
	$stmt->bindParam(':userId', $profileId, PDO::PARAM_STR);
	$stmt->execute();
	$row = $stmt->fetch(PDO::FETCH_ASSOC);


	$current_state = 0;

	$request = checkRequest($user_id,$profileId);
		if($request){
			if($request['sender']==$user_id){
				// we have send the request
				$current_state = "2";
			}else{
				$current_state="3";
				//we have received the request
			}
		}else{
			if(checkFriend($user_id,$profileId)){
				$current_state = "1";
				//we are friends
			}else{
				$current_state="4";
				//we are unknown to one another
			}
		}

	$row['state']= $current_state;

	echo json_encode($row);

});



// Api for search
$app->get('/app/search',function($request){

	include __DIR__ .'/../app/helpers/dbhelper.php';

	$keyword = $request->getQueryParam('keyword');

	$stmt = $pdo->prepare("
						SELECT * from users 
						where name like '$keyword%'
						limit 10

					");


	$stmt->execute();
	$row = $stmt->fetchAll(PDO::FETCH_ASSOC);

	echo json_encode($row);

});



// Api for laoding Friends and Requests
$app->get('/app/loadfriends',function($request){

	include __DIR__ .'/../app/helpers/dbhelper.php';

	$userId = $request->getQueryParam('userId');

	$stmt = $pdo->prepare('
								SELECT users.* FROM `users` 
								Inner JOIN `requests`
								ON users.uid = requests.sender
								 WHERE `receiver` = :userId'
							);

		 $stmt->bindParam(':userId', $userId, PDO::PARAM_STR);

		$stmt->execute();	

		$row['requests']= $stmt->fetchAll(PDO::FETCH_ASSOC);



		$stmt = $pdo->prepare('
								SELECT users.* FROM `users` 
								Inner JOIN `friends`
								ON users.uid = friends.profileId
								 WHERE friends.userId = :userId'
							);

		 $stmt->bindParam(':userId', $userId, PDO::PARAM_STR);

		$stmt->execute();		
		$row['friends'] = $stmt->fetchAll(PDO::FETCH_ASSOC);	


		echo json_encode($row);

});



?>