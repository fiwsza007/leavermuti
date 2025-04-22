<?php
include('../conn/conn.php');

// Get form data
$id = $_POST['id'];
$prefix = $_POST['prefix'];
$fname = $_POST['fname'];
$lname = $_POST['lname'];
$email = $_POST['email'];
$department = $_POST['department'];
$position = $_POST['position'];
$status = $_POST['status'];
$startwork = $_POST['startwork'];

// Check if email already exists for other employees
$check_email = "SELECT id FROM employees WHERE email = ? AND id != ?";
$check_stmt = $conn->prepare($check_email);
$check_stmt->bind_param("si", $email, $id);
$check_stmt->execute();
$result = $check_stmt->get_result();

if ($result->num_rows > 0) {
    $response['status'] = 'error';
    $response['message'] = 'อีเมลนี้มีในระบบแล้ว กรุณาใช้อีเมลอื่น';
    echo json_encode($response);
    exit;
}
$check_stmt->close();

// Continue with update if email is unique
$sql = "UPDATE employees SET 
        prefix = ?, 
        fname = ?, 
        lname = ?, 
        email = ?,
        department = ?,
        position = ?,
        staffstatus = ?,
        startwork = ?
        WHERE id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("isssiiisi", $prefix, $fname, $lname, $email, $department, $position, $status, $startwork, $id);

$response = array();
if ($stmt->execute()) {
    $response['status'] = 'success';
    $response['message'] = 'Employee data updated successfully';
} else {
    $response['status'] = 'error';
    $response['message'] = 'Error updating employee data: ' . $stmt->error;
}

$stmt->close();
$conn->close();

echo json_encode($response);
?>