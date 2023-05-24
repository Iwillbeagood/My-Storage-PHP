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
    $required_params = array('userid', 'itemid', 'table');
    foreach ($required_params as $param) {
        if (!isset($_POST[$param]) || empty(trim($_POST[$param]))) {
            http_response_code(200);
            echo json_encode(array(
                'status' => 'false',
                'message' => "$param is required"), JSON_UNESCAPED_UNICODE);
            return;
        }
    }

    $itemid = $_POST["itemid"];
    $userid = $_POST["userid"];
    $table = $_POST["table"];

    // 테이블에서 해당 userid 정보 제거
    $query = "SELECT itemimage FROM items WHERE itemid='$itemid'";
    $result = mysqli_query($connect, $query);
    while($row = mysqli_fetch_array($result)) {
        $itemimage = $row["itemimage"];
        
        if ($itemimage !== null && $itemimage !== "") { // itemimage가 비어있지 않은 경우에만 파일 삭제 수행
            $file_path = '/var/www/html' . parse_url($itemimage, PHP_URL_PATH); // URL 디코딩
            if (file_exists($file_path)) { // 파일이 존재하면
                $file_handle = fopen($file_path, 'r'); // 파일을 읽기 모드로 열기
                if ($file_handle) { // 파일 핸들이 유효하면
                    fclose($file_handle); // 파일 핸들 닫기
                    if (unlink($file_path)) { 
                        // 파일 삭제
                    } else {
                        http_response_code(500);
                        echo json_encode(array(
                            'status' => 'false',
                            'message' => '파일 삭제에 실패했습니다'
                        ), JSON_UNESCAPED_UNICODE);
                        return;
                    }
                } else {
                    http_response_code(500);
                    echo json_encode(array(
                        'status' => 'false',
                        'message' => '파일 열기에 실패했습니다'
                    ), JSON_UNESCAPED_UNICODE);
                    return;
                }
            }
        }
    }

    if($table == "items") {
        $query = "DELETE FROM items WHERE userid='$userid' AND itemid='$itemid'";
    } else {
        $query = "DELETE FROM usedItems WHERE userid='$userid' AND usedItemid='$itemid'";
    }
    $result = mysqli_query($connect, $query);

    if ($result) {
        http_response_code(200);
        echo json_encode(array(
            'status' => 'true',
            'message' => '물건이 성공적으로 삭제되었습니다'), JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(200);
        echo json_encode(array(
            'status' => 'false',
            'message' => '물건 삭제에 실패하였습니다'), JSON_UNESCAPED_UNICODE);
    }
} else {
    http_response_code(405);
    echo json_encode(array(
        'status' => 'false',
        'message' => 'Method Not Allowed'), JSON_UNESCAPED_UNICODE);
}

mysqli_close($connect);
?>
