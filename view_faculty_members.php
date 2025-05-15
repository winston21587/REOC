<?php include 'Website Loading Screen/loader.php'; ?> <!-- call these for website loading animation -->
<link rel="stylesheet" href="Website Loading Screen/loader.css"> <!-- call these for website loading animation -->
<script src="Website Loading Screen/loader.js"></script> <!-- call these for website loading animation -->
<?php include './navbar/navbar.php'; ?> <!-- call these for the navbar -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>WMSU Faculty Researchers</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    :root {
      --font-main: 'Segoe UI', sans-serif;
      --text-color: #111;
      --subtext-color: #555;
      --card-bg: #fff;
      --shadow: rgba(0, 0, 0, 0.08);
      --gradient-bg: linear-gradient(to bottom, #fdfbfb, #e6ebf5, #d8e4f0);
    }

    .faculty-section {
      font-family: var(--font-main);
      background: var(--gradient-bg);
      color: var(--text-color);
      padding-top: 80px;
    }

    .faculty-header {
      text-align: center;
      padding: 60px 20px 40px;
      max-width: 900px;
      margin: auto;
    }

    .faculty-header h1 {
      font-size: 2.4rem;
      font-weight: 700;
      margin-bottom: 20px;
    }

    .faculty-header p {
      font-size: 1.1rem;
      color: var(--subtext-color);
      line-height: 1.6;
    }

    .faculty-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 30px;
      padding: 0 20px 80px;
      max-width: 1100px;
      margin: 0 auto;
    }

    .faculty-card {
      background: var(--card-bg);
      border-radius: 16px;
      overflow: hidden;
      box-shadow: 0 8px 18px var(--shadow);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      animation: fadeInUp 0.5s ease both;
    }

    .faculty-card:hover {
      transform: translateY(-6px);
      box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12);
    }

    .faculty-image {
      width: 100%;
      height: 240px;
      object-fit: cover;
      display: block;
    }

    .faculty-info {
      padding: 16px;
    }

    .faculty-name {
      font-size: 1.1rem;
      font-weight: 600;
      color: #222;
      margin-bottom: 6px;
    }

    .faculty-title {
      font-size: 0.95rem;
      color: var(--subtext-color);
    }

    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @media (max-width: 600px) {
      .faculty-header h1 {
        font-size: 2rem;
      }

      .faculty-header p {
        font-size: 1rem;
      }

      .faculty-image {
        height: 180px;
      }

      .faculty-info {
        padding: 12px;
      }

      .faculty-name {
        font-size: 1rem;
      }

      .faculty-title {
        font-size: 0.9rem;
      }
    }
  </style>
</head>
<body>

  <div class="faculty-section">
    <section class="faculty-header">
      <h1>Meet the WMSU Faculty Researchers</h1>
      <p>Western Mindanao State University takes pride in its dedicated faculty researchers who drive innovation, lead transformative studies, and champion the pursuit of academic excellence for the region and beyond.</p>
    </section>

    <section class="faculty-grid">
      <div class="faculty-card">
        <img src="placeholder.svg" alt="Dr. Maria Santos" class="faculty-image">
        <div class="faculty-info">
          <div class="faculty-name">Dr. Maria Santos</div>
          <div class="faculty-title">Lead Researcher, Health Sciences</div>
        </div>
      </div>
      <div class="faculty-card">
        <img src="placeholder.svg" alt="Dr. James Castillo" class="faculty-image">
        <div class="faculty-info">
          <div class="faculty-name">Dr. James Castillo</div>
          <div class="faculty-title">Senior Researcher, Environmental Studies</div>
        </div>
      </div>
      <div class="faculty-card">
        <img src="placeholder.svg" alt="Prof. Angela Reyes" class="faculty-image">
        <div class="faculty-info">
          <div class="faculty-name">Prof. Angela Reyes</div>
          <div class="faculty-title">Researcher, Educational Development</div>
        </div>
      </div>
      <div class="faculty-card">
        <img src="placeholder.svg" alt="Dr. Carlos Lim" class="faculty-image">
        <div class="faculty-info">
          <div class="faculty-name">Dr. Carlos Lim</div>
          <div class="faculty-title">Director, Agricultural Research</div>
        </div>
      </div>
      <div class="faculty-card">
        <img src="placeholder.svg" alt="Engr. Lea Mercado" class="faculty-image">
        <div class="faculty-info">
          <div class="faculty-name">Engr. Lea Mercado</div>
          <div class="faculty-title">Researcher, Engineering Innovations</div>
        </div>
      </div>
      <div class="faculty-card">
        <img src="placeholder.svg" alt="Dr. Ramon Cruz" class="faculty-image">
        <div class="faculty-info">
          <div class="faculty-name">Dr. Ramon Cruz</div>
          <div class="faculty-title">Research Fellow, Marine Biology</div>
        </div>
      </div>
    </section>
  </div>

</body>
</html>
