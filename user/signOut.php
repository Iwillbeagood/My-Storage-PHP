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

    // 비밀번호 검증
    $userpassword = $_POST["userpassword"];
    $query = "SELECT * FROM users WHERE userid='$userid' AND userpassword='$userpassword'";
    $result = mysqli_query($connect, $query);
    if (mysqli_num_rows($result) === 0) {
        http_response_code(200);
        echo json_encode(array(
            'status' => 'false',
            'message' => '비밀번호가 일치하지 않습니다'), JSON_UNESCAPED_UNICODE);
        return;
    }

    // users 테이블에서 해당 userid 정보 제거
    $query = "DELETE FROM users WHERE userid='$userid'";
    $result = mysqli_query($connect, $query);

    // userhome 테이블에서 해당 userid 정보 제거
    $query = "DELETE FROM userhome WHERE userid='$userid'";
    $result = mysqli_query($connect, $query);

    // items 테이블에서 해당 userid 정보 제거
    $query = "DELETE FROM items WHERE userid='$userid'";
    $result = mysqli_query($connect, $query);

    http_response_code(200);
    echo json_encode(array(
        'status' => 'true',
        'message' => '회원탈퇴에 성공하였습니다'), JSON_UNESCAPED_UNICODE);

} else {
    http_response_code(405);
    echo json_encode(array(
        'status' => 'false',
        'message' => 'Method Not Allowed'), JSON_UNESCAPED_UNICODE);
}

mysqli_close($connect);
?>
