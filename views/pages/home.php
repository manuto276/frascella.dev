<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Emanuele Frascella - Full Stack Developer</title>

    <link href="/css/tailwind.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/pages/home.css">

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
        <div class="copyright paragraph tw-absolute tw-top-1/2 tw-left-1/2 tw-transform tw--translate-x-1/2 tw--translate-y-1/2 tw-text-center tw-uppercase">
            &copy; 2025 Emanuele Frascella
        </div>
        <div class="lan paragraph tw-absolute tw-top-1/2 tw-right-8 tw-transform tw--translate-y-1/2">
            LAN EN >
        </div>
    </header>

    <section class="tw-h-[100vh] section-1">
        <!-- Fixed Background Image -->
        <img
            src="/images/hero-background.png"
            alt=""
            class="tw-fixed tw-inset-0 tw-w-[100vw] tw-h-[100vh] tw-z-[-3]
                tw-cursor-default tw-pointer-events-none
                hero-background"
        >

        <!-- Hero Title -->
        <div class="tw-fixed tw-inset-0 tw-w-[100vw] tw-h-[100vh]
                tw-cursor-default tw-z-[-2]">
            <div class="hero-title-container tw-text-center">
                <h1 class="tw-text-center headline1">Frascella</h1>
            </div>
        </div>

        <!-- Fixed Hero Image -->
        <div class="tw-fixed tw-inset-0 tw-w-[100vw] tw-h-[100vh]
                tw-cursor-default tw-pointer-events-none
                hero-image-container">
            <img
                src="/images/hero-graffiti.png"
                alt=""
                class="tw-w-[100vw] tw-h-[100vh]
                    tw-cursor-default tw-pointer-events-none
                    hero-image"
            >
        </div>

        <!-- Scroll Indicator -->
        <div class="tw-fixed tw-bottom-0 tw-left-0 tw-right-0 tw-p-4 tw-flex tw-justify-center tw-items-center tw-gap-4 tw-opacity-50">
            <?php include __DIR__ . '/../components/scroll-indicator.php'; ?>
            <span class="tw-text-[var(--text-color)] tw-text-sm">scroll down to explore</span>
        </div>
    </section>

    <!-- JS Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.13.0/dist/gsap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.13.0/dist/ScrollTrigger.min.js"></script>
    <script src="https://unpkg.com/split-type"></script>

    <!-- Custom JS -->
    <script src="/js/pages/home.js"></script>
</body>
</html>
