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
    $required_params = array('userid', 'room_names', 'bathroom_names');
    $missing_params = array();
    foreach ($required_params as $param) {
        if (!isset($_POST[$param]) || empty(trim($_POST[$param]))) {
            $missing_params[] = $param;
        }
    }
    if (count($missing_params) > 0) {
        http_response_code(400);
        echo json_encode(array(
            'status' => 'false',
            'message' => '필수 항목 누락: ' . implode(', ', $missing_params)), JSON_UNESCAPED_UNICODE);
        return;
    }

    $userid = $_POST["userid"];
    
    // users 테이블에서 해당 userid가 존재하는지 확인
    $query = "SELECT * FROM users WHERE userid = '$userid'";
    $result = mysqli_query($connect, $query);
    if(mysqli_num_rows($result) == 0) {
        // 해당 userid가 존재하지 않는 경우
        http_response_code(200);
        echo json_encode(array(
            'status' => 'false',
            'message' => '해당 사용자가 존재하지 않습니다.'), JSON_UNESCAPED_UNICODE);
        return;
    }
    
    $room_names = mysqli_real_escape_string($connect, json_encode($_POST["room_names"]));
    $bath_names = mysqli_real_escape_string($connect, json_encode($_POST["bathroom_names"]));
    $etc_name = mysqli_real_escape_string($connect, json_encode($_POST["etc_name"]));
    
    $query = "UPDATE userhome SET room_names='$room_names', bathroom_names='$bath_names'";
    if (!empty($etc_name)) {
        $query .= ", etc_name='$etc_name'";
    }
    $query .= " WHERE userid='$userid'";
    

    $result = mysqli_query($connect, $query);

    if ($result) {
        http_response_code(200);
        echo json_encode(array(
            'status' => 'true',
            'message' => '사용자 구조 정보가 업데이트 되었습니다'), JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(500);
        echo json_encode(array(
            'status' => 'false',
            'message' => '사용자 구조 정보 업데이트에 실패하였습니다'), JSON_UNESCAPED_UNICODE);
    }
} else {
    http_response_code(405);
    echo json_encode(array(
        'status' => 'false',
        'message' => 'Method Not Allowed'), JSON_UNESCAPED_UNICODE);
}

mysqli_close($connect);
?>
