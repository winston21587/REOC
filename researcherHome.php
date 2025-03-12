<?php include 'Website Loading Screen/loader.php'; ?> <!-- call these for website loading animation -->
<link rel="stylesheet" href="Website Loading Screen/loader.css"> <!-- call these for website loading animation -->
<script src="Website Loading Screen/loader.js"></script> <!-- call these for website loading animation -->
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
<body>

<!-- Header Section -->

<header>
  <a href="#" class="brand">
    <img src="img/logos.png" class="logo">
    <span class="reoc">Research Ethics Oversite Committee Portal</span>
  </a>

  <div class="menu-btn">     
    <span class="burger" ></span>
    <div class="navigation">
      <div class="navigation-items">
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
  
</div>

<section class="home">
      <div class="gradient"></div>
        <img decoding="async" class="img-slide active" src="./img/reocpic.jpg" ></img>
        <img decoding="async" class="img-slide" src="./img/wmsu2.jpg" ></img>
        <img decoding="async" class="img-slide" src="./img/wmsu1.jpg" ></img>
        <img decoding="async" class="img-slide" src="./img/wmsu5.jpg" ></img>
        <img decoding="async" class="img-slide" src="./img/wmsu1.jpg" ></img>


        <div class="content active">
            <h1>Best in Service<br></h1>
            <p>The Research Ethics Oversight Committee (REOC) offers the highest standard of service in Mindanao, ensuring that all research activities adhere to ethical principles and guidelines. With a commitment to safeguarding the rights and welfare of research participants, the REOC provides comprehensive review processes, expert guidance, and timely support. Their dedication to upholding ethical integrity in research has established them as a trusted authority, making them the go-to committee for researchers seeking ethical approval in Mindanao.</p>
          <a href="SubmitFiles.php" style="    transition: 0.3s ease;">Submit Application</a>
        </div>
        <div class="content">
          <h1>Ethical Excellence Guaranteed<br></h1>
          <p>The Research Ethics Oversight Committee (REOC) at Western Mindanao State University (WMSU) provides the best ethical review services in Mindanao. As a leading institution, WMSU ensures that all research projects meet the highest ethical standards, safeguarding participants’ rights and promoting responsible research. Through rigorous evaluation and expert guidance, REOC at WMSU supports researchers by providing swift, transparent, and thorough reviews, positioning the university as a pillar of ethical integrity in the region.</p>
         
        </div>
        <div class="content">
          <h1>High Standards for Research Ethics<br></h1>
          <p>WMSU REOC has been granted Level 2 Accreditation by the Philippine Health Research Ethics Board (PHREB). This Level 2 accreditation is a testament to the committee's dedication and commitment to upholding the highest standards of research ethics. It empowers WMSU REOC to conduct thorough research reviews across all research categories, except clinical trials.</p>
          
        </div>
       
        <div class="slider-navigation">
            <div class="nav-btn active"></div>
            <div class="nav-btn"></div>
            <div class="nav-btn"></div>
        
    </section>





<section class="divider"></section>


 <h1 class="vision"> WMSU-REOC VISION, MISSION, GOALS</h1>
 <div class="section1">


    <div class="slide-container ">
        <div class="slide-content">
            <div class="card-wrapper swiper-wrapper">



                <div class="card swiper-slide">
                    <div class="image-content">
                        <span class="overlay"> <h2 class="name1">Vision</h2></span>
                    </div>

                    <div class="card-content">
                      <br>
                        <p class="description"><?php echo nl2br(htmlspecialchars($vision)); ?></p>
                      <br>
                    </div>
                </div>

                <div class="card swiper-slide">
                  <div class="image-content">
                      <span class="overlay"> <h2 class="name1">Mission</h2></span>
                  </div>

                  <div class="card-content">
                    <br>
                      <p class="description"><?php echo nl2br(htmlspecialchars($mission)); ?></p>
                    <br>
                  </div>
              </div>


              <div class="card swiper-slide">
                <div class="image-content">
                    <span class="overlay"> <h2 class="name1">Goals</h2></span>
                </div>

                <div class="card-content">
                  <br>
                    <p class="description"><?php echo nl2br(htmlspecialchars($goals)); ?></p>
                  <br>
                </div>
            </div>

                
            </div>
        </div>

    </div>
    
   


 

</div>


<section class="divider"></section>
    <h1 class="vision1"> WMSU-REOC FACULTY</h1>
  
<!-- Display Faculty Members -->
<?php
    // Display Faculty Members
    $sqlFaculty = "SELECT id, name, picture FROM faculty_members";
    $resultFaculty = $conn->query($sqlFaculty);

    if ($resultFaculty->num_rows > 0) {
        echo "<div class='gallery'>"; // Change <ul> to <div> with a class for styling
        while ($row = $resultFaculty->fetch_assoc()) {
            $picturePath = "Faculty Members/" . $row['picture'];
            echo "<div class='gallery-item' >
                    <img   src='" . $picturePath . "' alt='" . htmlspecialchars($row['name']) . "' class='faculty-img' >
                  </div>";
        }
        echo "</div>";
    } else {
        echo "<p>No faculty members found.</p>";
    }
?>






    </div>

 


  <section class="divider"></section>
<!-- newfaq -->
  <h2>Frequently Asked Questions</h2>
    <table>
        <tr>
           
            <th>Question</th>
            <th>Answer</th>
            
        </tr>
        <?php
        if ($fresult->num_rows > 0) {
            while ($row = $fresult->fetch_assoc()) {
                echo "<tr>
                       
                        <td>" . htmlspecialchars($row["question"]) . "</td>
                        <td>" . htmlspecialchars($row["answer"]) . "</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No FAQs available</td></tr>";
        }
        ?>
    </table>

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
<?php
    // Display Schedules
    $sqlSchedule = "SELECT id, name, picture FROM Schedule";
    $resultSchedule = $conn->query($sqlSchedule);

    if ($resultSchedule->num_rows > 0) {
        echo "<div class='gallery'>"; // Change <ul> to <div> with a class for styling
        while ($row = $resultSchedule->fetch_assoc()) {
            $picturePath = "Schedules/" . $row['picture'];
            echo "<div class='gallery-item'>
                    <img src='" . $picturePath . "' alt='" . htmlspecialchars($row['name']) . "' class='schedule-img'>
                  </div>";
        }
        echo "</div>";
    } else {
        echo "<p>No schedules found.</p>";
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

