<?php include 'Website Loading Screen/loader.php'; ?> <!-- call these for website loading animation -->
<link rel="stylesheet" href="Website Loading Screen/loader.css"> <!-- call these for website loading animation -->
<script src="Website Loading Screen/loader.js"></script> <!-- call these for website loading animation -->
<?php include './navbar/navbar.php'; ?> <!-- call these for the navbar -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
  <title>Gallery</title>
  <style>
    * { box-sizing: border-box; }
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background: #f5f5f5;
      color: #333;
    }
    .container {
      max-width: 1200px;
      padding-top: 60px;
      margin: auto;
      padding: 1rem;
    }
    h2 { margin-bottom: 1rem; }

    .gallery-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
      gap: 1rem;
    }
    .folder {
      background: #fff;
      border: 1px solid #ccc;
      border-radius: 5px;
      overflow: hidden;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      cursor: pointer;
      transition: box-shadow 0.3s;
    }
    .folder:hover {
      box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }
    .folder img {
      width: 100%;
      aspect-ratio: 4/3;
      object-fit: cover;
    }
    .folder-info {
      padding: 0.5rem;
      font-size: 0.9rem;
    }
    .folder-info strong {
      display: block;
      margin-bottom: 0.25rem;
    }
    .folder-info span {
      color: #555;
    }

    .modal {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.8);
      justify-content: center;
      align-items: center;
      z-index: 1000;
    }
    .modal.active {
      display: flex;
    }
    
    #modalImage {
      max-width: 80%;
      max-height: 80%;
      object-fit: contain;
      border-radius: 4px;
    }

    .modal button {
      position: absolute;
      background: rgba(255,255,255,0.9);
      border: none;
      padding: 0.5rem;
      border-radius: 4px;
      cursor: pointer;
      transition: background 0.2s;
    }
    .modal button:hover {
      background: rgba(255,255,255,1);
    }
    .close-btn {
      top: 1rem;
      right: 1rem;
    }
    .nav-arrow {
      top: 50%;
      transform: translateY(-50%);
      width: 2rem;
      height: 2rem;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .nav-arrow.prev {
      left: 1rem;
    }
    .nav-arrow.next {
      right: 1rem;
    }
    .fullscreen-btn {
      bottom: 1rem;
      right: 1rem;
    }
    .modal svg {
      width: 1.2rem;
      height: 1.2rem;
      fill: #333;
    }

    @media (max-width: 600px) {
      .folder-info { font-size: 0.8rem; }
      #modalImage {
        max-width: 95%;
        max-height: 70%;
      }
    }
  </style>
</head>
<body>

  <div class="container">
    <h2>Gallery</h2>
    <div class="gallery-grid" id="galleryGrid">
      <!-- JS will inject folders here -->
    </div>
  </div>

  <div class="modal" id="imageModal">
  <button class="close-btn" onclick="closeModal()" aria-label="Close">
  <svg viewBox="0 0 24 24"
       stroke="currentColor"
       stroke-width="2"
       fill="none"
       stroke-linecap="round"
       stroke-linejoin="round">
    <line x1="6" y1="6" x2="18" y2="18"></line>
    <line x1="6" y1="18" x2="18" y2="6"></line>
  </svg>
</button>
    <button class="nav-arrow prev" onclick="prevImage()" aria-label="Previous">
      
      <svg viewBox="0 0 24 24"><path d="M15 18l-6-6 6-6"/></svg>
    </button>
    <img id="modalImage" src="" alt=""/>
    <button class="nav-arrow next" onclick="nextImage()" aria-label="Next">
      
      <svg viewBox="0 0 24 24"><path d="M9 6l6 6-6 6"/></svg>
    </button>
    <button class="fullscreen-btn" onclick="toggleFullScreen()" aria-label="Fullscreen">
      
      <svg viewBox="0 0 24 24"><path d="M4 4h6v2H6v4H4V4zm10 0h6v6h-2V6h-4V4zm6 10v6h-6v-2h4v-4h2zm-10 6H4v-6h2v4h4v2z"/></svg>
    </button>
  </div>


  <script>

    const folders = [
      {
        title: "Zamboanga Training",
        thumbnail: "./img/placeholder.jpg",
        images: [
          "./img/placeholder.jpg",
          "./img/placeholder.jp",
          "./img/placeholder.jpg"
        ]
      },
      {
        title: "Iloilo Seminar",
        thumbnail: "./img/placeholder.jpg",
        images: [
          "./img/placeholder.jpg",
          "./img/placeholder.jpg"
        ]
      },
      {
        title: "Tacloban Session",
        thumbnail: "./img/placeholder.jpg",
        images: [
          "./img/placeholder.jpg"
        ]
      }
    ];

    const galleryGrid   = document.getElementById('galleryGrid');
    const imageModal    = document.getElementById('imageModal');
    const modalImage    = document.getElementById('modalImage');

    let currentImages = [], currentIndex = 0;

    folders.forEach(folder => {
      const card = document.createElement('div');
      card.className = 'folder';
      card.innerHTML = `
        <img src="${folder.thumbnail}" alt="${folder.title}">
        <div class="folder-info">
          <strong>${folder.title}</strong>
          <span>(${folder.images.length} images)</span>
        </div>
      `;
      card.onclick = () => openModal(folder.images, 0);
      galleryGrid.appendChild(card);
    });

    function openModal(images, startIdx) {
      currentImages = images;
      currentIndex  = startIdx;
      modalImage.src = currentImages[currentIndex];
      imageModal.classList.add('active');
    }

    function closeModal() {
      imageModal.classList.remove('active');
      modalImage.src = '';
      if (document.fullscreenElement) {
        document.exitFullscreen();
      }
    }

    function prevImage() {
      currentIndex = (currentIndex - 1 + currentImages.length) % currentImages.length;
      modalImage.src = currentImages[currentIndex];
    }

    function nextImage() {
      currentIndex = (currentIndex + 1) % currentImages.length;
      modalImage.src = currentImages[currentIndex];
    }

    function toggleFullScreen() {
      if (!document.fullscreenElement) {
        imageModal.requestFullscreen();
      } else {
        document.exitFullscreen();
      }
    }

    document.addEventListener('keydown', e => {
      if (!imageModal.classList.contains('active')) return;
      if (e.key === 'ArrowRight') nextImage();
      if (e.key === 'ArrowLeft')  prevImage();
      if (e.key === 'Escape')     closeModal();
    });
  </script>
</body>
</html>
