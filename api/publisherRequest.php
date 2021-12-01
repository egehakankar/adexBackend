<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET,POST,PUT,DELETE,OPTIONS,FILE');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Origin, Accept');
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Max-Age: 3600");
if ($_SERVER["REQUEST_METHOD"] == "OPTIONS") exit();

require 'config.php';

$_POST = json_decode(file_get_contents('php://input'), true);

if ($_POST) {

    //http_response_code(200);
    $type = $_POST['type'];

    if ($type == "getGames") {
        $id = $_POST['id'];
        $cat = $_POST['category'];
        $sql = "SELECT
            G.gameID,
            G.game_name,
            G.imageO,
            G.price,
            G.last_updated
        FROM
            game G, request R
        WHERE 
            G.genre = '$cat'AND R.gameID = G.gameID AND G.active = 'no' AND R.publisherID = '$id'
        ";
        $result = $db->query($sql);

        $games = mysqli_fetch_all($result, MYSQLI_ASSOC);
        $gamesData = json_encode($games);

        echo '{"games":' . $gamesData . '}';
    } else if ($type == "publishGame") {
        $id = $_POST['id'];
        $gameID = $_POST['gameID'];
        $action = $_POST['action'];

        if($action == 1)
        {
            $sql = "UPDATE game
            SET active = 'yes'
            WHERE gameID = '$gameID'";
            $result = $db->query($sql);

            $sql2 = "DELETE FROM request
            WHERE gameID = $gameID";
            $result2 = $db->query($sql2);

            $sql3 = "INSERT INTO publish(publisherID, gameID) VALUES (
                '$id',
                '$gameID'
                );";
            $result3 = $db->query($sql3);

            if($result3)
            {
                echo json_encode(array(
                    "checkPI" => 1
                ));
            }
            else
            {
                echo json_encode(array(
                    "checkPI" => 0
                ));
            }
        }
        else if ($action == 2)
        {
            $sql = "DELETE FROM request
            WHERE gameID = $gameID";
            $result = $db->query($sql);

            if($result)
            {
                echo json_encode(array(
                    "checkPI" => 2
                ));
            }
            else
            {
                echo json_encode(array(
                    "checkPI" => 0
                ));
            }
        }
    }
} else {
    echo json_encode(["check" => false]);
}
