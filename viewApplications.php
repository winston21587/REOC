<?php include 'Website Loading Screen/loader.php'; ?> <!-- call these for website loading animation -->
<link rel="stylesheet" href="Website Loading Screen/loader.css"> <!-- call these for website loading animation -->
<script src="Website Loading Screen/loader.js"></script> <!-- call these for website loading animation -->
<?php include './navbar/navbar.php'; ?> <!-- call these for the navbar -->
<?php
session_start();

// Regenerate session ID to prevent fixation
if (!isset($_SESSION['user_id'])) {
    session_regenerate_id(true); // Regenerate session id on first visit
}

// Check if the user is logged in and if their role is 'Researcher'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Researcher') {
    header("Location: login.php");
    exit();
}

// Start CSRF token generation if not already set
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Logout logic
if (isset($_POST['logout'])) {
    // Validate CSRF token
    if (hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        session_destroy(); // Destroy the session to log the user out
        header("Location: login.php");
        exit();
    } else {
        // Handle CSRF token validation failure (optional)
        echo "<script>alert('Invalid CSRF token.');</script>";
    }
}
require_once 'dbConnCode.php';
// SQL query to fetch all Vision, Mission, and Goal
$sql_vm = "SELECT * FROM vision_mission ORDER BY FIELD(statement_type, 'Vision', 'Mission', 'Goals'), last_updated DESC";
$result_vm = $conn->query($sql_vm);

// Initialize variables
$vision = '';
$mission = '';
$goals = ''; // Single goal variable

// Check if data is available
if ($result_vm->num_rows > 0) {
    // Loop through the result set
    while ($row = $result_vm->fetch_assoc()) {
        // Categorize the content based on the statement type
        if ($row['statement_type'] == 'Vision') {
            $vision = $row['content'];
        } elseif ($row['statement_type'] == 'Mission') {
            $mission = $row['content'];
        } elseif ($row['statement_type'] == 'Goals') {
            $goals = $row['content']; // Store only the first goal found
        }
    }
} else {
    // Default messages if no data is found
    $vision = 'No Vision statement found.';
    $mission = 'No Mission statement found.';
    $goals = 'No Goal has been defined yet.';
}

function getResearchTitlesAndAppointments($userId) {
    // Assuming $conn is your database connection variable
    global $conn;

    $sql = "SELECT rt.study_protocol_title 
            FROM Researcher_title_informations rt
            WHERE rt.user_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    $titlesAndAppointments = [];
    while ($row = $result->fetch_assoc()) {
        $titlesAndAppointments[] = $row;
    }
    $stmt->close();

    return $titlesAndAppointments;
}
?>





<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REOC PORTAL</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css">
    <script src="//cdn.jsdelivr.net/gh/freeps2/a7rarpress@main/swiper-bundle.min.js"></script>
    <link rel="stylesheet" href="./css/styles.css">
    <link rel="stylesheet" href="./css/swiper.css">
    <link rel="icon" type="image/x-icon" href="./img/reoclogo1.jpg">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Include FullCalendar CSS -->
<link href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/4.2.0/core/main.min.css' rel='stylesheet' />
<link href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/4.2.0/daygrid/main.min.css' rel='stylesheet' />

<!-- Include FullCalendar JS -->
<script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/4.2.0/core/main.min.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/4.2.0/daygrid/main.min.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/4.2.0/interaction/main.min.js'></script>
<!--===============================================================================================-->
<body>
	
<style>

header {
    z-index: 999;
   
    position: sticky; 
    top: 0; 
    left: 0;
    width: 100%;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 200px;
    transition: 0.5s ease;
    background-color: #ffffff; 
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.logo{
    position: relative;
    height: 43px;
    margin-right: 10px; 
    top:12px;
    width: 19%;
}

header .brand{
 margin-bottom: 10px;

   color: #a14242;
     right: 50%;
    font-size: 1.5rem;
    font-weight: 700;
    text-transform: uppercase;
    text-decoration: none;

}


.reoc{
    position: relative;
    top: 14px;
}

header .brand:hover{
    color: #990101;
}

header .navigation{
    position: relative;
}

header .navigation .navigation-items a{
    position: relative;
    top: 5px;
    color : #a14242;
    font-size: 1em;
    font-weight: 700;
    text-decoration: none;
    margin-left: 30px;
    transition: 0.3s ease;
}

header .navigation .navigation-items a:before{
    content: '';
    position: absolute;
    background: #990101;
    width: 0;
    height: 3px;
    bottom: 0;
    left: 0;
    transition: 0.3s ease;
}

header .navigation .navigation-items a:hover:before{
    width: 100%;
    background: #990101;
}
     
        .logout-button {
            background-color: #dc3545;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-left: 20px; /* Space between navbar and logout button */
        }
        .logout-button:hover {
            background-color: #c82333;
        }
        .main-content {
          position: relative;
         margin-left: 550px;
            flex: 1;
            padding: 20px;


          
  justify-content: center;   /* Centers horizontally */
  align-items: center;       /* Centers vertically */
    
        }
       
         /* Calendar Styling */
         #calendar {
          position: relative;
        
            max-width: 600px; /* Adjust the width of the calendar */
          
            padding: 10px;
            margin-top: 30px;
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .fc-header-toolbar {
            font-size: 14px; /* Reduce header font size */
        }
        .fc-day {
            font-size: 12px; /* Reduce day cell font size */
        }
        .fc-title {
            font-size: 10px; /* Reduce event title font size */
        }
        .titles-appointments {
          width: fit-content;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    padding: 20px;
    margin-top: 20px;
    position: relative;
    left: -170px;
}

.titles-appointments h3 {
    color: #800000; /* Use the same color as header for consistency */
    margin-top: 0;
}

.titles-appointments ul {
    list-style: none;
    padding: 0;
    margin: 10px 0 0 0;
}

.titles-appointments li {
    padding: 10px;
    border-bottom: 1px solid #eee;
    margin-bottom: 5px;
    color: #333;
}

.titles-appointments li:last-child {
    border-bottom: none;
}

.title, .appointment {
    display: block; /* Makes it easier to read on separate lines */
    font-weight: bold;
}

.appointment {
    margin-top: 5px;
    font-weight: normal;
    color: #555;
}


.faculty-img{
    width: 50%;
    height: 100%;
    padding-bottom: 50px;
    padding-top: 20px;
    justify-content: center;
    align-items: center;
}


.office-schedule{
    position: relative;
    left: 200px;
}


.vision {
    background-color: #F8F7F4;
    position: relative;
    padding-top: 100px;
    left: -270px;
    text-align: center;
}

.schedbtn {
  background-color: #a14242; /* Green background */
  color: white;             /* White text */
  font-size: 16px;          /* Adjust font size */
  padding: 10px 10px;       /* Padding for size */
  margin-top: 50px;
  border: none;             /* Remove default border */
  border-radius: 5px;       /* Rounded corners */
  cursor: pointer;          /* Pointer cursor on hover */
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Subtle shadow */
  transition: background-color 0.3s ease, transform 0.2s ease; /* Smooth transitions */
}

.schedbtn:hover {
  background-color: #812b2c;

  transform: scale(1.05);    /* Slightly larger on hover */
}


.title{
  width: 1100px; /* Set a specific width */
    /* white-space: nowrap;  Prevent text from wrapping */
    overflow: hidden; /* Hide the overflow */
 /*    text-overflow: ellipsis; Add ellipsis when the text overflows */
    text-align: center;
    overflow-wrap: break-word;
    word-wrap: break-word;
    

}
.title:hover{
  /* word-wrap: break-word;  Old method */
    /* overflow-wrap: break-word;  Modern method */
    /* width: 1100px;  Set a width to test */
}


    </style>
</head>
<body>

<!-- Main Content -->
<div class="main-content">
<h1 class="vision"> Appointment Schedule</h1>
   
    <button class="schedbtn" id="rescheduleButton" data-user-id="<?php echo htmlspecialchars($user_id); ?>">Reschedule Appointment</button>
    
<div id='calendar'></div>

   <!-- Display research titles and appointments -->
   <?php
    $titlesAndAppointments = getResearchTitlesAndAppointments($_SESSION['user_id']);
    if (!empty($titlesAndAppointments)) {
        echo "<div class='titles-appointments'>";
        echo "<h3>Your Research Titles and Appointments:</h3>";
        echo "<ul>";
        foreach ($titlesAndAppointments as $item) {
            echo "<li><span class='title'>" . htmlspecialchars($item['study_protocol_title']) . "</span></li>";
        }
        echo "</ul>";
        echo "</div>";
    } else {

        echo "<p>No submitted files.</p>";
    }
    ?>
</div>
 </d>   <!-- unknown close tag -->




<script>
document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');
    calendarEl.style.display = 'none'; // Initially hide the calendar

    let unavailableDates = []; // Unavailable dates
    let pendingDates = []; // Pending appointment dates

    const calendar = new FullCalendar.Calendar(calendarEl, {
        plugins: ['interaction', 'dayGrid'],
        defaultView: 'dayGridMonth',
        validRange: {
            start: new Date() // Prevent selecting past dates
        },
        businessHours: {
            daysOfWeek: [1, 2, 3, 4, 5] // Monday to Friday
        },
        dateClick: function (info) {
            const clickedDate = new Date(info.dateStr);
            const dayOfWeek = clickedDate.getDay(); // 0 = Sunday, 6 = Saturday

            if (dayOfWeek === 0 || dayOfWeek === 6) {
                Swal.fire('Unavailable!', 'Weekends are not available for scheduling.', 'error');
                return;
            }

            if (unavailableDates.includes(info.dateStr)) {
                Swal.fire('Unavailable!', 'You cannot select this date as it is unavailable.', 'error');
            } else {
                rescheduleAppointment(info.dateStr);
            }
        }
    });

    document.getElementById('rescheduleButton').addEventListener('click', function () {
        const isDisplayed = calendarEl.style.display;
        calendarEl.style.display = isDisplayed === 'block' ? 'none' : 'block';

        if (calendarEl.style.display === 'block') {
            fetch('getUnavailableDates.php')
                .then(response => response.json())
                .then(data => {
                    unavailableDates = Array.isArray(data.unavailableDates) ? data.unavailableDates : [];
                    
                    calendar.removeAllEvents();

                    // Mark unavailable dates as background events
                    unavailableDates.forEach(date => {
                        calendar.addEvent({
                            start: date,
                            allDay: true,
                            rendering: 'background',
                            color: '#ff9f89' // Highlight unavailable dates
                        });
                    });

                    // Fetch pending appointments
                    return fetch('getPendingAppointments.php');
                })
                .then(response => response.json())
                .then(data => {
                    pendingDates = Array.isArray(data.pendingDates) ? data.pendingDates : [];

                    // Mark pending appointment dates in green with a professional message
                    pendingDates.forEach(date => {
                        calendar.addEvent({
                            start: date,
                            allDay: true,
                            rendering: 'background',
                            color: '#90EE90', // Highlight pending dates in green
                            title: 'Your appointment is scheduled on this day.' // Add a professional tooltip
                        });
                    });

                    calendar.render();
                })
                .catch(error => {
                    console.error('Error fetching dates:', error);
                });
        }
    });

    calendar.render();

    function rescheduleAppointment(newDate) {
        Swal.fire({
            title: 'Confirm Rescheduling',
            text: `Reschedule your appointment to ${newDate}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, reschedule it!'
        }).then((result) => {
            if (result.isConfirmed) {
                const userId = document.getElementById('rescheduleButton').getAttribute('data-user-id');

                fetch('rescheduleAppointment.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `newDate=${encodeURIComponent(newDate)}&userId=${encodeURIComponent(userId)}&csrf_token=${encodeURIComponent('<?php echo $_SESSION['csrf_token']; ?>')}`
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Rescheduled!', 'Your appointment has been rescheduled.', 'success').then(() => {
                                // Refresh calendar events
                                window.location.href = 'researcherHome.php';
                                calendar.refetchEvents();
                            });
                        } else {
                            Swal.fire('Error!', data.message || 'Could not reschedule. Please try again.', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Error!', 'A network or server error occurred.', 'error');
                    });
            }
        });
    }
});

</script>



	<footer class="footer">
		<div class="owl-carousel">
	  
		  <a href="#" class="gallery__photo">
			<img src="img/wmsu55.jpg" alt="" />
		 
		  </a>
		  <a href="#" class="gallery__photo">
			<img src="img/wmsu11.jpg" alt="" />
		  
		  </a>
		  <a href="#" class="gallery__photo">
			<img src="img/reoc11.jpg" alt="" />
		   
		  </a>
		  <a href="#" class="gallery__photo">
			<img src="img/wmsu22.jpg" alt="" />
		  
		  </a>
		  <a href="#" class="gallery__photo">
			<img src="img/reoc22.jpg" alt="" />
		   
		  </a>
		  <a href="#" class="gallery__photo">
			<img src="img/wmsu44.jpg" alt="" />
		   
		  </a>
	  
	  </footer>
	

	  
   
  
  
  
  <!-- partial -->

  
	<script src='https://code.jquery.com/jquery-3.6.0.min.js'></script>
	<script src='https://unpkg.com/feather-icons'></script>
	
	  <script src="./js/footer.js"></script>
	  
	

	<script src="./js/fonts.js"></script>
  
  

</body>
</html>
