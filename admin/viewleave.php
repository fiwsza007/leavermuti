<?php
include('conn/conn.php');

date_default_timezone_set('Asia/Bangkok');
$customCurrentDate = new DateTime('');

// ดึงข้อมูลพนักงาน
$sql_employees = "SELECT id, fname, lname, staffstatus, startwork FROM employees";
$result_employees = $conn->query($sql_employees);

$employees = [];
while ($row = $result_employees->fetch_assoc()) {
    $startDate = new DateTime($row['startwork']);
    $totalMonths = max(0, $customCurrentDate->diff($startDate)->y * 12 + $customCurrentDate->diff($startDate)->m);

    $employees[$row['id']] = [
        'fullname' => $row['fname'] . ' ' . $row['lname'],
        'staffstatus' => $row['staffstatus'],
        'totalMonths' => $totalMonths
    ];
}

// ดึงข้อมูล leavetype (ทุกประเภทการลา)
$sql_leavetype = "SELECT leavetypeid, leavetypename, staffid, leaveofyear, stackleaveday, workage, workage_type, workageday FROM leavetype";
$result_leavetype = $conn->query($sql_leavetype);

$leaveTypes = [];
$leavetypeDetails = [];
while ($row = $result_leavetype->fetch_assoc()) {
    $leaveTypes[$row['staffid']][$row['leavetypename']][] = $row;
    $leavetypeDetails[$row['leavetypeid']] = [
        'leaveofyear' => $row['leaveofyear'],
        'stackleaveday' => $row['stackleaveday'] ?? 0,
        'leavetypename' => $row['leavetypename']
    ];
}

// ดึงข้อมูลจากตาราง leaveday
$sql_leaveday = "SELECT ld.leavedayid, ld.empid, ld.leavetype, ld.staffstatus, ld.day, ld.stackleaveday, e.fname, e.lname, e.startwork 
                 FROM leaveday ld 
                 JOIN employees e ON ld.empid = e.id";
$result_leaveday = $conn->query($sql_leaveday);

$leaveData = [];
$usedLeavetypenames = [];
while ($row = $result_leaveday->fetch_assoc()) {
    $startDate = new DateTime($row['startwork']);
    $totalMonths = max(0, $customCurrentDate->diff($startDate)->y * 12 + $customCurrentDate->diff($startDate)->m);

    $leaveData[$row['empid']][$row['leavetype']] = [
        'leavedayid' => $row['leavedayid'],
        'fullname' => $row['fname'] . ' ' . $row['lname'],
        'leavetype' => isset($row['leavetype']) ? $row['leavetype'] : null,
        'staffstatus' => $row['staffstatus'],
        'day' => isset($row['day']) ? $row['day'] : 0,
        'stackleaveday' => isset($row['stackleaveday']) ? $row['stackleaveday'] : 0,
        'totalMonths' => $totalMonths
    ];
}

// ดึงชื่อ leavetype และ staffstatus เพื่อใช้ในการแสดงผล
$sql_leavetype_names = "SELECT leavetypeid, leavetypename FROM leavetype";
$result_leavetype_names = $conn->query($sql_leavetype_names);
$leavetypeNames = [];
while ($row = $result_leavetype_names->fetch_assoc()) {
    $leavetypeNames[$row['leavetypeid']] = $row['leavetypename'];
    foreach ($leaveData as $empRecords) {
        if (isset($empRecords[$row['leavetypeid']]) && !in_array($row['leavetypename'], $usedLeavetypenames)) {
            $usedLeavetypenames[] = $row['leavetypename'];
        }
    }
}
sort($usedLeavetypenames);

$sql_staffstatus = "SELECT staffid, staffname FROM staffstatus";
$result_staffstatus = $conn->query($sql_staffstatus);
$staffstatusNames = [];
while ($row = $result_staffstatus->fetch_assoc()) {
    $staffstatusNames[$row['staffid']] = $row['staffname'];
}

// ฟังก์ชันเลือก leavetype ตามเงื่อนไขสำหรับแต่ละ leavetypename
function selectLeaveType($leaves, $totalMonths) {
    $selectedLeave = null;
    $maxWorkAgeDay = -1;
    $minWorkAge = PHP_INT_MAX;

    foreach ($leaves as $leave) {
        if (!isset($leave['workageday'])) {
            continue;
        }

        if ($totalMonths >= $leave['workageday']) {
            if ($leave['workageday'] > $maxWorkAgeDay) {
                $maxWorkAgeDay = $leave['workageday'];
                $minWorkAge = $leave['workage'];
                $selectedLeave = $leave;
            } elseif ($leave['workageday'] == $maxWorkAgeDay && $leave['workage'] < $minWorkAge) {
                $minWorkAge = $leave['workage'];
                $selectedLeave = $leave;
            }
        }
    }

    return $selectedLeave;
}

// ตรวจสอบข้อมูลที่ต้องอัพเดท
$pendingUpdateCount = 0;
$pendingUpdateDetails = [];

foreach ($employees as $empID => $empData) {
    $staffstatus = $empData['staffstatus'];
    $totalMonths = $empData['totalMonths'];

    if (isset($leaveTypes[$staffstatus])) {
        foreach ($leaveTypes[$staffstatus] as $leaveTypeName => $leaves) {
            $selectedLeave = selectLeaveType($leaves, $totalMonths);
            if (!$selectedLeave) {
                continue;
            }

            $newLeavetype = $selectedLeave['leavetypeid'];

            $found = false;
            if (isset($leaveData[$empID])) {
                foreach ($leaveData[$empID] as $data) {
                    if (isset($data['leavetype']) && isset($leavetypeNames[$data['leavetype']]) && $leavetypeNames[$data['leavetype']] == $leaveTypeName) {
                        $found = true;
                        if ($data['leavetype'] != $newLeavetype) {
                            $pendingUpdateCount++;
                            $pendingUpdateDetails[] = "พนักงาน ID $empID: ควรเปลี่ยน $leaveTypeName จาก leavetype {$data['leavetype']} เป็น $newLeavetype";
                        }
                        break;
                    }
                }
            }

            if (!$found) {
                $pendingUpdateCount++;
                $pendingUpdateDetails[] = "พนักงาน ID $empID: ควรเพิ่ม $leaveTypeName (leavetype $newLeavetype)";
            }
        }
    }
}

// ตรวจสอบและอัพเดท leavetype
$updateCount = 0;
$updateMessages = [];

if (isset($_POST['update_leaveday'])) {
    foreach ($employees as $empID => $empData) {
        if (empty($empID)) {
            $updateMessages[] = "พนักงาน ID ว่างเปล่า: ไม่สามารถดำเนินการได้";
            continue;
        }

        $staffstatus = $empData['staffstatus'];
        $totalMonths = $empData['totalMonths'];

        if (isset($leaveTypes[$staffstatus])) {
            foreach ($leaveTypes[$staffstatus] as $leaveTypeName => $leaves) {
                $selectedLeave = selectLeaveType($leaves, $totalMonths);
                if (!$selectedLeave) {
                    continue;
                }

                $newLeavetype = $selectedLeave['leavetypeid'];

                $found = false;
                $existingRecord = null;
                if (isset($leaveData[$empID])) {
                    foreach ($leaveData[$empID] as $data) {
                        if (isset($data['leavetype']) && isset($leavetypeNames[$data['leavetype']]) && $leavetypeNames[$data['leavetype']] == $leaveTypeName) {
                            $found = true;
                            $existingRecord = $data;
                            break;
                        }
                    }
                }

                if ($found) {
                    if ($existingRecord['leavetype'] != $newLeavetype) {
                        $sql_update = "UPDATE leaveday SET leavetype = ? WHERE leavedayid = ?";
                        $stmt_update = $conn->prepare($sql_update);
                        $stmt_update->bind_param('ii', $newLeavetype, $existingRecord['leavedayid']);
                        if ($stmt_update->execute()) {
                            $updateCount++;
                            $updateMessages[] = "พนักงาน ID $empID: เปลี่ยน $leaveTypeName จาก leavetype {$existingRecord['leavetype']} เป็น $newLeavetype";
                            $leaveData[$empID][$existingRecord['leavetype']]['leavetype'] = $newLeavetype;
                            $leaveData[$empID][$newLeavetype] = $leaveData[$empID][$existingRecord['leavetype']];
                            unset($leaveData[$empID][$existingRecord['leavetype']]);
                        } else {
                            $updateMessages[] = "พนักงาน ID $empID: ไม่สามารถอัพเดท $leaveTypeName ได้: " . $stmt_update->error;
                        }
                        $stmt_update->close();
                    }
                } else {
                    $sql_insert = "INSERT INTO leaveday (empid, leavetype, staffstatus, day, stackleaveday) VALUES (?, ?, ?, 0, 0)";
                    $stmt_insert = $conn->prepare($sql_insert);
                    $stmt_insert->bind_param('sii', $empID, $newLeavetype, $staffstatus);
                    if ($stmt_insert->execute()) {
                        $updateCount++;
                        $updateMessages[] = "พนักงาน ID $empID: เพิ่ม $leaveTypeName (leavetype $newLeavetype)";
                        $leaveData[$empID][$newLeavetype] = [
                            'leavedayid' => $conn->insert_id,
                            'fullname' => $empData['fullname'],
                            'leavetype' => $newLeavetype,
                            'staffstatus' => $staffstatus,
                            'day' => 0,
                            'stackleaveday' => 0,
                            'totalMonths' => $totalMonths
                        ];
                        if (!in_array($leaveTypeName, $usedLeavetypenames)) {
                            $usedLeavetypenames[] = $leaveTypeName;
                            sort($usedLeavetypenames);
                        }
                    } else {
                        $updateMessages[] = "พนักงาน ID $empID: ไม่สามารถเพิ่ม $leaveTypeName ได้: " . $stmt_insert->error;
                    }
                    $stmt_insert->close();
                }
            }
        }
    }
}

// อัพเดทค่า day และ stackleaveday เมื่อกดบันทึกจาก modal
$showAlert = false;
$alertType = '';
$alertMessage = '';

if (isset($_POST['update_day'])) {
    $leavedayid = $_POST['leavedayid'];
    $new_day = isset($_POST['new_day']) ? intval($_POST['new_day']) : 0;
    $new_stackleaveday = isset($_POST['new_stackleaveday']) ? intval($_POST['new_stackleaveday']) : 0;

    if (empty($leavedayid)) {
        $alertType = 'error';
        $alertMessage = 'ข้อมูลไม่ครบถ้วน: leavedayid หายไป';
        $showAlert = true;
    } else {
        $sql_update_day = "UPDATE leaveday SET day = ?, stackleaveday = ? WHERE leavedayid = ?";
        $stmt_update_day = $conn->prepare($sql_update_day);
        $stmt_update_day->bind_param('iii', $new_day, $new_stackleaveday, $leavedayid);

        if ($stmt_update_day->execute()) {
            foreach ($leaveData as $empID => $leaveRecords) {
                foreach ($leaveRecords as $leavetype => $data) {
                    if ($data['leavedayid'] == $leavedayid) {
                        $leaveData[$empID][$leavetype]['day'] = $new_day;
                        $leaveData[$empID][$leavetype]['stackleaveday'] = $new_stackleaveday;
                        break 2;
                    }
                }
            }
            $alertType = 'success';
            $alertMessage = "อัพเดทจำนวนวันลาและวันลาสะสมสำเร็จสำหรับ leavedayid $leavedayid";
            $showAlert = true;
        } else {
            $alertType = 'error';
            $alertMessage = "ไม่สามารถอัพเดทจำนวนวันลาได้: " . $stmt_update_day->error;
            $showAlert = true;
        }
        $stmt_update_day->close();
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.2.0/remixicon.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="assets/css/table.css">
    <title>Dashboard</title>
    <style>
        @media (max-width: 576px) {
            .modal-dialog {
                margin: 0.5rem;
            }
            .modal-content {
                font-size: 0.9rem;
            }
            .modal-header h5 {
                font-size: 1.1rem;
            }
            .modal-body {
                padding: 1rem;
            }
            .form-control {
                font-size: 0.9rem;
            }
            .btn {
                font-size: 0.9rem;
                padding: 0.5rem;
            }
        }
        @media (min-width: 576px) {
            .modal-sm {
                max-width: 400px;
            }
        }
        .table-responsive {
            width: 100%;
        }
        table.dataTable {
            width: 100% !important;
        }
    </style>
</head>
<body>
    <?php include('component/sidebar.php'); ?>
    <main class="main container3" id="main">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">อัพเดทประเภทการลา</h3>
                <p class="card-text mt-2 mb-0">วันที่ปัจจุบัน: <?= $customCurrentDate->format('d/m/') . ($customCurrentDate->format('Y') ) ?></p>
                <p class="card-text mt-2 mb-0">ข้อมูลที่ต้องอัพเดท: <strong><?= $pendingUpdateCount ?> รายการ</strong></p>
                <?php if ($pendingUpdateCount > 0): ?>
                    <div class="alert alert-warning mt-2" role="alert">
                        รายละเอียดข้อมูลที่ต้องอัพเดท:
                        <ul>
                            <?php foreach ($pendingUpdateDetails as $detail): ?>
                                <li><?= $detail ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                <form method="POST" class="mt-2">
                    <button type="submit" name="update_leaveday" class="btn btn-primary">อัพเดทข้อมูล</button>
                </form>
                <?php if ($updateCount > 0 || !empty($updateMessages)): ?>
                    <div class="alert alert-success mt-2" role="alert">
                        อัพเดทประเภทการลาสำเร็จ! มีการเปลี่ยนแปลง: <strong><?= $updateCount ?> รายการ</strong>
                        <ul>
                            <?php foreach ($updateMessages as $message): ?>
                                <li><?= $message ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <?php if (empty($usedLeavetypenames)): ?>
                    <div class="alert alert-info mt-3" role="alert">
                        ยังไม่มีข้อมูลการลาในระบบ กรุณากด "อัพเดทข้อมูล" เพื่อเพิ่มข้อมูล
                    </div>
                <?php else: ?>
                    <!-- สร้างแท็บสำหรับแต่ละประเภทการลาที่มีข้อมูล -->
                    <ul class="nav nav-tabs" id="leaveTypeTab" role="tablist">
                        <?php foreach ($usedLeavetypenames as $index => $leavetypename): ?>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link <?= $index === 0 ? 'active' : '' ?>" 
                                        id="<?= str_replace(' ', '-', $leavetypename) ?>-tab" 
                                        data-bs-toggle="tab" 
                                        data-bs-target="#<?= str_replace(' ', '-', $leavetypename) ?>" 
                                        type="button" 
                                        role="tab" 
                                        aria-controls="<?= str_replace(' ', '-', $leavetypename) ?>" 
                                        aria-selected="<?= $index === 0 ? 'true' : 'false' ?>">
                                    <?= $leavetypename ?>
                                </button>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <!-- เนื้อหาของแต่ละแท็บ -->
                    <div class="tab-content" id="leaveTypeTabContent">
                        <?php foreach ($usedLeavetypenames as $index => $leavetypename): ?>
                            <div class="tab-pane fade <?= $index === 0 ? 'show active' : '' ?>" 
                                 id="<?= str_replace(' ', '-', $leavetypename) ?>" 
                                 role="tabpanel" 
                                 aria-labelledby="<?= str_replace(' ', '-', $leavetypename) ?>-tab">
                                <div class="table-responsive mt-3">
                                    <table id="example-<?= str_replace(' ', '-', $leavetypename) ?>" 
                                           class="table table-striped table-bordered dt-responsive nowrap" 
                                           style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>รหัสพนักงาน</th>
                                                <th>ชื่อ-นามสกุล</th>
                                                <th>สถานะพนักงาน</th>
                                                <th>จำนวนวันลา (ใช้/ทั้งหมด)</th>
                                                <th>วันลาสะสม (ใช้/ทั้งหมด)</th>
                                                <th>ระยะเวลาทำงาน (เดือน)</th>
                                                <th>การดำเนินการ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($employees as $empID => $empData): ?>
                                                <tr>
                                                    <?php
                                                    $staffstatus = $empData['staffstatus'];
                                                    $totalMonths = $empData['totalMonths'];
                                                    $selectedLeavetype = null;
                                                    $day = 0;
                                                    $stackleaveday = 0;
                                                    $stackleaveday_max = 0;
                                                    $leaveofyear = 0;
                                                    $leavedayid = null;

                                                    if (isset($leaveTypes[$staffstatus][$leavetypename])) {
                                                        $selectedLeave = selectLeaveType($leaveTypes[$staffstatus][$leavetypename], $totalMonths);
                                                        if ($selectedLeave) {
                                                            $selectedLeavetype = $selectedLeave['leavetypeid'];
                                                            $leaveofyear = $selectedLeave['leaveofyear'];
                                                            $stackleaveday_max = $leavetypeDetails[$selectedLeavetype]['stackleaveday'];

                                                            if (isset($leaveData[$empID][$selectedLeavetype])) {
                                                                $day = $leaveData[$empID][$selectedLeavetype]['day'];
                                                                $stackleaveday = $leaveData[$empID][$selectedLeavetype]['stackleaveday'];
                                                                $leavedayid = $leaveData[$empID][$selectedLeavetype]['leavedayid'];
                                                            } else {
                                                                // ถ้าไม่มีข้อมูลใน $leaveData ให้กำหนดค่าเริ่มต้น
                                                                $day = 0;
                                                                $stackleaveday = 0;
                                                                $leavedayid = null;
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                    <td><?= $empID ?></td>
                                                    <td><?= $empData['fullname'] ?></td>
                                                    <td><?= isset($staffstatusNames[$empData['staffstatus']]) ? $staffstatusNames[$empData['staffstatus']] : 'ไม่ระบุ' ?></td>
                                                    <td><?= $day ?>/<?= $leaveofyear ?: '0' ?></td>
                                                    <td><?= $stackleaveday ?>/<?= $stackleaveday_max ?: '0' ?></td>
                                                    <td><?= $totalMonths ?></td>
                                                    <td>
                                                        <?php if ($selectedLeavetype && $leavedayid): ?>
                                                            <button type="button" class="btn btn-warning btn-sm edit-day-btn" 
                                                                    data-bs-toggle="modal" 
                                                                    data-bs-target="#editDayModal"
                                                                    data-leavedayid="<?= $leavedayid ?>"
                                                                    data-day="<?= $day ?>"
                                                                    data-stackleaveday="<?= $stackleaveday ?>"
                                                                    data-leaveofyear="<?= $leaveofyear ?>"
                                                                    data-stackleavedaymax="<?= $stackleaveday_max ?>">
                                                                แก้ไข
                                                            </button>
                                                        <?php else: ?>
                                                            <span>ไม่มีข้อมูล</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Modal สำหรับแก้ไขจำนวนวันลา -->
        <div class="modal fade" id="editDayModal" tabindex="-1" aria-labelledby="editDayModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editDayModalLabel">แก้ไขจำนวนวันลา</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="new_day" class="form-label">จำนวนวันลาใหม่</label>
                                <input type="number" class="form-control" id="new_day" name="new_day" min="0">
                            </div>
                            <div class="mb-3">
                                <label for="new_stackleaveday" class="form-label">จำนวนวันลาสะสมใหม่</label>
                                <input type="number" class="form-control" id="new_stackleaveday" name="new_stackleaveday" min="0">
                            </div>
                            <input type="hidden" id="leavedayid" name="leavedayid">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                            <button type="submit" name="update_day" class="btn btn-primary">บันทึก</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/responsive.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="assets/js/main.js"></script>
    <script>
    $(document).ready(function() {
        var dataTables = {};

        <?php foreach ($usedLeavetypenames as $leavetypename): ?>
            dataTables['<?= str_replace(' ', '-', $leavetypename) ?>'] = $('#example-<?= str_replace(' ', '-', $leavetypename) ?>').DataTable({
                responsive: true,
                autoWidth: false,
                scrollX: true,
                pageLength: 5,
                lengthMenu: [
                    [5, 15, 25, 50, -1],
                    [5, 15, 25, 50, "ทั้งหมด"]
                ],
                language: {
                    lengthMenu: "แสดง _MENU_ รายการ",
                    zeroRecords: "ไม่พบข้อมูล",
                    info: "แสดงหน้า _PAGE_ จาก _PAGES_",
                    infoEmpty: "ไม่มีข้อมูล",
                    infoFiltered: "(กรองจากทั้งหมด _MAX_ รายการ)",
                    search: "ค้นหา:",
                    paginate: {
                        first: "หน้าแรก",
                        last: "หน้าสุดท้าย",
                        next: "ถัดไป",
                        previous: "ก่อนหน้า"
                    }
                }
            });
        <?php endforeach; ?>

        $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
            var targetTab = $(e.target).data('bs-target');
            var tableId = targetTab.replace('#', 'example-');
            if (dataTables[tableId.replace('example-', '')]) {
                dataTables[tableId.replace('example-', '')].columns.adjust().responsive.recalc();
            }
        });

        var firstTab = $('.nav-tabs .nav-link.active').data('bs-target');
        var firstTableId = firstTab.replace('#', 'example-');
        if (dataTables[firstTableId.replace('example-', '')]) {
            dataTables[firstTableId.replace('example-', '')].columns.adjust().responsive.recalc();
        }

        $(window).on('resize', function() {
            $.each(dataTables, function(key, table) {
                table.columns.adjust().responsive.recalc();
            });
        });

        $(document).on('click', '.edit-day-btn', function() {
            const leavedayid = $(this).data('leavedayid');
            const day = $(this).data('day');
            const stackleaveday = $(this).data('stackleaveday');
            const leaveofyear = $(this).data('leaveofyear');
            const stackleavedaymax = $(this).data('stackleavedaymax');

            $('#editDayModal #leavedayid').val(leavedayid);
            $('#editDayModal #new_day').val(day);
            $('#editDayModal #new_stackleaveday').val(stackleaveday);
            $('#editDayModal #new_day').attr('max', leaveofyear);
            $('#editDayModal #new_stackleaveday').attr('max', stackleavedaymax);
        });

        <?php if ($showAlert): ?>
            Swal.fire({
                icon: '<?= $alertType ?>',
                title: '<?= $alertType === 'success' ? 'สำเร็จ' : 'เกิดข้อผิดพลาด' ?>',
                text: '<?= $alertMessage ?>',
                showConfirmButton: false,
                timer: 1500
            });
        <?php endif; ?>
    });
    </script>
</body>
</html>

<?php $conn->close(); ?>