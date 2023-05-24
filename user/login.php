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
    $required_params = array('userid', 'userpassword');
    foreach ($required_params as $param) {
        if (!isset($_POST[$param]) || empty(trim($_POST[$param]))) {
            http_response_code(200);
            echo json_encode(array(
                'status' => 'false',
                'message' => "$param is required"), JSON_UNESCAPED_UNICODE);
            return;
        }
    }

    $userid = $_POST["userid"];

    // 중복 아이디 검증
    $id_query = "SELECT userid FROM users WHERE userid='$userid'";
    $id_result = mysqli_query($connect, $id_query);
    if (mysqli_num_rows($id_result) === 0) {
        http_response_code(200);
        echo json_encode(array(
            'status' => 'false',
            'message' => '아이디가 존재하지 않습니다'), JSON_UNESCAPED_UNICODE);
        return;
    }

    $userpassword = $_POST["userpassword"];

    // 비밀번호 검증
    $query = "SELECT * FROM users WHERE userid='$userid' AND userpassword='$userpassword'";
    $result = mysqli_query($connect, $query);
    if (mysqli_num_rows($result) === 0) {
        http_response_code(200);
        echo json_encode(array(
            'status' => 'false',
            'message' => '비밀번호가 일치하지 않습니다'), JSON_UNESCAPED_UNICODE);
        return;
    }

    http_response_code(200);
    echo json_encode(array(
        'status' => 'true',
        'message' => '로그인에 성공하였습니다'), JSON_UNESCAPED_UNICODE);

} else {
    http_response_code(405);
    echo json_encode(array(
        'status' => 'false',
        'message' => 'Method Not Allowed'), JSON_UNESCAPED_UNICODE);
}

mysqli_close($connect);
?>
