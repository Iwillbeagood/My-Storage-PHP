
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
    $required_params = array('username', 'userid', 'userpassword', 'userphone');
    foreach ($required_params as $param) {
        if (!isset($_POST[$param]) || empty(trim($_POST[$param]))) {
            http_response_code(400);
            echo json_encode(array(
                'status' => 'false',
                'message' => "$param is required"), JSON_UNESCAPED_UNICODE);
            return;
        }
    }

    // userid 중복 체크
    $userid = $_POST["userid"];
    $id_query = "SELECT userid FROM users WHERE userid='$userid'";
    $id_result = mysqli_query($connect, $id_query);
    $id_exists = mysqli_num_rows($id_result) > 0;

    // userphone 중복 체크
    $userphone = $_POST["userphone"];
    $phone_query = "SELECT userphone FROM users WHERE userphone='$userphone'";
    $phone_result = mysqli_query($connect, $phone_query);
    $phone_exists = mysqli_num_rows($phone_result) > 0;

    // userid와 userphone 중복 체크 후 삽입
    if ($id_exists) {
        http_response_code(200);
        echo json_encode(array(
            'status' => 'false',
            'message' => '중복된 아이디가 존재합니다'), JSON_UNESCAPED_UNICODE);
    } else if ($phone_exists) {
        http_response_code(200);
        echo json_encode(array(
            'status' => 'false',
            'message' => '중복된 핸드폰 번호가 존재합니다'), JSON_UNESCAPED_UNICODE);
    } else {
        $username = $_POST["username"];
        $userpassword = $_POST["userpassword"];
        $query = "INSERT INTO users (username, userid, userpassword, userphone, created_at, updated_at) VALUES ('$username', '$userid', '$userpassword', '$userphone', NOW(), NOW())";
        $result = mysqli_query($connect, $query);
        if ($result) {
            http_response_code(200);
            echo json_encode(array(
                'status' => 'true',
                'message' => '회원가입이 완료되었습니다'), JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(500);
            echo json_encode(array(
                'status' => 'false',
                'message' => '회원가입에 실패하였습니다'), JSON_UNESCAPED_UNICODE);
        }
    }
} else {
    http_response_code(405);
    echo json_encode(array(
        'status' => 'false',
        'message' => 'Method Not Allowed'), JSON_UNESCAPED_UNICODE);
}

mysqli_close($connect);
?>
