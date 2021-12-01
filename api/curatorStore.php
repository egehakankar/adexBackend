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
        ";
        $result = $db->query($sql);

        $games = mysqli_fetch_all($result, MYSQLI_ASSOC);
        $gamesData = json_encode($games);

        echo '{"games":' . $gamesData . '}';
    }
    else if($type == "seeReviews")
    {
        $gameID = $_POST['gameID'];
        $sql = "SELECT C.username, R.textO, R.dateO, R.ratingO
                FROM review AS R, curator AS C
                WHERE C.curatorID = R.curatorID AND R.gameID = '$gameID';";
        $result = $db->query($sql);

        $reviews = mysqli_fetch_all($result, MYSQLI_ASSOC);
        $reviewsData = json_encode($reviews);

        echo '{"reviews":' . $reviewsData . '}';
    }
    else if($type == "createReview")
    {
        $id = $_POST['id'];
        $gameID = $_POST['gameID'];
        $review = $_POST['rewText'];
        $rating = $_POST['rating'];
        $now = date_create()->format('Y-m-d H:i:s');

        $sql = "INSERT INTO review(curatorID, gameID, dateO, textO, ratingO) VALUES (
            '$id',
            '$gameID',
            '$now',
            '$review',
            '$rating'
            );";
        $result = $db->query($sql);

        if ($result) {
            echo json_encode(array(
                "checkRII" => true
            ));
        } else {
            echo json_encode(array(
                "checkRII" => false
            ));
        }
    }
} else {
    echo json_encode(["check" => false]);
}
