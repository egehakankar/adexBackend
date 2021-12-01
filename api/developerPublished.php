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
            game G, develop D
        WHERE 
            G.genre = '$cat'AND D.gameID = G.gameID AND G.active = 'yes' AND D.developerID = '$id'
        ";
        $result = $db->query($sql);

        $games = mysqli_fetch_all($result, MYSQLI_ASSOC);
        $gamesData = json_encode($games);

        echo '{"games":' . $gamesData . '}';
    } else if ($type == "updateGame") {
        $gameID = $_POST['gameID'];
        $now = date_create()->format('Y-m-d H:i:s');

        $sql = "UPDATE game
        SET last_updated = '$now'
        WHERE gameID = '$gameID'";
        $result = $db->query($sql);

        if ($result) {
            echo json_encode(array(
                "checkUI" => true
            ));
        } else {
            echo json_encode(array(
                "checkUI" => false
            ));
        }
    }
} else {
    echo json_encode(["check" => false]);
}
