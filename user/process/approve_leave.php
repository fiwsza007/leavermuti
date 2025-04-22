<?php
include('../conn/conn.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $leaveId = $_POST['leave_id'];
    $currentStatus = $_POST['current_status'];
    $employeesid = $_POST['employeesid'];
    $leavetype = $_POST['leavetype'];
    $leaveday = $_POST['leaveday'];
    $note = $_POST['note'];




    // Update leave status based on current status
    switch ($currentStatus) {
        case 'รอหัวหน้าอนุมัติ':
            $newStatus = 'รอรองผ.อ.อนุมัติ';
            $sql = "UPDATE leaves SET leavestatus = ? WHERE leavesid = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('si', $newStatus, $leaveId);
            
            if ($stmt->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'อนุมัติการลาเรียบร้อยแล้ว']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'เกิดข้อผิดพลาดในการอนุมัติ']);
            }
            $stmt->close();
            break;

        case 'รอรองผ.อ.อนุมัติ':
            $newStatus = 'รอผ.อ.อนุมัติ';
            $sql = "UPDATE leaves SET leavestatus = ? WHERE leavesid = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('si', $newStatus, $leaveId);
            
            if ($stmt->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'อนุมัติการลาเรียบร้อยแล้ว']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'เกิดข้อผิดพลาดในการอนุมัติ']);
            }
            $stmt->close();
            break;

        case 'รอผ.อ.อนุมัติ':
            if($note=='ลา'){
            $newStatus = 'อนุมัติ';
            $sql = "UPDATE leaves SET leavestatus = ? WHERE leavesid = ?";
            $sql2 = "UPDATE leaveday SET day = day - ? WHERE leavetype = ? AND empid = ?";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('si', $newStatus, $leaveId);
            
            $stmt2 = $conn->prepare($sql2);
            $stmt2->bind_param('isi', $leaveday, $leavetype, $employeesid);
            
            if ($stmt->execute() && $stmt2->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'อนุมัติการลาเรียบร้อยแล้ว']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'เกิดข้อผิดพลาดในการอนุมัติ']);
            }
            $stmt->close();
            $stmt2->close();
            break;
        }
        else if($note=='ยกเลิกลาและคืนวันลา'){
            $newStatus = 'อนุมัติ';
            $sql = "UPDATE leaves SET leavestatus = ? WHERE leavesid = ?";
            $sql2 = "UPDATE leaveday SET day = day + ? WHERE leavetype = ? AND empid = ?";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('si', $newStatus, $leaveId);
            
            $stmt2 = $conn->prepare($sql2);
            $stmt2->bind_param('isi', $leaveday, $leavetype, $employeesid);
            
            if ($stmt->execute() && $stmt2->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'อนุมัติการลาเรียบร้อยแล้ว']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'เกิดข้อผิดพลาดในการอนุมัติ']);
            }
            $stmt->close();
            $stmt2->close();
            break;

        }
        else{
            $newStatus = 'อนุมัติ';
            $sql = "UPDATE leaves SET leavestatus = ? WHERE leavesid = ?";
          ;
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('si', $newStatus, $leaveId);

            
            if ($stmt->execute() ) {
                echo json_encode(['status' => 'success', 'message' => 'อนุมัติการยกเลิกลาเรียบร้อยแล้ว']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'เกิดข้อผิดพลาดในการอนุมัติ']);
            }
            $stmt->close();
        
            break;
        }
    }

} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
