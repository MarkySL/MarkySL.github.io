// Add Hovered Class to selected list item
let list = document.querySelectorAll(".nav_bar li");

function activeLink() {
    list.forEach(item=>{
        item.classList.remove("hovered");
    });
    this.classList.add("hovered");
}

list.forEach(item => item.addEventListener("mouseover", activeLink));

// Navigation Bar Menu Toggle
let toggle = document.querySelector(".toggle"); //html toggle class
let nav_bar = document.querySelector(".nav_bar"); //html nav_bar class
let main_bar = document.querySelector(".main_bar"); //html main_bar class

toggle.onclick = function() {
    nav_bar.classList.toggle("active");
    main_bar.classList.toggle("active");
}


