<?php
include('conn/conn.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.2.0/remixicon.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="assets/css/table.css">

    <title>Dashboard</title>
</head>
<style>
    .card {
        max-width: 1000px;
        /* เพิ่มความกว้างสูงสุดของ card */
        margin: 2rem auto;
    }

    .table {
        width: 100%;
    }

    .table td:first-child {
        width: 200px;
        /* กำหนดความกว้างคอลัมน์แรก */
        background-color: #f8f9fa;
    }

    .table td:last-child {
        width: 70%;
        /* กำหนดความกว้างคอลัมน์ที่สอง */
    }
</style>

<body>
    <!-- Include Sidebar -->



    <!--=============== MAIN ===============-->
    <main class="main container3" id="main">
        <?php
        include('component/sidebar.php');
        $sql = "SELECT leavetypeid, leavetypename, workage, workageday, staffid FROM leavetype WHERE staffid = '$staffid'";

        // Execute the query
        $result = $conn->query($sql);

        // Initialize an empty array to store the results
        $leaveTypes = array();

        // ตรวจสอบว่าได้ผลลัพธ์หรือไม่
        if ($result->num_rows > 0) {
            // Array ชั่วคราวเพื่อเก็บข้อมูลที่กรองแล้วตาม leavetypename และ staffid
            $filteredLeaveTypes = array();

            // คำนวณอายุงาน (totalMonths)
            $sql1 = "SELECT startwork FROM employees WHERE id = ?";
            $stmt = $conn->prepare($sql1);
            $stmt->bind_param("i", $userID); // ผูกค่า staffid เป็นตัวแปรที่ส่งเข้ามา
            $stmt->execute();
            $result1 = $stmt->get_result();
            $row1 = $result1->fetch_assoc();

            if ($row1 && !empty($row1['startwork'])) {
                $startwork = $row1['startwork'];
                $startworkDateTime = new DateTime($startwork);
                $currentDate = new DateTime();
                $startDate = new DateTime($startwork);
                $interval = $currentDate->diff($startDate);
                $totalMonths = ($interval->y * 12) + $interval->m; // คำนวณเดือนทั้งหมด
            } else {
                // กำหนด totalMonths เป็น 0 หากไม่สามารถดึง startwork ได้
                $totalMonths = 0;
            }

            while ($row = $result->fetch_assoc()) {
                $key = $row['leavetypename'] . '-' . $row['staffid']; // สร้างคีย์เฉพาะตาม leavetypename และ staffid

                // ตรวจสอบว่า workageday >= totalMonths หรือไม่
                if ($row['workageday'] <= $totalMonths) {
                    // ถ้า workageday น้อยกว่าหรือเท่ากับ totalMonths ให้ทำการกรองข้อมูล
                    if (!isset($filteredLeaveTypes[$key])) {
                        $filteredLeaveTypes[$key] = $row;
                    } else {
                        // ถ้า workageday ต่างกัน เลือกแถวที่มี workageday มากที่สุด
                        if ($filteredLeaveTypes[$key]['workageday'] < $row['workageday']) {
                            $filteredLeaveTypes[$key] = $row;
                        }
                        // ถ้า workageday เท่ากัน ให้เลือกแถวที่มี workage น้อยที่สุด
                        else if ($filteredLeaveTypes[$key]['workageday'] == $row['workageday'] && $filteredLeaveTypes[$key]['workage'] > $row['workage']) {
                            $filteredLeaveTypes[$key] = $row;
                        }
                    }
                }
            }

            // เปลี่ยน array แบบ associative เป็น array ที่มีดัชนีเป็นตัวเลข
            $leaveTypes = array_values($filteredLeaveTypes);

            // จัดเรียงข้อมูลตาม leavetypeid จากน้อยไปหามาก
            usort($leaveTypes, function ($a, $b) {
                return $a['leavetypeid'] - $b['leavetypeid'];
            });
        } else {
            echo "No records found";
        }

        // ตัวอย่างการพิมพ์ข้อมูลใน array
        // print_r($leaveTypes);
        // Example of printing the array
        // print_r($leaveTypes);


        $sql = "SELECT leavetypeid, leavetypename,workageday FROM leavetype where staffid = '$staffid'"; //;วดป
        $result = $conn->query($sql);


        $sql1 = "SELECT startwork FROM employees WHERE id = ?";
        $stmt = $conn->prepare($sql1);
        $stmt->bind_param("i", $userID); // ผูกค่า userID เป็นตัวแปรที่ส่งเข้ามา
        $stmt->execute();
        $result1 = $stmt->get_result();
        $row = $result1->fetch_assoc();

        // ตรวจสอบว่ามีข้อมูลหรือไม่
        if ($row) {
            // คำนวณอายุงานจาก startwork
            $startwork = $row['startwork']; // วันที่เริ่มทำงานของพนักงาน

            // แปลงปี พ.ศ. เป็นปี ค.ศ. หากยังเป็น พ.ศ.
            $startworkDateTime = new DateTime($startwork);
            if ($startworkDateTime->format('Y') > date('Y')) {
                $startworkYear = $startworkDateTime->format('Y') - 543;
                $startwork = $startworkYear . $startworkDateTime->format('-m-d');
            }

            $currentDate = new DateTime(); // วันที่ปัจจุบัน
            $startDate = new DateTime($startwork); // แปลง startwork เป็น DateTime
            $interval = $currentDate->diff($startDate); // คำนวณความแตกต่าง

            // แปลงปีและเดือนเป็นจำนวนเดือนรวม
            $totalMonths = ($interval->y * 12) + $interval->m;

            // แสดงผลลัพธ์
            // echo "พนักงานมีอายุงานทั้งหมด: $totalMonths เดือน";
        }




        ?>

        <div class="card ">
            <div class="card-header">
                <h3 class="card-title">แบบฟอร์มการลา</h3>
            </div>
            <div class="card-body">
                <div class="leave-form-container">
                    <!-- Add in <head> section -->
                    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

                    <!-- Fix form fields -->
                    <form id="leaveForm" action="add/process_leave.php" method="post" enctype="multipart/form-data">
                        <table class="table table-bordered">
                            <tbody>
                                <input type="hidden" name="employeeid" value="<?php echo $userID ?>">
                                <tr>
                                    <td><label for="userName" class="form-label">ชื่อ</label></td>
                                    <td>
                                        <input type="text" class="form-control" id="userName" name="userName" value="<?php echo $userName ?>" readonly>
                                    </td>
                                </tr>
                                <tr>
                                    <td><label for="position" class="form-label">ตำแหน่ง</label></td>
                                    <td>
                                        <input type="text" class="form-control" id="position" name="position" value="<?php echo $position ?>" readonly>
                                    </td>
                                </tr>
                                <tr>
                                    <td><label class="form-label">สังกัด</label></td>
                                    <td>
                                        <input type="text" class="form-control" id="headdepart" name="headdepart" value="<?php echo $headdepart ?>" readonly><br>
                                        <input type="text" class="form-control" id="department" name="department" value="<?php echo $department ?>" readonly>

                                    </td>
                                </tr>
                                <tr>
                                    <td><label class="form-label">ประเภทการลา</label></td>
                                    <td>
                                        <select class="form-select" id="leavetype" name="leavetype" required>
                                            <option value="">-- เลือกประเภทการลา --</option>
                                            <?php
                                            // ตรวจสอบว่ามีข้อมูลใน $leaveTypes หรือไม่
                                            if (!empty($leaveTypes)) {
                                                foreach ($leaveTypes as $row2) {
                                                    echo '<option value="' . $row2['leavetypeid'] . '">' . $row2['leavetypename'] . '</option>';
                                                }
                                            } else {
                                                echo '<option value="">ไม่มีข้อมูลประเภทการลา</option>';
                                            }
                                            ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td><label for="leavestart" class="form-label">วันที่เริ่มต้นลา</label></td>
                                    <td><input type="date" class="form-control" id="leavestart" name="leavestart" min="<?php echo date('Y-m-d'); ?>" required></td>
                                </tr>
                                <tr>
                                    <td><label for="leaveend" class="form-label">วันที่สิ้นสุดการลา</label></td>
                                    <td><input type="date" class="form-control" id="leaveend" name="leaveend" min="<?php echo date('Y-m-d'); ?>" required></td>
                                </tr>
                                <tr>
                                    <td><label for="file" class="form-label">สิ่งที่ต้องแนบ</label></td>
                                    <td>
                                        <input type="file" class="form-control" id="file" name="file[]" multiple>
                                        <small class="form-text text-muted"></small>
                                        <small id="fileRequired" class="form-text text-danger" >* จำเป็นต้องแนบใบรับรองแพทย์สำหรับการลาป่วย 2 วันขึ้นไป หรือ ลาคลอดบุตร</small>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="text-center ">
                                        <button type="submit" class="btn btn-custom btn btn-outline-primary">ยืนยัน</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
        </div>
        <!--=============== MAIN JS ===============-->
        <script src="assets/js/main.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Remove duplicate scripts below -->
        <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
        <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
        <script src="https://cdn.datatables.net/responsive/2.4.1/js/responsive.bootstrap5.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            $(document).ready(function() {
                // Date validation
                $('#leavestart, #leaveend').on('change', function() {
                    const startDate = new Date($('#leavestart').val());
                    const endDate = new Date($('#leaveend').val());

                    if (startDate > endDate) {
                        Swal.fire({
                            icon: 'error',
                            title: 'ข้อผิดพลาด!',
                            text: 'วันที่สิ้นสุดต้องมากกว่าหรือเท่ากับวันที่เริ่มต้น'
                        });
                        $(this).val('');
                    }
                });

                $('#leaveForm').on('submit', function(e) {
                    e.preventDefault();

                    if (!$('#leavestart').val() || !$('#leaveend').val()) {
                        Swal.fire({
                            icon: 'error',
                            title: 'ข้อผิดพลาด!',
                            text: 'กรุณาระบุวันที่ให้ครบถ้วน'
                        });
                        return;
                    }

                    var formData = new FormData(this);

                    $.ajax({
                        url: 'add/process_leave.php',
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        dataType: 'json', // Add this line
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'สำเร็จ!',
                                    text: 'บันทึกข้อมูลการลาเรียบร้อยแล้ว',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    window.location.href = 'leavestatus.php';
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'ผิดพลาด!',
                                    text: response.message || 'เกิดข้อผิดพลาดในการบันทึกข้อมูล'
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX Error:', status, error);
                            console.log('Response:', xhr.responseText);
                            Swal.fire({
                                icon: 'error',
                                title: 'ผิดพลาด!',
                                text: 'เกิดข้อผิดพลาดในการเชื่อมต่อ กรุณาลองใหม่อีกครั้ง'
                            });
                        }
                    });
                });
            });
        </script>