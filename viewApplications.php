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
require_once 'class/clean.php';
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
    <title>REOC APPLICATION</title>
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
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->


    <script>
    // document.addEventListener('DOMContentLoaded', function() {
    //     let calendarEl = document.getElementById('calendar');
    //     let calendar = new FullCalendar.Calendar(calendarEl, {
    //         initialView: 'dayGridMonth',
    //         selectable: true,
    //         events: 'fetch_available_dates.php',  // Fetch available dates dynamically
    //         dateClick: function(info) {
    //             fetchTimeSlots(info.dateStr);
    //         }
    //     });
    //     calendar.render();

    //     function fetchTimeSlots(date) {
    //         $.post("fetch_time_slots.php", { date: date, consultant_id: 1 }, function(response) {
    //             let slots = JSON.parse(response);
    //             let slotsHtml = "<h3>Available Time Slots for " + date + "</h3>";

    //             if (slots.length > 0) {
    //                 slots.forEach(slot => {
    //                     slotsHtml += `<button onclick="book('${date}', '${slot.start_time}', '${slot.end_time}')">
    //                                     ${slot.start_time} - ${slot.end_time}
    //                                   </button><br>`;
    //                 });
    //             } else {
    //                 slotsHtml += "<p>No available slots for this date.</p>";
    //             }

    //             document.getElementById("timeSlots").innerHTML = slotsHtml;
    //         });
    //     }

    //     window.book = function(date, startTime, endTime) {
    //         $.post("book_appointment.php", { 
    //             date: date, 
    //             start_time: startTime, 
    //             end_time: endTime, 
    //             researcher_id: 2, 
    //             consultant_id: 1 
    //         }, function(response) {
    //             alert(response);
    //             calendar.refetchEvents();
    //         });
    //     }
    // });

    //  document.addEventListener('DOMContentLoaded', function () {
    //      const calendarEl = document.getElementById('calendar');
    //      calendarEl.style.display = 'none'; // Initially hide the calendar

    //      let unavailableDates = []; // Unavailable dates
    //      let pendingDates = []; // Pending appointment dates

    //      const calendar = new FullCalendar.Calendar(calendarEl, {
    //          plugins: ['interaction', 'dayGrid'],
    //          defaultView: 'dayGridMonth',
    //          validRange: {
    //              start: new Date() // Prevent selecting past dates
    //          },
    //          businessHours: {
    //              daysOfWeek: [1, 2, 3, 4, 5] // Monday to Friday
    //          },
    //          dateClick: function (info) {
    //              const clickedDate = new Date(info.dateStr);
    //              const dayOfWeek = clickedDate.getDay(); // 0 = Sunday, 6 = Saturday

    //              if (dayOfWeek === 0 || dayOfWeek === 6) {
    //                  Swal.fire('Unavailable!', 'Weekends are not available for scheduling.', 'error');
    //                  return;
    //              }

    //              if (unavailableDates.includes(info.dateStr)) {
    //                  Swal.fire('Unavailable!', 'You cannot select this date as it is unavailable.', 'error');
    //              } else {
    //                  rescheduleAppointment(info.dateStr);
    //              }
    //          }
    //      });

    //      document.getElementById('rescheduleButton').addEventListener('click', function () {
    //          const isDisplayed = calendarEl.style.display;
    //          calendarEl.style.display = isDisplayed === 'block' ? 'none' : 'block';

    //          if (calendarEl.style.display === 'block') {
    //              fetch('getUnavailableDates.php')
    //                  .then(response => response.json())
    //                  .then(data => {
    //                      unavailableDates = Array.isArray(data.unavailableDates) ? data.unavailableDates : [];

    //                      calendar.removeAllEvents();

    //                      // Mark unavailable dates as background events
    //                      unavailableDates.forEach(date => {
    //                          calendar.addEvent({
    //                              start: date,
    //                              allDay: true,
    //                              rendering: 'background',
    //                              color: '#ff9f89' // Highlight unavailable dates
    //                          });
    //                      });

    //                      // Fetch pending appointments
    //                      return fetch('getPendingAppointments.php');
    //                  })
    //                  .then(response => response.json())
    //                  .then(data => {
    //                      pendingDates = Array.isArray(data.pendingDates) ? data.pendingDates : [];

    //                      // Mark pending appointment dates in green with a professional message
    //                      pendingDates.forEach(date => {
    //                          calendar.addEvent({
    //                              start: date,
    //                              allDay: true,
    //                              rendering: 'background',
    //                              color: '#90EE90', // Highlight pending dates in green
    //                              title: 'Your appointment is scheduled on this day.' // Add a professional tooltip
    //                          });
    //                      });

    //                      calendar.render();
    //                  })
    //                  .catch(error => {
    //                      console.error('Error fetching dates:', error);
    //                  });
    //          }
    //      });

    //      calendar.render();

    //      function rescheduleAppointment(newDate) {
    //          Swal.fire({
    //              title: 'Confirm Rescheduling',
    //              text: `Reschedule your appointment to ${newDate}?`,
    //              icon: 'question',
    //              showCancelButton: true,
    //              confirmButtonColor: '#3085d6',
    //              cancelButtonColor: '#d33',
    //              confirmButtonText: 'Yes, reschedule it!'
    //          }).then((result) => {
    //              if (result.isConfirmed) {
    //                  const userId = document.getElementById('rescheduleButton').getAttribute('data-user-id');

    //                  fetch('rescheduleAppointment.php', {
    //                      method: 'POST',
    //                      headers: {
    //                          'Content-Type': 'application/x-www-form-urlencoded',
    //                      },
    //                      body: `newDate=${encodeURIComponent(newDate)}&userId=${encodeURIComponent(userId)}&csrf_token=${encodeURIComponent('<?php echo $_SESSION['csrf_token']; ?>')}`
    //                  })
    //                      .then(response => response.json())
    //                      .then(data => {
    //                          if (data.success) {
    //                              Swal.fire('Rescheduled!', 'Your appointment has been rescheduled.', 'success').then(() => {
    //                                  // Refresh calendar events
    //                                  window.location.href = 'researcherHome.php';
    //                                  calendar.refetchEvents();
    //                              });
    //                          } else {
    //                              Swal.fire('Error!', data.message || 'Could not reschedule. Please try again.', 'error');
    //                          }
    //                      })
    //                      .catch(error => {
    //                          console.error('Error:', error);
    //                          Swal.fire('Error!', 'A network or server error occurred.', 'error');
    //                      });
    //              }
    //          });
    //      }
    //  });
    </script>
    <!--===============================================================================================-->

<body>



        <!-- Header Section -->

        <?php include './navbar/navbar.php'; ?>





        <!-- Main Content -->
        <div class="main-content">
            <h1 class="vision"> Appointment Schedule</h1>

            <!-- <button class="schedbtn" id="rescheduleButton" data-user-id="<?php echo htmlspecialchars($user_id); ?>">Reschedule Appointment</button> -->

            <!-- <div id='calendar'></div>
<div id="timeSlots"></div> -->


            <!-- Display research titles and appointments -->
            <?php
    // $titlesAndAppointments = getResearchTitlesAndAppointments($_SESSION['user_id']);
    $titlesAndAppointments = $applicants->getAllApplicants($_SESSION['user_id']);
    if (!empty($titlesAndAppointments)) {
        echo "<div class='titles-appointments'>";
        echo "<h3>Your Research Titles and Appointments:</h3>";
        echo "<ul class='AppointmentList'>";
        foreach ($titlesAndAppointments as $item) {
          echo "<li> 
          <div>
          <span class='title'>" . htmlspecialchars($item['study_protocol_title']) . "</span>";
          if(!empty($applicants->getAppointedDate($item['id']))){
          $dateOfAppointment = $applicants->getAppointedDate($item['id']);
            echo  "<span class='dateOfAppointment'> Appointed Date: "  . $dateOfAppointment['appointment_date'] . "</span>";
            echo  "<span class='dateOfAppointment'> at " . time_format($dateOfAppointment['start_time']). ' - ' . time_format($dateOfAppointment['end_time']) . "</span> 
            </div>";
        ?>

            <div class="Files">
                <button data-id="<?= $item['id'] ?>" data-title="<?= $item['study_protocol_title'] ?>">View
                    Files</button>
            </div>
            <div class="status">
                <?php if(htmlspecialchars($item['status']) == "Notifying"){ ?>
                <div>
                    <span>Your Certification is ready!</span>
                    <p>please get your certificate at REOC office</p>
                </div>
                <?php }else{ ?>
                <span class="status">Status: <?= htmlspecialchars($item['status']) ?></span>
                <?php } ?>
            </div>

            <?php
          }else{
            echo "could not find date";
          }
          echo "</li>";
        }
        echo "</ul>";   
        echo "</div>";
    } else {

        echo "<p>No submitted files.</p>";
    }
    ?>


        </div>
        <!-- </d>   unknown close tag -->

        <!-- <footer class="footer">
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
	 -->




        <div class="modal" id="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <span class="ResearchtitleModal"></span>
                <h2>Your Files </h2>
                <div>
                    <p> files submitted:</p>
                    <ul class="file-list">

                    </ul>
                </div>
            </div>

        </div>

        <!-- partial -->


        <script src='https://code.jquery.com/jquery-3.6.0.min.js'></script>
        <script>
        $(document).ready(function() {
            const modal = $("#modal");
            const btn = $(".Files button");
            const span = $(".close");
            const modalTitle = $(".ResearchtitleModal");
            const btnID = btn.data("id");
            const btnTitle = btn.data("title");
            btn.on("click", function() {
                modal.css("display", "flex");
                const id = $(this).data("id");
                modalTitle.text("Title: " + btnTitle );
                $.ajax({
                    url: "getFiles.php",
                    type: "POST",
                    data: {
                        id: id
                    },
                    success: function(data) {
                        console.log(data);
                        // const files = JSON.parse(data);
                        const files = data;
                        console.log(files); // Debugging line
                        const fileList = $(".file-list");
                        fileList.empty(); // Clear previous list
                        if (files.length > 0) {
                            files.forEach(function(file) {
                                fileList.append("<li><a href='" + file.file_path +
                                    "' target='_blank'>" + file.filename +
                                    "</a></li>");
                            });
                        } else {
                            fileList.append("<li>No files found.</li>");
                        }
                    },
                    error: function() {
                        console.error("Error fetching files.");
                    }
                });
            });

            span.on("click", function() {
                modal.css("display", "none");
            });

            $(window).on("click", function(event) {
                if ($(event.target).is(modal)) {
                    modal.css("display", "none");
                }
            });
        });
        </script>


    </body>

</html>