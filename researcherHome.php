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
    $_SESSION['csrf_token'] = bin2hex(string: random_bytes(32));
}

// Logout logic
if (isset($_POST['logout'])) {
  session_destroy(); // Destroy the session to log the user out
  header("Location: login.php");
  exit();
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

    $sql = "SELECT rt.study_protocol_title, a.appointment_date
            FROM Researcher_title_informations rt
            JOIN appointments a ON rt.id = a.researcher_title_id
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
// Fetch FAQs
$sql = "SELECT * FROM faq ORDER BY created_at DESC";
$fresult = $conn->query($sql);
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
<link rel="stylesheet" href="./css/ResearchHomePhp.css">
<!-- Include FullCalendar JS -->
<script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/4.2.0/core/main.min.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/4.2.0/daygrid/main.min.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/4.2.0/interaction/main.min.js'></script>

</head>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const menuBtn = document.getElementById("mobile-menu-btn");
    const mobileMenu = document.getElementById("mobile-menu");

    menuBtn.addEventListener("click", function () {
        mobileMenu.classList.toggle("active");
    });

    // Close menu when clicking outside
    document.addEventListener("click", function (event) {
        if (!menuBtn.contains(event.target) && !mobileMenu.contains(event.target)) {
            mobileMenu.classList.remove("active");
        }
    });
});

</script>
<body>
</div>

<div class="HomePageWeb">

<section class="hero-section">
  
  <div class="hero-background">
   <img src="./img/reocpic.jpg" alt="home-bg">
    <div class="overlay"></div>
  </div>

  <div class="hero-content">
    <h1>Upholding Ethical Research Standards</h1>
    <p>
      The Research Ethics Oversight Committee (REOC) at Western Mindanao State
      University (WMSU) ensures that all research meets the highest ethical
      standards. With a commitment to integrity, transparency, and participant
      protection, REOC provides expert review and guidance, making ethical
      approval seamless and efficient.
    </p>
    <p>
      As a Level 2 accredited committee by the Philippine Health Research
      Ethics Board (PHREB), WMSU REOC is a trusted authority for researchers
      across Mindanao, offering thorough evaluations for diverse research
      fields—excluding clinical trials.
    </p>
    <p>Join us in advancing ethical research.</p>

    <a href="SubmitFiles.php" class="btn-submit">Submit Application</a>
  </div>
</section>

<style>

  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
  }

  .hero-section {
    position: relative;
    width: 100%;
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: flex-start;
    overflow: hidden;
  }

  .hero-background {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
  }

  .hero-background img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
  }

  .overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    border-radius: 0 !important;
  }

  .hero-content {
    position: relative;
    z-index: 2;
    max-width: 600px;
    color: #fff;
    padding: 2rem;
    margin-left: 3%;
  }

  .hero-content h1 {
    font-weight: bold;
    font-size: 3rem;
    margin-bottom: 1rem;
  }

  .hero-content p {
    font-size: 1.3rem;
    line-height: 1.6;
    margin-bottom: 1rem;
  }

  .btn-submit {
    display: inline-block;
    background-color: #990101;
    color: #fff;
    padding: 0.75rem 1.5rem;
    border-radius: 5px;
    text-decoration: none;
    transition: background-color 0.3s ease;
    font-size: 1rem;
    font-weight: bold;
  }

  .btn-submit:hover {
    background-color:rgb(0, 0, 0);
  }

  @media (max-width: 768px) {
    .hero-content {
      margin-left: 3%;
      padding: 1rem;
      max-width: 90%;
    }

    .hero-content h1 {
      font-size: 2rem;
    }

    .hero-content p {
      font-size: 1rem;
    }

    .btn-submit {
      font-size: 0.9rem;
      padding: 0.6rem 1.2rem;
    }
  }
</style>


</div>


<br>
<br>

<img src="./img/msg.png" alt="WMSU REOC Mission Visual" class="w-full h-auto max-h-64 object-contain rounded-lg shadow-sm transition-all duration-300 hover:scale-98">
<div class="mission-vision-goals">
    <style>

        * { 
            margin: 0; 
            padding: 0; 
            box-sizing: border-box; 
        }
        
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            background: transparent;
            overflow-x: hidden;
        }

        .msg-container {
            display: flex;
            flex-direction: column;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            gap: 20px;
        }
        .msg2 {
            width: 100%;
            height: auto;
            display: block;
        }

        h3 {
            color: #990101 !important;
            font-size: 2.5rem !important;
            font-weight: 700 !important;
            margin: 1rem 0;
        }

        p {
            font-size: 1rem !important;
            margin-bottom: 1rem;
        }

        .hr {
            opacity: 25%;
            margin: 1.5rem 0;
        }

        .str {
            font-size: 1.2rem !important;
        }
        
        @media (min-width: 768px) {
            .msg-container {
                flex-direction: row;
                align-items: flex-start;
                padding: 40px 20px;
            }
            
            .msg {
                width: 60%;
                padding-right: 40px;
            }
            
            .msg2 {
                width: 40%;
                position: sticky;
                top: 20px;
            }
        }
    </style>
 
<div class="msg-container">
        <div class="msg">
            <hr class="hr">
            <h3>Mission</h3>
            <p>WMSU REOC(CERC) safeguards the general welfare of human participants and animal subjects in the conduct of researches.</p>
            
            <hr class="hr">
            <h3>Vision</h3>
            <p>The Western Mindanao State University Research Ethics Oversight Committee (WMSU REOC) / College Research Ethics Committee (CERC) is an accredited board instituted to conduct ethics review in various fields of researches that involve human participants and animal subjects in the University and the region.</p>
            
            <hr class="hr">
            <h3>Goals</h3>
            <strong class="str">Ethical Review Excellence</strong>
            <p>WMSU REOC is committed to conducting a high-quality and standardized ethical review process to safeguard the rights and welfare of research participants.</p>
            
            <strong class="str">Expert Multidisciplinary Review</strong>
            <p>We establish and maintain a diverse pool of professional reviewers to ensure thorough and efficient evaluations through expedited and full review procedures.</p>
            
            <strong class="str">Commitment to Ethical Compliance</strong>
            <p>We uphold strict adherence to ethical standards in the implementation of all research protocols.</p>
            <hr class="hr">
        </div>
        
        <img class="msg2" src="./img/msg2.png" alt="WMSU REOC Visual">
    </div>
</div>


</div>
<!-- Include Bootstrap (if not already included) -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<section class="divider"></section>
<h1 class="text-center mt-4 fw-bold">WMSU-REOC FACULTY</h1>

<div class="container py-4">
    <div class="row justify-content-center">
        <?php
            $sqlFaculty = "SELECT id, name, picture FROM faculty_members";
            $resultFaculty = $conn->query($sqlFaculty);

            if ($resultFaculty->num_rows > 0) {
                while ($row = $resultFaculty->fetch_assoc()) {
                    $picturePath = "Faculty Members/" . $row['picture'];
                    echo "<div class='col-md-6 col-sm-7 col-12 mb-4'>
                            <div class='card shadow-lg border-0'>
                                <img src='" . $picturePath . "' alt='" . htmlspecialchars($row['name']) . "' class='card-img-top faculty-img'>
                                <div class='card-body text-center'>
                                    <h5 class='card-title fw-semibold'>" . htmlspecialchars($row['name']) . "</h5>
                                </div>
                            </div>
                          </div>";
                }
            } else {
                echo "<p class='text-center text-muted'>No faculty members found.</p>";
            }
        ?>
    </div>
</div>

<style>
 .faculty-img {
    width: 100%;
    height: auto;
    max-width: 100%;
    display: block;
    margin: 0 auto;
    object-fit: contain;
}

@media (min-width: 992px) {
    .faculty-img {
        max-width: 1200px;
    }
    .card {
        max-width: 90%;
        margin: 0 auto; 
    }
}
.container {
    max-width: 100%;
    padding: 0 5vw;
}
</style>






    </div>

 


  <section class="divider"></section>
<!-- newfaq -->
<link rel="stylesheet" href="./css/faq.css">
<script src="./js/faqrh.js"></script>

<div class="faq-wrapper">
<div class="faq-container">
        <h2>Frequently Asked Questions</h2>

        <div class="faq-item">
            <div class="faq-question">How will I know if my research is for exemption? <span>+</span></div>
            <div class="faq-answer">You will be notified through Gmail if your research is exempted from the review process. Keep an eye on your inbox for any official communications regarding your submission.</div>
        </div>

        <div class="faq-item">
            <div class="faq-question">What types of research are typically exempt from review? <span>+</span></div>
            <div class="faq-answer">Certain types of research, especially those involving minimal risk to participants, may be exempt from the full review process. Examples include studies using anonymous surveys, observational studies in public settings, or research involving publicly available data.</div>
        </div>

        <div class="faq-item">
            <div class="faq-question">How can I submit for application? <span>+</span></div>
            <div class="faq-answer">To submit your review application, ensure first that you have the hard copies of the necessary documents that can be downloaded through the downloadables tab.</div>
        </div>

        <div class="faq-item">
            <div class="faq-question">How long does an expedited review take? <span>+</span></div>
            <div class="faq-answer">Expedited reviews take approximately 15 days to be completed.</div>
        </div>

        <div class="faq-item">
            <div class="faq-question">How will I know if my research has changed its status? <span>+</span></div>
            <div class="faq-answer">You will be notified through Gmail within weeks after submission from the review process.</div>
        </div>

        <div class="faq-item">
            <div class="faq-question">What should I do if I haven't received a notification about my research study? <span>+</span></div>
            <div class="faq-answer">If you haven't received a notification, it's best to wait a little longer as the review process can take time. Check your inbox and spam folder.</div>
        </div>

        <div class="faq-item">
            <div class="faq-question">How long does it usually take for a research paper to be reviewed? <span>+</span></div>
            <div class="faq-answer">The time varies depending on several factors. Typically, it can take anywhere from a few weeks to several months.</div>
        </div>

    </div>

    </div>

    

<!-- newfaq -->
<!-- faq temporary close <h1 class="vision1"> FREQUENTLY ASKED QUESTIONS</h1>

<div class="faq-container">
 <div class="acc">
<div class="containeracc">

  <div class="accordion">
    <div class="accordion-item">
      <button id="accordion-button-1" aria-expanded="false"><span class="accordion-title">How long does it usually take for a research paper to be reviewed?</span><span class="icon" aria-hidden="true"></span></button>
      <div class="accordion-content">
        <p>The time it takes for a research paper to be reviewed can vary depending on several factors. Typically, it can take anywhere from a few weeks to several months. Some journals or conferences might provide quicker feedback, while others could take longer due to the complexity of the paper or the availability of reviewers. On average, it’s common to expect an initial review process to take around 1 to 3 months. However, if revisions are required, the overall timeline could extend further as the authors respond to feedback and submit updated versions for further review.</p>
      </div>
    </div>
    <div class="accordion-item">
      <button id="accordion-button-2" aria-expanded="false"><span class="accordion-title">How will I know if my research is for exemption?</span><span class="icon" aria-hidden="true"></span></button>
      <div class="accordion-content">
        <p>You will be notified through Gmail if your research is exempted from the review process. Keep an eye on your inbox for any official communications regarding your submission.</p>
      </div>
    </div>
    <div class="accordion-item">
      <button id="accordion-button-3" aria-expanded="false"><span class="accordion-title">What types of research are typically exempt from review?</span><span class="icon" aria-hidden="true"></span></button>
      <div class="accordion-content">
        <p>Certain types of research, especially those involving minimal risk to participants, may be exempt from the full review process. Examples include studies using anonymous surveys, observational studies in public settings, or research involving publicly available data. However, the determination of exemption will be made by the review board, and you will be notified via Gmail if your study qualifies for exemption.</p>
      </div>
    </div>
    <div class="accordion-item">
      <button id="accordion-button-4" aria-expanded="false"><span class="accordion-title">What should I do if I haven't received a notification about my exemption?</span><span class="icon" aria-hidden="true"></span></button>
      <div class="accordion-content">
        <p>If you haven't received a notification about the exemption of your research, it's best to wait a little longer as the review process can take time. Ensure that you're regularly checking your Gmail inbox and spam folder. If an extended period passes without any updates, you may contact the review board for further clarification.</p>
      </div>
    </div>
    <div class="accordion-item">
      <button id="accordion-button-5" aria-expanded="false"><span class="accordion-title">Who should I contact if I have further questions about my research review?</span><span class="icon" aria-hidden="true"></span></button>
      <div class="accordion-content">
        <p>If you have any further questions regarding your research review or exemption status, you should contact the support team or the specific review board handling your submission. Details on how to reach them are typically provided in the submission guidelines or in previous communications sent to your Gmail. Be sure to use the official contact methods to ensure a timely response.</p>
      </div>
    </div>
  </div>
</div>
</div>
-->










 <!-- Office Schedule Section -->
 <div class="office-schedule">
 
<!-- Display Schedules -->

<!-- if u want with a no image and empty space warning use this: <?php
/*
if (!isset($conn)) {
    die("Database connection error.");
}

$sqlSchedule = "SELECT id, name, picture FROM Schedule";
$resultSchedule = $conn->query($sqlSchedule);

if ($resultSchedule && $resultSchedule->num_rows > 0) {
    echo "<div class='gallery'>";
    while ($row = $resultSchedule->fetch_assoc()) {
        $name = htmlspecialchars($row['name']);
        $picturePath = "Schedules/" . htmlspecialchars($row['picture']);

        if (!empty($row['picture']) && file_exists($picturePath)) {
            echo "<div class='gallery-item'>
                    <img src='$picturePath' alt='$name' class='schedule-img'>
                  </div>";
        }
    }
    echo "</div>";
}
*/
?> -->

<?php

if (!isset($conn)) {
    die("Database connection error.");
}

$sqlSchedule = "SELECT id, name, picture FROM Schedule";
$resultSchedule = $conn->query($sqlSchedule);

if ($resultSchedule && $resultSchedule->num_rows > 0) {
    echo "<div class='gallery'>";
    while ($row = $resultSchedule->fetch_assoc()) {
        $name = htmlspecialchars($row['name']);
        $picturePath = "Schedules/" . htmlspecialchars($row['picture']);

        if (!empty($row['picture']) && file_exists($picturePath)) {
            echo "<div class='gallery-item'>
                    <img src='$picturePath' alt='$name' class='schedule-img'>
                  </div>";
        }
    }
    echo "</div>";
}
?>



</div>

</div>





<!-- Footer Section -->


<!-- partial -->
<script src='https://code.jquery.com/jquery-3.2.1.min.js'></script><script  src="./script.js"></script>


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

</footer>


<!-- partial -->
  <script src='https://code.jquery.com/jquery-3.6.0.min.js'></script>
<script src='https://unpkg.com/feather-icons'></script><script  src="footer.js"></script>













<!-- partial -->
<script  src="./script.js"></script>
<script src="./js/main.js"></script>
<script src="./js/swiper.js"></script>
<script src="./js/footer.js"></script>
<script src="./js/faq.js"></script>

</div>
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


</body>
</html>
