<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Emanuele Frascella - Full Stack Developer</title>

    <link href="/css/tailwind.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/pages/home.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Bebas Neue and DM Sans Fonts -->
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap">
    </noscript>

    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0">
    <meta name="description" content="Welcome to frascella.dev, a personal project by Emanuele Frascella.">
    <meta name="keywords" content="frascella.dev, Emanuele Frascella, web development, full-stack developer, backend, frontend">
    <meta name="author" content="Emanuele Frascella">
</head>

<body class="tw-p-0 tw-m-0 tw-bg-black tw-text-[var(--text-color)] tw-font-sans">
    <header class="tw-fixed tw-top-0 tw-left-0 tw-right-0 tw-z-10 tw-px-8 tw-py-4 tw-h-[46px]">
        <div class="date-time paragraph tw-absolute tw-uppercase tw-top-1/2 tw-left-8 tw-transform tw--translate-y-1/2">
            <?php
            echo date('DH:i:s');
            ?>
        </div>
        <div class="paragraph tw-hidden md:tw-block
                tw-absolute tw-top-1/2 tw-left-1/2 
                tw-transform tw--translate-x-1/2 tw--translate-y-1/2 
                tw-text-center tw-uppercase">
            &copy; 2025 Emanuele Frascella
        </div>
        <div class="lan paragraph tw-absolute tw-top-1/2 tw-right-8 tw-transform tw--translate-y-1/2">
            LAN EN >
        </div>
    </header>

    <section
        id="landing"
        class="tw-min-h-[100vh] tw-h-[min(1024px, 100vh)] tw-relative">
        <!-- Fixed Background Image -->
        <img
            id="landing-background"
            src="/images/hero-background.png"
            alt=""
            class="tw-fixed tw-inset-0 tw-w-[100vw] tw-h-[100vh] tw-z-[-3]
                tw-cursor-default tw-pointer-events-none
                tw-object-cover tw-object-center">

        <!-- Hero Title -->
        <h1 
            id="hero-title"
            class="tw-text-center headline1
            tw-fixed tw-z-[-2]
            tw-transition-all tw-duration-300 tw-ease-in-out
            tw-top-1/2 tw-left-1/2 tw-transform
            tw--translate-x-1/2 tw--translate-y-1/2
            !tw-leading-[0.8]">
            Frascella
        </h1>

        <!-- Fixed Hero Image -->
        <img
            id="hero"
            src="/images/hero-graffiti.png"
            alt=""
            class="tw-fixed tw-inset-0 tw-h-[100vh] tw-w-fit tw-left-1/2 tw-top-1/2
                tw-transform tw--translate-x-1/2 tw--translate-y-1/2
                tw-object-cover tw-object-center tw-z-[-1]
                tw-cursor-default tw-pointer-events-none
                tw-scale-x-[1.2] tw-scale-y-[1.2] 
                md:tw-scale-x-[1.4] md:tw-scale-y-[1.4] lg:tw-scale-x-[1.6] lg:tw-scale-y-[1.6]
                tw-transition-all tw-duration-300 tw-ease-in-out">
    </section>

    <!-- JS Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.13.0/dist/gsap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.13.0/dist/ScrollTrigger.min.js"></script>
    <script src="https://unpkg.com/split-type"></script>
    <script src="
    https://cdn.jsdelivr.net/npm/fitty@2.4.2/dist/fitty.min.js
    "></script>

    <!-- Custom JS -->
    <script src="/js/home.js"></script>
</body>

</html>