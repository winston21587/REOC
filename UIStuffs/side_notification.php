<?php ?>
<script src="https://cdn.tailwindcss.com"></script>

<div class="flex flex-col gap-2 w-auto max-w-[15rem] sm:max-w-[18rem] text-[10px] sm:text-xs z-50">
  <div
    id="notif-1"
    role="status"
    aria-live="polite"
    class="info-alert cursor-default flex items-center justify-between w-full py-2 sm:py-3 rounded-lg bg-white px-[10px] shadow-sm transition-all duration-150 ease-out"
    style="will-change: opacity, transform;"
  >
    <div class="flex gap-2">
      <div class="flex-none w-8 h-8 flex items-center justify-center text-red-600 bg-red-50 rounded-lg">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
          stroke-width="1.8"
          stroke="currentColor"
          class="w-5 h-5"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0a3 3 0 11-6 0h6z"
          ></path>
        </svg>
      </div>
      <div class="min-w-0">
        <p class="text-red-700 font-medium">Meeting Reminder</p>
        <p class="text-gray-500 text-[11px] break-words">You are scheduled for a meeting on January 1, 2025.</p>
      </div>
    </div>

    <button
      class="text-red-600 hover:bg-red-50 p-1 rounded-md transition-colors ease-linear flex items-center justify-center w-8 h-8"
      aria-label="close notification"
      onclick="closeNotif('notif-1')"
    >
      <svg
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24"
        stroke-width="1.5"
        stroke="currentColor"
        class="w-5 h-5"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          d="M6 18 18 6M6 6l12 12"
        ></path>
      </svg>
    </button>
  </div>
</div>

<script>
  function closeNotif(id) {
    const el = document.getElementById(id);
    if (!el) return;
    el.style.transition = 'opacity 180ms ease, transform 180ms ease';
    el.style.opacity = '0';
    el.style.transform = 'translateY(-6px)';
    setTimeout(() => el.remove(), 190);
  }
</script>
