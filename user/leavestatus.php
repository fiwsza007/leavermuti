<?php
// Set timezone to Thailand



?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.2.0/remixicon.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="assets/css/table.css">
    
    <title>สถานะการลา</title>
</head>

<body>
    <?php include('component/sidebar.php'); ?>

    <main class="main container3" id="main">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">สถานะใบลา</h3>
                <p class="card-text mt-2 mb-0">วันที่ปัจจุบัน: <?= $currentDate ?></p>
            </div>
            <div class="card-body">
                <table id="statusTable" class="table table-striped table-bordered dt-responsive nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th>ลำดับ</th>
                            <th>ยื่น</th>
                            <th>สถานะ</th>
                            <th>ชื่อ-นามสกุล</th>
                            <th>ประเภทการลา</th>
                            <th>วันที่เริ่มลา</th>
                            <th>วันที่สิ้นสุด</th>
                            <th>จำนวนวัน</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        include('conn/conn.php');
                        $sql_status = "SELECT leaves.*, CONCAT(pr.prefixname, e.fname, ' ', e.lname) as fullname, leavetype.leavetypename 
                                   FROM leaves
                                   LEFT JOIN employees e ON leaves.employeesid = e.id
                                   LEFT JOIN prefix pr ON e.prefix = pr.prefixid
                                   LEFT JOIN leavetype ON leaves.leavetype = leavetype.leavetypeid
                                   WHERE leaves.employeesid = $userID ";
                        $result_status = mysqli_query($conn, $sql_status);
                        $i = 1;
                        while ($row = mysqli_fetch_assoc($result_status)) {
                        ?>
                            <tr>
                                <td><?php echo $i++; ?></td>
                                <td><?php echo $row['note']; ?></td>  
                                <td style="background-color: <?php
                                                                switch ($row['leavestatus']) {
                                                                    case 'รอหัวหน้าอนุมัติ':
                                                                        echo '#FFE082; color: black'; // สีเหลืองอ่อน
                                                                        break;
                                                                    case 'รอรองผ.อ.นุมัติ':
                                                                        echo '#FFD54F; color: black'; // สีเหลืองกลาง
                                                                        break;
                                                                    case 'รอผ.อ.นุมัติ':
                                                                        echo '#FFC107; color: black'; // สีเหลืองเข้ม
                                                                        break;
                                                                    case 'รออนุมัติ':
                                                                        echo '#FFB300; color: black'; // สีเหลืองส้ม
                                                                        break;
                                                                    case 'อนุมัติ':
                                                                        echo '#198754; color: white';
                                                                        break;
                                                                    case 'รอยกเลิก':
                                                                        echo '#E83F25; color: white';
                                                                        break;    

                                                                    case 'ไม่อนุมัติ':
                                                                        echo '#DC3545; color: white';
                                                                        break;
                                                                }
                                                                ?>">
                                                           
                                    <?php echo $row['leavestatus']; ?>
                                </td>
                                <td><?php echo $row['fullname']; ?></td>

                                <td><?php echo $row['leavetypename']; ?></td>
                                <td><?php echo date('d/m/Y', strtotime($row['leavestart'])); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($row['leaveend'])); ?></td>
                                <td><?php
                                    $start = new DateTime($row['leavestart']);
                                    $end = new DateTime($row['leaveend']);
                                    $interval = new DateInterval('P1D');
                                    $daterange = new DatePeriod($start, $interval, $end->modify('+1 day'));

                                    $workdays = 0;
                                    // Get holidays from database
                                    $sql_holidays = "SELECT holidayday FROM holiday";
                                    $result_holidays = $conn->query($sql_holidays);
                                    $holidays = [];

                                    if ($result_holidays) {
                                        while ($holiday = $result_holidays->fetch_assoc()) {
                                            $holidays[] = $holiday['holidayday'];
                                        }
                                    }

                                    foreach ($daterange as $date) {
                                        // Check if it's a weekday (1-5) AND not a holiday
                                        if ($date->format('N') < 6 && !in_array($date->format('Y-m-d'), $holidays)) {
                                            $workdays++;
                                        }
                                    }
                                    echo $workdays;
                                    ?>
                                </td>


                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script src="assets/js/main.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/responsive.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#statusTable').DataTable({
                responsive: true,
                language: {
                    url: 'assets/js/datatable-lang/th.json'
                }
            });
        });
    </script>
</body>

</html>