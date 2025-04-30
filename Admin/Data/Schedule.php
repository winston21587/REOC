<?php
    require_once '../../class/Admin.php';
    include '../../class/clean.php';
    session_start();
// Regenerate session ID to prevent fixation
if (!isset($_SESSION['user_id'])) {
    session_regenerate_id(true); // Regenerate session id on first visit
}

// Check if the user is logged in and if their role is 'Admin'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

// Start CSRF token generation if not already set
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$admin = new admin();
$sched = $admin->getSchedule();



$firstDay = date('Y-m-01'); 
$currentMonth = date('F Y'); // Get the current month and year

function getWeekday($date) {
    $timestamp = strtotime($date);
    return date('l', $timestamp); // 'l' (lowercase 'L') gives full textual representation of the day
}
// function getAllDatesFromMonth($month) {
//     $dates = [];
//     $weekdays = [
//         'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'
//     ];
//     $firstDay = date('Y-m-01', strtotime($month)); // First day of the month
//     $lastDay = date('Y-m-t', strtotime($month)); // Last day of the month

//     $currentDate = $firstDay;
//     while ($currentDate <= $lastDay) {
//         $dates[] = $currentDate;
//         $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
//     }


//     foreach ($dates as $date) {
//         if(getWeekday($date) == 'Monday'){
//             $weekdays[0] =+ $date;
//         }
//         if(getWeekday($date) == 'Tuesday'){
//             $weekdays[1] =+ $date;
//         }
//         if(getWeekday($date) == 'Wednesday'){
//             $weekdays[2] =+ $date;
//         }
//         if(getWeekday($date) == 'Thursday'){
//             $weekdays[3] =+ $date;
//         }
//         if(getWeekday($date) == 'Friday'){
//             $weekdays[4] =+ $date;
//         }


//         return $weekdays;
//     }

// }


function getAllDatesFromMonth($month) {
    $weekdays = [
        'Monday' => [],
        'Tuesday' => [],
        'Wednesday' => [],
        'Thursday' => [],
        'Friday' => []
    ];

    $firstDay = date('Y-m-01', strtotime($month)); // First day of the month
    $lastDay = date('Y-m-t', strtotime($month));   // Last day of the month

    $currentDate = $firstDay;
    while ($currentDate <= $lastDay) {
        $weekday = date('l', strtotime($currentDate)); // Get the weekday name
        if (isset($weekdays[$weekday])) {
            $weekdays[$weekday][] = $currentDate; // Add the date to the corresponding weekday
        }
        $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 day')); // Move to the next day
    }

    return $weekdays;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin-Schedule</title>
    <link rel="icon" type="image/x-icon" href="../../img/reoclogo1.jpg">
    <link rel="stylesheet" href="../../sidebar/sidebar.css">
    <link rel="stylesheet" href="../../css/admin.css">
    <link rel="stylesheet" href="../../css/admin-app-data.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src='https://code.jquery.com/jquery-3.6.0.min.js'></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.min.css">
    <script src="https://cdn.datatables.net/2.2.2/js/dataTables.min.js"></script>
</head>

<body>
    <?php require '../../sidebar/sidebar.html' ?>
    <div class="main-content">
        <div class="head-content">
            <h2>Schedule</h2>
            <div>
                <button>Create</button>
            </div>
        </div>

    </div>
    <div>
        <table id="myTable" class="display">
            <thead>
                <tr>
                    <th>Monday</th>
                    <th>Tuesday</th>
                    <th>Wednesday</th>
                    <th>Thursday</th>
                    <th>Friday</th>
                </tr>
            </thead>
            <tbody>
                <?php
        $weekdays = getAllDatesFromMonth($currentMonth); // Replace with the desired month
        $maxRows = max(array_map('count', $weekdays)); // Get the maximum number of rows needed
        for ($i = 0; $i < $maxRows; $i++): ?>
        <tr>
           <?php foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'] as $day): ?>
              <td> <button class="openDate" data-date="<?= $weekdays[$day][$i] ?>"> <?= !empty($weekdays[$day][$i]) ? date('M d', strtotime($weekdays[$day][$i])) : '' ?> </button></td>
           
                <?php endforeach; ?>
        </tr>
        
       <?php endfor; ?>
            </tbody>
        </table>
    </div>

        <div class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2>Schedule</h2>
            </div>
        </div>

</body>
<script>
$(document).ready(function() {
    $('#myTable').DataTable({
        "paging": true, // Enables pagination
        "searching": true, // Enables search box
        "ordering": false, // Enables sorting
        "info": true, // Shows "Showing X of Y entries"
        "lengthMenu": [5, 10, 25, 50], // Controls entries per page
    });
});
$('.close').on('click', function () {
    const modal = $('.modal');
    modal.hide(); // Hide the modal when the close button is clicked
});
$(window).on('click', function (event) {
    const modal = $('.modal');
    if ($(event.target).is(modal)) {
        modal.hide(); // Hide the modal when clicking outside of it
    }
});
$(document).on('click', '.openDate', function () {
    const date = $(this).data('date'); // Get the date from the data-date attribute
    const modal = $('.modal');
    const modalContent = $('.modal-content');
    modalContent.append('<p>' + date + '</p>'); // Append the date to the modal content
    modal.show(); // Show the modal
    // Perform actions with the selected date
});
</script>

</html>