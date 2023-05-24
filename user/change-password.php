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
    $required_params = array('userid', 'userpassword', 'userphone');
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
    $userpassword = $_POST["userpassword"];
    $userphone = $_POST["userphone"];

    // DB에서 사용자 검증
    $query = "SELECT * FROM users WHERE userid='$userid' AND userphone='$userphone'";
    $result = mysqli_query($connect, $query);
    
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $db_password = $row["userpassword"];

        // 입력한 비밀번호와 DB 비밀번호 검증
        if ($userpassword === $db_password) {
            http_response_code(200);
            echo json_encode(array(
                'status' => 'false',
                'message' => '비밀번호가 기존의 비밀번호와 일치합니다.'), JSON_UNESCAPED_UNICODE);

        } else {
            // 입력한 비밀번호와 DB 비밀번호 불일치 시 업데이트 수행
            $update_query = "UPDATE users SET userpassword='$userpassword' WHERE userid='$userid' AND userphone='$userphone'";
            $update_result = mysqli_query($connect, $update_query);

            if ($update_result) {
                http_response_code(200);
                echo json_encode(array(
                    'status' => 'true',
                    'message' => '비밀번호가 업데이트 되었습니다.'), JSON_UNESCAPED_UNICODE);
            } else {
                http_response_code(200);
                echo json_encode(array(
                    'status' => 'false',
                    'message' => '비밀번호 업데이트에 실패했습니다.'), JSON_UNESCAPED_UNICODE);
            }
        }
    } else {
        http_response_code(200);
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
