//JAVASCRIPT CODES

document.addEventListener("DOMContentLoaded", function () {
// Get the modal and buttons
var modal = document.getElementById("modal");
var openModalBtn = document.getElementById("openModalBtn");
var closeModalBtn = document.getElementById("closeModalBtn");

// Open the modal when the open button is clicked
openModalBtn.addEventListener("click", function () {
  modal.style.display = "block";
});

// Close the modal when the close button is clicked
closeModalBtn.addEventListener("click", function () {
  modal.style.display = "none";
});

// Close the modal if the user clicks outside the modal content
window.addEventListener("click", function (event) {
  if (event.target === modal) {
    modal.style.display = "none";
  }
});

// Close the alert messages
var closeAlertButtons = document.querySelectorAll(".close-alert");
closeAlertButtons.forEach(function (button) {
    button.addEventListener("click", function (e) {
        e.target.parentElement.style.display = "none";
    });
});
});



