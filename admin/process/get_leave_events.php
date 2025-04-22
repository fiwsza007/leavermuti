<?php
include('../conn/conn.php');

$sql = "SELECT DATE(leavestart) as start_date, 
        COUNT(DISTINCT employeesid) as leave_count
        FROM leaves 
        WHERE leavestatus IN ('รออนุมัติ', 'อนุมัติ', 'รอผ.อ.อนุมัติ', 'รอรองผ.อ.อนุมัติ', 'รอหัวหน้าอนุมัติ')
        GROUP BY start_date";

$result = $conn->query($sql);
$events = array();

while($row = $result->fetch_assoc()) {
    $dateStr = $row['start_date'];
    
    // Skip weekends
    $date = new DateTime($dateStr);
    if($date->format('N') >= 6) continue;
    
    $events[] = array(
        'title' => 'ลา ' . $row['leave_count'] . ' คน',
        'start' => $dateStr,
        'backgroundColor' => '#4e73df',
        'count' => $row['leave_count']
    );
}

header('Content-Type: application/json');
echo json_encode($events);
?>