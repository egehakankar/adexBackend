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
            game G, publish P
        WHERE 
            G.genre = '$cat'AND P.publisherID = '$id' AND P.gameID = G.gameID AND P.publisherID = '$id'
        ";
        $result = $db->query($sql);

        $games = mysqli_fetch_all($result, MYSQLI_ASSOC);
        $gamesData = json_encode($games);

        echo '{"games":' . $gamesData . '}';
    } else if ($type == "changePrice") {
        $id = $_POST['id'];
        $gameID = $_POST['gameID'];
        $price = $_POST['pubU'];

        $sql = "UPDATE game
            SET discount_amount = ABS(price - '$price'), price = '$price'
            WHERE gameID = '$gameID'";
        $result = $db->query($sql);

        if ($result) {
            echo json_encode(array(
                "checkRRI" => 1
            ));
        } else {
            echo json_encode(array(
                "checkRRI" => 0
            ));
        }
    }
} else {
    echo json_encode(["check" => false]);
}
