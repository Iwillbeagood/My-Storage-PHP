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
    $required_params = array('userid', 'itemname', 'itemcount');
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

    // itemname 중복 체크
    $itemname = $_POST["itemname"];
    $name_query = "SELECT * FROM items WHERE itemname='$itemname'";
    $name_result = mysqli_query($connect, $name_query);
    $name_exists = mysqli_num_rows($name_result) > 0;
    
    // usedItems 테이블에서도 itemname 중복 체크
    $used_name_query = "SELECT * FROM usedItems WHERE usedItemname='$itemname'";
    $used_name_result = mysqli_query($connect, $used_name_query);
    $used_name_exists = mysqli_num_rows($used_name_result) > 0;

    if ($used_name_exists) {
        http_response_code(200);
        echo json_encode(array(
        'status' => 'false',
        'message' => '사용 완료에 동일한 이름의 물건이 존재합니다'), JSON_UNESCAPED_UNICODE);
    } else {
        // userid와 userphone 중복 체크 후 삽입
        if ($name_exists) {
            $row = mysqli_fetch_assoc($name_result);
            $itemid = $row['itemid'];

            http_response_code(200);
            echo json_encode(array(
                'status' => "$itemid",
                'message' => '중복된 물건이 존재합니다.\n기존의 물건에 개수를 추가하시려면 확인을 눌러주세요'), JSON_UNESCAPED_UNICODE);
        } else {
            $userid = $_POST["userid"];
            $itemplace = $_POST["itemplace"];
            $itemstore = $_POST["itemstore"];
            $itemcount = $_POST["itemcount"];

            $query = "INSERT INTO items (userid, itemname, itemimage, itemplace, itemstore, itemcount) VALUES ('$userid', '$itemname', '$itemimage_url', '$itemplace', '$itemstore', '$itemcount')";
            $result = mysqli_query($connect, $query);

            if ($result) {
                http_response_code(200);
                echo json_encode(array(
                    'status' => 'true',
                    'message' => '물건 추가가 완료되었습니다'), JSON_UNESCAPED_UNICODE);
            } else {
                http_response_code(200);
                echo json_encode(array(
                    'status' => 'false',
                    'message' => '물건 추가에 실패하였습니다'), JSON_UNESCAPED_UNICODE);
            }
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
