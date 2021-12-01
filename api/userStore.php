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
        $sql = "SELECT
            gameID,
            game_name,
            imageO,
            price
        FROM
            game
        WHERE 
            genre = '$cat' AND active = 'yes'
        EXCEPT(
            SELECT
                G.gameID,
                G.game_name,
                G.imageO,
                G.price
            FROM
                game G
            INNER JOIN library L ON
                G.gameID = L.gameID
            WHERE
                L.userID = '$id')
        ";
        $result = $db->query($sql);

        $games = mysqli_fetch_all($result, MYSQLI_ASSOC);
        $gamesData = json_encode($games);

        echo '{"games":' . $gamesData . '}';
    } else if ($type == "userBalance") {
        $id = $_POST['id'];
        $sql = "SELECT balance FROM user WHERE userID = '$id'";
        $result = $db->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo json_encode(array(
                    "balance" => $row["balance"]
                ));
            }
        } else {
            echo json_encode(array(
                "balance" => 5,
            ));
        }
    } else if ($type == "balanceInc") {
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

            $sql5 = "DELETE FROM wishlist
            WHERE userID = '$id' AND gameID = '$gameid'";
            $result5 = $db->query($sql5);

            if ($result5) {
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

        if (mysqli_num_rows($result) == 0) {
            $sql3 = "INSERT INTO wishlist(userID, gameID) VALUES (
                '$id',
                '$gameid')";
            $result3 = $db->query($sql3);

            echo json_encode(array(
                "gameid" => $gameid,
                "checkAI2" => true
            ));
        } else {
            echo json_encode(array(
                "checkAI2" => false
            ));
        }
    } else if ($type == "getGames2") {
        $id = $_POST['id'];
        $cat = $_POST['category'];
        $ser = $_POST['searchN'];
        $sql = "SELECT
            gameID,
            game_name,
            imageO,
            price
        FROM
            game
        WHERE 
            genre = '$cat' AND active = 'yes' AND game_name LIKE '%$ser%'
        EXCEPT(
            SELECT
                G.gameID,
                G.game_name,
                G.imageO,
                G.price
            FROM
                game G
            INNER JOIN library L ON
                G.gameID = L.gameID
            WHERE
                L.userID = '$id')
        ";
        $result = $db->query($sql);

        $games = mysqli_fetch_all($result, MYSQLI_ASSOC);
        $gamesData = json_encode($games);

        echo '{"games":' . $gamesData . '}';
    } else if ($type == "getGamesTop") {
        $id = $_POST['id'];

        $sql = "SELECT G.gameID, G.game_name,  G.price, G.imageO, count(1) as boughtCount
                FROM Game as G natural join Library as L
                WHERE G.gameID not in (
                    select G.gameID from Game G natural join Library L where L.userID = '$id'
                )
                GROUP BY G.game_name
                ORDER BY boughtCount DESC
                LIMIT 10;
                        ";
        $result = $db->query($sql);

        $games = mysqli_fetch_all($result, MYSQLI_ASSOC);
        $gamesData = json_encode($games);

        echo '{"games":' . $gamesData . '}';
    }
} else {
    echo json_encode(["check" => false]);
}
