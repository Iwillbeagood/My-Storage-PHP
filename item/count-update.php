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
    $required_params = array('userid', 'itemid', 'extracount');
    foreach ($required_params as $param) {
        if (!isset($_POST[$param]) || empty(trim($_POST[$param]))) {
            http_response_code(200);
            echo json_encode(array(
                'status' => 'false',
                'message' => "$param is required"), JSON_UNESCAPED_UNICODE);
            return;
        }
    }

    $itemid = $_POST["itemid"];
    $userid = $_POST["userid"];
    $extracount = $_POST["extracount"];
    $query = "SELECT itemcount FROM items WHERE userid='$userid' AND itemid='$itemid'";
    $result = mysqli_query($connect, $query);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $itemcount = $row['itemcount'] + $extracount;
        $query = "UPDATE items SET itemcount='$itemcount' WHERE userid='$userid' AND itemid='$itemid'";
        $result = mysqli_query($connect, $query);
        if ($result) {
            http_response_code(200);
            echo json_encode(array(
                'status' => 'true',
                'message' => '물건 개수가 변경되었습니다'), JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(200);
            echo json_encode(array(
                'status' => 'false',
                'message' => '물건 개수 변경에 실패하였습니다'), JSON_UNESCAPED_UNICODE);
        }
    } else {
        http_response_code(200);
        echo json_encode(array(
            'status' => 'false',
            'message' => '물건 개수 변경에 실패하였습니다'), JSON_UNESCAPED_UNICODE);
    }
} else {
    http_response_code(405);
    echo json_encode(array(
        'status' => 'false',
        'message' => 'Method Not Allowed'), JSON_UNESCAPED_UNICODE);
}

mysqli_close($connect);
?>