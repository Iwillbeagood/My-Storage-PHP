<?php
$host = "localhost";
$user = "jun";
$password = "Qwg76046418!";
$database = "user";

// mysql 연결
$connect = mysqli_connect($host, $user, $password, $database) or die("mysql 연결 실패");

// POST 데이터 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 필수 항목 검증
    $required_params = array('userid', 'usedItemid');
    foreach ($required_params as $param) {
        if (!isset($_POST[$param]) || empty(trim($_POST[$param]))) {
            http_response_code(200);
            echo json_encode(array(
                'status' => 'false',
                'message' => "$param is required"), JSON_UNESCAPED_UNICODE);
            return;
        }
    }

    // userid가 users 테이블에 존재하는지 확인
    $userid = $_POST["userid"];
    $user_query = "SELECT userid FROM users WHERE userid='$userid'";
    $user_result = mysqli_query($connect, $user_query);
    $user_exists = mysqli_num_rows($user_result) > 0;

    if (!$user_exists) {
        http_response_code(200);
        echo json_encode(array(
            'status' => 'false',
            'message' => '존재하지 않는 사용자입니다'), JSON_UNESCAPED_UNICODE);
        return;
    }

    // usedItems 테이블에서 해당하는 레코드를 가져옴
    $usedItemid = $_POST["usedItemid"];
    $item_query = "SELECT * FROM usedItems WHERE userid='$userid' AND usedItemid='$usedItemid'";
    $item_result = mysqli_query($connect, $item_query);
    $item_exists = mysqli_num_rows($item_result) > 0;

    if (!$item_exists) {
        http_response_code(200);
        echo json_encode(array(
            'status' => 'false',
            'message' => '존재하지 않는 물건입니다'), JSON_UNESCAPED_UNICODE);
        return;
    }

    // usedItems 테이블에서 해당하는 레코드를 items 테이블로 이동시킴
    $row = mysqli_fetch_assoc($item_result);
    $itemname = $row['usedItemname'];
    $itemimage = $row['usedItemimage'];
    $itemplace = $row['usedItemplace'];
    $itemstore = $row['usedItemstore'];
    $items_query = "INSERT INTO items (userid, itemname, itemimage, itemplace, itemstore, itemcount) VALUES ('$userid', '$itemname', '$itemimage', '$itemplace', '$itemstore', 1)";
    $items_result = mysqli_query($connect, $items_query);

    if ($items_result) {
        $query = "DELETE FROM usedItems WHERE userid='$userid' AND usedItemid='$usedItemid'";
        $result = mysqli_query($connect, $query);


        if ($result) {
            http_response_code(200);
            echo json_encode(array(
                'status' => 'true',
                'message' => '물건 이동에 성공하였습니다'), JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(200);
            echo json_encode(array(
                'status' => 'false',
                'message' => '물건 삭제에 실패하였습니다'), JSON_UNESCAPED_UNICODE);
        }
    } else {
        http_response_code(200);
        echo json_encode(array(
            'status' => 'false',
            'message' => 'usedItems 테이블에 물건 삭제에 실패하였습니다'), JSON_UNESCAPED_UNICODE);
    }
} else {
    http_response_code(405);
    echo json_encode(array(
    'status' => 'false',
    'message' => 'Method Not Allowed'), JSON_UNESCAPED_UNICODE);
}

mysqli_close($connect);
?>