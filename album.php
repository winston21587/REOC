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

    .pagination-bar {
      position: absolute;
      left: 0; right: 0; bottom: 1.5rem;
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 2px;
      z-index: 10;
    }
    .pagination-bar button, .pagination-bar span {
      border: 1px solid #ddd;
      background: #fff;
      color: #333;
      margin: 0 1px;
      padding: 3px 8px; 
      border-radius: 5px;
      font-size: 0.95rem; 
      cursor: pointer;
      transition: border 0.2s, background 0.2s;
    }
    .pagination-bar button.active-page {
      border: 2px solid #333;
      background: #f5f5f5;
      font-weight: bold;
      cursor: default;
    }
    .pagination-bar button:disabled {
      color: #aaa;
      border-color: #eee;
      background: #fafafa;
      cursor: not-allowed;
    }
    .pagination-bar span {
      border: none;
      background: none;
      color: #888;
      padding: 0 6px;
      cursor: default;
    }

    .modal .close-btn,
    .modal .nav-arrow,
    .modal .fullscreen-btn {
      position: absolute;
      /* ...other styles... */
    }

    .view-all-grid {
      position: fixed; 
      left: 0; right: 0; top: 0; bottom: 0;
      background: rgba(0,0,0,0.97);
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: flex-start;
      z-index: 2000; 
      padding: 2rem 1rem 1rem 1rem;
      overflow-y: auto;
    }
    .view-all-grid .grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
      gap: 16px;
      width: 100%;
      max-width: 800px;
      margin-top: 3rem;
    }
    .view-all-grid img {
      width: 100%;
      aspect-ratio: 4/3;
      object-fit: cover;
      border-radius: 6px;
      cursor: pointer;
      border: 2px solid transparent;
      transition: border 0.2s;
    }
    .view-all-grid img:hover {
      border: 2px solid #333;
    }
    .view-all-grid .close-viewall {
      position: absolute;
      top: 2rem;
      right: 2rem;
      background: #fff;
      border: none;
      border-radius: 4px;
      padding: 6px 16px;
      font-size: 1rem;
      cursor: pointer;
      z-index: 2010;
    }
  </style>
</head>
<body>

  <div class="container">
    <h2>Gallery</h2>
    <div class="gallery-grid" id="galleryGrid">
    </div>
    <div id="viewAllGrid" class="view-all-grid" style="display:none;"></div>
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
    <div id="pagination" class="pagination-bar"></div>
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

    function renderPagination() {
      const pagination = document.getElementById('pagination');
      if (!pagination) return;
      pagination.innerHTML = '';
      if (currentImages.length <= 1) {
        pagination.style.display = 'none';
        document.getElementById('thumbnails').style.display = 'none';
        document.getElementById('viewAllGrid').style.display = 'none';
        return;
      }
      pagination.style.display = 'flex';

      // --- View All Button ---
      const viewAllBtn = document.createElement('button');
      viewAllBtn.textContent = 'View All';
      viewAllBtn.style.marginRight = '10px';
      viewAllBtn.onclick = () => {
        showViewAllGrid();
      };
      pagination.appendChild(viewAllBtn);

      const total = currentImages.length;
      const maxPagesToShow = 5;
      let pages = [];

      if (total <= maxPagesToShow) {
        for (let i = 0; i < total; i++) {
          pages.push(i);
        }
      } else {
        pages.push(0);
        let start = Math.max(1, currentIndex - 1);
        let end = Math.min(total - 2, currentIndex + 1);
        if (currentIndex <= 2) {
          start = 1;
          end = Math.min(total - 2, maxPagesToShow - 2);
        } else if (currentIndex >= total - 3) {
          start = Math.max(1, total - (maxPagesToShow - 1));
          end = total - 2;
        }
        if (start > 1) pages.push('...');
        for (let i = start; i <= end; i++) {
          pages.push(i);
        }
        if (end < total - 2) pages.push('...');
        if (total > 1) pages.push(total - 1);
      }

      // Previous button
      const prevBtn = document.createElement('button');
      prevBtn.textContent = '‹ Previous';
      prevBtn.disabled = currentIndex === 0;
      prevBtn.onclick = () => { prevImage(); };
      pagination.appendChild(prevBtn);

      // Page buttons
      pages.forEach(p => {
        if (p === '...') {
          const span = document.createElement('span');
          span.textContent = '...';
          pagination.appendChild(span);
        } else {
          const btn = document.createElement('button');
          btn.textContent = (p + 1).toString();
          if (p === currentIndex) {
            btn.classList.add('active-page');
            btn.disabled = true;
          }
          btn.onclick = () => jumpToImage(p);
          pagination.appendChild(btn);
        }
      });

      // Next button
      const nextBtn = document.createElement('button');
      nextBtn.textContent = 'Next ›';
      nextBtn.disabled = currentIndex === total - 1;
      nextBtn.onclick = () => { nextImage(); };
      pagination.appendChild(nextBtn);
    }

    function showViewAllGrid() {
      const gridContainer = document.getElementById('viewAllGrid');
      gridContainer.innerHTML = `
        <button class="close-viewall" onclick="hideViewAllGrid()">Close</button>
        <div class="grid"></div>
      `;
      const grid = gridContainer.querySelector('.grid');
      currentImages.forEach((img, idx) => {
        const thumb = document.createElement('img');
        thumb.src = img;
        thumb.onclick = () => {
          jumpToImage(idx);
          hideViewAllGrid();
        };
        grid.appendChild(thumb);
      });
      gridContainer.style.display = 'flex';
    }

    function hideViewAllGrid() {
      document.getElementById('viewAllGrid').style.display = 'none';
    }

    function jumpToImage(idx) {
      currentIndex = idx;
      modalImage.src = currentImages[currentIndex];
      renderPagination();
    }

    function openModal(images, startIdx) {
      currentImages = images;
      currentIndex  = startIdx;
      modalImage.src = currentImages[currentIndex];
      imageModal.classList.add('active');
      renderPagination();
    }

    function closeModal() {
      imageModal.classList.remove('active');
      modalImage.src = '';
      if (document.fullscreenElement) {
        document.exitFullscreen();
      }
      const pagination = document.getElementById('pagination');
      if (pagination) pagination.innerHTML = '';
    }

    function prevImage() {
      currentIndex = (currentIndex - 1 + currentImages.length) % currentImages.length;
      modalImage.src = currentImages[currentIndex];
      renderPagination();
    }

    function nextImage() {
      currentIndex = (currentIndex + 1) % currentImages.length;
      modalImage.src = currentImages[currentIndex];
      renderPagination();
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

