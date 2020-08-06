<?php

///////////////////////////////////////////////////////////
///////////// Set session or return false /////////////
///////////////////////////////////////////////////////////
function login($email, $pwd)
{
    $user = fetchAdmin($email, $pwd);
    
    if (!$user) {
        $_SESSION['error'] = 'error';
        $_SESSION['errorMsg'] = 'Invalid login details.';
        return false;
    }

    $_SESSION['errorMsg'] = '';
    $_SESSION['userId'] = $user['userId'];
    return $user;
}

function fetchAdmin($username, $pwd)
{
    global $PDO;
    $query = "SELECT * FROM admins WHERE username = ? AND pwd = ? LIMIT 1";
    $stmnt = $PDO->prepare($query);
    $stmnt->bindValue(1, $username);
    $stmnt->bindValue(2, $pwd);
    
    if (!$stmnt->execute()) {
        return false;
    }
    
    $row = $stmnt->fetch();
    return $row ? $row : false;
}

function fetchRandomMessage()
{
    global $PDO;
    $query = "SELECT * FROM start_msg ORDER BY RAND() LIMIT 1";
    $stmnt = $PDO->prepare($query);
    if (!$stmnt->execute()) {
        return false;
    }
    $row = $stmnt->fetch();
    return $row;
}

function getChain($status = 0, $completed = 0)
{
    global $PDO;
    $query = "SELECT * FROM `chain` WHERE `status` = ? AND `completed` = ? ORDER BY updatedOn DESC LIMIT 1";
    $stmnt = $PDO->prepare($query);
    $stmnt->bindValue(1, $status);
    $stmnt->bindValue(2, $completed);
    if (!$stmnt->execute()) {
        return false;
    }
    $row = $stmnt->fetch();
    return $row;
}

function getUserById($userId)
{
    global $PDO;
    $query = "SELECT * FROM users WHERE userId = ? LIMIT 1";
    $stmnt = $PDO->prepare($query);
    $stmnt->bindValue(1, $userId);
    
    if (!$stmnt->execute()) {
        return false;
    }
    
    $row = $stmnt->fetch();
    return $row ? $row : false;
}

function getUsersCSV()
{
    global $PDO;
    $query = "SELECT * FROM users ORDER BY chain ASC";
    $stmnt = $PDO->prepare($query);
    if (!$stmnt->execute()) {
        return false;
    }

    $headers = array();

    $all = $stmnt->fetchAll(PDO::FETCH_ASSOC);
    if(count($all)){
        $headers = array_keys($all[0]);
    }

    $query = "SELECT * FROM users ORDER BY chain ASC";
    $stmnt = $PDO->prepare($query);
    if (!$stmnt->execute()) {
        return false;
    }

    $users = array();
    $users[] = $headers;

    while($row = $stmnt->fetch(PDO::FETCH_ASSOC)){
        $users[] = $row;
    }

    return $users;
}

function createChain($msgId){    
    global $PDO;
    $query = "INSERT INTO `chain` (`msgId`, `status`, `completed`, `updatedOn`, `createdOn`) VALUES (?, '1', '0', CURRENT_TIMESTAMP, NOW())";
    $stmnt = $PDO->prepare($query);
    $counter = 1;
    $stmnt->bindValue($counter++, $msgId);
    
    if (!$stmnt->execute()) {
        return false;
    }

    return $PDO->lastInsertId();
}

function createUser($chainId, $place){    
    global $PDO;
    $query = "INSERT INTO `users` (`chain`, `place`, `updatedOn`, `createdOn`) VALUES (?, ?, CURRENT_TIMESTAMP, NOW())";
    $stmnt = $PDO->prepare($query);
    $counter = 1;
    $stmnt->bindValue($counter++, $chainId);
    $stmnt->bindValue($counter++, $place);
    
    if (!$stmnt->execute()) {
        echo "QUERY FAILED: " . json_encode($stmnt->errorInfo());
        return false;
    }

    return $PDO->lastInsertId();
}

function getUsersCountByChain($chainId){    
    global $PDO;
    $query = "SELECT COUNT(*) FROM users WHERE chain = ?";
    $stmnt = $PDO->prepare($query);
    $counter = 1;
    $stmnt->bindValue($counter++, $chainId);
    
    if (!$stmnt->execute()) {
        return false;
    }

    return $stmnt->fetchColumn();
}

function fetchMessageByChain($chainId)
{
    global $PDO;
    $query = "SELECT * FROM `chain` c LEFT JOIN start_msg m ON m.id = c.msgId WHERE c.chainId = ? LIMIT 1";
    $stmnt = $PDO->prepare($query);
    $counter = 1;
    $stmnt->bindValue($counter++, $chainId);

    if (!$stmnt->execute()) {
        return false;
    }

    $row = $stmnt->fetch();
    return $row ? $row : false;
}

function saveUserAnswer($userId, $answer, $time)
{
    global $PDO;
    $query = "UPDATE users SET answer = ? , timeSpent = ? , updatedOn = NOW() WHERE userId = ?";
    $stmnt = $PDO->prepare($query);
    $counter = 1;
    $stmnt->bindValue($counter++, $answer);
    $stmnt->bindValue($counter++, $time);
    $stmnt->bindValue($counter++, $userId);

    if (!$stmnt->execute()) {
        return false;
    }

    return true;
}

function updateChainStatus($chainId, $status)
{
    global $PDO;
    $query = "UPDATE chain SET `status` = ? , updatedOn = NOW() WHERE chainId = ?";
    $stmnt = $PDO->prepare($query);
    $counter = 1;
    $stmnt->bindValue($counter++, $status);
    $stmnt->bindValue($counter++, $chainId);

    if (!$stmnt->execute()) {
        return false;
    }

    return true;
}

function updateChain($chainId, $userId, $status, $completed)
{
    global $PDO;
    $query = "UPDATE chain SET `status` = ? , `completed` = ? , user = ? , updatedOn = NOW() WHERE chainId = ?";
    $stmnt = $PDO->prepare($query);
    $counter = 1;
    $stmnt->bindValue($counter++, $status);
    $stmnt->bindValue($counter++, $completed);
    $stmnt->bindValue($counter++, $userId);
    $stmnt->bindValue($counter++, $chainId);

    if (!$stmnt->execute()) {
        return false;
    }

    return true;
}

function fetchTotalParticipants()
{
    global $PDO;
    $query = "SELECT COUNT(*) FROM `users` WHERE answer IS NOT NULL";
    $stmnt = $PDO->prepare($query);

    if (!$stmnt->execute()) {
        return false;
    }

    return $stmnt->fetchColumn();
}

function fetchTotalChains()
{
    global $PDO;
    $query = "SELECT COUNT(*) FROM `chain`";
    $stmnt = $PDO->prepare($query);

    if (!$stmnt->execute()) {
        return false;
    }

    return $stmnt->fetchColumn();
}

function fetchTotalCompletedChains()
{
    global $PDO;
    $query = "SELECT COUNT(*) FROM `chain` WHERE completed = 1";
    $stmnt = $PDO->prepare($query);

    if (!$stmnt->execute()) {
        return false;
    }

    return $stmnt->fetchColumn();
}

function fetchTotalUsersCompletedChains()
{
    global $PDO;
    $query = "SELECT COUNT(*) FROM users WHERE chain IN (SELECT chainId FROM `chain` WHERE completed = 1)";
    $stmnt = $PDO->prepare($query);

    if (!$stmnt->execute()) {
        return false;
    }

    return $stmnt->fetchColumn();
}