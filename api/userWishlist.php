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
        $sql = "SELECT
            gameID,
            game_name,
            imageO,
            price
        FROM
            game
            INTERSECT(
            SELECT
                G.gameID,
                G.game_name,
                G.imageO,
                G.price
            FROM
                game G
            INNER JOIN wishlist W ON
                G.gameID = W.gameID
            WHERE
                W.userID = '$id' AND G.active = 'yes')
        ";
        $result = $db->query($sql);

        $games = mysqli_fetch_all($result, MYSQLI_ASSOC);
        $gamesData = json_encode($games);

        echo '{"games":' . $gamesData . '}';
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
    } else if ($type == "removeWishlist") {
        $id = $_POST['id'];
        $gameid = $_POST['gameid'];

        $sql5 = "DELETE FROM wishlist
            WHERE userID = '$id' AND gameID = '$gameid'";
        $result5 = $db->query($sql5);

        if ($result5) {
            echo json_encode(array(
                "checkW" => true
            ));
        } else {
            echo json_encode(array(
                "checkW" => false
            ));
        }
    }
} else {
    echo json_encode(["check" => false]);
}
