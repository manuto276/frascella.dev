document.addEventListener("DOMContentLoaded", () => {
  gsap.registerPlugin(ScrollTrigger);

  const section = document.querySelector("section");     // ora la section intera
  const htext = document.getElementById("htext");        // contenitore orizzontale
  const titleEl = document.getElementById("htext-title");
  const hscrollEl = document.getElementById("hscroll");

  // split in caratteri
  new SplitType(titleEl, { types: "chars" });

  // distanza orizzontale da coprire
  const textWidth = titleEl.scrollWidth;
  const distanceToScroll = textWidth - window.innerWidth;

  // ridimensiona la section per ospitare lo scroll verticale
  // altezza = viewport + spazio orizzontale da “convertire” in scroll
  section.style.height = `${window.innerHeight + distanceToScroll}px`;

  // timeline orizzontale + pin + scrub
  const tl = gsap.to(titleEl, {
    x: `-${distanceToScroll}px`,
    ease: "none",
    scrollTrigger: {
      trigger: htext,
      start: "top top",
      pin: true,
      scrub: true,
      // finisce dopo che la page ha scorreto distanceToScroll px
      end: `+=${distanceToScroll}`
    }
  });

  // animazione carattere per carattere
  titleEl.querySelectorAll(".char").forEach((char, i) => {
    const dir = i % 2 === 0 ? -1 : 1;
    const yStart = gsap.utils.random(60, 120) * dir;
    const rot = gsap.utils.random(-35, 35);

    gsap.fromTo(char, {
      yPercent: yStart,
      rotation: rot
    }, {
      yPercent: 0,
      rotation: 0,
      ease: "elastic.out(1.2,1)",
      scrollTrigger: {
        containerAnimation: tl,
        trigger: char,
        start: "left 100%",
        end: "left 0%",
        scrub: 0.5
      }
    });
  });

  // fade-out scroll indicator dopo 100px di scroll
  if (hscrollEl) {
    gsap.to(hscrollEl, {
      opacity: 0,
      y: 50,
      duration: 0.5,
      scrollTrigger: {
        trigger: section,
        start: "top+=100 top",
        toggleActions: "play none none reverse"
      }
    });
  }

  // aggiorna tutto al resize
  window.addEventListener("resize", () => {
    const newTextW = titleEl.scrollWidth;
    const newDist = newTextW - window.innerWidth;
    section.style.height = `${window.innerHeight + newDist}px`;
    tl.vars.x = `-${newDist}px`;
    tl.scrollTrigger.end = `+=${newDist}`;
    ScrollTrigger.refresh();
  });
});
