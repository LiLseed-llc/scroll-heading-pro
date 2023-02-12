// Vanilla JS equivalent code

(function() {

document.addEventListener("DOMContentLoaded", function() {
  // Define variables
  var box = document.querySelector(".scrollheading-target .scrollheading-box");
  var boxHeight = box.offsetHeight;
  var currentPosition = 0;
  var targetPosition = box.getBoundingClientRect().top + window.scrollY;

  // Get the color and size settings from scrollheading_vars
  var bgColor = scrollheading_vars.background_color;
  var textColor = scrollheading_vars.color;

  // Set the color and size of the box
  box.style.backgroundColor = bgColor;
  box.style.color = textColor;

  // Additional code
  document.querySelector('.scrollheading-box').style.backgroundColor = scrollheading_vars.box_color;
  document.querySelector('.scrollheading-box').style.color = scrollheading_vars.color;

  // requestAnimationFrame function
  function scrollheading() {
    var windowTop = window.scrollY;

    // Don't get offset values if scroll position doesn't change
    if (currentPosition != windowTop) {

      // Box is fixed to the top of the screen
      if (windowTop > targetPosition) {
        box.classList.add("scrollheading-fixed", "active");
      } else {
        box.classList.remove("scrollheading-fixed", "active");
      }

      // Update scroll position
      currentPosition = windowTop;
    }

    // Call requestAnimationFrame again
    requestAnimationFrame(scrollheading);
  }

  // Scroll and don't disappear on PC and mobile
  var timeout;
  window.addEventListener("scroll", function() {
    requestAnimationFrame(scrollheading);
  });

  // Delete button click event
  document.addEventListener("click", function(event) {
    if (event.target.classList.contains("delete-button")) {
      var boxId = event.target.dataset.boxId;
      var xhr = new XMLHttpRequest();
      xhr.open("POST", scrollheadingAjax.ajaxurl);
      xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
      xhr.onload = function() {
        if (xhr.status === 200) {
          // Remove from list
          var targetRow = document.querySelector('tr[data-box-id="' + boxId + '"]');
          targetRow.parentNode.removeChild(targetRow);
        }
      };
      xhr.send("action=delete_box&boxId=" + boxId);
    }
  });

});

})();