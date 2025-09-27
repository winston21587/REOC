<?php include 'Website Loading Screen/loader.php'; ?> <!-- call these for website loading animation -->
<?php
// Database connection
require 'dbConnCode.php'; // Replace with your database connection file
require 'class/Admin.php'; // Include the Admin class for fetching data
// Fetch mission, vision, and goals from the database
$query = "SELECT statement_type, content FROM vision_mission";
$result = $conn->query($query);

$vms = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $vms[strtolower($row['statement_type'])] = $row['content']; // Use lowercase keys for consistency
    }
}

// Default values if no data is found
$mission = $vms['mission'] ?? 'Mission content not available.';
$vision = $vms['vision'] ?? 'Vision content not available.';
$goals = $vms['goals'] ?? 'Goals content not available.';



// Fetch FAQs from the database
$query = "SELECT question, answer FROM faq";
$result = $conn->query($query);

$faqs = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $faqs[] = [
            'question' => $row['question'],
            'answer' => $row['answer']
        ];
    }
}

    $admin = new admin();
    $cmsData = $admin->getcmsData();


    function getValue($cmsData, $type) {
    foreach ($cmsData as $row) {
        if ($row['type'] === $type) {
            return $row['content']; // Return the matching row
        }
    }
    return null; // Return null if no match is found
}

?>

<link rel="stylesheet" href="Website Loading Screen/loader.css"> <!-- call these for website loading animation -->
<script src="Website Loading Screen/loader.js"></script> <!-- call these for website loading animation -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>WMSUReoc</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"/>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    .fade-in {
     opacity: 0;
     transform: translateY(20px);
     transition: opacity 1s ease-out, transform 1s ease-out;
    }
    .fade-in.visible {
    opacity: 1;
    transform: translateY(0);
    }
    html, body {
      height: 100%;
      font-family: 'Montserrat', sans-serif;
      background: #f4f4f4;
    }
    nav {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 60px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 0 30px;
      z-index: 1000;
      transition: top 0.3s, background-color 0.3s, color 0.3s;
      background-color: transparent;
    }
    .nav-left {
      display: flex;
      align-items: center;
    }
    .logo {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      margin-right: 10px;
      overflow: hidden;
    }
    .logo img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    .nav-left h1, .nav-right a, .nav-right button {
      color: white;
      transition: color 0.3s;
    }
    .nav-right {
      display: flex;
      align-items: center;
    }
    .nav-right a {
      margin-right: 15px;
      font-size: 1rem;
      text-decoration: none;
      cursor: pointer;
    }
    .nav-right button {
      border: none;
      padding: 9px 22px;
      cursor: pointer;
      background: linear-gradient(45deg, #8B0000, #B22222);
      border-radius: 4px;
      font-size: 1rem;
      color: white;
    }
    .hamburger {
      display: none;
      font-size: 1.5rem;
      color: white;
      cursor: pointer;
    }
    .mobile-menu {
      display: none;
      position: fixed;
      top: 60px;
      right: 0;
      background: rgba(255,255,255,0.95);
      width: 200px;
      box-shadow: -2px 0 5px rgba(0,0,0,0.2);
      flex-direction: column;
      z-index: 1000;
    }
    .mobile-menu a, .mobile-menu button {
      color: black;
      padding: 15px;
      border-bottom: 1px solid #ddd;
      text-decoration: none;
      text-align: left;
      background: none;
      font-size: 1rem;
      border: none;
      cursor: pointer;
    }
    .slider-container {
      position: relative;
      width: 100%;
      height: 100vh;
      overflow: hidden;
    }
    .slide {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100vh;
      opacity: 0;
      transition: opacity 1s ease-in-out;
      background-size: cover;
      background-position: center;
    }
    .slide.active {
      opacity: 1;
      z-index: 1;
    }
    .overlay {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0,0,0,0.6);
      z-index: 1;
    }
    .slide-content {
      position: absolute;
      top: 50%;
      left: 7%;
      transform: translateY(-50%);
      z-index: 2;
      max-width: 600px;
      text-align: left;
      color: white;
      padding: 20px;
    }
    .slide-content h2 {
      margin-bottom: 10px;
      font-size: 2.5rem;
    }
    .slide-content p {
      font-size: 1.1rem;
      line-height: 1.5;
    }
    .arrow {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      border: none;
      color: white;
      font-size: 2rem;
      padding: 10px;
      cursor: pointer;
      background: none;
      z-index: 10000;
    }
    .arrow.left {
      left: 30px;
    }
    .arrow.right {
      right: 30px;
    }
   
    @media (max-width: 768px) {
      .nav-right {
        display: none;
      }
      .hamburger {
        display: block;
      }
      .slide-content {
        left: 5%;
        padding: 10px;
      }
      .slide-content h2 {
        font-size: 2rem;
      }
      .slide-content p {
        font-size: 1rem;
      }
      .arrow {
        display: none;
      }
    }

    .team-section {
      text-align: center;
      padding: 50px 20px;
      background-color:rgb(255, 255, 255);
    }
    .team-section h2 {
      margin-bottom: 30px;
      font-size: 2rem;
    }
    .team-container {
      display: flex;
      justify-content: center;
      gap: 30px;
      flex-wrap: wrap;
    }
    .team-member {
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      max-width: 300px;
      text-align: center;
    }
    .team-member img {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      border: 4px solid #b30505;
      object-fit: cover;
    }
    .team-member h3{
      margin-top: 15px;
      white-space: nowrap;
      font-size: 22px !important;
    }
    .team-member p {
      font-size: 12px;
      color: gray;
      margin: 7px 0;
    }
    .team-member .role {
      font-weight: bold;
      margin-top: -2px;
      color: #007bff;
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
            font-weight: 400 !important;
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
                width: 36%;
                position: sticky;
                top: 20px;
            }
        }

 

    .reoc-join-wrapper {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    padding: 60px 80px;
    max-width: 1400px;
    margin: 0 auto;
  }

  .reoc-join-image img {
    max-width: 100%;
    height: auto;
    border-radius: 12px;
  }

  .reoc-join-image {
    flex: 1 1 45%;
    min-width: 300px;
  }

  .reoc-join-content {
    flex: 1 1 45%;
    min-width: 300px;
  }

  .reoc-join-title {
    font-size: 2.2rem;
    color: #333;
    margin-bottom: 20px;
    position: relative;
  }

  .reoc-join-title::before {
    content: '';
    display: block;
    width: 50px;
    height: 4px;
    background: #990101;
    margin-bottom: 12px;
  }

  .reoc-join-text {
    font-size: 1rem;
    color: #555;
    line-height: 1.6;
    margin-bottom: 30px;
  }

  .reoc-join-btn {
    display: inline-block;
    background-color: #990101;
    color: #fff;
    padding: 12px 26px;
    font-weight: bold;
    text-decoration: none;
    border-radius: 6px;
    transition: background-color 0.3s;
  }

  @media (max-width: 992px) {
    .reoc-join-wrapper {
      padding: 40px 20px;
    }
  }
  @media (max-width: 768px) {
    .reoc-join-wrapper {
      flex-direction: column;
      text-align: center;
      padding: 30px 20px;
    }
    .reoc-join-title::before {
    width: 0px;
  }
    .reoc-join-image,
    .reoc-join-content {
      flex: 1 1 100%;
    }
  }
  </style>
</head>
<body>
  <nav id="navbar">
    <div class="nav-left">
      <div class="logo">
        <img src="./img/reoc-nobg.png" alt="Logo" />
      </div>
      <h1>WMSUReoc</h1>
    </div>
    <div class="nav-right">
      <a href="login.php">have an account?</a>
      <button onclick="location.href='login.php'" type="button">Login</button>
    </div>
    <div class="hamburger" id="hamburger"><i class="fas fa-bars"></i></div>
  </nav>
  
  <div class="mobile-menu" id="mobileMenu">
    <a href="login.php"> have an account?</a>
    <button onclick="location.href='login.php'" type="button">Login</button>
  </div>
  
  <div class="slider-container">
    <div class="slide active" style="background-image: url('./img/reocpic.jpg');">
      <div class="overlay"></div>
      <div class="slide-content">
        <h2><?= getValue($cmsData, "Page_1_head") ?></h2>
        <p><?= getValue($cmsData, "Page_1_text") ?></p>
      </div>
    </div>
    <div class="slide" style="background-image: url('./img/reoc2.jpg');">
      <div class="overlay"></div>
      <div class="slide-content">
        <h2><?= getValue($cmsData, "Page_2_head") ?></h2>
        <p><?= getValue($cmsData, "Page_2_text") ?></p>
      </div>
    </div>
    <div class="slide" style="background-image: url('./img/reoc3.jpg');">
      <div class="overlay"></div>
      <div class="slide-content">
        <h2><?= getValue($cmsData, "Page_3_head") ?></h2>
        <p><?= getValue($cmsData, "Page_3_text") ?></p>
      </div>
    </div>
    <button class="arrow left" id="prev"><i class="fas fa-chevron-left"></i></button>
    <button class="arrow right" id="next"><i class="fas fa-chevron-right"></i></button>
  </div>

<div class="msg-container">
<div class="msg">
    <hr class="hr">
    <h3>Mission</h3>
    <p><?= htmlspecialchars($mission) ?></p>
    
    <hr class="hr">
    <h3>Vision</h3>
    <p><?= htmlspecialchars($vision) ?></p>
    
    <hr class="hr">
    <h3>Goals</h3>
    <p><?= htmlspecialchars($goals) ?></p>
</div>
        
        <img class="msg2" src="./img/msg2.png" alt="WMSU REOC Visual">
    </div>
</div>

<!-- <section class="team-section fade-in">
    <h2>MEET OUR FACULTY MEMBERS</h2>
    <br>
    <div class="team-container">
      <div class="team-member">
        <img src="./img/shrek.jpg" alt="Mary Brown">
        <h3>Shrek</h3>
        <p class="role">Creative Leader</p>
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
        
      </div>
      <div class="team-member">
        <img src="./img/shrek2.jpg" alt="Bob Greenfield">
        <h3>Lord Farquaad</h3>
        <p class="role">Programming Guru</p>
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
      </div>
      <div class="team-member">
        <img src="./img/shrek3.jpg" alt="Ann Richmond">
        <h3>Donkey</h3>
        <p class="role">Sales Manager</p>
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
      </div>
    </div>
  </section> -->

<link rel="stylesheet" href="./css/faq.css">
<script src="./js/faqrh.js"></script>

<!-- <div class="faq-wrapper">
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
</div> -->

<div class="faq-wrapper">
    <div class="faq-container">
        <h2>Frequently Asked Questions</h2>
        <?php foreach ($faqs as $faq): ?>
            <div class="faq-item">
                <div class="faq-question"><?= htmlspecialchars($faq['question']) ?> <span>+</span></div>
                <div class="faq-answer"><?= htmlspecialchars($faq['answer']) ?></div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="reoc-join-wrapper">
  <div class="reoc-join-image">
    <img src="./img/join.png" alt="Art Style Image">
  </div>
  <div class="reoc-join-content">
    <h2 class="reoc-join-title">Join Us Now</h2>  
    <p class="reoc-join-text"><?= getValue($cmsData, "Join_Us_text") ?>    </p>
    <a href="signup.php" class="reoc-join-btn">Join Us</a>  
  </div>
</div>

<?php include('./footer/footer.php'); ?>

  <script>
    const slides = document.querySelectorAll('.slide');
    const next = document.getElementById('next');
    const prev = document.getElementById('prev');
    let currentIndex = 0;
    function showSlide(index) {
      slides.forEach((slide, i) => {
        slide.classList.remove('active');
        if (i === index) {
          slide.classList.add('active');
        }
      });
    }
    next.addEventListener('click', () => {
      currentIndex = (currentIndex + 1) % slides.length;
      showSlide(currentIndex);
    });
    prev.addEventListener('click', () => {
      currentIndex = (currentIndex - 1 + slides.length) % slides.length;
      showSlide(currentIndex);
    });
    setInterval(() => {
      currentIndex = (currentIndex + 1) % slides.length;
      showSlide(currentIndex);
    }, 5000);
    const navbar = document.getElementById('navbar');
    let lastScrollY = window.scrollY;
    window.addEventListener('scroll', () => {
      const currentScrollY = window.scrollY;
      if (currentScrollY < lastScrollY) {
        navbar.style.top = "0";
        navbar.style.backgroundColor = currentScrollY > 0 ? "rgba(255, 255, 255, 0.9)" : "transparent";
        navbar.style.color = currentScrollY > 0 ? "black" : "white";
        document.querySelector(".nav-left h1").style.color = currentScrollY > 0 ? "black" : "white";
        document.querySelectorAll(".nav-right a").forEach(el => el.style.color = currentScrollY > 0 ? "black" : "white");
      } else {
        navbar.style.top = "-80px";
      }
      lastScrollY = currentScrollY;
    });
    const hamburger = document.getElementById('hamburger');
    const mobileMenu = document.getElementById('mobileMenu');
    hamburger.addEventListener('click', () => {
      mobileMenu.style.display = mobileMenu.style.display === 'flex' ? 'none' : 'flex';
    });
    window.addEventListener('resize', () => {
      if(window.innerWidth > 768) {
        mobileMenu.style.display = 'none';
      }
    });
    document.addEventListener("DOMContentLoaded", function () {
    const fadeInElements = document.querySelectorAll(".fade-in");
    const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add("visible");
      }
    });
  }, { threshold: 0.1 });
   fadeInElements.forEach(el => observer.observe(el));
  });
  </script>
</body>
</html>
