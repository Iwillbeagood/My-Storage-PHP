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
    $required_params = array('userid', 'itemid', 'itemname', 'itemcount');
    foreach ($required_params as $param) {
        if (!isset($_POST[$param]) || empty(trim($_POST[$param]))) {
            http_response_code(200);
            echo json_encode(array(
                'status' => 'false',
                'message' => "$param is required"), JSON_UNESCAPED_UNICODE);
            return;
        }
    }

    // 이미지 업로드
    $upload_dir = '/var/www/html/images/';
    if (!empty($_FILES['itemimage']['name'])) {
        $extension = pathinfo($_FILES['itemimage']['name'], PATHINFO_EXTENSION);
        $random_filename = uniqid() . "." . $extension;
        $upload_file = $upload_dir . $random_filename;

        if (move_uploaded_file($_FILES['itemimage']['tmp_name'], $upload_file)) {
            $itemimage_url = 'http://43.201.89.62/images/' . $random_filename;
        } else {
            http_response_code(200);
            echo json_encode(array(
                'status' => 'false',
                'message' => '이미지 업로드에 실패하였습니다'), JSON_UNESCAPED_UNICODE);
            return;
        }
    } else {
        $itemimage_url = '';
    }
    

    // userid가 users 테이블에 존재하는지 확인
    $userid = $_POST["userid"];
    $user_query = "SELECT userid FROM users WHERE userid='$userid'";
    $user_result = mysqli_query($connect, $user_query);
    $user_exists = mysqli_num_rows($user_result) > 0;

    if (!$user_exists) {
        http_response_code(200);
        echo json_encode(array(
            'status' => 'false',
            'message' => '존재하지 않는 사용자입니다'), JSON_UNESCAPED_UNICODE);
        return;
    }

    // itemid로 아이템 찾기
    $itemid = $_POST["itemid"];
    $item_query = "SELECT * FROM items WHERE userid='$userid' AND itemid='$itemid'";
    $item_result = mysqli_query($connect, $item_query);
    $item_exists = mysqli_num_rows($item_result) > 0;

    if ($item_exists) {
        $itemname = $_POST["itemname"];
        $itemplace = $_POST["itemplace"];
        $itemstore = $_POST["itemstore"];
        $itemcount = $_POST["itemcount"];

        $row = mysqli_fetch_assoc($item_result);
        $itemimage = '';
        if ($itemimage_url != '') {
            $itemimage = ", itemimage='$itemimage_url'";

            $originimage = $_POST["originimage"];
            // 기존의 이미지 삭제
            if (!empty($originimage)) { // originimage가 비어 있지 않은 경우에만 실행
                $file_path = '/var/www/html' . parse_url($originimage, PHP_URL_PATH); // URL 디코딩
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
        $query = "UPDATE items SET itemplace='$itemplace', itemstore='$itemstore', itemcount='$itemcount'$itemimage WHERE itemid='$itemid'";
        $result = mysqli_query($connect, $query);

        if ($result) {
            http_response_code(200);
            echo json_encode(array(
                'status' => 'true',
                'message' => '물건 정보 수정이 완료되었습니다'), JSON_UNESCAPED_UNICODE);
        } else {
        http_response_code(200);
        echo json_encode(array(
        'status' => 'false',
        'message' => '물건 정보 수정에 실패하였습니다'), JSON_UNESCAPED_UNICODE);
        }
    } else {
        http_response_code(200);
        echo json_encode(array(
            'status' => 'false',
            'message' => '물건이 존재하지 않습니다'), JSON_UNESCAPED_UNICODE);
    }
} else {
    http_response_code(405);
    echo json_encode(array(
    'status' => 'false',
    'message' => 'Method Not Allowed'), JSON_UNESCAPED_UNICODE);
}
        
mysqli_close($connect);
?>