<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Emanuele Frascella - Full Stack Developer</title>

  <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0" />
  <meta name="description" content="Welcome to frascella.dev, a personal project by Emanuele Frascella." />
  <meta name="keywords" content="frascella.dev, Emanuele Frascella, web development, full-stack developer, backend, frontend" />
  <meta name="author" content="Emanuele Frascella" />

  <link href="/css/tailwind.css" rel="stylesheet" />
  <link rel="stylesheet" href="/css/style.css" />
  <link rel="stylesheet" href="/css/pages/landing.css" />

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />

  <!-- Bebas Neue and DM Sans Fonts -->
  <link
    rel="preload"
    href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap"
    as="style"
    onload="this.onload=null;this.rel='stylesheet'"
  />
  <noscript>
    <link
      rel="stylesheet"
      href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap"
    />
  </noscript>
</head>

<body class="tw-m-0 tw-p-0 tw-bg-black tw-text-[var(--text-color)] tw-font-sans">
  <header class="tw-fixed tw-inset-x-0 tw-top-0 tw-z-10 tw-h-[46px] tw-w-full tw-px-8 tw-py-4">
    <div class="date-time paragraph tw-absolute tw-left-8 tw-top-1/2 tw--translate-y-1/2 tw-transform tw-uppercase">
      <?php echo date('D H:i:s'); ?>
    </div>

    <div class="paragraph tw-absolute tw-left-1/2 tw-top-1/2 tw-hidden tw--translate-x-1/2 tw--translate-y-1/2 tw-transform tw-text-center tw-uppercase md:tw-block">
      &copy; 2025 Emanuele Frascella
    </div>

    <div class="lan paragraph tw-absolute tw-right-8 tw-top-1/2 tw--translate-y-1/2 tw-transform">
      LAN EN &gt;
    </div>
  </header>

  <section id="landing" class="tw-relative tw-min-h-[100vh] tw-h-[min(1024px,100vh)]">
    <!-- Fixed Background Image (decorative) -->
    <img
      id="landing-background"
      src="/images/hero-background.png"
      alt=""
      aria-hidden="true"
      decoding="async"
      fetchpriority="high"
      draggable="false"
      class="tw-fixed tw-inset-0 tw-z-[-3] tw-h-[100vh] tw-w-[100vw] tw-object-cover tw-object-center tw-pointer-events-none tw-cursor-default"
    />

    <!-- Hero Title -->
    <h1
      id="hero-title"
      class="headline1 tw-fixed tw-left-1/2 tw-top-1/2 tw-z-[-2] tw-text-center tw-transform tw--translate-x-1/2 tw--translate-y-1/2 tw-transition-all tw-duration-300 tw-ease-in-out !tw-leading-[0.8]"
    >
      Frascella
    </h1>

    <!-- Fixed Hero Image (PNG con trasparenza, in primo piano) -->
    <img
      id="hero"
      src="/images/hero-graffiti.png"
      alt=""
      aria-hidden="true"
      decoding="async"
      fetchpriority="high"
      draggable="false"
      class="tw-fixed tw-left-1/2 tw-top-1/2 tw-inset-0 tw-z-[-1] tw-h-[100vh] tw-w-fit tw-object-cover tw-object-center tw-transform tw--translate-x-1/2 tw--translate-y-1/2 tw-scale-[1.2] md:tw-scale-[1.4] lg:tw-scale-[1.6] tw-pointer-events-none tw-cursor-default tw-transition-all tw-duration-300 tw-ease-in-out"
    />
  </section>

  <!-- JS Libraries -->
  <script src="https://cdn.jsdelivr.net/npm/gsap@3.13.0/dist/gsap.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/gsap@3.13.0/dist/ScrollTrigger.min.js"></script>
  <script src="https://unpkg.com/split-type"></script>
  <script src="https://cdn.jsdelivr.net/npm/fitty@2.4.2/dist/fitty.min.js"></script>

  <!-- Custom JS -->
  <script src="/js/home.js"></script>
</body>
</html>
