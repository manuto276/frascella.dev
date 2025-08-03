<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Home</title>

    <link href="/css/tailwind.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/pages/home.css">

    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0">
    <meta name="description" content="Welcome to frascella.dev, a personal project by Emanuele Frascella.">
    <meta name="keywords" content="frascella.dev, Emanuele Frascella, web development, full-stack developer, backend, frontend">
    <meta name="author" content="Emanuele Frascella">
</head>
<body class="tw-p-0 tw-m-0 tw-bg-black tw-text-[var(--text-color)] tw-font-sans">
    <section class="tw-h-[100vh]">
        <!-- Fixed Background Image -->
        <img
            src="/images/hero-graffiti.png"
            alt=""
            class="tw-fixed tw-inset-0 tw-w-[100vw] tw-h-[100vh] tw-z-[-1]
                tw-cursor-default tw-pointer-events-none
                hero-image"
        >

        <!-- Masked Title -->
        <div class="hero-title-mask tw-fixed tw-inset-0 tw-w-[100vw] tw-h-[100vh] tw-z-[-1]
                tw-cursor-default tw-pointer-events-none tw-flex tw-items-center tw-justify-center">
            <h1 class="tw-text-center">Frascella</h1>
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

    <!-- Custom Script -->
    <script src="/js/home.js"></script>
</body>
</html>
