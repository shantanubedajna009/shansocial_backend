<?php


	$app->post('/app/postcomment',function($request){
	    include __DIR__ . '/../app/helpers/dbhelper.php';

	
		// The actual Comment
		$comment = $request->getParsedBody()['comment'];
		

		// UserId of a user who is making the comment
		$commentBy =  $request->getParsedBody()['commentBy'];



		/*
			if user is making comment to post
					it will be 0
			and if user is replying to a comment
					it will be equal to post Id
		*/
		$superParentId =  $request->getParsedBody()['superParentId'];


		
		/*
				if user is making comment to post
					it will be postId
			and if user is replying to a comment
					it will be equal to commentId on which the user is replying to
		*/
		$parentId =  $request->getParsedBody()['parentId'];



		/*
			Simple flag to check whether a comment has child comments or not
			it will be 0 by default until someone replys to comment
	
		*/
		$hasSubComment =  $request->getParsedBody()['hasSubComment'];



		/*
				It is the userId of a postOwner
		*/
		$postUserId =  $request->getParsedBody()['postUserId'];



		/*
			Simple flag to check whether user is replying to a post or a comment
		*/
		$level = $request->getParsedBody()['level'];


		/*
			if user is replying to a comment
				it will be ownerId (ie. userId ) of the comment on which user is replying to
		*/
		$commentUserId = $request->getParsedBody()['commentUserId'];
		
		$response = array();
		$stmt = $pdo->prepare("
		INSERT INTO `comments` 
		(`comment`, `commentBy`, `commentDate`,`superParentId`,`parentId`,`hasSubComment`,`level`) 
		VALUES (:comment, :commentBy,current_timestamp ,:superParentId,:parentId,:hasSubComment,:level);");
			
			$stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
			$stmt->bindParam(':commentBy', $commentBy, PDO::PARAM_STR);
			$stmt->bindParam(':superParentId', $superParentId, PDO::PARAM_STR);
			$stmt->bindParam(':parentId', $parentId, PDO::PARAM_STR);
			$stmt->bindParam(':hasSubComment', $hasSubComment, PDO::PARAM_STR);
			$stmt->bindParam(':level', $level, PDO::PARAM_STR);
			$stmt= $stmt->execute();
			$cid = $pdo->lastInsertId();

			if($stmt){
				if($level==0){


					// increase the comment count of the post
					$stmt = $pdo->prepare("
					UPDATE `posts` SET `hasComment` = 1, `commentCount` = `commentCount`+1   WHERE `postId` = :parentId");
					$stmt->bindParam(":parentId", $parentId, PDO::PARAM_INT);
					$stmt = $stmt->execute();
				
				}else{

					// else increase the post comment count , as well as set the hasComment of the parent comment to 1
					
					$stmt = $pdo->prepare("
					UPDATE `posts` SET `commentCount` = `commentCount`+1   WHERE `postId` = :superParentId");
					$stmt->bindParam(":superParentId", $superParentId, PDO::PARAM_INT);
					$stmt = $stmt->execute();
					
					$stmt = $pdo->prepare("
					UPDATE `comments` SET `hasSubComment` = 1   WHERE `cid` = :parentId");
					$stmt->bindParam(":parentId", $parentId, PDO::PARAM_INT);
					$stmt = $stmt->execute();
				}
		
				$comment = null;
				
				if($level==0){
					$comment = getCommenttedData($parentId,$cid);
				}else{
					$comment = getSubCommenttedData($superParentId,$parentId,$cid);
				}
				
				$response['comment']=$comment;
	
				$response['subComments'] =array(
										'total'=>0,
										'lastComment'=>array()
										);
					
		$stmt = $pdo->prepare("
		INSERT INTO `notifications` 
		(`notificationTo`, `notificationFrom`, `type`,`notificationTime`,`postId`) 
		VALUES (:notificationTo, :notificationFrom,:type, current_timestamp,:postId);");
			
        $type = 2;
        $updatePostId = 0;
        
        if($level==0){
            $updatePostId = $parentId;
        }else{
            $updatePostId = $superParentId;
        }

        $stmt->bindParam(':notificationTo', $postUserId, PDO::PARAM_STR);
        $stmt->bindParam(':notificationFrom', $commentBy, PDO::PARAM_STR);
        $stmt->bindParam(':postId', $updatePostId, PDO::PARAM_STR);
        $stmt->bindParam(':type', $type, PDO::PARAM_INT);
        $stmt= $stmt->execute();
                    
        if($level==1){
        
            $stmt = $pdo->prepare("
			INSERT INTO `notifications` 
			(`notificationTo`, `notificationFrom`, `type`,`notificationTime`,`postId`) 
			VALUES (:notificationTo, :notificationFrom,:type, current_timestamp,:postId);");
            
			$type = 3;
            $stmt->bindParam(':notificationTo', $commentUserId, PDO::PARAM_STR);
            $stmt->bindParam(':notificationFrom', $commentBy, PDO::PARAM_STR);
            $stmt->bindParam(':postId', $superParentId, PDO::PARAM_STR);
            $stmt->bindParam(':type', $type, PDO::PARAM_INT);
            $stmt= $stmt->execute();

                
            }
    }
        $result['result']=array($response);

        echo json_encode($result);
	
					
			
	});

	







	$app->get('/app/retrivetopcomment',function($request){
		include __DIR__ . '/../app/helpers/dbhelper.php';

		$postId = $request->getQueryParam('postId');
		$response=array();
		$comment = retriveTopLevelComment($postId);
		$response['result']=$comment;

		echo json_encode($response);	
				
					
	});






	


	$app->get('/app/retrivelowlevelcomment',function($request){
		include __DIR__ . '/../app/helpers/dbhelper.php';

		$postId = $request->getQueryParam('postId');
		$commentId = $request->getQueryParam('commentId');

		$response=array();
		$comment = retriveLowLevelComment($postId,$commentId);
		$response=$comment;

		echo json_encode($response);
					
	});


?>