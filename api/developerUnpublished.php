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
            G.price
        FROM
            game G, develop D
        WHERE 
            G.genre = '$cat'AND D.gameID = G.gameID AND G.active = 'no' AND D.developerID = '$id'
        ";
        $result = $db->query($sql);

        $games = mysqli_fetch_all($result, MYSQLI_ASSOC);
        $gamesData = json_encode($games);

        echo '{"games":' . $gamesData . '}';
    } else if ($type == "addGame") {
        $id = $_POST['id'];
        $gameName = $_POST['gameName'];
        $genreGame = $_POST['genreGame'];
        $price = $_POST['price'];
        $now = date_create()->format('Y-m-d H:i:s');
        $imageO = $_POST['imageO'];

        $sql = "SELECT game_name FROM game WHERE game_name = '$gameName'";
        $result = $db->query($sql);

        if (mysqli_num_rows($result) == 0) {
            $sql2 = "INSERT INTO game(game_name, genre, last_updated, price, imageO, active, discount_amount) VALUES (
                '$gameName',
                '$genreGame',
                '$now',
                '$price',
                'fdsfd',
                'no',
                '0'
                );";
            $result2 = $db->query($sql2);

            $sql3 = "SELECT gameID FROM game WHERE game_name = '$gameName'";
            $result3 = $db->query($sql3);

            $gameID = 0;

            if ($result3->num_rows > 0) {
                while ($row = $result3->fetch_assoc()) {
                    $gameID = $row["gameID"];
                }
            }

            $sql4 = "INSERT INTO develop(developerID, gameID) VALUES (
                '$id',
                '$gameID'
                );";
            $result4 = $db->query($sql4);

            if ($result4) {
                echo json_encode(array(
                    "checkAI" => 2
                ));
            } else {
                echo json_encode(array(
                    "checkAI" => 0
                ));
            }
        } else {
            echo json_encode(array(
                "checkAI" => 1
            ));
        }
    } else if ($type == "sendRequest") {
        $id = $_POST['id'];
        $gameID = $_POST['gameID'];
        $pubName = $_POST['pubU'];

        $sql = "SELECT publisherID FROM publisher_company WHERE username = '$pubName'";
        $result = $db->query($sql);

        if (mysqli_num_rows($result)!=0)
        {
            $pubID = 0;

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $pubID = $row["publisherID"];
                }
            }

            $sql2 = "SELECT * FROM request WHERE gameID = '$gameID'";
            $result2 = $db->query($sql2);

            if ($result2->num_rows > 0) {
                echo json_encode(array(
                    "checkRRI" => 2
                ));
            }
            else
            {
                $sql3 = "INSERT INTO request(publisherID, developerID, gameID) VALUES (
                    '$pubID',
                    '$id',
                    '$gameID')";
                $result3 = $db->query($sql3);
                if($result3)
                {
                    echo json_encode(array(
                        "checkRRI" => 1
                    ));
                }
                else
                {
                    echo json_encode(array(
                        "checkRRI" => 0
                    ));
                }
            }
        }
        else
        {
            echo json_encode(array(
                "checkRRI" => 3
            ));
        }
    }
} else {
    echo json_encode(["check" => false]);
}
