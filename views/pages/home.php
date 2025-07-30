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
<body class="tw-p-0 tw-m-0 tw-bg-[var(--background-color)] tw-text-[var(--text-color)] tw-font-sans">
    <section class="tw-h-[100svh]">
        <!-- Fixed Background Image -->
        <img
            src="/images/hero.gif"
            alt=""
            class="tw-fixed tw-inset-0 tw-object-cover tw-w-full tw-h-[100svh] tw-z-[-1]
                tw-filter tw-saturate-[0.9] tw-brightness-105 tw-hue-rotate-[8deg]
                tw-cursor-default tw-pointer-events-none"
        >

        <!-- Scroll Indicator -->
        <div
            id="hscroll"
            class="tw-fixed tw-top-0 tw-left-0 tw-w-full tw-h-[100svh] 
                tw-flex tw-flex-col tw-items-center tw-justify-end tw-gap-4 tw-p-4
                tw-z-10 tw-opacity-75 tw-pointer-events-none">
            <?php include __DIR__ . '/../components/scroll-indicator.php'; ?>
            <div class="tw-text-[var(--background-color)] tw-text-sm tw-opacity-75">
                Scroll down to explore
            </div>
        </div>

        <div id="htext" class="tw-overflow-hidden tw-h-[100svh] tw-flex tw-items-center tw-z-20">
            <h1 id="htext-title"
                class="tw-whitespace-nowrap tw-font-bold
                    tw-text-[var(--background-color)]
                    tw-px-[100vw] text-center">
                “Even My Exceptions Have Style”
            </h1>
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
