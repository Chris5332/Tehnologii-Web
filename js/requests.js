fetch("partials/header.html").then(response => response.text()).
then(html => {
    document.getElementById("requests-header").innerHTML=html;

    const allButtons=document.querySelectorAll(".topnav button");
    allButtons.forEach(btn => btn.classList.remove("active-btn"));
    const requests=document.getElementById("requests-btn");
    if(requests)
        requests.classList.add("active-btn");

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
    document.getElementById("requests-footer").innerHTML=html;

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
        {
            const panelBtn=document.getElementById("panel-btn");
            if(panelBtn)
                panelBtn.style.display = "none";
        }
    }
    else
        pageNavigation("login");// redirect if not logged in
}).catch(error=> console.log('Error: ', error));

showFilterOption("received");

function showFilterOption(option){
    fetch(`php/requests.php?filter=${option}`).then(response => response.json())
    .then(result => {

        const display=document.getElementById("requests");
        if(result.success)
        {
            display.innerHTML="";

            if(result.data.length===0)
            {
                const noPets=document.createElement("p");
                noPets.textContent="No requests found!";
                noPets.className="noPets-class";
                display.appendChild(noPets);
                return;
            }

            result.data.forEach(req =>{
                const card=document.createElement("div");
                card.className="req-card";

                const containerForTexts=document.createElement("div");
                containerForTexts.className="container-texts";

                if(option==="received")
                {
                    const message=document.createElement("p");
                    message.textContent="[(id:"+req.user_id+")";
                    
                    if(req.is_family===1)
                        message.textContent+=" Family: ";
                    else
                        message.textContent+=" User: ";

                    message.textContent+=req.user_name+"] requests ";

                    if(req.is_group===1)
                        message.textContent+="group ";
                    else
                        message.textContent+="pet ";

                    message.textContent+=req.animal_name+"(id:"+req.animal_id+").";
                    containerForTexts.append(message);

                    const buttonAccept=document.createElement("button");
                    buttonAccept.textContent="Accept";
                    buttonAccept.className="button-accept";

                    const buttonDecline=document.createElement("button");
                    buttonDecline.textContent="Decline";
                    buttonDecline.className="button-decline";

                    buttonAccept.onclick= () => {
                        const formData=new FormData();
                        formData.append("field","accept");
                        formData.append("id",req.request_id);

                        fetch("php/request_respond.php", {
                            method: 'POST',
                            body: formData
                        }).then(response => response.json())
                        .then(result => {
                            if(result.success)
                            {
                                alert(result.message);
                                showFilterOption("received");
                            }
                            else
                            {
                                alert(result.message);
                            }
                        })
                        .catch(error => console.log('Error: ', error));
                    };

                    buttonDecline.onclick= () => {
                        const formData=new FormData();
                        formData.append("field","decline");
                        formData.append("id",req.request_id);

                        fetch("php/request_respond.php", {
                            method: 'POST',
                            body: formData
                        }).then(response => response.json())
                        .then(result => {
                            if(result.success)
                            {
                                alert(result.message);
                                showFilterOption("received");
                            }
                            else
                            {
                                alert(result.message);
                            }
                        })
                        .catch(error => console.log('Error: ', error));
                    };

                    card.append(containerForTexts,buttonAccept,buttonDecline);
                    display.appendChild(card);
                }
                else if (option==="sent")
                {
                    const message=document.createElement("p");
                    message.textContent="Requested ";

                    if(req.is_group===1)
                        message.textContent+=" group: ";
                    else
                        message.textContent+=" pet: ";

                    message.textContent+=req.animal_name+"(id:"+req.animal_id+") has been "+req.message+".";
                    containerForTexts.append(message);

                    const buttonRemoveNotification=document.createElement("button");
                    buttonRemoveNotification.textContent="Clear notification";
                    buttonRemoveNotification.className="button-remove-notification";

                    buttonRemoveNotification.onclick= () => {
                        const formData=new FormData();
                        formData.append("field","removeNotification");
                        formData.append("id",req.notification_id);

                        fetch("php/request_respond.php", {
                            method: 'POST',
                            body: formData
                        }).then(response => response.json())
                        .then(result => {
                            if(result.success)
                            {
                                alert(result.message);
                                showFilterOption("sent");
                            }
                            else
                            {
                                alert(result.message);
                            }
                        })
                        .catch(error => console.log('Error: ', error));
                    };

                    card.append(containerForTexts,buttonRemoveNotification);
                    display.appendChild(card);
                }
            });
        }
        else
        {
            display.innerHTML="Error occurred while searching for your pets.";
        }
    }).catch(error=> console.log('Error: ', error));
}


document.getElementById("filter-select").addEventListener("change", function () {
    const option=this.value;
    showFilterOption(option);
});