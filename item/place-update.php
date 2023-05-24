<?php
$host = "localhost";
$user = "jun";
$password = "Qwg76046418!";
$database = "user";

// mysql 연결
$connect = mysqli_connect($host, $user, $password, $database) or die("mysql 연결 실패");

header('Content-Type: application/x-www-form-urlencoded; charset=utf-8');

// POST 데이터 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 필수 항목 검증
    $required_params = array('userid', 'itemid', 'itemplace');
    foreach ($required_params as $param) {
        if (!isset($_POST[$param]) || empty(trim($_POST[$param]))) {
            http_response_code(200);
            echo json_encode(array(
                'status' => 'false',
                'message' => "$param is required"), JSON_UNESCAPED_UNICODE);
            return;
        }
    }

    // itemplace 업데이트
    $userid = $_POST['userid'];
    $itemid = $_POST['itemid'];
    $itemplace = $_POST['itemplace'];

    // SQL 쿼리 실행
    $query = "UPDATE items SET itemplace = '$itemplace' WHERE userid = '$userid' AND itemid = '$itemid'";
    $result = mysqli_query($connect, $query);

    if ($result) {
        http_response_code(200);
        echo json_encode(array(
            'status' => 'true',
            'message' => '물건 이동이 완료 되었습니다'), JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(500);
        echo json_encode(array(
            'status' => 'false',
            'message' => '물건 이동에 실패 했습니다'), JSON_UNESCAPED_UNICODE);
    }
} else {
    http_response_code(405);
    echo json_encode(array(
        'status' => 'false',
        'message' => 'Method Not Allowed'), JSON_UNESCAPED_UNICODE);
}

mysqli_close($connect);
?>
