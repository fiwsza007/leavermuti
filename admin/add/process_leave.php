
<?php
include('../conn/conn.php');  // Fix connection path

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $employeeid = $_POST['employeeid'];
    $leavetype = $_POST['leavetype'];
    $leavestart = $_POST['leavestart'];
    $leaveend = $_POST['leaveend'];
    $leavestatus = 'รออนุมัติ';
    
    // ตรวจสอบการลาซ้ำซ้อน
    $check_sql = "SELECT * FROM leaves 
                  WHERE employeesid = ? 
                  AND ((leavestart BETWEEN ? AND ?) 
                  OR (leaveend BETWEEN ? AND ?)
                  OR (? BETWEEN leavestart AND leaveend)
                  OR (? BETWEEN leavestart AND leaveend))
                  AND leavestatus != 'rejected'";
    
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("issssss", $employeeid, $leavestart, $leaveend, $leavestart, $leaveend, $leavestart, $leaveend);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'คุณมีการลาในช่วงเวลานี้แล้ว']);
        exit;
    }

    // เริ่ม transaction
    $conn->begin_transaction();
    
    try {
        // บันทึกข้อมูลการลา
        $sql = "INSERT INTO leaves (employeesid, leavetype, leavestart, leaveend, leavestatus,note) 
                VALUES (?, ?, ?, ?, ?,?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iissss", $employeeid, $leavetype, $leavestart, $leaveend, $leavestatus,'ลา');
        
        if (!$stmt->execute()) {
            throw new Exception("Error executing query: " . $stmt->error);
        }
        
        $leaveid = $conn->insert_id;
        
        // จัดการไฟล์แนบ
        if (!empty($_FILES['file']['name'][0])) {
            $uploadDir = "../../uploads/leaves/";  // Fix upload path
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            foreach ($_FILES['file']['tmp_name'] as $key => $tmp_name) {
                $file_name = $_FILES['file']['name'][$key];
                $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
                $new_file_name = time() . '_' . $key . '.' . $file_ext;
                $file_path = $uploadDir . $new_file_name;
                
                if (move_uploaded_file($tmp_name, $file_path)) {
                    // บันทึกข้อมูลไฟล์
                    $sql = "UPDATE leaves SET file = ? WHERE leaveid = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("si", $new_file_name, $leaveid);
                    $stmt->execute();
                }
            }
        }
        
        $conn->commit();
        echo json_encode(['status' => 'success', 'message' => 'บันทึกข้อมูลการลาสำเร็จ']);
        
    } catch (Exception $e) {
        $conn->rollback();
        error_log($e->getMessage());  // Log the error
        echo json_encode(['status' => 'error', 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
    }
}
?>