document.getElementById("logout-btn").addEventListener("click", () => {
    fetch("php/logout.php").then(response => response.json()).then(result => {
        if(result.success)
            pageNavigation("login");
    })
    .catch(error => console.log("Logout error: " + error));
});

document.getElementById("menu-btn").addEventListener("click", () => {
    const menu_item=document.querySelector(".topnav");
    menu_item.classList.toggle("responsive");
});

document.getElementById("home-btn").addEventListener("click", () => {
    pageNavigation("dashboard");
});

document.getElementById("addpets-btn").addEventListener("click", () => {
    pageNavigation("addpets");
});

document.getElementById("mypets-btn").addEventListener("click", () => {
    pageNavigation("mypets");
});

document.getElementById("browse-btn").addEventListener("click", () => {
    pageNavigation("browse");
});

document.getElementById("requests-btn").addEventListener("click", () => {
    pageNavigation("requests");
});

document.getElementById("panel-btn").addEventListener("click", () => {
    pageNavigation("panel");
});