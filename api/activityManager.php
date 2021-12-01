<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET,POST,PUT,DELETE,OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

if ($_SERVER["REQUEST_METHOD"] == "OPTIONS") exit();

require 'config.php';

$_POST = json_decode(file_get_contents('php://input'), true);

if ($_POST) {

    http_response_code(200);
    $id = $_POST['id'];
    $sqlDrop = "DROP VIEW `adexdatabase`.`allActivities`;";
    $db->query($sqlDrop);
    $now = date_create()->format('Y-m-d H:i:s');
    $sqlView = "
        CREATE VIEW allActivities AS 
        SELECT 
        (SELECT username
        FROM adexdatabase.user
        WHERE userID = F.userID1) as username1, \"became friend with\" as action, 
        (SELECT username
        FROM adexdatabase.user
        WHERE userID = F.userID2) as username2, \"on\", F.dateO
        FROM adexdatabase.friend as F
        WHERE (userID1 = '$id' OR userID2 = '$id') AND F.dateO BETWEEN CURDATE() - INTERVAL 1 WEEK AND '$now'
        
        UNION
        
        SELECT 
        (SELECT username
        FROM adexdatabase.user
        WHERE userID = C.userID) as username, \"commented on\", 
        (SELECT game_name
        FROM adexdatabase.game
        WHERE gameID = C.gameID), \"on\", C.dateO
        FROM adexdatabase.friend as F, adexdatabase.comment as C
        WHERE ((F.userID2 IN (SELECT userID2 FROM friend WHERE userID1='$id') AND F.userID2 = C.userID)
        OR (F.userID1 IN (SELECT userID1 FROM friend WHERE userID2='$id') AND F.userID1 = C.userID))
        AND C.dateO BETWEEN CURDATE() - INTERVAL 1 WEEK AND '$now'
        
        UNION
        
        SELECT 
        (SELECT username
        FROM adexdatabase.user
        WHERE userID = L.userID) as username, \"bought this game: \", 
        (SELECT game_name
        FROM adexdatabase.game
        WHERE gameID = L.gameID) as game, \"on\", L.dateO
        FROM adexdatabase.friend as F, adexdatabase.library as L
        WHERE ((F.userID2 IN (SELECT userID2 FROM friend WHERE userID1='$id') AND F.userID2 = L.userID)
        OR (F.userID1 IN (SELECT userID1 FROM friend WHERE userID2='$id') AND F.userID1 = L.userID))
        AND L.dateO BETWEEN CURDATE() - INTERVAL 1 WEEK AND '$now'
        
        UNION
        
        SELECT 
        (SELECT username
        FROM adexdatabase.curator
        WHERE curatorID = R.curatorID) as curator, \"reviewed\", 
        (SELECT game_name
        FROM adexdatabase.game
        WHERE gameID = R.gameID) as game, \"on\", R.dateO  
        FROM adexdatabase.follow as F, adexdatabase.review as R
        WHERE F.userID = '$id' AND F.curatorID = R.curatorID AND R.dateO  BETWEEN CURDATE() - INTERVAL 1 WEEK AND '$now'
        ORDER BY dateO;
        
    ";
    $db->query($sqlView);
    $sqlAct = "SELECT * FROM allActivities;";
    $result = $db->query($sqlAct);
    $activities = mysqli_fetch_all($result, MYSQLI_ASSOC);
    $activitiesData = json_encode($activities);

    echo '{"activities":' . $activitiesData . '}';
    
} else {
    echo json_encode(["check" => false]);
}
