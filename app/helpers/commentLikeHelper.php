<?php

function getCommentLikeCount($cid){

	include __DIR__.'/../helpers/dbhelper.php';
	
	$stmt =  $pdo->prepare("SELECT likeCount from `comments` WHERE `cid` = :cid LIMIT 1");
	$stmt->bindParam(':cid', $cid, PDO::PARAM_STR);
	$stmt->execute();
	$userInfo =$stmt->fetch(PDO::FETCH_OBJ);
	return $userInfo;

}


function checkCommentLike($userId, $cid){

	include __DIR__.'/../helpers/dbhelper.php';


	$stmt = $pdo->prepare("SELECT * FROM `commentLikes` WHERE `likedBy` = :userId AND `commentId` = :cid");
	$stmt->bindParam(":userId", $userId, PDO::PARAM_INT);
	$stmt->bindParam(":cid", $cid, PDO::PARAM_INT);
	$stmt->execute();

	return $stmt->fetch(PDO::FETCH_OBJ);

}

?>