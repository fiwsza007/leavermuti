<?php
include('../conn/conn.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // รับข้อมูลจาก AJAX และตรวจสอบคีย์
    $leaveId = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $supervisor_id = isset($_POST['supervisor_id']) && $_POST['supervisor_id'] !== '0' ? intval($_POST['supervisor_id']) : null;
    $deputy_id = isset($_POST['deputy_id']) && $_POST['deputy_id'] !== '0' ? intval($_POST['deputy_id']) : null;
    $director_id = isset($_POST['director_id']) && $_POST['director_id'] !== '0' ? intval($_POST['director_id']) : null;
    $workdays = isset($_POST['workdays']) ? intval($_POST['workdays']) : 0;

    // ดีบั๊กข้อมูลที่รับมา
    error_log("Received POST data in approve_leave.php: " . json_encode($_POST));
    error_log("Processed values: leaveId=$leaveId, supervisor_id=$supervisor_id, deputy_id=$deputy_id, director_id=$director_id, workdays=$workdays");

    // ตรวจสอบรหัสใบลา
    if ($leaveId <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'รหัสใบลาไม่ถูกต้อง']);
        exit;
    }
    elseif($supervisor_id== null && $deputy_id!=null ){
        $sql = "UPDATE leaves SET 
        leavestatus = 'รอรองผ.อ.อนุมัติ',
        approver1 = " . ($supervisor_id !== null ? "'$supervisor_id'" : "NULL") . ",
        approver2 = " . ($deputy_id !== null ? "'$deputy_id'" : "NULL") . ",
        approver3 = " . ($director_id !== null ? "'$director_id'" : "NULL") . ",
        day = ?
        WHERE leavesid = ?";
    }
    elseif($supervisor_id== null && $deputy_id== null){
        $sql = "UPDATE leaves SET 
        leavestatus = 'รอผ.อ.อนุมัติ',
        approver1 = " . ($supervisor_id !== null ? "'$supervisor_id'" : "NULL") . ",
        approver2 = " . ($deputy_id !== null ? "'$deputy_id'" : "NULL") . ",
        approver3 = " . ($director_id !== null ? "'$director_id'" : "NULL") . ",
        day = ?
        WHERE leavesid = ?";
    }
    else{
        $sql = "UPDATE leaves SET 
        leavestatus = 'รอหัวหน้าอนุมัติ',
        approver1 = " . ($supervisor_id !== null ? "'$supervisor_id'" : "NULL") . ",
        approver2 = " . ($deputy_id !== null ? "'$deputy_id'" : "NULL") . ",
        approver3 = " . ($director_id !== null ? "'$director_id'" : "NULL") . ",
        day = ?
        WHERE leavesid = ?";
    }
    // เตรียมคำสั่ง SQL โดยจัดการค่า NULL
 

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $workdays, $leaveId);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $conn->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}

$conn->close();
?>
approve_leave