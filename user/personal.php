<?php
include('conn/conn.php');

$sql3 = "SELECT * FROM staffstatus";
$result3 = $conn->query($sql3);

$sql5 = "SELECT *  FROM position";
$result5 = $conn->query($sql5);

$sql6 = "SELECT *  FROM prefix";
$result6 = $conn->query($sql6);

$sql7 = "SELECT *  FROM subdepart";
$result7 = $conn->query($sql7);

$sql8 = "SELECT *  FROM headepart";
$result8 = $conn->query($sql8);

$sql4 = "SELECT employees.id,employees.fname,employees.lname,staffstatus.staffid, CONCAT(prefix.prefixname,employees.fname, ' ', employees.lname) AS fullname, 
                employees.department,subdepart.subdepartname,staffstatus.staffname AS staffstatus ,position.positionname,position.positionid,startwork,prefixid,
                employees.email,employees.pic,headepart.headepartname,password
         FROM employees 
         LEFT JOIN staffstatus ON employees.staffstatus = staffstatus.staffid
         LEFT JOIN subdepart ON employees.department = subdepart.subdepartid
         LEFT JOIN prefix on employees.prefix = prefix.prefixid
         LEFT JOIN position on employees.position = position.positionid
         LEFT JOIN headepart on subdepart.headepartid = headepart.headepartid
         ";

$result4 = $conn->query($sql4);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!--=============== REMIXICONS ===============-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.2.0/remixicon.css">
    <!--=============== CSS ===============-->
    <link rel="stylesheet" href="assets/css/styles.css">
    <!-- รวม FullCalendar CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">
    <!-- FullCalendar JS -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <link rel="stylesheet" href="assets/css/table.css">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <link rel="stylesheet" href="assets/css/table.css">
    <!-- Add SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Dashboard</title>
</head>
<style>
    .border-left-primary {
        border-left: 4px solid #4e73df !important;
    }

    .border-left-success {
        border-left: 4px solid #1cc88a !important;
    }

    .border-left-warning {
        border-left: 4px solid #f6c23e !important;
    }

    .border-left-danger {
        border-left: 4px solid #e74a3b !important;
    }

    .text-gray-300 {
        color: #dddfeb !important;
    }

    .text-gray-800 {
        color: #5a5c69 !important;
    }

    .shadow {
        box-shadow: 0 .15rem 1.75rem 0 rgba(58, 59, 69, .15) !important;
    }

    .border-left-info {
        border-left: 4px solid #36b9cc !important;
    }
</style>

<body>
    <!-- Include Sidebar -->
    <?php include('component/sidebar.php'); ?>

    <!--=============== MAIN ===============-->
    <main class="main container3" id="main">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">ข้อมูลส่วนตัว</h3>
                <div>
                    <button type="button" class="btn btn-primary btn-sm me-2" data-bs-toggle="modal" data-bs-target="#editProfilePic">
                        <i class="ri-camera-line"></i> แก้ไขรูป
                    </button>
                    <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editProfile">
                        <i class="ri-edit-line"></i> แก้ไขข้อมูล
                    </button>
                </div>
            </div>
            <div class="card-body">
                <?php while ($row = $result4->fetch_assoc()) {
                    if ($row['id'] == $userID) { ?>
                        <div class="row">
                            <img src="../admin/uploads/<?php echo $row['pic']; ?>" alt="Profile" class="img-fluid rounded-circle" style="width: 200px; height: 200px; object-fit: cover;">
                            <div class="col-md-8">
                                <div class="row mb-3">
                                    <input type="hidden" class="form-control" value="<?php echo $row['staffid']; ?>" readonly>
                                    <div class="col-md-6">
                                        <label class="form-label">ชื่อ-นามสกุล</label>
                                        <input type="text" class="form-control" value="<?php echo $row['fullname']; ?>" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">หน่วยงาน</label>
                                        <input type="text" class="form-control" value="<?php echo $row['headepartname']; ?>" readonly>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">งาน</label>
                                        <input type="text" class="form-control" value="<?php echo $row['subdepartname']; ?>" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">ตำแหน่ง</label>
                                        <input type="text" class="form-control" value="<?php echo $row['positionname']; ?>" readonly>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">สถานะ</label>
                                        <input type="text" class="form-control" value="<?php echo $row['staffstatus']; ?>" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">อีเมล</label>
                                        <input type="email" class="form-control" value="<?php echo $row['email']; ?>" readonly>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">วันที่เริ่มงาน</label>
                                        <input type="text" class="form-control" value="<?php echo date('d/m/Y', strtotime($row['startwork'])); ?>" readonly>
                                    </div>

                                </div>
                            </div>
                        </div>
                <?php }
                } ?>
            </div>
        </div>
    </main>

    <!-- Modal for Profile Picture Edit -->
    <div class="modal fade" id="editProfilePic" tabindex="-1" aria-labelledby="editProfilePicLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProfilePicLabel">แก้ไขรูปโปรไฟล์</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="update_profile_pic.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?php echo $userID; ?>">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="profilePic" class="form-label">เลือกรูปภาพ</label>
                            <input type="file" class="form-control" id="profilePic" name="profilePic" accept="image/*" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                        <button type="submit" class="btn btn-primary">บันทึก</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal for Profile Edit -->
    <div class="modal fade" id="editProfile" tabindex="-1" aria-labelledby="editProfileLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProfileLabel">แก้ไขข้อมูลส่วนตัว</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="update_profile.php" method="POST">
                    <?php 
                    // Reset the result pointer and fetch the row data again
                    $result4->data_seek(0);
                    while ($row = $result4->fetch_assoc()) {
                        if ($row['id'] == $userID) { 
                    ?>
                    <input type="hidden" name="id" value="<?php echo $userID; ?>">
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">คำนำหน้า</label>
                                <select class="form-select" name="prefix">
                                    <?php 
                                    $result6->data_seek(0);
                                    while($prefix = $result6->fetch_assoc()) { ?>
                                        <option value="<?php echo $prefix['prefixid']; ?>" <?php echo ($prefix['prefixid'] == $row['prefixid']) ? 'selected' : ''; ?>>
                                            <?php echo $prefix['prefixname']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">อีเมล</label>
                                <input type="email" class="form-control" name="email" value="<?php echo $row['email']; ?>">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">ชื่อ</label>
                                <input type="text" class="form-control" name="fname" value="<?php echo $row['fname']; ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">นามสกุล</label>
                                <input type="text" class="form-control" name="lname" value="<?php echo $row['lname']; ?>">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">รหัสผ่าน</label>
                                <input type="password" class="form-control" name="password" id="password" placeholder="ใส่รหัสผ่านใหม่หากต้องการเปลี่ยน" oninput="checkPasswordMatch()">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">ยืนยันรหัสผ่าน</label>
                                <input type="password" class="form-control" name="confirm_password" id="confirm_password" placeholder="ยืนยันรหัสผ่านใหม่" oninput="checkPasswordMatch()">
                                <small id="passwordMatch" class="form-text"></small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                        <button type="submit" class="btn btn-primary">บันทึก</button>
                    </div>
                    <?php 
                        }
                    } 
                    ?>
                </form>
            </div>
        </div>
    </div>
</body>
<!-- Add Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<!--=============== MAIN JS ===============-->
<script src="assets/js/main.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>



</html>

<script>
function checkPasswordMatch() {
    var password = document.getElementById('password').value;
    var confirmPassword = document.getElementById('confirm_password').value;
    var matchText = document.getElementById('passwordMatch');
    
    if (password === '' && confirmPassword === '') {
        matchText.innerHTML = '';
        return;
    }
    
    if (password === confirmPassword) {
        matchText.style.color = 'green';
        matchText.innerHTML = 'รหัสผ่านตรงกัน';
    } else {
        matchText.style.color = 'red';
        matchText.innerHTML = 'รหัสผ่านไม่ตรงกัน';
    }
}
</script>

