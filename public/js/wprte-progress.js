(function () {
  if (typeof WPRTE_PROGRESS === "undefined") return;

  // Grab config from PHP
  var position = WPRTE_PROGRESS.position || "top";
  var height = WPRTE_PROGRESS.height || 3;
  var color = WPRTE_PROGRESS.color || "#3b82f6";

  var progress = document.getElementById("wprte-progress");
  if (!progress) return;

  // Apply custom CSS vars
  progress.classList.add(position);
  var bar = progress.querySelector(".wprte-progress-bar");
  if (bar) {
    bar.style.setProperty("--wprte-height", height + "px");
    bar.style.setProperty("--wprte-color", color);
  }

  function updateProgress() {
    var scrollTop = window.scrollY || document.documentElement.scrollTop;
    var docHeight = document.body.scrollHeight - window.innerHeight;
    var scrolled = docHeight > 0 ? (scrollTop / docHeight) * 100 : 0;
    if (bar) bar.style.width = scrolled + "%";
  }

  window.addEventListener("scroll", updateProgress);
  window.addEventListener("resize", updateProgress);
  document.addEventListener("DOMContentLoaded", updateProgress);

  // Run once on load
  updateProgress();
})();
