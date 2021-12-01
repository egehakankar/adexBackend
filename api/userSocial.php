<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET,POST,PUT,DELETE,OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

if ($_SERVER["REQUEST_METHOD"] == "OPTIONS") exit();

require 'config.php';

$_POST = json_decode(file_get_contents('php://input'), true);

if ($_POST) {

    http_response_code(200);
    $type = $_POST['type'];

    if ($type == "getFriends") {
        $id = $_POST['id'];
        $cat = $_POST['category'];

       
        if($cat == "friends"){
            $sql = "SELECT U1.userID, U1.image, U1.username
        FROM User U1 join Friend F join User U2
        WHERE U2.userID = '$id' 
        AND ((F.userID2 =  U1.userID AND F.userID1 = U2.userID) OR (F.userID1 =  U1.userID AND F.userID2 = U2.userID))
        ORDER BY U1.username
        ";
        }
        else if ($cat == "curators") {
            $id = $_POST['id'];
            $sql = "SELECT C.curatorID, C.image, C.username
            FROM User U join Follow F join Curator C
            WHERE U.userID = '$id' 
            AND (F.userID =  U.userID AND F.curatorID = C.curatorID) 
            ORDER BY C.username
            ";
        }

        else if ($cat == "requests") {
            $id = $_POST['id'];
            $sql = "SELECT U1.userID, U1.image, U1.username
                FROM User U1 join Friend_Request F join User U2
                WHERE U2.userID = '$id' 
                AND (F.userID2 =  U2.userID AND F.userID1 = U1.userID)
                ORDER BY U1.username
                ";
        }
        
        $result = $db->query($sql);
     
        $friends = mysqli_fetch_all($result, MYSQLI_ASSOC);
        $friendsData = json_encode($friends);
        echo '{"friends":' . $friendsData . '}';
    } 
    else if ($type == "addFriend") {
        $id = $_POST['id'];
        $id2 = $_POST['id2'];
        $now = date_create()->format('Y-m-d H:i:s');

        $sql0 = "SELECT * FROM user where username = '$id2';";
        $result0 = $db->query($sql0);
        if(mysqli_num_rows($result0)==0){
            echo json_encode(array(
                "checkF" => 4
            ));
        }

        else{
            $sql1 = "SELECT * FROM friend where (userID1 = '$id' and userID2 = (SELECT userID FROM user where username = '$id2'))
            or (userID2 = '$id' and userID1 = (SELECT userID FROM user where username = '$id2'));";
            $result1 = $db->query($sql1);

            if(mysqli_num_rows($result1)!=0){
                echo json_encode(array(
                    "checkF" => 2
                ));
            }

            else {
                $sql2 = "SELECT * FROM friend_request where (userID1 = '$id' and userID2 = (SELECT userID FROM user where username = '$id2'));";
                $result2 = $db->query($sql2);
                if(mysqli_num_rows($result2)!=0){
                    echo json_encode(array(
                        "checkF" => 3
                    ));
                }
                else{
                    //$sqla = "SELECT userID FROM user where username = '$id2';";
                    //$resulta = $db->query($sql2);
                    $sql = "INSERT INTO friend_request(userID1, userID2, dateO) VALUES (
                        '$id',
                        (SELECT userID FROM user where username = '$id2'),
                        '$now'
                    );";
                    $result = $db->query($sql);
                    if($result){
                        echo json_encode(array(
                            "checkB" => true,
                            "checkF" => 1
                        ));
                    }
                }           
            }   
        }
        
    }
    else if ($type == "followCurator") {
        $id = $_POST['id'];
        $curId = $_POST['curId'];
        $now = date_create()->format('Y-m-d H:i:s');

        $sql0 = "SELECT * FROM curator where (username = '$curId');";
        $result0 = $db->query($sql0);
        if(mysqli_num_rows($result0)==0){
            echo json_encode(array(
                "check0" => 3
            ));
        }
        else{
            $sql1 = "SELECT * FROM follow where (userID = '$id' and curatorID = (SELECT curatorID FROM curator where username = '$curId'));";
            $result1 = $db->query($sql1);

            if(mysqli_num_rows($result1)!=0){
                echo json_encode(array(
                    "check0" => 2
                ));
            }

            else{
                $sql = "INSERT INTO follow(userID, curatorID, dateO) VALUES (
                    '$id',
                    (SELECT curatorID FROM curator where username = '$curId'),
                    '$now'
                );";
                $result = $db->query($sql);
                if($result){
                    echo json_encode(array(
                        "checkCu" => true,
                        "check0" => 1
                    ));
                }
                else{
                    echo json_encode(array(
                        "checkCu" => false
                    ));
                }
            }
        }
        
    }
    else if ($type == "seeWishlist") {

        $userID = $_POST['id2'];
        $sql = "SELECT game_name,price,gameID
                FROM game natural join wishlist natural join user 
                WHERE userID = '$userID';";
        $result = $db->query($sql);
        $wishlist = mysqli_fetch_all($result, MYSQLI_ASSOC);
        $wishlistData = json_encode($wishlist);
        echo '{"wishlist":' . $wishlistData . '}';
    }

    else if($type == "acceptReq"){
        $id = $_POST['id'];
        $id2 = $_POST['id2'];
        $now = date_create()->format('Y-m-d H:i:s');
        $sql = "INSERT INTO friend(userID1, userID2, dateO) VALUES (
            '$id',
            '$id2',
            '$now'
        );";
        $result = $db->query($sql);
        $sql = "DELETE FROM friend_request
        WHERE userID1 = '$id2' AND userID2 = '$id'";
        
        $result = $db->query($sql);
        if($result){
            echo json_encode(array(
                "checkB" => true
            ));
        }
        else{
            echo json_encode(array(
                "checkB" => false
            ));
        }
    }

    else if($type == "rejectReq"){
        $id = $_POST['id'];
        $id2 = $_POST['id2'];
        $now = date_create()->format('Y-m-d H:i:s');
        $sql = "DELETE FROM friend_request
        WHERE userID1 = '$id2' AND userID2 = '$id'";
        $result = $db->query($sql);
        if($result){
            echo json_encode(array(
                "checkR" => true
            ));
        }
        else{
            echo json_encode(array(
                "checkR" => false
            ));
        }

    }

    else if($type == "seeReview"){
        $id = $_POST['id'];
        $curId = $_POST['curId'];
        $sql = "SELECT textO,ratingO,game_name
        FROM review natural join game
        WHERE curatorID= '$curId';";
        $result = $db->query($sql);
        $reviews = mysqli_fetch_all($result, MYSQLI_ASSOC);
        $reviewsData = json_encode($reviews);
        echo '{"reviews":' . $reviewsData . '}';
    }
    else if($type == "buyGameFF"){

        $id3 = $_POST['id2'];
        $id = $_POST['id'];

        $gameid = $_POST['gameid'];

        $sql = "SELECT price FROM game WHERE gameID = '$gameid'";
        $result = $db->query($sql);
        $price = $result->fetch_object()->price;

        $sql2 = "SELECT userID FROM user WHERE balance >= '$price' AND userID = '$id'";
        $result2 = $db->query($sql2);

        if($result2->fetch_object()){
            $mode0 = 0;

            $now = date_create()->format('Y-m-d H:i:s');
           
            $sql3 = "INSERT INTO library(userID, gameID, modeO, dateO) VALUES (
                '$id3',
                '$gameid',
                '$mode0',
                '$now');";
            $result3 = $db->query($sql3);

            $sql4 = "UPDATE user SET balance = balance - '$price' WHERE userID = '$id'";
            $result4 = $db->query($sql4);

            $sql5 = "DELETE FROM wishlist
            WHERE userID = '$id3' AND gameID = '$gameid'";
            $result5 = $db->query($sql5);

            if ($result5) {
                echo json_encode(array(
                    "gameid" => $gameid,
                    "checkBuy" => 1
                ));
            } else {
                echo json_encode(array(
                    "gameid" => $gameid,
                    "checkBuy" => 0
                ));
            }
        }
        
        else{
            echo json_encode(array(
                "gameid" => $gameid,
                "checkBuy" => 2
            ));
        }
    }
    
} 
    
 else {
    echo json_encode(["check" => false]);
}
