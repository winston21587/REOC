<?php include 'Website Loading Screen/loader.php'; ?>
<?php
require 'dbConnCode.php';
require 'class/Admin.php';
$query = "SELECT statement_type, content FROM vision_mission";
$result = $conn->query($query);
$vms = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $vms[strtolower($row['statement_type'])] = $row['content'];
    }
}
$mission = $vms['mission'] ?? 'Mission content not available.';
$vision = $vms['vision'] ?? 'Vision content not available.';
$goals = $vms['goals'] ?? 'Goals content not available.';
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
            return $row['content'];
        }
    }
    return null;
}
?>
<link rel="stylesheet" href="Website Loading Screen/loader.css">
<script src="Website Loading Screen/loader.js"></script>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>WMSUReoc</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            montserrat: ['Montserrat', 'sans-serif'],
          },
        },
      },
    };
  </script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"/>
  <style>
    .slide.active { opacity: 1 !important; z-index: 1 !important; }
    .fade-in { opacity: 0; transform: translateY(20px); transition: opacity 1s ease-out, transform 1s ease-out; }
    .fade-in.visible { opacity: 1; transform: translateY(0); }
  </style>
</head>
<body class="font-montserrat bg-[#f4f4f4]">
  <nav id="navbar" class="fixed top-0 left-0 w-full h-[60px] flex justify-between items-center px-[30px] z-[1000] transition-all duration-300 bg-transparent">
    <div class="flex items-center">
      <div class="w-[40px] h-[40px] rounded-full mr-[10px] overflow-hidden">
        <img src="./img/reoc-nobg.png" alt="Logo" class="w-full h-full object-cover" />
      </div>
  <h1 class="text-white transition-colors text-xl font-bold">WMSUReoc</h1>
    </div>
    <div class="nav-right hidden md:flex items-center">
  <a href="login.php" class="mr-[15px] text-[1rem] no-underline cursor-pointer text-white font-bold">have an account?</a>
  <button onclick="location.href='login.php'" type="button" class="px-[22px] py-[9px] rounded-[4px] text-[1rem] bg-gradient-to-br from-[#8B0000] to-[#B22222] text-white font-bold">Login</button>
    </div>
    <div id="hamburger" class="hamburger block md:hidden text-[1.5rem] text-white cursor-pointer"><i class="fas fa-bars"></i></div>
  </nav>
  <div id="mobileMenu" class="mobile-menu hidden fixed top-[60px] right-0 bg-white/95 w-[200px] shadow-md flex-col z-[1000]">
    <a href="login.php" class="text-black p-[15px] border-b border-gray-200 text-left text-[1rem]"> have an account?</a>
    <button onclick="location.href='login.php'" type="button" class="text-black p-[15px] text-left bg-none border-none">Login</button>
  </div>
  <div class="slider-container relative w-full h-screen overflow-hidden">
    <div class="slide active absolute inset-0 w-full h-screen opacity-0 transition-opacity duration-1000 bg-cover bg-center" style="background-image: url('./img/reocpic.jpg');">
      <div class="overlay absolute inset-0 bg-black/60 z-10"></div>
      <div class="slide-content absolute top-1/2 left-[7%] -translate-y-1/2 z-20 max-w-[600px] text-left text-white p-[20px] md:left-[7%]">
  <h2 class="mb-[10px] text-[2.5rem] md:text-[2.5rem] font-bold"><?= getValue($cmsData, "Page_1_head") ?></h2>
  <p class="text-[1.1rem] leading-[1.5] md:text-[1.1rem]"><?= getValue($cmsData, "Page_1_text") ?></p>
      </div>
    </div>
    <div class="slide absolute inset-0 w-full h-screen opacity-0 transition-opacity duration-1000 bg-cover bg-center" style="background-image: url('./img/reoc2.jpg');">
      <div class="overlay absolute inset-0 bg-black/60 z-10"></div>
      <div class="slide-content absolute top-1/2 left-[7%] -translate-y-1/2 z-20 max-w-[600px] text-left text-white p-[20px] md:left-[7%]">
  <h2 class="mb-[10px] text-[2.5rem] md:text-[2.5rem] font-bold"><?= getValue($cmsData, "Page_2_head") ?></h2>
  <p class="text-[1.1rem] leading-[1.5] md:text-[1.1rem]"><?= getValue($cmsData, "Page_2_text") ?></p>
      </div>
    </div>
    <div class="slide absolute inset-0 w-full h-screen opacity-0 transition-opacity duration-1000 bg-cover bg-center" style="background-image: url('./img/reoc3.jpg');">
      <div class="overlay absolute inset-0 bg-black/60 z-10"></div>
      <div class="slide-content absolute top-1/2 left-[7%] -translate-y-1/2 z-20 max-w-[600px] text-left text-white p-[20px] md:left-[7%]">
  <h2 class="mb-[10px] text-[2.5rem] md:text-[2.5rem] font-bold"><?= getValue($cmsData, "Page_3_head") ?></h2>
  <p class="text-[1.1rem] leading-[1.5] md:text-[1.1rem]"><?= getValue($cmsData, "Page_3_text") ?></p>
      </div>
    </div>
    <button id="prev" class="arrow left absolute top-1/2 -translate-y-1/2 left-[30px] text-white text-[2rem] p-[10px] bg-none z-[10000] hidden md:block"><i class="fas fa-chevron-left"></i></button>
    <button id="next" class="arrow right absolute top-1/2 -translate-y-1/2 right-[30px] text-white text-[2rem] p-[10px] bg-none z-[10000] hidden md:block"><i class="fas fa-chevron-right"></i></button>
  </div>
  <div class="msg-container max-w-[1200px] mx-auto p-[20px] gap-[20px] flex flex-col md:flex-row md:items-start md:py-[40px] md:px-[20px]">
    <div class="msg md:w-[60%] md:pr-[40px]">
      <hr class="hr opacity-[0.25] my-[24px] border-t">
      <h3 class="text-[#990101] text-[2.5rem] font-[700] my-[1rem]">Mission</h3>
  <p class="text-[1rem] mb-[1rem]"><?= htmlspecialchars($mission) ?></p>
      <hr class="hr opacity-[0.25] my-[24px] border-t">
      <h3 class="text-[#990101] text-[2.5rem] font-[700] my-[1rem]">Vision</h3>
  <p class="text-[1rem] mb-[1rem]"><?= htmlspecialchars($vision) ?></p>
      <hr class="hr opacity-[0.25] my-[24px] border-t">
      <h3 class="text-[#990101] text-[2.5rem] font-[700] my-[1rem]">Goals</h3>
  <p class="text-[1rem] mb-[1rem]"><?= htmlspecialchars($goals) ?></p>
    </div>
    <img class="msg2 w-full md:w-[36%] md:sticky md:top-[20px]" src="./img/msg2.png" alt="WMSU REOC Visual">
  </div>
  <link rel="stylesheet" href="./css/faq.css">
  <script src="./js/faqrh.js"></script>
  <div class="faq-wrapper">
    <div class="faq-container max-w-[900px] mx-auto p-[20px]">
      <h2 class="text-[2rem] font-[700] mb-[24px]">Frequently Asked Questions</h2>
      <?php foreach ($faqs as $faq): ?>
          <div class="faq-item mb-[12px]">
              <div class="faq-question font-[600] cursor-pointer flex justify-between"><?= htmlspecialchars($faq['question']) ?> <span>+</span></div>
              <div class="faq-answer"><?= htmlspecialchars($faq['answer']) ?></div>
          </div>
      <?php endforeach; ?>
    </div>
  </div>
  <div class="reoc-join-wrapper max-w-[1400px] mx-auto flex items-center justify-between flex-wrap p-[60px_80px] md:p-[60px_80px]">
    <div class="reoc-join-image flex-1 min-w-[300px] p-[10px]">
      <img src="./img/join.png" alt="Art Style Image" class="max-w-full h-auto rounded-[12px]">
    </div>
    <div class="reoc-join-content flex-1 min-w-[300px] p-[10px]">
      <div class="before:block before:w-[50px] before:h-[4px] before:bg-[#990101] before:mb-[12px]"></div>
      <h2 class="reoc-join-title text-[2.2rem] text-[#333] mb-[20px]">Join Us Now</h2>
  <p class="reoc-join-text text-[1rem] text-[#555] leading-[1.6] mb-[30px] font-bold"><?= getValue($cmsData, "Join_Us_text") ?></p>
      <a href="signup.php" class="reoc-join-btn inline-block bg-[#990101] text-white px-[26px] py-[12px] font-[700] rounded-[6px]">Join Us</a>
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
    const navH1 = navbar.querySelector('h1');
    const navLinks = navbar.querySelectorAll('.nav-right a');
    const navButton = navbar.querySelector('.nav-right button');
    window.addEventListener('scroll', () => {
      const currentScrollY = window.scrollY;
      if (currentScrollY < lastScrollY) {
        navbar.style.top = "0";
        if (currentScrollY > 0) {
          navbar.classList.add('bg-white', 'shadow');
          navbar.classList.remove('bg-transparent');
          navH1.classList.remove('text-white');
          navH1.classList.add('text-black');
          navLinks.forEach(el => {
            el.classList.remove('text-white');
            el.classList.add('text-black');
          });
          if (navButton) {
            navButton.classList.add('text-white');
            navButton.classList.remove('text-black');
          }
        } else {
          navbar.classList.remove('bg-white', 'shadow');
          navbar.classList.add('bg-transparent');
          navH1.classList.remove('text-black');
          navH1.classList.add('text-white');
          navLinks.forEach(el => {
            el.classList.remove('text-black');
            el.classList.add('text-white');
          });
          if (navButton) {
            navButton.classList.add('text-white');
            navButton.classList.remove('text-black');
          }
        }
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