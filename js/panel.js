fetch("partials/header.html").then(response => response.text()).
then(html => {
    document.getElementById("panel-header").innerHTML=html;

    const allButtons=document.querySelectorAll(".topnav button");
    allButtons.forEach(btn => btn.classList.remove("active-btn"));
    const panel=document.getElementById("panel-btn");
    if(panel)
        panel.classList.add("active-btn");

    const oldStyle=document.getElementById("header-style");
    if(oldStyle)
        oldStyle.remove();
    
    const headerLink=document.createElement("link");
    headerLink.href="css/header.css";
    headerLink.rel="stylesheet";
    headerLink.id="header-style";
    document.head.appendChild(headerLink);

    const oldScript=document.getElementById("header-script");
    if(oldScript)
      oldScript.remove();

    const script=document.createElement("script");
    script.src=`js/header.js`;
    script.id="header-script";
    document.body.appendChild(script);

})
.catch(error=> console.log('Error in header.html: ', error));


fetch("partials/footer.html").then(response => response.text()).
then(html => {
    document.getElementById("panel-footer").innerHTML=html;

    const oldFooterStyle=document.getElementById("footer-style");
    if(oldFooterStyle)
        oldFooterStyle.remove();
    
    const footerLink=document.createElement("link");
    footerLink.href="css/footer.css";
    footerLink.rel="stylesheet";
    footerLink.id="footer-style";
    document.head.appendChild(footerLink);
})
.catch(error=> console.log('Error in footer.html: ', error));


fetch("php/user_status.php").then(response => response.json())
.then(result => {
    if(result.success)
    {
        if(!result.is_admin)
        {
            pageNavigation("dashboard");
            return;
        }
        const footerUser=document.getElementById("footer-user");
        if(footerUser)
        {
            let role="User";
            if(result.is_admin===true)
            {    role="Admin";}
            else if(result.is_family===true)
            {    role="Family";}

            footerUser.textContent=`${role}: ${result.user_name}`;
        }
    }
    else
        pageNavigation("login");// redirect if not logged in
}).catch(error=> console.log('Error: ', error));


document.getElementById("db-init-btn").addEventListener("click", () => {
    fetch("scripts/init_db.php").then(response => response.json()).then(result => {
        if(result.success)
        {
            alert(result.message);
        }
        else
        {
            pageNavigation("notfound");
        }
    }).catch(error => console.log("DB initialization error: " + error));
});

document.getElementById("deletePet-form").addEventListener("submit", function (event) {
    event.preventDefault();

    const id=document.getElementById("deletePet-input").value;

    const formData=new FormData();
    formData.append("deletePet",id);

    fetch("php/panel.php",{
        method: "POST",
        body: formData
    }).then(response => response.json())
    .then(result => {
        alert(result.message);
    }).catch(error => console.log("Deleting pet error: " + error));
});