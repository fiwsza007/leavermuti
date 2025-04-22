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

    <title>ปฏิทินการลา</title>
</head>

<body>
    <?php include('component/sidebar.php'); ?>
    
    <main class="main container3" id="main">
        <div class="container-fluid">
            <div class="row mt-3">
                <div class="col-md-8 mx-auto">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0">ปฏิทินการลา</h5>
                        </div>
                        <div class="card-body">
                            <div id="calendar"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <style>
        .fc-day-today {
            background-color: rgb(249, 144, 64) !important;
        }

        .fc-event {
            cursor: pointer;
        }

        .holiday-event {
            background-color: #ff4d4d;
            border: none;
        }

        /* Add these styles for a more compact calendar */
        .card-body {
            max-width: 800px;
            margin: 0 auto;
        }

        .fc {
            font-size: 0.9em;
        }

        .fc .fc-toolbar.fc-header-toolbar {
            margin-bottom: 0.5em;
        }

        .fc .fc-button {
            padding: 0.2em 0.4em;
        }

        .fc .fc-daygrid-day {
            min-height: 50px;
        }

        .fc .fc-daygrid-day-frame {
            min-height: 50px;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'th',
                height: 'auto',
                contentHeight: 600,
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth'
                },
                buttonText: {
                    today: 'วันนี้'
                },
                events: function(fetchInfo, successCallback, failureCallback) {
                    $.ajax({
                        url: 'process/get_leave_events.php',
                        type: 'GET',
                        success: function(response) {
                            successCallback(response);
                        },
                        error: function() {
                            failureCallback();
                        }
                    });
                },
                eventDidMount: function(info) {
                    info.el.title = 'จำนวนผู้ลา: ' + info.event.extendedProps.count + ' คน';
                }
            });
            calendar.render();
        });
    </script>

    <script src="assets/js/main.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>