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
    
    // userhome 테이블에서 해당 userid의 정보 삭제
    $query = "DELETE FROM userhome WHERE userid = '$userid'";
    $result = mysqli_query($connect, $query);
    if (!$result) {
        // 정보 삭제에 실패한 경우
        http_response_code(200);
        echo json_encode(array(
            'status' => 'false',
            'message' => '사용자 집 정보 삭제에 실패하였습니다.'), JSON_UNESCAPED_UNICODE);
        return;
    }

    http_response_code(200);
    echo json_encode(array(
        'status' => 'true',
        'message' => '사용자 정보 삭제가 완료되었습니다.'
    ), JSON_UNESCAPED_UNICODE);
    
} else {
    http_response_code(405);
    echo json_encode(array(
        'status' => 'false',
        'message' => 'Method Not Allowed'), JSON_UNESCAPED_UNICODE);
}

mysqli_close($connect);
?>
