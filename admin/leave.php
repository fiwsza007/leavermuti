<?php
include('conn/conn.php');

date_default_timezone_set('Asia/Bangkok');
$currentDate = date('d/m/Y');
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.2.0/remixicon.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="assets/css/table.css">
    <title>Dashboard</title>
</head>
<body>
    <?php include('component/sidebar.php'); ?>

    <main class="main container3" id="main">
        <div class="container-fluid">
            <div class="row mt-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">รายการใบลา</h3>
                            <p class="card-text mt-2 mb-0">วันที่ปัจจุบัน: <?php echo $currentDate; ?></p>
                        </div>
                        <div class="card-body">
                            <table id="leaveTable" class="table table-striped table-bordered dt-responsive nowrap" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>ลำดับ</th>
                                        <th>ยื่น</th>
                                        <th hidden>รหัสใบลา</th>
                                        <th>ชื่อ-นามสกุล</th>
                                        <th>บทบาท</th>
                                        <th>ประเภท</th>
                                        <th>เริ่ม</th>
                                        <th>สิ้นสุด</th>
                                        <th>จำนวนวัน</th>
                                        <th>หัวหน้างาน</th>
                                        <th>รองผู้อำนวยการ</th>
                                        <th>ผู้อำนวยการ</th>
                                        <th>สถานะ</th>
                                        <th>จัดการ</th>
                                        <th>อนุมัติด่วน</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sql = "SELECT leaves.*, 
                                            CONCAT(pr.prefixname, e.fname, ' ', e.lname) as fullname, 
                                            leavetype.leavetypename, 
                                            e.id, 
                                            r.level, 
                                            r.rolename
                                            FROM leaves
                                            LEFT JOIN employees e ON leaves.employeesid = e.id
                                            LEFT JOIN prefix pr ON e.prefix = pr.prefixid
                                            LEFT JOIN leavetype ON leaves.leavetype = leavetype.leavetypeid
                                            LEFT JOIN position p ON e.position = p.positionid
                                            LEFT JOIN role r ON p.roleid = r.roleid
                                            WHERE leaves.leavestatus = 'รออนุมัติ'";
                                    $result = mysqli_query($conn, $sql);
                                    $i = 1;
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        $rowLevel = $row['level'] ?? 1; // ถ้าไม่มี level ตั้งเป็น 1
                                        $rowRoleName = $row['rolename'] ?? 'พนักงานทั่วไป';
                                    ?>
                                        <tr data-level="<?php echo $rowLevel; ?>">
                                            <td><?php echo $i++; ?></td>
                                            <td><?php echo $row['note'] ?? '-'; ?></td>
                                            <td hidden><?php echo $row['leavesid']; ?></td>
                                            <td><?php echo $row['fullname']; ?></td>
                                            <td><?php echo $rowRoleName ; ?></td>
                                            <td><?php echo $row['leavetypename']; ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($row['leavestart'])); ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($row['leaveend'])); ?></td>
                                            <td>
                                                <?php
                                                $start = new DateTime($row['leavestart']);
                                                $end = new DateTime($row['leaveend']);
                                                $interval = new DateInterval('P1D');
                                                $daterange = new DatePeriod($start, $interval, $end->modify('+1 day'));

                                                $workdays = 0;
                                                $sql_holidays = "SELECT holidayday FROM holiday";
                                                $result_holidays = $conn->query($sql_holidays);
                                                $holidays = [];

                                                if ($result_holidays) {
                                                    while ($holiday = $result_holidays->fetch_assoc()) {
                                                        $holidays[] = $holiday['holidayday'];
                                                    }
                                                }

                                                foreach ($daterange as $date) {
                                                    if ($date->format('N') < 6 && !in_array($date->format('Y-m-d'), $holidays)) {
                                                        $workdays++;
                                                    }
                                                }
                                                echo $workdays;
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                $sql_sup = "SELECT e.id, CONCAT(pr.prefixname, e.fname, ' ', e.lname) AS fullname, r.level
                                                            FROM employees e 
                                                            LEFT JOIN prefix pr ON e.prefix = pr.prefixid
                                                            LEFT JOIN position p ON e.position = p.positionid
                                                            LEFT JOIN role r ON p.roleid = r.roleid
                                                            WHERE r.level = 2";
                                                $result_sup = $conn->query($sql_sup);
                                                $supervisors = $result_sup->fetch_all(MYSQLI_ASSOC);
                                                ?>
                                                <select class="form-select supervisor-select" id="supervisorid_<?php echo $row['leavesid']; ?>" name="supervisor" <?php echo $rowLevel >= 2 ? 'disabled' : ''; ?>>
                                                    <option value="0">-- เลือกหัวหน้างาน --</option>
                                                    <?php foreach ($supervisors as $rowapp): ?>
                                                        <option value="<?php echo $rowapp['id']; ?>">
                                                            <?php echo $rowapp['fullname'] ; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </td>
                                            <td>
                                                <?php
                                                $sql_dep = "SELECT e.id, CONCAT(pr.prefixname, e.fname, ' ', e.lname) AS fullname, r.level
                                                            FROM employees e 
                                                            LEFT JOIN prefix pr ON e.prefix = pr.prefixid
                                                            LEFT JOIN position p ON e.position = p.positionid
                                                            LEFT JOIN role r ON p.roleid = r.roleid
                                                            WHERE r.level = 3";
                                                $result_dep = $conn->query($sql_dep);
                                                $deputies = $result_dep->fetch_all(MYSQLI_ASSOC);
                                                ?>
                                                <select class="form-select" id="deputyid_<?php echo $row['leavesid']; ?>" name="deputy" <?php echo $rowLevel >= 3 ? 'disabled' : ''; ?>>
                                                    <option value="0">-- เลือกรองผู้อำนวยการ --</option>
                                                    <?php foreach ($deputies as $rowdep): ?>
                                                        <option value="<?php echo $rowdep['id']; ?>">
                                                            <?php echo $rowdep['fullname'] ; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </td>
                                            <td>
                                                <?php
                                                $sql_dir = "SELECT e.id, CONCAT(pr.prefixname, e.fname, ' ', e.lname) AS fullname, r.level
                                                            FROM employees e 
                                                            LEFT JOIN prefix pr ON e.prefix = pr.prefixid
                                                            LEFT JOIN position p ON e.position = p.positionid
                                                            LEFT JOIN role r ON p.roleid = r.roleid
                                                            WHERE r.level = 4 OR r.level = 3";
                                                $result_dir = $conn->query($sql_dir);
                                                $directors = $result_dir->fetch_all(MYSQLI_ASSOC);
                                                ?>
                                                <select class="form-select" id="directorid_<?php echo $row['leavesid']; ?>" name="director" <?php echo $rowLevel >= 4 ? 'disabled' : ''; ?>>
                                                    <option value="0">-- เลือกผู้อำนวยการ --</option>
                                                    <?php foreach ($directors as $rowdir): ?>
                                                        <option value="<?php echo $rowdir['id']; ?>">
                                                            <?php echo $rowdir['fullname'] ; ?>
                                                        
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </td>
                                            <td style="background-color: <?php
                                                switch ($row['leavestatus']) {
                                                    case 'รออนุมัติ':
                                                        echo '#FFC107; color: black';
                                                        break;
                                                    case 'อนุมัติ':
                                                        echo '#198754; color: white';
                                                        break;
                                                    case 'ไม่อนุมัติ':
                                                        echo '#DC3545; color: white';
                                                        break;
                                                }
                                                ?>">
                                                <?php echo $row['leavestatus']; ?>
                                            </td>
                                            <td>
                                                <button class="btn btn-outline-success btn-sm approve-btn"
                                                        data-leavesid="<?php echo $row['leavesid']; ?>"
                                                        data-workdays="<?php echo $workdays; ?>">
                                                    <i class="ri-check-line"></i>
                                                </button>
                                                <button class="btn btn-outline-danger btn-sm reject-btn" 
                                                        data-id="<?php echo $row['leavesid']; ?>">
                                                    <i class="ri-close-line"></i>
                                                </button>
                                            </td>
                                            <td>
                                                <button class="btn btn-outline-warning btn-sm">
                                                    <i class="ri-flashlight-line"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/responsive.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script>
        $(document).ready(function() {
            // กำหนด DataTable
            const table = $('#leaveTable').DataTable({
                responsive: true,
                language: {
                    url: 'assets/js/datatable-lang/th.json'
                }
            });

            // เก็บค่าเริ่มต้นจาก <select> ลงใน Data Attribute ทันทีเมื่อหน้าโหลด
            $('#leaveTable tbody tr').each(function() {
                const row = $(this);
                const leaveId = row.find('td:eq(2)').text(); // รหัสใบลา
                const supervisorId = $(`#supervisorid_${leaveId}`).val() || '0';
                const deputyId = $(`#deputyid_${leaveId}`).val() || '0';
                const directorId = $(`#directorid_${leaveId}`).val() || '0';

                row.data('supervisor-id', supervisorId);
                row.data('deputy-id', deputyId);
                row.data('director-id', directorId);

                console.log(`Initial values set for leave ${leaveId}:`, {
                    supervisorId: supervisorId,
                    deputyId: deputyId,
                    directorId: directorId
                });
            });

            // เก็บค่าจาก <select> ไว้ใน Data Attribute เมื่อมีการเปลี่ยนแปลง
            $('#leaveTable').on('change', '.supervisor-select', function() {
                const row = $(this).closest('tr');
                const supervisorId = $(this).val() || '0';
                row.data('supervisor-id', supervisorId);
                console.log(`Supervisor ID updated for leave ${row.find('td:eq(2)').text()}: ${supervisorId}`);
            });

            $('#leaveTable').on('change', 'select[name="deputy"]', function() {
                const row = $(this).closest('tr');
                const deputyId = $(this).val() || '0';
                row.data('deputy-id', deputyId);
                console.log(`Deputy ID updated for leave ${row.find('td:eq(2)').text()}: ${deputyId}`);
            });

            $('#leaveTable').on('change', 'select[name="director"]', function() {
                const row = $(this).closest('tr');
                const directorId = $(this).val() || '0';
                row.data('director-id', directorId);
                console.log(`Director ID updated for leave ${row.find('td:eq(2)').text()}: ${directorId}`);
            });

            $('#leaveTable').on('click', '.approve-btn', function() {
                const leaveId = $(this).data('leavesid');
                const workdays = $(this).data('workdays');
                const row = $(this).closest('tr');
                const rowLevel = parseInt(row.data('level')) || 1;

                // ดึงค่าจาก Data Attribute ก่อน
                let supervisorId = row.data('supervisor-id') || '0';
                let deputyId = row.data('deputy-id') || '0';
                let directorId = row.data('director-id') || '0';

                // ในโหมด Responsive ตรวจสอบว่า Child Row ถูกขยายหรือยัง
                if (table.responsive.hasHidden()) {
                    const responsiveRow = table.row(row);
                    if (!responsiveRow.child.isShown()) {
                        responsiveRow.child.show();
                        // รอให้ Child Row ถูกเรนเดอร์
                        setTimeout(() => {
                            // อัพเดทค่าใน Data Attribute จาก <select> ใน Child Row
                            const childRow = row.next('.child');
                            if (childRow.length) {
                                const supervisorSelect = childRow.find(`#supervisorid_${leaveId}`);
                                const deputySelect = childRow.find(`#deputyid_${leaveId}`);
                                const directorSelect = childRow.find(`#directorid_${leaveId}`);

                                if (supervisorSelect.length) {
                                    supervisorId = supervisorSelect.val() || '0';
                                    row.data('supervisor-id', supervisorId);
                                }
                                if (deputySelect.length) {
                                    deputyId = deputySelect.val() || '0';
                                    row.data('deputy-id', deputyId);
                                }
                                if (directorSelect.length) {
                                    directorId = directorSelect.val() || '0';
                                    row.data('director-id', directorId);
                                }
                            }
                        }, 100); // รอ 100ms เพื่อให้ Child Row ถูกเรนเดอร์
                    }
                }

                // ถ้ายังไม่ได้ค่า ให้ดึงจาก <select> โดยตรง
                const supervisorSelect = $(`#supervisorid_${leaveId}`);
                const deputySelect = $(`#deputyid_${leaveId}`);
                const directorSelect = $(`#directorid_${leaveId}`);

                if (supervisorSelect.length && supervisorId === '0') {
                    supervisorId = supervisorSelect.val() || '0';
                }
                if (deputySelect.length && deputyId === '0') {
                    deputyId = deputySelect.val() || '0';
                }
                if (directorSelect.length && directorId === '0') {
                    directorId = directorSelect.val() || '0';
                }

                // ดีบั๊กค่าที่ดึงมา
                console.log('Debug values before sending:', {
                    leaveId: leaveId,
                    workdays: workdays,
                    rowLevel: rowLevel,
                    supervisorId: supervisorId,
                    deputyId: deputyId,
                    directorId: directorId
                });

                // ตรวจสอบสำหรับรองผู้อำนวยการ (level 3)
                if (rowLevel < 4 && directorId === '0') {
                    Swal.fire({
                        title: 'คำเตือน',
                        text: 'คุณไม่ได้เลือกผู้อำนวยการ ต้องการดำเนินการต่อหรือไม่?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'ดำเนินการต่อ',
                        cancelButtonText: 'ยกเลิก'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            sendApprovalRequest(leaveId, workdays, supervisorId, deputyId, directorId);
                        }
                    });
                } else {
                    sendApprovalRequest(leaveId, workdays, supervisorId, deputyId, directorId);
                }
            });

            function sendApprovalRequest(leaveId, workdays, supervisorId, deputyId, directorId) {
                // ส่งข้อมูล
                const dataToSend = {
                    id: leaveId,
                    supervisor_id: supervisorId,
                    deputy_id: deputyId,
                    director_id: directorId,
                    workdays: workdays
                };

                // ดีบั๊กข้อมูลที่ส่ง
                console.log('Data being sent to approve_leave.php:', dataToSend);

                $.ajax({
                    url: 'process/approve_leave.php',
                    type: 'POST',
                    data: dataToSend,
                    dataType: 'json',
                    success: function(response) {
                        console.log('Response from approve_leave.php:', response);
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'ดำเนินการสำเร็จ!',
                                text: 'ทำการอนุมัติเรียบร้อยแล้ว',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                window.location.href = 'leavestatus.php';
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'เกิดข้อผิดพลาด!',
                                text: response.message || 'ไม่สามารถดำเนินการได้'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        let errorMessage = 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์';
                        try {
                            const response = JSON.parse(xhr.responseText);
                            errorMessage = response.message || errorMessage;
                        } catch (e) {
                            errorMessage = xhr.responseText || error;
                        }
                        console.error('Error:', errorMessage);
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด!',
                            text: errorMessage
                        });
                    }
                });
            }

            $('#leaveTable').on('click', '.reject-btn', function() {
                const leaveId = $(this).data('id');
                console.log('Debug values:', { leaveId: leaveId });

                Swal.fire({
                    title: 'ยืนยันการไม่อนุมัติ?',
                    text: "คุณต้องการไม่อนุมัติใบลานี้ใช่หรือไม่?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'ใช่, ไม่อนุมัติ!',
                    cancelButtonText: 'ยกเลิก'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'process/reject_leave.php',
                            type: 'POST',
                            data: { leave_id: leaveId },
                            dataType: 'json',
                            success: function(response) {
                                if (response.status === 'success') {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'ดำเนินการสำเร็จ!',
                                        text: 'ทำการไม่อนุมัติเรียบร้อยแล้ว',
                                        showConfirmButton: false,
                                        timer: 1500
                                    }).then(() => {
                                        window.location.href = 'leavestatus.php';
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'เกิดข้อผิดพลาด!',
                                        text: response.message || 'ไม่สามารถดำเนินการได้'
                                    });
                                }
                            },
                            error: function(xhr, status, error) {
                                let errorMessage = 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์';
                                try {
                                    const response = JSON.parse(xhr.responseText);
                                    errorMessage = response.message || errorMessage;
                                } catch (e) {
                                    errorMessage = xhr.responseText || error;
                                }
                                console.error('Error:', errorMessage);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'เกิดข้อผิดพลาด!',
                                    text: errorMessage
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>
<?php $conn->close(); ?>