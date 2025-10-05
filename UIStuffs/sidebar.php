<?php ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Side Navigation Bar</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');
    
    * {
      font-family: "Poppins", sans-serif;
    }
    
    .nav-item.has-dropdown .submenu {
      overflow: hidden;
      height: 0;
      opacity: 0;
      padding: 0;              
      margin-top: 0;
      transition:
        height 0.5s cubic-bezier(0.4, 0, 0.2, 1),
        opacity 0.4s ease,
        padding 0.4s ease,
        margin-top 0.4s ease;
    }

    .nav-item.has-dropdown.open .submenu {
      opacity: 1;
      padding: 0 16px;       
      margin-top: 8px;    
      box-shadow: 0 6px 18px rgba(2,6,23,0.45);
    }

    .sidebar.collapsed .submenu {
      display: none !important;
    }

    .sidebar.collapsed .nav-label,
    .sidebar.collapsed .dropdown-caret {
      opacity: 0;
      visibility: hidden;
      pointer-events: none;
      transition: opacity 0.4s ease 0s;
    }

    .sidebar .nav-label {
      overflow: hidden;
      text-overflow: ellipsis;
      max-width: calc(100% - 40px);
    }
  </style>
</head>
<body class="bg-white min-h-screen">

  <aside class="sidebar fixed w-[270px] bg-[#151A2D] h-screen transition-all duration-600 ease-in-out pb-6">
    <header class="sidebar-header flex relative py-[25px] px-5 items-center justify-between">
      <a href="#" class="header-logo">
        <img src="icon.png" alt="icon-logo" class="w-12 h-12 block object-contain rounded-full">
      </a>

      <button class="toggler sidebar-toggler absolute right-5 h-[35px] w-[35px] text-[#151A2D] border-none cursor-pointer flex bg-white items-center justify-center rounded-lg transition-all duration-600 ease-in-out hover:bg-[#dde4fb]" aria-label="Toggle sidebar">
        <span class="material-symbols-rounded text-[1.75rem] transition-all duration-600 ease-in-out">menu</span>
      </button>
      <button class="toggler menu-toggler hidden h-[30px] w-[30px] text-[#151A2D] border-none cursor-pointer flex bg-white items-center justify-center rounded-lg transition-all duration-600 ease-in-out hover:bg-[#dde4fb]" aria-label="Toggle menu">
        <span class="material-symbols-rounded text-[1.3rem]">menu</span>
      </button>
    </header>

    <nav class="sidebar-nav">
      <ul class="nav-list primary-nav flex flex-col gap-1 px-[15px] translate-y-[15px] transition-all duration-600 ease-in-out">
        <li class="nav-item relative">
          <a href="#" class="nav-link text-white flex gap-3 whitespace-nowrap rounded-lg py-3 px-[15px] items-center no-underline transition-all duration-600 ease-in-out hover:bg-white hover:text-[#151A2D]">
            <span class="nav-icon material-symbols-rounded">dashboard</span>
            <span class="nav-label">Dashboard</span>
          </a>
          <span class="nav-tooltip absolute -top-[10px] opacity-0 text-[#151A2D] hidden pointer-events-none py-[6px] px-3 rounded-lg whitespace-nowrap bg-white left-[calc(100%+25px)] shadow-[0_5px_10px_rgba(0,0,0,0.1)] transition-none">Dashboard</span>
        </li>

        <li class="nav-item has-dropdown relative">
          <a href="#" class="nav-link text-white flex gap-3 whitespace-nowrap rounded-lg py-3 px-[15px] items-center no-underline transition-all duration-600 ease-in-out hover:bg-white hover:text-[#151A2D]">
            <span class="nav-icon material-symbols-rounded">folder</span>
            <span class="nav-label">Content</span>
            <span class="dropdown-caret material-symbols-rounded transition-transform duration-500 ease-in-out">expand_more</span>
          </a>
          <ul class="submenu bg-[#1b1f33] rounded-lg">
            <li><a href="#" class="sub-link block py-[10px] px-4 text-[#cbd5e1] no-underline text-[0.95rem] transition-all duration-250 ease-in-out rounded-[6px] hover:bg-[#334155] hover:text-white">FAQ</a></li>
            <li><a href="#" class="sub-link block py-[10px] px-4 text-[#cbd5e1] no-underline text-[0.95rem] transition-all duration-250 ease-in-out rounded-[6px] hover:bg-[#334155] hover:text-white">Mission and Vision</a></li>
            <li><a href="#" class="sub-link block py-[10px] px-4 text-[#cbd5e1] no-underline text-[0.95rem] transition-all duration-250 ease-in-out rounded-[6px] hover:bg-[#334155] hover:text-white">Page Content</a></li>
          </ul>
          <span class="nav-tooltip absolute -top-[10px] opacity-0 text-[#151A2D] hidden pointer-events-none py-[6px] px-3 rounded-lg whitespace-nowrap bg-white left-[calc(100%+25px)] shadow-[0_5px_10px_rgba(0,0,0,0.1)] transition-none">Content</span>
        </li>

        <li class="nav-item relative">
          <a href="#" class="nav-link text-white flex gap-3 whitespace-nowrap rounded-lg py-3 px-[15px] items-center no-underline transition-all duration-600 ease-in-out hover:bg-white hover:text-[#151A2D]">
            <span class="nav-icon material-symbols-rounded">school</span>
            <span class="nav-label">Colleges</span>
          </a>
          <span class="nav-tooltip absolute -top-[10px] opacity-0 text-[#151A2D] hidden pointer-events-none py-[6px] px-3 rounded-lg whitespace-nowrap bg-white left-[calc(100%+25px)] shadow-[0_5px_10px_rgba(0,0,0,0.1)] transition-none">Colleges</span>
        </li>

        <li class="nav-item has-dropdown relative">
          <a href="#" class="nav-link text-white flex gap-3 whitespace-nowrap rounded-lg py-3 px-[15px] items-center no-underline transition-all duration-600 ease-in-out hover:bg-white hover:text-[#151A2D]">
            <span class="nav-icon material-symbols-rounded">groups</span>
            <span class="nav-label">Manage Data</span>
            <span class="dropdown-caret material-symbols-rounded transition-transform duration-500 ease-in-out">expand_more</span>
          </a>
          <ul class="submenu bg-[#1b1f33] rounded-lg">
            <li><a href="#" class="sub-link block py-[10px] px-4 text-[#cbd5e1] no-underline text-[0.95rem] transition-all duration-250 ease-in-out rounded-[6px] hover:bg-[#334155] hover:text-white">Users</a></li>
            <li><a href="#" class="sub-link block py-[10px] px-4 text-[#cbd5e1] no-underline text-[0.95rem] transition-all duration-250 ease-in-out rounded-[6px] hover:bg-[#334155] hover:text-white">Application Form</a></li>
            <li><a href="#" class="sub-link block py-[10px] px-4 text-[#cbd5e1] no-underline text-[0.95rem] transition-all duration-250 ease-in-out rounded-[6px] hover:bg-[#334155] hover:text-white">Toggled Form</a></li>
            <li><a href="#" class="sub-link block py-[10px] px-4 text-[#cbd5e1] no-underline text-[0.95rem] transition-all duration-250 ease-in-out rounded-[6px] hover:bg-[#334155] hover:text-white">REOC Data</a></li>
          </ul>
          <span class="nav-tooltip absolute -top-[10px] opacity-0 text-[#151A2D] hidden pointer-events-none py-[6px] px-3 rounded-lg whitespace-nowrap bg-white left-[calc(100%+25px)] shadow-[0_5px_10px_rgba(0,0,0,0.1)] transition-none">Manage Data</span>
        </li>

        <li class="nav-item relative">
          <a href="#" class="nav-link text-white flex gap-3 whitespace-nowrap rounded-lg py-3 px-[15px] items-center no-underline transition-all duration-600 ease-in-out hover:bg-white hover:text-[#151A2D]">
            <span class="nav-icon material-symbols-rounded">settings</span>
            <span class="nav-label">Settings</span>
          </a>
          <span class="nav-tooltip absolute -top-[10px] opacity-0 text-[#151A2D] hidden pointer-events-none py-[6px] px-3 rounded-lg whitespace-nowrap bg-white left-[calc(100%+25px)] shadow-[0_5px_10px_rgba(0,0,0,0.1)] transition-none">Settings</span>
        </li>
      </ul>

      <ul class="nav-list secondary-nav absolute bottom-[30px] w-full flex flex-col gap-1 px-[15px] translate-y-[15px] transition-all duration-600 ease-in-out">
        <li class="nav-item relative">
          <a href="#" class="nav-link text-white flex gap-3 whitespace-nowrap rounded-lg py-3 px-[15px] items-center no-underline transition-all duration-600 ease-in-out hover:bg-white hover:text-[#151A2D]">
            <span class="nav-icon material-symbols-rounded">account_circle</span>
            <span class="nav-label">Profile</span>
          </a>
          <span class="nav-tooltip absolute -top-[10px] opacity-0 text-[#151A2D] hidden pointer-events-none py-[6px] px-3 rounded-lg whitespace-nowrap bg-white left-[calc(100%+25px)] shadow-[0_5px_10px_rgba(0,0,0,0.1)] transition-none">Profile</span>
        </li>
        <li class="nav-item relative">
          <a href="#" class="nav-link text-white flex gap-3 whitespace-nowrap rounded-lg py-3 px-[15px] items-center no-underline transition-all duration-600 ease-in-out hover:bg-white hover:text-[#151A2D]">
            <span class="nav-icon material-symbols-rounded">logout</span>
            <span class="nav-label">Logout</span>
          </a>
          <span class="nav-tooltip absolute -top-[10px] opacity-0 text-[#151A2D] hidden pointer-events-none py-[6px] px-3 rounded-lg whitespace-nowrap bg-white left-[calc(100%+25px)] shadow-[0_5px_10px_rgba(0,0,0,0.1)] transition-none">Logout</span>
        </li>
      </ul>
    </nav>
  </aside>

  <style>
    .sidebar.collapsed {
      width: 85px;
    }

    .sidebar.collapsed .sidebar-header .toggler {
      transform: translate(-4px, 65px);
    }

    .sidebar.collapsed .sidebar-header .toggler span {
      transform: rotate(180deg);
    }

    .sidebar.collapsed .sidebar-nav .primary-nav {
      transform: translateY(65px);
    }

    .sidebar.collapsed .sidebar-nav .nav-link {
      border-radius: 12px;
    }

    .sidebar.collapsed .sidebar-nav .nav-link .nav-label {
      opacity: 0;
      pointer-events: none;
    }

    .sidebar.collapsed .sidebar-nav .nav-tooltip {
      display: block;
    }

    .sidebar-nav .nav-item:hover .nav-tooltip {
      opacity: 1;
      pointer-events: auto;
      transform: translateY(50%);
      transition: all 0.6s ease;
    }

    @media (max-width: 1024px) {
      .sidebar {
        height: 56px;
        margin: 13px;
        overflow-y: hidden;
        scrollbar-width: none;
        width: calc(100% - 26px);
        max-height: calc(100vh - 26px);
      }

      .sidebar.menu-active {
        overflow-y: auto;
      }

      .sidebar .sidebar-header {
        position: sticky;
        top: 0;
        z-index: 20;
        border-radius: 16px;
        background: #151A2D;
        padding: 8px 10px;
      }

      .sidebar-header .header-logo img {
        width: 40px;
        height: 40px;
      }

      .sidebar-header .sidebar-toggler,
      .sidebar-nav .nav-item:hover .nav-tooltip {
        display: none;
      }

      .sidebar-header .menu-toggler {
        display: flex;
      }

      .sidebar .sidebar-nav .nav-list {
        padding: 0 10px;
      }

      .sidebar-nav .nav-link {
        gap: 10px;
        padding: 10px;
        font-size: 0.94rem;
      }

      .sidebar-nav .nav-link .nav-icon {
        font-size: 1.37rem;
      }

      .sidebar-nav .secondary-nav {
        position: relative;
        bottom: 0;
        margin: 40px 0 30px;
      }
    }
  </style>

  <script>
    const sidebar = document.querySelector(".sidebar");
    const sidebarToggler = document.querySelector(".sidebar-toggler");
    const menuToggler = document.querySelector(".menu-toggler");

    let collapsedSidebarHeight = "56px";
    let fullSidebarHeight = "calc(100vh - 32px)";

    sidebarToggler.addEventListener("click", () => {
      sidebar.classList.toggle("collapsed");

      if (sidebar.classList.contains("collapsed")) {
        document.querySelectorAll(".nav-item.has-dropdown").forEach(li => {
          li.classList.remove("open");
          const sub = li.querySelector(".submenu");
          if (sub) sub.style.height = "0px";
        });
      }
    });

    const toggleMenu = (isMenuActive) => {
      sidebar.style.height = isMenuActive ? `${sidebar.scrollHeight}px` : collapsedSidebarHeight;
      menuToggler.querySelector("span").innerText = isMenuActive ? "close" : "menu";
    }

    menuToggler.addEventListener("click", () => {
      toggleMenu(sidebar.classList.toggle("menu-active"));
    });

    window.addEventListener("resize", () => {
      if (window.innerWidth >= 1024) {
        sidebar.style.height = fullSidebarHeight;
      } else {
        sidebar.classList.remove("collapsed");
        sidebar.style.height = "auto";
        toggleMenu(sidebar.classList.contains("menu-active"));
      }
    });

    document.querySelectorAll('.nav-item.has-dropdown').forEach(item => {
      const link = item.querySelector('.nav-link');
      const submenu = item.querySelector('.submenu');

      link.addEventListener('click', e => {
        e.preventDefault();

        if (sidebar.classList.contains('collapsed')) {
          sidebar.classList.remove('collapsed');
          return;
        }

        const isOpen = item.classList.contains('open');

        if (!isOpen) {
          submenu.style.height = submenu.scrollHeight + 'px';
          item.classList.add('open');
        } else {
          submenu.style.height = submenu.scrollHeight + 'px';
          submenu.offsetHeight;
          submenu.style.height = '0px';
          item.classList.remove('open');
        }
      });
    });

    document.querySelectorAll('.sidebar-nav .nav-link').forEach(link => {
      link.addEventListener('click', () => {
        if (sidebar.classList.contains('collapsed')) {
          sidebar.classList.remove('collapsed');
        }
      });
    });
  </script>

</body>
</html>