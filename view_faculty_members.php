<?php include 'Website Loading Screen/loader.php'; ?> <!-- call these for website loading animation -->
<link rel="stylesheet" href="Website Loading Screen/loader.css"> <!-- call these for website loading animation -->
<script src="Website Loading Screen/loader.js"></script> <!-- call these for website loading animation -->
<?php include './navbar/navbar.php'; ?> <!-- call these for the navbar -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Faculty & Team Showcase</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      color: #333;
      line-height: 1.6;
    }
    .hero {
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
      padding: 20px;
      background-color: #f0f0f0;
    }
    .hero-container {
      display: flex;
      width: 90%;
      max-width: 1200px;
      background: #fff;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .hero-text {
      flex: 1;
      padding: 40px;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }
    .hero-text h1 {
      font-size: 48px;
      margin-bottom: 20px;
    }
    .hero-text p {
      font-size: 18px;
      margin-bottom: 10px;
    }
    .hero-image {
      flex: 1;
      background: #ddd;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .hero-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    .main-title {
      text-align: center;
      margin: 40px 0 20px 0;
    }
    .main-title h2 {
      font-size: 36px;
    }
    .faculty-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 20px;
      padding: 0 20px 40px 20px;
    }
    .faculty-card {
      background-color: #fff;
      border: 1px solid #ddd;
      text-align: center;
      padding: 15px;
      cursor: pointer;
      transition: transform 0.2s, box-shadow 0.2s;
    }
    .faculty-card img {
      width: 100%;
      height: auto;
      aspect-ratio: 1/1;
      object-fit: cover;
      margin-bottom: 10px;
    }
    .faculty-card h3 {
      font-size: 20px;
      margin-top: 10px;
    }
    .overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0,0,0,0.8);
      opacity: 0;
      visibility: hidden;
      transition: opacity 0.3s ease, visibility 0.3s ease;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }
    .overlay.show {
      opacity: 1;
      visibility: visible;
    }
    .overlay-content {
      position: relative;
      max-width: 500px;
      background-color: #fff;
      padding: 20px;
      text-align: center;
      border-radius: 4px;
      animation: scaleIn 0.3s ease forwards;
    }
    @keyframes scaleIn {
      from {
        transform: scale(0.8);
        opacity: 0.5;
      }
      to {
        transform: scale(1);
        opacity: 1;
      }
    }
    .close {
      position: absolute;
      top: 10px;
      right: 15px;
      font-size: 24px;
      cursor: pointer;
    }
    .overlay-content img {
      max-width: 100%;
      height: auto;
      margin-bottom: 15px;
    }
    .overlay-content h2 {
      margin-bottom: 10px;
    }
    .overlay-content p {
      line-height: 1.5;
    }
  
    @media (max-width: 768px) {
      .hero-container {
        flex-direction: column;
      }
      .hero-text, .hero-image {
        flex: none;
        width: 100%;
      }
      .faculty-grid {
        grid-template-columns: 1fr 1fr;
      }
    }
    @media (max-width: 480px) {
      .faculty-grid {
        grid-template-columns: 1fr;
      }
      .hero-text h1 {
        font-size: 36px;
      }
    }
  </style>
</head>
<body>
  <section class="hero">
    <div class="hero-container">
      <div class="hero-text">
        <h1>Inspiring Faculty & Team</h1>
        <p>
          Welcome to our institution where excellence in education meets innovation.
        </p>
        <p>
          Our dedicated faculty and team members are here to guide and inspire you.
        </p>
      </div>
      <div class="hero-image">
        <img src="./img/reoc33.jpg" alt="Faculty Introduction" />
      </div>
    </div>
  </section>
  
  <section class="main-title">
    <h2></h2>
  </section>

  <section class="faculty-grid">
    
    <div class="faculty-card" 
         data-name="John Doe"
         data-description="John is a seasoned professor of Mathematics with over 20 years of teaching experience.">
      <img src="./img/faculty-ph.jpg" alt="John Doe" />
      <h3>John Doe</h3>
    </div>
    
    <div class="faculty-card" 
         data-name="Jane Smith"
         data-description="Jane specializes in English Literature and has authored several acclaimed publications.">
      <img src="./img/faculty-ph.jpg" alt="Jane Smith" />
      <h3>Jane Smith</h3>
    </div>
    
    <div class="faculty-card" 
         data-name="Alice Johnson"
         data-description="Alice's expertise in Computer Science has inspired many innovative projects.">
      <img src="./img/faculty-ph.jpg" alt="Alice Johnson" />
      <h3>Alice Johnson</h3>
    </div>
    
    <div class="faculty-card" 
         data-name="Bob Brown"
         data-description="Bob is known for his engaging lectures in History and Social Studies.">
      <img src="./img/faculty-ph.jpg" alt="Bob Brown" />
      <h3>Bob Brown</h3>
    </div>
    
    <div class="faculty-card" 
         data-name="Carol White"
         data-description="Carol teaches Biology with a focus on environmental science and conservation.">
      <img src="./img/faculty-ph.jpg" alt="Carol White" />
      <h3>Carol White</h3>
    </div>
    
    <div class="faculty-card" 
         data-name="David Black"
         data-description="David is a physics expert with an innovative approach to teaching complex concepts.">
      <img src="./img/faculty-ph.jpg" alt="David Black" />
      <h3>David Black</h3>
    </div>
    
    <div class="faculty-card" 
         data-name="Emily Green"
         data-description="Emily brings a creative flair to the art department with her modern techniques.">
      <img src="./img/faculty-ph.jpg" alt="Emily Green" />
      <h3>Emily Green</h3>
    </div>
    
    <div class="faculty-card" 
         data-name="Frank Blue"
         data-description="Frank's expertise in Economics makes him a favorite among students for his insightful lectures.">
      <img src="./img/faculty-ph.jpg" alt="Frank Blue" />
      <h3>Frank Blue</h3>
    </div>
    
    <div class="faculty-card" 
         data-name="Grace Red"
         data-description="Grace's innovative approach to Chemistry captivates her students and fosters discovery.">
      <img src="./img/faculty-ph.jpg" alt="Grace Red" />
      <h3>Grace Red</h3>
    </div>
  </section>
  
  <div id="overlay" class="overlay">
    <div class="overlay-content">
      <span id="closeOverlay" class="close">&times;</span>
      <img id="overlayImage" src="" alt="Faculty Member" />
      <h2 id="overlayName"></h2>
      <p id="overlayDescription"></p>
    </div>
  </div>
  
  <script>
    document.addEventListener("DOMContentLoaded", function() {
      const facultyCards = document.querySelectorAll(".faculty-card");
      const overlay = document.getElementById("overlay");
      const closeOverlay = document.getElementById("closeOverlay");
      const overlayImage = document.getElementById("overlayImage");
      const overlayName = document.getElementById("overlayName");
      const overlayDescription = document.getElementById("overlayDescription");
    
      facultyCards.forEach(card => {
        card.addEventListener("click", () => {
          const name = card.getAttribute("data-name");
          const description = card.getAttribute("data-description");
          const image = card.querySelector("img").src;
    
          overlayName.textContent = name;
          overlayDescription.textContent = description;
          overlayImage.src = image;
    
          overlay.classList.add("show");
        });
      });
    
      closeOverlay.addEventListener("click", () => {
        overlay.classList.remove("show");
      });
    
      overlay.addEventListener("click", (e) => {
        if (e.target === overlay) {
          overlay.classList.remove("show");
        }
      });
    });
  </script>
</body>
</html>
