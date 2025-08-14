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
    onload="this.onload=null;this.rel='stylesheet'" />
  <noscript>
    <link
      rel="stylesheet"
      href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" />
  </noscript>
</head>

<body class="tw-m-0 tw-p-0 tw-bg-black tw-text-[var(--text-color)] tw-font-sans">
  <section id="landing" 
      class="tw-relative tw-min-h-screen tw-w-full tw-py-16 tw-px-4 sm:tw-px-6 md:tw-px-12 lg:tw-px-24 tw-flex tw-flex-col tw-justify-end">
    <img
      id="hero"
      src="/images/hero.png"
      alt=""
      aria-hidden="true"
      decoding="async"
      fetchpriority="high"
      draggable="false"
      class="tw-absolute tw-inset-0 tw-z-[-3] tw-h-[100vh] tw-w-[100vw] tw-object-cover tw-pointer-events-none tw-cursor-default tw-object-[center_45%]"
    />

    <div class="tw-max-w-6xl tw-mx-auto tw-grid tw-gap-12 sm:tw-grid-cols-1 md:tw-grid-cols-2 md:tw-items-start">

      <h1 class="display-lg tw-leading-tight sm:tw-col-span-1 md:tw-col-span-8 lg:tw-col-span-13">
        I BREAK LIMITS.<br>
        <span class="block md:tw-col-start-3 lg:tw-col-start-4">
          THEN REDEFINE THEM.
        </span>
      </h1>

      <div class="sm:tw-col-span-1 md:tw-col-start-4 md:tw-col-span-5 lg:tw-col-span-13 tw-text-justify">
        <h2 class="title-lg">
          I’M EMANUELE FRASCELLA — A SOFTWARE DEVELOPER OBSESSED WITH PERFORMANCE, SCALABILITY, AND ELEGANT SOLUTIONS.
        </h2>
      </div>
    </div>
  </section>

  <section id="contact" class="tw-bg-black tw-text-white tw-py-16 tw-px-4 sm:tw-px-6 md:tw-px-12 lg:tw-px-24">
    <div class="tw-max-w-6xl tw-mx-auto tw-grid tw-gap-12 sm:tw-grid-cols-1 md:tw-grid-cols-2 md:tw-items-start">

      <div>
        <h2 class="display-lg">Let's Build Something Unstoppable.</h2>
        <p class="tw-mt-4 tw-text-lg tw-text-gray-400">
          Got an idea, a challenge, or just want to say hi? Drop me a message — I read every single one.
        </p>
      </div>

      <form class="tw-grid tw-gap-6">
        <div>
          <input type="text" name="name" placeholder="Your Name" class="custom-input" />
        </div>

        <div>
          <input type="email" name="email" placeholder="Your Email" class="custom-input" />
        </div>

        <div>
          <input type="text" name="subject" placeholder="Subject" class="custom-input" />
        </div>

        <div>
          <textarea name="message" rows="5" placeholder="Your Message" class="custom-input"></textarea>
        </div>

        <div>
          <button type="submit" class="custom-button">
            Send Message
          </button>
        </div>
      </form>
    </div>
  </section>

  <footer class="tw-bg-black tw-text-gray-500 tw-text-center tw-py-6 tw-text-sm">
    &copy; 2025 Emanuele Frascella — All rights reserved.
  </footer>

  <!-- JS Libraries -->
  <script src="https://cdn.jsdelivr.net/npm/gsap@3.13.0/dist/gsap.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/gsap@3.13.0/dist/ScrollTrigger.min.js"></script>
  <script src="https://unpkg.com/split-type"></script>
  <script src="https://cdn.jsdelivr.net/npm/fitty@2.4.2/dist/fitty.min.js"></script>

  <!-- Custom JS -->
  <script src="/js/pages/home.js"></script>
</body>

</html>