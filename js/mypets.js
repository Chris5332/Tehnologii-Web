fetch("partials/header.html").then(response => response.text()).
then(html => {
    document.getElementById("mypets-header").innerHTML=html;

    const allButtons=document.querySelectorAll(".topnav button");
    allButtons.forEach(btn => btn.classList.remove("active-btn"));
    const mypets=document.getElementById("mypets-btn");
    if(mypets)
        mypets.classList.add("active-btn");

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
    document.getElementById("mypets-footer").innerHTML=html;

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


fetch("php/mypets.php").then(response => response.json())
.then(result => {
    if(result.success)
    {
        const display=document.getElementById("mypets");
        display.innerHTML="";

        if(result.data.length===0)
        {
            const noPets=document.createElement("p");
            noPets.textContent="You have no pets added!";
            noPets.className="noPets-class";
            display.appendChild(noPets);
            return;
        }

        result.data.forEach(pet =>{
            const card=document.createElement("div");
            card.className="pet-card";

            const img=document.createElement("img");
            if(!pet.image_path)
                img.src="assets/no-photo.jpg";
            else
                img.src=pet.image_path;
            img.alt="Pet image";
            img.className="pet-img";
            img.draggable=false;

            const containerForTexts=document.createElement("div");
            containerForTexts.className="container-texts";

            const name=document.createElement("p");
            name.textContent=pet.name;

            const species=document.createElement("p");
            species.textContent="Species: "+pet.species;

            const breed=document.createElement("p");
            breed.textContent="Breed: "+pet.breed;

            const health_status=document.createElement("p");
            health_status.textContent="Health status: "+pet.health_status;

            const region=document.createElement("p");
            region.textContent="Region: "+pet.region;

            if(pet.is_group===1)
                name.textContent="Group of Pets: "+pet.name;

            containerForTexts.append(name,species,breed,health_status,region);

            const button=document.createElement("button");
            button.textContent="View Pet";
            button.className="button-view";
            button.onclick= ()=>{ pageNavigation(`animal?id=${pet.id}`);}

            card.append(img,containerForTexts,button);
            display.appendChild(card);
        });
    }
    else
    {
        display.innerHTML="Error occurred while searching for your pets.";
    }
}).catch(error=> console.log('Error: ', error));