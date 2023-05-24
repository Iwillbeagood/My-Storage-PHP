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
    $required_params = array('userid');
    foreach ($required_params as $param) {
        if (!isset($_POST[$param]) || empty(trim($_POST[$param]))) {
            http_response_code(200);
            echo json_encode(array(
                'status' => 'false',
                'message' => "$param is required"), JSON_UNESCAPED_UNICODE);
            return;
        }
    }


    $userid = mysqli_real_escape_string($connect, $_POST['userid']);
    
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
    
    // userhome 테이블에서 해당 userid의 정보 가져오기
    $query = "SELECT * FROM userhome WHERE userid = '$userid'";
    $result = mysqli_query($connect, $query);
    if(mysqli_num_rows($result) == 0) {
        // 해당 유저의 집 정보가 없는 경우
        http_response_code(200);
        echo json_encode(array(
            'status' => 'false',
            'message' => '해당 사용자의 집 정보가 존재하지 않습니다.'), JSON_UNESCAPED_UNICODE);
        return;
    }

    // userhome 정보를 UserHomeInfo 객체로 변환
    $row = mysqli_fetch_array($result);
    
    http_response_code(200);
    echo json_encode(array(
        'status' => 'true',
        'message' => '사용자 집 정보 조회가 완료되었습니다.',
        'livingroom' => $row['livingroom'],
        'kitchen' => $row['kitchen'],
        'storage' => $row['storage'],
        'room_names' => json_decode($row['room_names'], true),
        'bathroom_names' => json_decode($row['bathroom_names'], true),
        'etc_name' => json_decode($row['etc_name'], true)
    ), JSON_UNESCAPED_UNICODE);
    
} else {
    http_response_code(405);
    echo json_encode(array(
        'status' => 'false',
        'message' => 'Method Not Allowed'), JSON_UNESCAPED_UNICODE);
}

mysqli_close($connect);
?>
