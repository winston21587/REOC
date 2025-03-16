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
require_once 'class/Applicants.php';

$applicants = new Applicants();

// function unnesessary_function(){
// SQL query to fetch all Vision, Mission, and Goal
// $sql_vm = "SELECT * FROM vision_mission ORDER BY FIELD(statement_type, 'Vision', 'Mission', 'Goals'), last_updated DESC";
// $result_vm = $conn->query($sql_vm);

// // Initialize variables
// $vision = '';
// $mission = '';
// $goals = ''; // Single goal variable

// // Check if data is available
// if ($result_vm->num_rows > 0) {
//     // Loop through the result set
//     while ($row = $result_vm->fetch_assoc()) {
//         // Categorize the content based on the statement type
//         if ($row['statement_type'] == 'Vision') {
//             $vision = $row['content'];
//         } elseif ($row['statement_type'] == 'Mission') {
//             $mission = $row['content'];
//         } elseif ($row['statement_type'] == 'Goals') {
//             $goals = $row['content']; // Store only the first goal found
//         }
//     }
// } else {
//     // Default messages if no data is found
//     $vision = 'No Vision statement found.';
//     $mission = 'No Mission statement found.';
//     $goals = 'No Goal has been defined yet.';
// }

// function getResearchTitlesAndAppointments($userId) {
//     // Assuming $conn is your database connection variable
//     global $conn;

//     $sql = "SELECT rt.study_protocol_title 
//             FROM Researcher_title_informations rt
//             WHERE rt.user_id = ?";
    
//     $stmt = $conn->prepare($sql);
//     $stmt->bind_param("i", $userId);
//     $stmt->execute();
//     $result = $stmt->get_result();

//     $titlesAndAppointments = [];
//     while ($row = $result->fetch_assoc()) {
//         $titlesAndAppointments[] = $row;
//     }
//     $stmt->close();

//     return $titlesAndAppointments;
// }
// }
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
    <link rel="stylesheet" href="./css/viewapp.css">
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
	

</head>
<body>

<!-- Header Section -->

<header>
  <a href="#" class="brand">
    <img src="img/logos.png" class="logo">
    <span class="reoc">Research Ethics Oversite Committee Portal</span>
  </a>

  <div class="menu-btn">
    <div class="navigation">
      <div class="navigation-items">
        <a href="researcherHome.php">Home</a>
        <div class="dropdown1">
          <a href="#">Applications</a>
          <div class="dropdown-content1">
            <div class="file-item1">
              <a href="SubmitFiles.php">Submit Application</a>
            </div>
            <div class="file-item1">
              <a href="viewApplications.php">View Applications</a>
            </div>
          </div>
        </div>

        <div class="dropdown">
          <a href="#">Downloadables</a>
          <div class="dropdown-content">
            <div class="file-item">
              <span><strong>Application Form (WMSU-REOC-FR-001)</strong></span>
              <a href="./files/2-FR.002-Application-Form.doc" download>Download</a>
            </div>
            <div class="file-item">
              <span><strong>Study Protocol Assessment Form (WMSU-REOC-FR-004)</strong></span>
              <a href="./files/4-FR.004-Study-Protocol-Assessment-Form-Copy.docx" download>Download</a>
            </div>
            <div class="file-item">
              <span><strong>Informed Consent Assessment Form (WMSU-REOC-FR-005)</strong></span>
              <a href="./files/5-FR.005-Informed-Consent-Assessment-Form (1).docx" download>Download</a>
            </div>
            <div class="file-item">
              <span><strong>Exempt Review Assessment Form (WMSU-REOC-FR-006)</strong></span>
              <a href="./files/6-FR.006-EXEMPT-REVIEW-ASSESSMENT-FORM (1).docx" download>Download</a>
            </div>
          </div>
        </div>

        <a href="./instructions.html">Instructions</a>
     

        <!-- Logout Button -->
        <form method="POST" action="researcherHome.php" style="display: inline;">
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
          <button type="submit" name="logout" class="logout-button">Logout</button>
        </form>
      </div>
    </div>
  </div>
</header>
  
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


  
  

<!-- Main Content -->
<div class="main-content">
<h1 class="vision"> Appointment Schedule</h1>
   
    <button class="schedbtn" id="rescheduleButton" data-user-id="<?php echo htmlspecialchars($user_id); ?>">Reschedule Appointment</button>
    
<div id='calendar'></div>

   <!-- Display research titles and appointments -->
   <?php
    // $titlesAndAppointments = getResearchTitlesAndAppointments($_SESSION['user_id']);
    $titlesAndAppointments = $applicants->getAllApplicants($_SESSION['user_id']);
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
	  
		</div>
		<div class="footer__redes">
		  <ul class="footer__redes-wrapper">
			<li>
			  <a href="#" class="footer__link">
				<i class=""></i>
				Normal Road, Baliwasan, Z.C.
			  </a>
			</li>
			<li>
			  <a href="#" class="footer__link">
				<i class=""></i>
				09112464566
			  </a>
			</li>
			<li>
			  <a href="#" class="footer__link">
				<i class=""></i>
				wmsureoc@gmail.com
			  </a>
			</li>
			<li>
			  <a href="#" class="footer__link">
				<i class="fab fa-phone-alt"></i>
				
			  </a>
			</li>
		  </ul>
		</div>
		<div class="separador"></div>
		<p class="footer__texto">RESEARCH ETHICS OVERSITE COMMITTEE - WMSU</p>
	  </footer>
	

	  
   
  
  
  
  <!-- partial -->

  
	<script src='https://code.jquery.com/jquery-3.6.0.min.js'></script>
	<script src='https://unpkg.com/feather-icons'></script>
	
	  <script src="./js/footer.js"></script>
	  
	

	<script src="./js/fonts.js"></script>
  
  

</body>
</html>