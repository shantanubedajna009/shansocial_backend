<?php

function getLikeCount($postId){

	include __DIR__.'/../helpers/dbhelper.php';
	
	$stmt =  $pdo->prepare("SELECT likeCount from `posts` WHERE `postId` = :postId LIMIT 1");
	$stmt->bindParam(':postId', $postId, PDO::PARAM_STR);
	$stmt->execute();
	$userInfo =$stmt->fetch(PDO::FETCH_OBJ);
	return $userInfo;

}


function checkLike($userId, $postId){

	include __DIR__.'/../helpers/dbhelper.php';


	$stmt = $pdo->prepare("SELECT * FROM `likes` WHERE `likedBy` = :userId AND `postId` = :postId");
	$stmt->bindParam(":userId", $userId, PDO::PARAM_INT);
	$stmt->bindParam(":postId", $postId, PDO::PARAM_INT);
	$stmt->execute();

	return $stmt->fetch(PDO::FETCH_OBJ);

}

?>