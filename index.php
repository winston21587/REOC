<?php include 'Website Loading Screen/loader.php'; ?> <!-- call these for website loading animation -->
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
    .team-section {
      text-align: center;
      padding: 50px 20px;
      background-color: #f8f8f8;
    }
    .team-section h2 {
      margin-bottom: 30px;
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
    .team-member h3 {
      margin-top: 15px;
      font-size: 20px;
    }
    .team-member p {
      font-size: 14px;
      color: gray;
      margin: 5px 0;
    }
    .team-member .role {
      font-weight: bold;
      color: #007bff;
    }
    .social-icons {
      margin-top: 10px;
    }
    .social-icons a {
      display: inline-block;
      margin: 5px;
      text-decoration: none;
      color: white;
      background: #a70707;
      width: 30px;
      height: 30px;
      line-height: 30px;
      border-radius: 50%;
      text-align: center;
    }
    .social-icons a i {
      font-size: 16px;
      margin-top: 7px;
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
      <a href="signup.php">Don't have an account?</a>
      <button onclick="location.href='login.php'" type="button">Login</button>
    </div>
    <div class="hamburger" id="hamburger"><i class="fas fa-bars"></i></div>
  </nav>
  
  <div class="mobile-menu" id="mobileMenu">
    <a href="signup.php">Don't have an account?</a>
    <button onclick="location.href='login.php'" type="button">Login</button>
  </div>
  
  <div class="slider-container">
    <div class="slide active" style="background-image: url('./img/reocpic.jpg');">
      <div class="overlay"></div>
      <div class="slide-content">
        <h2>Welcome to REOC: Where Research Meets Collaboration</h2>
        <p>At REOC, we’re creating a space where students, faculty, and researchers come together to innovate, share knowledge, and grow. Whether you're looking to collaborate on research projects, share valuable resources, or connect with peers, REOC is your go-to hub for academic success. Join our thriving community today and be a part of something bigger.</p>
      </div>
    </div>
    <div class="slide" style="background-image: url('./img/reoc2.jpg');">
      <div class="overlay"></div>
      <div class="slide-content">
        <h2>Collaborate, Share, Achieve</h2>
        <p>The power of collaboration lies in the exchange of ideas. REOC makes it easy for students to pass materials, participate in discussions, and contribute to ongoing research projects. From sharing research papers to organizing study sessions, everything you need to stay connected and productive is just a click away.The power of collaboration lies in the exchange of ideas. REOC makes it easy for students to pass materials, participate in discussions, and contribute to ongoing research projects. From sharing research papers to organizing study sessions, everything you need to stay connected and productive is just a click away.</p>
      </div>
    </div>
    <div class="slide" style="background-image: url('./img/reoc3.jpg');">
      <div class="overlay"></div>
      <div class="slide-content">
        <h2>Effortless Meeting & Event Coordination</h2>
        <p>Tired of juggling schedules and missing important events? REOC streamlines the process of meeting organization. Plan study groups, research sessions, or school events with ease. Our intuitive platform lets you set dates, send invitations, and manage attendance—all in one place. Stay on top of your academic goals without the hassle.</p>
      </div>
    </div>
    <button class="arrow left" id="prev"><i class="fas fa-chevron-left"></i></button>
    <button class="arrow right" id="next"><i class="fas fa-chevron-right"></i></button>
  </div>

  <section class="team-section fade-in">
    <h2>Meet the Faculty Members</h2>
    <br>
    <div class="team-container">
      <div class="team-member">
        <img src="./img/shrek.jpg" alt="Mary Brown">
        <h3>Shrek</h3>
        <p class="role">Creative Leader</p>
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
        <div class="social-icons">
          <a href="#"><i class="fab fa-facebook-f"></i></a>
          <a href="#"><i class="fab fa-twitter"></i></a>
          <a href="#"><i class="fab fa-instagram"></i></a>
        </div>
      </div>
      <div class="team-member">
        <img src="./img/shrek2.jpg" alt="Bob Greenfield">
        <h3>Lord Farquaad</h3>
        <p class="role">Programming Guru</p>
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
        <div class="social-icons">
          <a href="#"><i class="fab fa-facebook-f"></i></a>
          <a href="#"><i class="fab fa-twitter"></i></a>
          <a href="#"><i class="fab fa-instagram"></i></a>
        </div>
      </div>
      <div class="team-member">
        <img src="./img/shrek3.jpg" alt="Ann Richmond">
        <h3>Donkey</h3>
        <p class="role">Sales Manager</p>
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
        <div class="social-icons">
          <a href="#"><i class="fab fa-facebook-f"></i></a>
          <a href="#"><i class="fab fa-twitter"></i></a>
          <a href="#"><i class="fab fa-instagram"></i></a>
        </div>
      </div>
    </div>
  </section>

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
