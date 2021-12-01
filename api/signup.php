<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET,POST,PUT,DELETE,OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

if ($_SERVER["REQUEST_METHOD"] == "OPTIONS") exit();

require 'config.php';

$_POST = json_decode(file_get_contents('php://input'), true);

if ($_POST) {

    http_response_code(200);
    $type = $_POST['age'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $passwordO = $_POST['passwordO'];
    
    if($type == 'User')
    {
        $sql = "SELECT * FROM user WHERE (username = '$username' AND email = '$email');";
        $result = $db->query($sql);
    
        if ($result->num_rows > 0) {
            echo json_encode(array(
                "check" => false,
            ));
    
        } else {
            $balance = 0;
    
            $createUserSql = "INSERT INTO user(username, email, passwordO, balance, image) VALUES (
                '$username',
                '$email',
                '$passwordO',
                0,
                '');";
    
            $db->query($createUserSql);
    
            $ssql = "SELECT * FROM user WHERE username='$username'";
            $result = $db->query($ssql);
    
    
            while($row = $result->fetch_assoc()) 
            {
                echo json_encode(array(
                    "check" => true,
                    "id" => $row["userID"]
                ));
            }
        }
    }
    else if($type == 'Curator')
    {
        $sql = "SELECT * FROM curator WHERE (username = '$username' AND email = '$email');";
        $result = $db->query($sql);
    
        if ($result->num_rows > 0) {
            echo json_encode(array(
                "check" => false,
            ));
    
        } else {
            $createUserSql = "INSERT INTO curator(username, email, passwordO, image) VALUES (
                '$username',
                '$email',
                '$passwordO',
                '');";
    
            $db->query($createUserSql);
    
            $ssql = "SELECT * FROM curator WHERE username='$username'";
            $result = $db->query($ssql);
    
    
            while($row = $result->fetch_assoc()) 
            {
                echo json_encode(array(
                    "check" => true,
                    "id" => $row["curatorID"]
                ));
            }
        }
    }
    else if($type == 'Developer')
    {
        $sql = "SELECT * FROM developer_company WHERE (username = '$username' AND email = '$email');";
    $result = $db->query($sql);

    if ($result->num_rows > 0) {
        echo json_encode(array(
            "check" => false,
        ));

    } else {
        $createUserSql = "INSERT INTO developer_company(username, email, passwordO) VALUES (
            '$username',
            '$email',
            '$passwordO');";

        $db->query($createUserSql);

        $ssql = "SELECT * FROM developer_company WHERE username='$username'";
        $result = $db->query($ssql);


        while($row = $result->fetch_assoc()) 
        {
            echo json_encode(array(
                "check" => true,
                "id" => $row["developerID"]
            ));
        }
    }
    }
    else if($type == 'Publisher')
    {
        $sql = "SELECT * FROM publisher_company WHERE (username = '$username' AND email = '$email');";
    $result = $db->query($sql);

    if ($result->num_rows > 0) {
        echo json_encode(array(
            "check" => false,
        ));

    } else {
        $balance = 0;

        $createUserSql = "INSERT INTO publisher_company(username, email, passwordO) VALUES (
            '$username',
            '$email',
            '$passwordO');";

        $db->query($createUserSql);

        $ssql = "SELECT * FROM publisher_company WHERE username='$username'";
        $result = $db->query($ssql);


        while($row = $result->fetch_assoc()) 
        {
            echo json_encode(array(
                "check" => true,
                "id" => $row["publisherID"]
            ));
        }
    }
    }

    


} else {
    echo json_encode(["check" => false]);
}


?>