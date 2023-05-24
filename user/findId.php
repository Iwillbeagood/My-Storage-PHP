
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
    $required_params = array('userphone');
    foreach ($required_params as $param) {
        if (!isset($_POST[$param]) || empty(trim($_POST[$param]))) {
            http_response_code(400);
            echo json_encode(array(
                'status' => 'false',
                'message' => "$param is required"), JSON_UNESCAPED_UNICODE);
            return;
        }
    }

    $userphone = $_POST["userphone"];

    // DB에서 아이디 검증합니다.
    $query = "SELECT * FROM users WHERE userphone='$userphone'";
    $result = mysqli_query($connect, $query);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $userid = $row["userid"];

        http_response_code(200);
        echo json_encode(array(
            'status' => 'true',
            'message' => "해당하는 전화번호의\n 아이디는 ['$userid'] 입니다."), JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(404);
        echo json_encode(array(
            'status' => 'false',
            'message' => '일치하는 사용자 정보를 찾을 수 없습니다.'), JSON_UNESCAPED_UNICODE);
    }
} else {
    http_response_code(405);
    echo json_encode(array(
        'status' => 'false',
        'message' => 'Method Not Allowed'), JSON_UNESCAPED_UNICODE);
}

mysqli_close($connect);
?>