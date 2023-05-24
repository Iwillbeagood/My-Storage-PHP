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

    // userid를 받아와서 items에서 검색
    $userid = $_POST['userid'];

    $sql = "SELECT * FROM items WHERE userid='$userid'";
    $result = mysqli_query($connect, $sql);

    if ($result) {
        $response = array();
        $response['status'] = 'true';
        $response['message'] = 'success';
        $response['data'] = array();
        
        while ($row = mysqli_fetch_array($result)) {
            $item = array(
                'itemid' => $row['itemid'],
                'itemname' => $row['itemname'],
                'itemimage' => $row['itemimage'],
                'itemplace' => $row['itemplace'],
                'itemstore' => $row['itemstore'],
                'itemcount' => $row['itemcount']
            );
            array_push($response['data'], $item);
        }
        
        http_response_code(200);
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(200);
        echo json_encode(array(
            'status' => 'false',
            'message' => 'Database Error'), JSON_UNESCAPED_UNICODE);
    }

} else {
    http_response_code(405);
    echo json_encode(array(
        'status' => 'false',
        'message' => 'Method Not Allowed'), JSON_UNESCAPED_UNICODE);
}

mysqli_close($connect);
?>
