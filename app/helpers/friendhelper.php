<?php



function checkRequest($userId,$profileId){
    include __DIR__ .'/../helpers/dbhelper.php';

    $stmt = $pdo->prepare("SELECT * FROM `requests` WHERE `sender` = :userId AND `receiver` = :profileId OR `sender` = :profileId AND `receiver` = :userId");
    $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
    $stmt->bindParam(':profileId', $profileId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);

}

function checkFriend($userId,$profileId){

      include __DIR__ .'/../helpers/dbhelper.php';

        $stmt = $pdo->prepare("SELECT * FROM `friends` WHERE `userId` = :userId AND `profileId` = :profileId");
        $stmt->bindParam(':userId', $userId, PDO::PARAM_STR);
        $stmt->bindParam(':profileId', $profileId, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);

    }

 function sentRequest($userId,$profileId){

    $insertRequest = insert('requests', array('sender' => $userId, 'receiver' => $profileId, 'requestDate' => date('Y-m-d H:i:s')));
    $insertNotification = insert('notifications', array('notificationTo' => $profileId, 'notificationFrom' => $userId,'type'=>'4', 'notificationTime' => date('Y-m-d H:i:s'),'postId'=>'0'));
    

    if($insertRequest && $insertNotification){
        echo true;
    }else{
        echo false;
    }
                     
}

 function cancelRequest($userId,$profileId){

    $deleteRequest = delete('requests', array('sender' => $userId, 'receiver' => $profileId));
    $deleteRequestNotification = delete('notifications', array('notificationTo' => $profileId, 'notificationFrom' => $userId,'type' => '4'));
    if($deleteRequest && $deleteRequestNotification){
        echo true;
    }else{
        echo false;
    }
}

 function acceptRequest($userId,$profileId){

     include __DIR__ .'/../helpers/dbhelper.php';

    $addToFriendTable1= insert('friends', array('userId' => $userId, 'profileId' => $profileId, 'friendOn' => date('Y-m-d H:i:s')));
    $addToFriendTable2= insert('friends', array('userId' => $profileId, 'profileId' => $userId, 'friendOn' => date('Y-m-d H:i:s')));

    $insertNotificationAccepted = insert('notifications', array('notificationTo' => $profileId, 'notificationFrom' => $userId,'type'=>'5', 'notificationTime' => date('Y-m-d H:i:s'),'postId'=>'0'));

    if($addToFriendTable1 && $addToFriendTable2 && $insertNotificationAccepted){

    
            $deleteRequest1 = delete('requests', array('sender' => $userId, 'receiver' => $profileId));
            $deleteRequest2 = delete('requests', array('sender' => $profileId, 'receiver' => $userId));

            
                if($deleteRequest1 || $deleteRequest2){
                        echo true;
                }else{
                    echo false;
                }	

    }else{
        echo false;
    }
}

 function unFriend($userId,$profileId){

     include __DIR__ .'/../helpers/dbhelper.php';

        $unFriend1 = delete('friends', array('userId' => $userId, 'profileId' => $profileId));
        $unFriend2 = delete('friends', array('userId' => $profileId, 'profileId' => $userId));
        
        if($unFriend1 || $unFriend2 ){
            echo true;
        }else{
            echo false;
        }
}

 function insert($table, $fields = array()){

    include __DIR__ .'/../helpers/dbhelper.php';

    $columns = implode(',', array_keys($fields));

    $values  = ':'.implode(', :', array_keys($fields));

    $sql     = "INSERT INTO {$table} ({$columns}) VALUES ({$values})";

    if($stmt = $pdo->prepare($sql)){

        foreach ($fields as $key => $data) {
            $stmt->bindValue(':'.$key, $data);
        }

        $stmt->execute();
        return $pdo->lastInsertId();
    }
}

 function delete($table, $array){

    include __DIR__ .'/../helpers/dbhelper.php';
    
    $sql   = "DELETE FROM " . $table;
    $where = " WHERE ";

    foreach($array as $key => $value){
        $sql .= $where . $key . " = '" . $value . "'";
        $where = " AND ";
    }

    $sql .= ";";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    if($stmt){
        return true;
    }else{
        return false;
    }
}


?>