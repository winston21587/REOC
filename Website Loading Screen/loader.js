const loaderStartTime = Date.now();

window.addEventListener("load", () => {
  const minDisplayTime = 3000;
  const elapsedTime = Date.now() - loaderStartTime;
  const remainingTime = Math.max(0, minDisplayTime - elapsedTime);
  
  const loaderContainer = document.getElementById("loading-screen");
  
  setTimeout(() => {
    if (loaderContainer) {
      loaderContainer.style.opacity = "0";
      loaderContainer.style.transition = "opacity 0.5s ease-out";
      
      setTimeout(() => {
        loaderContainer.style.display = "none";
      }, 500);
    }
  }, remainingTime);
});
