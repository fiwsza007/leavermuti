<?php
header('Content-Type: application/json; charset=utf-8');
include("../sql/conn.php");

if(isset($_POST['id'])) {
    $id = $_POST['id'];
    
    // ตรวจสอบว่ามีพนักงานอยู่จริงหรือไม่
    $check_sql = "SELECT id FROM employees WHERE id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if($result->num_rows === 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'ไม่พบข้อมูลพนักงานที่ต้องการลบ'
        ]);
        exit;
    }
    
    // ดำเนินการลบข้อมูล
    $sql = "DELETE FROM employees WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode([
                'status' => 'success',
                'message' => 'ลบข้อมูลพนักงานเรียบร้อยแล้ว'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'ไม่สามารถลบข้อมูลได้'
            ]);
        }
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'เกิดข้อผิดพลาดในการลบข้อมูล: ' . $stmt->error
        ]);
    }
    
    $stmt->close();
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'ไม่ได้ระบุรหัสพนักงาน'
    ]);
}

$conn->close();
?>