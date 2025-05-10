fetch("partials/header.html").then(response => response.text()).
then(html => {
    document.getElementById("addpets-header").innerHTML=html;

    const allButtons=document.querySelectorAll(".topnav button");
    allButtons.forEach(btn => btn.classList.remove("active-btn"));
    const addpets=document.getElementById("addpets-btn");
    if(addpets)
        addpets.classList.add("active-btn");

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
    document.getElementById("addpets-footer").innerHTML=html;

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

        if(!result.is_admin)
            document.getElementById("panel-btn").style.display="none";
    }
    else
        pageNavigation("login");// redirect if not logged in
}).catch(error=> console.log('Error: ', error));


document.getElementById("photo-input").addEventListener("change", function () {
    const list=document.getElementById("photo-list");
    list.innerHTML='';
    
    const files=this.files;

    for(let i=0; i<files.length;i++)
    {
        const item=document.createElement("li");
        item.textContent=files[i].name;
        list.appendChild(item);
    }
});

document.getElementById("video-input").addEventListener("change", function () {
    const list=document.getElementById("video-list");
    list.innerHTML='';

    const files=this.files;

    for(let i=0;i<files.length;i++)
    {
        const item=document.createElement("li");
        item.textContent=files[i].name;
        list.appendChild(item);
    }
});

document.getElementById("audio-input").addEventListener("change", function () {
    const list=document.getElementById("audio-list");
    list.innerHTML='';

    const files=this.files;

    for(let i=0;i<files.length;i++)
    {
        const item=document.createElement("li");
        item.textContent=files[i].name;
        list.appendChild(item);
    }
});

document.getElementById("addpets-form").addEventListener("submit", function(event) {
    event.preventDefault();

    const form=document.getElementById("addpets-form");
    const formData= new FormData(form);

    let totalSize=0;
    ['photos[]', 'videos[]', 'audios[]'].forEach(field => {
        const files=formData.getAll(field);
        files.forEach(file => {
            if(file instanceof File)
                totalSize=totalSize+file.size;
        });
    });

    if(totalSize>200*1024*1024)
    {
        alert("Total size for uploads exceeds 200MB! Remove some files.");
        return;
    }

    fetch('php/addpets.php', {
        method: 'POST',
        body: formData
    }).then(response => response.json())
    .then(result => {
        if(result.success)
        {
            alert(result.message);
            location.reload();
        }
        else if(result.errors)
            alert(result.errors.join("\n"));
    })
    .catch(error => console.log('Error adding pets: ', error));
});