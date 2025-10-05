<?php ?>
<script src="https://cdn.tailwindcss.com"></script>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Generating</title>

  <script src="https://cdn.tailwindcss.com"></script>

  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">

  <style>
    .font-inter { font-family: "Inter", ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial; }

    .loader {
      position: absolute;
      top: 0; left: 0;
      width: 100%;
      aspect-ratio: 1 / 1;
      border-radius: 50%;
      background: transparent;
      z-index: 0;
      animation: loader-rotate 2s linear infinite;
      will-change: transform, box-shadow;
    }

    @keyframes loader-rotate {
      0% {
        transform: rotate(90deg);
        box-shadow:
          0 8px 18px 0 rgba(255,255,255,0.95) inset,
          0 18px 30px 0 rgba(255,110,110,0.95) inset,
          0 40px 60px 0 rgba(135,58,255,0.85) inset;
      }
      50% {
        transform: rotate(270deg);
        box-shadow:
          0 8px 18px 0 rgba(255,255,255,0.95) inset,
          0 18px 14px 0 rgba(214,10,71,0.95) inset,
          0 35px 60px 0 rgba(49,30,128,0.85) inset;
      }
      100% {
        transform: rotate(450deg);
        box-shadow:
          0 8px 18px 0 rgba(255,255,255,0.95) inset,
          0 18px 30px 0 rgba(255,110,110,0.95) inset,
          0 40px 60px 0 rgba(135,58,255,0.85) inset;
      }
    }

    .loader-letter {
      display: inline-block;
      opacity: 0.4;
      transform: translateY(0) scale(1);
      z-index: 10;
      border-radius: 9999px;
      animation: loader-letter-anim 2s infinite;
      will-change: transform, opacity, color;
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
    }

    @keyframes loader-letter-anim {
      0%, 100% {
        opacity: 0.4;
        transform: translateY(0) scale(1);
        color: rgba(255,255,255,0.9);
        filter: none;
        text-shadow: none;
      }
      20% {
        opacity: 1;
        transform: translateY(-6px) scale(1.15);
        color: rgb(255,80,95);
        text-shadow: 0 6px 18px rgba(255,60,80,0.25);
      }
      40% {
        opacity: 0.7;
        transform: translateY(0) scale(1);
        color: rgba(255,255,255,0.95);
        text-shadow: none;
      }
    }

    .loader-letter:nth-child(1)  { animation-delay: 0s;   }
    .loader-letter:nth-child(2)  { animation-delay: 0.08s; }
    .loader-letter:nth-child(3)  { animation-delay: 0.16s; }
    .loader-letter:nth-child(4)  { animation-delay: 0.24s; }
    .loader-letter:nth-child(5)  { animation-delay: 0.32s; }
    .loader-letter:nth-child(6)  { animation-delay: 0.40s; }
    .loader-letter:nth-child(7)  { animation-delay: 0.48s; }
    .loader-letter:nth-child(8)  { animation-delay: 0.56s; }
    .loader-letter:nth-child(9)  { animation-delay: 0.64s; }
    .loader-letter:nth-child(10) { animation-delay: 0.72s; }

    .glow-mask::before {
      content: "";
      position: absolute;
      inset: 0;
      border-radius: 9999px;
      background: radial-gradient(ellipse at center, rgba(255,255,255,0.02), transparent 35%);
      z-index: 1;
      pointer-events: none;
    }
  </style>

</head>

<body class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-neutral-900 flex items-center justify-center p-6">

  <div class="font-inter relative flex items-center justify-center w-44 h-44 text-lg sm:text-xl font-light text-white rounded-full select-none">

    <div class="loader rounded-full"></div>

    <div class="glow-mask rounded-full pointer-events-none"></div>

    <div class="relative z-20 flex gap-0 items-center justify-center text-[15px] sm:text-[17px] tracking-wide">
      <span class="loader-letter px-0">G</span>
      <span class="loader-letter px-0">e</span>
      <span class="loader-letter px-0">n</span>
      <span class="loader-letter px-0">e</span>
      <span class="loader-letter px-0">r</span>
      <span class="loader-letter px-0">a</span>
      <span class="loader-letter px-0">t</span>
      <span class="loader-letter px-0">i</span>
      <span class="loader-letter px-0">n</span>
      <span class="loader-letter px-0">g</span>
    </div>
  </div>


</body>
</html>
