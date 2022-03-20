<?php


function retriveTopLevelComment($postId){

    include __DIR__ . '/../helpers/dbhelper.php';

    $stmt = $pdo->prepare("
            SELECT comments.*,users.name,users.profileUrl,users.userToken
            FROM `comments`
            INNER JOIN `users`
                ON 	comments.commentBy = users.uid
            WHERE `parentId` = :postId AND `level`=0 ORDER BY commentDate DESC");

    $stmt->bindParam(":postId", $postId, PDO::PARAM_INT);
    $stmt->execute();
    
    $result = array();

    //		 $data= $stmt->fetchAll(PDO::FETCH_ASSOC);
    $comments= $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($comments as $key => $comment) {

            $subComments = array();

            $subComments['total']=0;
            $subComments['lastComment']=array();


            if($comment['hasSubComment']==1){

                $subComments['lastComment']=retriveLastComment($postId,$comment['cid']);
                $subComments['total']= retriveTotalCommentCount($postId,$comment['cid']);

            }

            $result[$key]['comment']=$comment;
            $result[$key]['subComments']=$subComments;


        }
        
    return ($result);
}


function retriveLowLevelComment($superParentId,$commentId){

    include __DIR__ . '/../helpers/dbhelper.php';

    $stmt = $pdo->prepare("
            SELECT comments.*,users.name,users.profileUrl,users.userToken
            FROM `comments`
            INNER JOIN `users`
                ON 	comments.commentBy = users.uid
            WHERE `superParentId`=:superParentId AND`parentId` = :commentId  AND `level`=1 ORDER BY commentDate DESC");
    
    $stmt->bindParam(":superParentId", $superParentId, PDO::PARAM_INT);
    $stmt->bindParam(":commentId", $commentId, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


function getCommenttedData($postId,$commentId){

    include __DIR__ . '/../helpers/dbhelper.php';
    
    $stmt = $pdo->prepare("
            SELECT comments.*,users.name,users.profileUrl,users.userToken
            FROM `comments`
            INNER JOIN `users`
                        ON 	comments.commentBy = users.uid
            WHERE `parentId` = :postId AND `cid`=:commentId");
    
    $stmt->bindParam(":postId", $postId, PDO::PARAM_INT);
    $stmt->bindParam(":commentId", $commentId, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetch(PDO::FETCH_ASSOC);
}


function getSubCommenttedData($superParentId,$parentId,$commentId){
    
    include __DIR__ . '/../helpers/dbhelper.php';
    
    $stmt = $pdo->prepare("
            SELECT comments.*,users.name,users.profileUrl,users.userToken
            FROM `comments`
            INNER JOIN `users`
                        ON 	comments.commentBy = users.uid
            WHERE `superParentId`=:superParentId AND`parentId` = :parentId AND `cid`=:commentId");
    
    $stmt->bindParam(":superParentId", $superParentId, PDO::PARAM_INT);
    $stmt->bindParam(":parentId", $parentId, PDO::PARAM_INT);
    $stmt->bindParam(":commentId", $commentId, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetch(PDO::FETCH_ASSOC);
}


function retriveLastComment($postId,$parentComment){
    
    include __DIR__ . '/../helpers/dbhelper.php';

    $stmt = $pdo->prepare("
            SELECT comments.comment,comments.commentBy, comments.commentDate,users.name,users.profileUrl
            FROM `comments`
            INNER JOIN `users`
                ON 	comments.commentBy = users.uid
            WHERE `superParentId`=:postId AND `parentId` = :parentComment AND `level`=1 ORDER BY commentDate DESC LIMIT 1");

    $stmt->bindParam(":postId", $postId, PDO::PARAM_INT);
    $stmt->bindParam(":parentComment", $parentComment, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchALL(PDO::FETCH_ASSOC);
}



function retriveTotalCommentCount($postId,$parentComment){

    include __DIR__ . '/../helpers/dbhelper.php';

    $stmt = $pdo->prepare("
            SELECT count(*) as totalCount
            FROM `comments`
            WHERE `superParentId`=:postId AND `parentId` = :parentComment AND `level`=1 ");

    $stmt->bindParam(":postId", $postId, PDO::PARAM_INT);
    $stmt->bindParam(":parentComment", $parentComment, PDO::PARAM_INT);
    $stmt->execute();
    $stmt= $stmt->fetchALL(PDO::FETCH_ASSOC);

    return ($stmt[0]['totalCount']);
}



?>