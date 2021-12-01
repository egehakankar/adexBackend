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
    $emailOrUsername = $_POST['emailUsername'];
    $passwordO = $_POST['passwordO'];

    if($type == "User")
    {
        $sql = "SELECT * FROM user WHERE (username = '$emailOrUsername' OR email = '$emailOrUsername') AND passwordO = '$passwordO'";
        $result = $db->query($sql);
        
        if ($result->num_rows > 0) 
        {
            while($row = $result->fetch_assoc()) 
            {
                echo json_encode(array(
                    "check" => true,
                    "id" => $row["userID"]
                ));
            }
        } 
        else 
        {
            echo json_encode(array(
                "check" => false,
            ));
        }
    }
    else if($type == "Curator")
    {
        $sql = "SELECT * FROM curator WHERE (username = '$emailOrUsername' OR email = '$emailOrUsername') AND passwordO = '$passwordO'";
        $result = $db->query($sql);
        
        if ($result->num_rows > 0) 
        {
            while($row = $result->fetch_assoc()) 
            {
                echo json_encode(array(
                    "check" => true,
                    "id" => $row["curatorID"]
                ));
            }
        } 
        else 
        {
            echo json_encode(array(
                "check" => false,
            ));
        }
    }
    else if($type == "Publisher")
    {
        $sql = "SELECT * FROM publisher_company WHERE (username = '$emailOrUsername' OR email = '$emailOrUsername') AND passwordO = '$passwordO'";
        $result = $db->query($sql);
        
        if ($result->num_rows > 0) 
        {
            while($row = $result->fetch_assoc()) 
            {
                echo json_encode(array(
                    "check" => true,
                    "id" => $row["publisherID"]
                ));
            }
        } 
        else 
        {
            echo json_encode(array(
                "check" => false,
            ));
        }
    }
    else if($type == "Developer")
    {
        $sql = "SELECT * FROM developer_company WHERE (username = '$emailOrUsername' OR email = '$emailOrUsername') AND passwordO = '$passwordO'";
        $result = $db->query($sql);
        
        if ($result->num_rows > 0) 
        {
            while($row = $result->fetch_assoc()) 
            {
                echo json_encode(array(
                    "check" => true,
                    "id" => $row["developerID"]
                ));
            }
        } 
        else 
        {
            echo json_encode(array(
                "check" => false,
            ));
        }
    }
    else
    {
        echo json_encode(["check" => false]);
    }
    
} else {
    echo json_encode(["check" => false]);
}
?>