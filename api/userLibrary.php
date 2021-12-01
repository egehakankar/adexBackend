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

    if ($type == "getGames") {
        $id = $_POST['id'];
        $cat = $_POST['category'];
        $sql = "SELECT G.gameID, G.game_name, G.imageO
        FROM library L natural join game G natural join user U
        WHERE U.userID = '$id' AND G.genre = '$cat' AND G.active = 'yes'
        ORDER BY game_name
        ";
        $result = $db->query($sql);

        $games = mysqli_fetch_all($result, MYSQLI_ASSOC);
        $gamesData = json_encode($games);

        echo '{"games":' . $gamesData . '}';
    } else if ($type == "seeMods") {
        $gameID = $_POST['gameID'];
        $sql = "SELECT modeO
                FROM mode
                WHERE gameID = '$gameID';";
        $result = $db->query($sql);

        $modes = mysqli_fetch_all($result, MYSQLI_ASSOC);
        $modesData = json_encode($modes);

        echo '{"modes":' . $modesData . '}';
    } else if ($type == "buildMod") {
        $id = $_POST['id'];
        $gameID = $_POST['gameID'];
        $mode = $_POST['curM'];
        $sql = "INSERT INTO mode(userID, gameID, modeO) VALUES (
            '$id',
            '$gameID',
            '$mode'
            );";
        $result = $db->query($sql);

        if ($result) {
            echo json_encode(array(
                "checkBI" => true
            ));
        } else {
            echo json_encode(array(
                "checkBI" => false
            ));
        }
    }
    else if($type == "seeComments")
    {
        $gameID = $_POST['gameID'];
        $sql = "SELECT U.username, C.textO, C.dateO, C.ratingO
                FROM comment AS C, user AS U
                WHERE U.userID = C.userID AND C.gameID = '$gameID';";
        $result = $db->query($sql);

        $comments = mysqli_fetch_all($result, MYSQLI_ASSOC);
        $commentsData = json_encode($comments);

        echo '{"comments":' . $commentsData . '}';
    }
    else if($type == "createComment")
    {
        $id = $_POST['id'];
        $gameID = $_POST['gameID'];
        $comment = $_POST['comText'];
        $rating = $_POST['rating'];
        $now = date_create()->format('Y-m-d H:i:s');

        $sql = "INSERT INTO comment(userID, gameID, dateO, textO, ratingO) VALUES (
            '$id',
            '$gameID',
            '$now',
            '$comment',
            '$rating'
            );";
        $result = $db->query($sql);

        if ($result) {
            echo json_encode(array(
                "checkCII" => true
            ));
        } else {
            echo json_encode(array(
                "checkCII" => false
            ));
        }
    } /*else if ($type == "balanceInc") {
        $id = $_POST['id'];
        $incB = $_POST['incB'];
        $sql = "UPDATE user SET balance = balance + '$incB' WHERE userID = '$id'";
        $result = $db->query($sql);

        $sql = "SELECT balance FROM user WHERE userID = '$id'";
        $result = $db->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo json_encode(array(
                    "balance" => $row["balance"],
                    "checkBI" => true
                ));
            }
        }
    } else if ($type == "buyGame") {
        $id = $_POST['id'];
        $gameid = $_POST['gameid'];

        $sql = "SELECT price FROM game WHERE gameID = '$gameid'";
        $result = $db->query($sql);
        $price = $result->fetch_object()->price;

        $sql2 = "SELECT userID FROM user WHERE balance >= '$price' AND userID = '$id'";
        $result2 = $db->query($sql2);

        $id2 = $result2->fetch_object()->userID;
        $mode0 = 0;

        $now = date_create()->format('Y-m-d H:i:s');
        if ($id2 == $id) {
            $sql3 = "INSERT INTO library(userID, gameID, modeO, dateO) VALUES (
                '$id2',
                '$gameid',
                '$mode0',
                '$now');";
            $result3 = $db->query($sql3);

            $sql4 = "UPDATE user SET balance = balance - '$price' WHERE userID = '$id'";
            $result4 = $db->query($sql4);

            if ($result4) {
                echo json_encode(array(
                    "gameid" => $gameid,
                    "checkBI2" => true
                ));
            } else {
                echo json_encode(array(
                    "checkBI2" => false
                ));
            }
        }
    } else if ($type == "addWishlist") {
        $id = $_POST['id'];
        $gameid = $_POST['gameid'];

        $sql = "SELECT userID FROM wishlist WHERE gameID = '$gameid' AND userID = '$id'";
        $result = $db->query($sql);

        if (mysqli_num_rows($result)==0)
        {
            $sql3 = "INSERT INTO wishlist(userID, gameID) VALUES (
                '$id',
                '$gameid')";
            $result3 = $db->query($sql3);

            echo json_encode(array(
                "gameid" => $gameid,
                "checkAI2" => true
            ));
        }
        else
        {
            echo json_encode(array(
                "checkAI2" => false
            ));
        }
    }*/
} else {
    echo json_encode(["check" => false]);
}
