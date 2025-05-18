fetch("partials/header.html").then(response => response.text()).
then(html => {
    document.getElementById("animal-public-header").innerHTML=html;

    const allButtons=document.querySelectorAll(".topnav button");
    allButtons.forEach(btn => btn.classList.remove("active-btn"));
    const animalPublic=document.getElementById("animalPublic-btn");
    if(animalPublic)
        animalPublic.classList.add("active-btn");

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
    document.getElementById("animal-public-footer").innerHTML=html;

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

function getParams(){
    let param;
    if(window.location.search){
        param=new URLSearchParams(window.location.search);
    }
    else if(window.location.hash.includes("?")){
        const delimiter=window.location.hash.split("?")[1];
        param=new URLSearchParams(delimiter);
    }
    else
        return null;
    
    return param.get("id");
};

fetch(`php/animal_public.php?id=${getParams()}`).then(response => response.json())
.then(result => {
    const display=document.getElementById("animalPublic");
    display.innerHTML="";
    if(result.success)
    {
        const pet=result.data;

        const card=document.createElement("div");
        card.className="pet-card";

        const containerForTexts=document.createElement("div");
        containerForTexts.className="container-texts";

        const nameTitle=document.createElement("p");
        nameTitle.id="animal-name";
        if(pet.is_group===0)
            nameTitle.textContent="Pet: "+pet.name;
        else
            nameTitle.textContent="Group of Pets: "+pet.name;
        containerForTexts.appendChild(nameTitle);
        
        const nameRow=document.createElement("div");
        nameRow.className="nameRow";

        const name=document.createElement("p");
        name.textContent="Name: "+pet.name;
        name.id="name-field";

        nameRow.append(name);
        containerForTexts.appendChild(nameRow);


        const birthRow=document.createElement("div");
        birthRow.className="birthRow";

        const birth=document.createElement("p");
        birth.textContent="Birth Date: "+pet.birth_date;
        birth.id="birth-field";

        birthRow.append(birth);
        containerForTexts.appendChild(birthRow);


        const speciesRow=document.createElement("div");
        speciesRow.className="speciesRow";

        const species=document.createElement("p");
        species.textContent="Species: "+pet.species;
        species.id="species-field";

        speciesRow.append(species);
        containerForTexts.appendChild(speciesRow);
        

        const breedRow=document.createElement("div");
        breedRow.className="breedRow";

        const breed=document.createElement("p");
        breed.textContent="Breed: "+pet.breed;
        breed.id="breed-field";

        breedRow.append(breed);
        containerForTexts.appendChild(breedRow);


        const healthRow=document.createElement("div");
        healthRow.className="healthRow";

        const health_status=document.createElement("p");
        health_status.textContent="Health Status: "+pet.health_status;
        health_status.id="health-field";

        healthRow.append(health_status);
        containerForTexts.appendChild(healthRow);


        const regionRow=document.createElement("div");
        regionRow.className="regionRow";

        const region_status=document.createElement("p");
        region_status.textContent="Region: "+pet.region;
        region_status.id="region-field";

        regionRow.append(region_status);
        containerForTexts.appendChild(regionRow);
            

        const pickupRow=document.createElement("div");
        pickupRow.className="pickupRow";

        const pickup_address=document.createElement("p");
        pickup_address.textContent="Pickup Address: "+pet.pickup_address;
        pickup_address.id="pickup-field";

        pickupRow.append(pickup_address);
        containerForTexts.appendChild(pickupRow);


        const descriptionRow=document.createElement("div");
        descriptionRow.className="descriptionRow";

        const description=document.createElement("p");
        description.textContent="Description: "+(pet.description||"No information avaible.");
        description.id="description-field";

        descriptionRow.append(description);
        containerForTexts.appendChild(descriptionRow);


        const feedingRow=document.createElement("div");
        feedingRow.className="feedingRow";

        const feeding_schedule=document.createElement("p");
        feeding_schedule.textContent="Feeding Schedule: "+(pet.feeding_schedule||"No information avaible.");
        feeding_schedule.id="feeding-field";

        feedingRow.append(feeding_schedule);
        containerForTexts.appendChild(feedingRow);

        
        const restrictionsRow=document.createElement("div");
        restrictionsRow.className="restrictionsRow";

        const restrictions=document.createElement("p");
        restrictions.textContent="Restrictions: "+(pet.restrictions||"No information avaible.");
        restrictions.id="restrictions-field";

        restrictionsRow.append(restrictions);
        containerForTexts.appendChild(restrictionsRow);


        const relationshipRow=document.createElement("div");
        relationshipRow.className="relationshipRow";

        const relationship=document.createElement("p");
        relationship.textContent="Relationship: "+(pet.relationship||"No information avaible.");
        relationship.id="relationship-field";

        relationshipRow.append(relationship);
        containerForTexts.appendChild(relationshipRow);


        const medicalRow=document.createElement("div");
        medicalRow.className="medicalRow";

        medicalTitle=document.createElement("p");
        medicalTitle.textContent="Medical History: ";
        medicalRow.append(medicalTitle);

        const medicalList=result.data_medical;
        if(medicalList.length===0)
        {
            const noMedical=document.createElement("p");
            noMedical.textContent="No history avaible.";
            noMedical.className="noMedical-text";
            medicalRow.appendChild(noMedical);
        }
        else
        {
            const noHistoryText=medicalRow.querySelector(".noMedical-text");
            if(noHistoryText)
                noHistoryText.remove();

            medicalList.forEach(value=>{
                const medicalSetContainer=document.createElement("div");
                medicalSetContainer.className="medicalSet-class";

                const med_desc=document.createElement("p");
                med_desc.textContent="Description: "+value.description;

                const med_treat=document.createElement("p");
                med_treat.textContent="Treatment: "+value.treatment;

                const med_date=document.createElement("p");
                med_date.textContent="Date: "+value.date;

                medicalSetContainer.append(med_date,med_desc,med_treat);
                medicalRow.appendChild(medicalSetContainer);
            });
        }

        containerForTexts.appendChild(medicalRow);


        const mediaTitle=document.createElement("p");
        mediaTitle.textContent="Media: ";

        containerForTexts.append(mediaTitle);
        card.append(containerForTexts);
        

        const slideShowImages=document.createElement("div");
        slideShowImages.className="slideShowImages";
        const imageTitle=document.createElement("p");
        imageTitle.textContent="Images: ";

        slideShowImages.append(imageTitle);

        const images=result.data_media.filter(media => media.type==="photo");

        if(images.length===0){
            const noImages=document.createElement("p");
            noImages.textContent="No images avaible.";
            noImages.className="noImages-text";
            slideShowImages.appendChild(noImages);
        }
        else{
            const noImagesText=slideShowImages.querySelector(".noImages-text");
            if(noImagesText)
                noImagesText.remove();
            
            images.forEach((photo,value)=>{
                const img=document.createElement("img");
                img.id="imgItem";
                img.src=photo.path;
            
                if(value===0)
                    img.style.display="block";
                else
                    img.style.display="none";
                img.draggable=false;
                img.className="slideshow-image";
                
                slideShowImages.appendChild(img);
            });
        }

        if(images.length>1)
        {
            const buttonLeft=document.createElement("button");
            buttonLeft.id="buttonLeft";
            buttonLeft.textContent="←";
            buttonLeft.onclick=()=>changeImage(-1);

            const buttonRight=document.createElement("button");
            buttonRight.textContent="→";
            buttonRight.id="buttonRight";
            buttonRight.onclick=()=>changeImage(1);

            slideShowImages.appendChild(buttonLeft);
            slideShowImages.appendChild(buttonRight);
        }
        card.appendChild(slideShowImages);


        const videosContainer=document.createElement("div");
        videosContainer.className="videosContainer";
        const videoTitle=document.createElement("p");
        videoTitle.textContent="Videos: ";

        videosContainer.append(videoTitle);

        const videoList=result.data_media.filter(media => media.type==="video");

        if(videoList.length===0){
            const noVideos=document.createElement("p");
            noVideos.textContent="No videos avaible.";
            noVideos.className="noVideos-text";
            videosContainer.appendChild(noVideos);
        }
        else{
            const noVideosText=videosContainer.querySelector(".noVideos-text");
            if(noVideosText)
                noVideosText.remove();
        
            videoList.forEach(vid => {
                const videoPlayer=document.createElement("video");
                videoPlayer.id="videoItem";
                videoPlayer.src=vid.path;
                videoPlayer.controls=true;
                videoPlayer.className="videosContainer-class";

                videosContainer.appendChild(videoPlayer);
            });
        }
        card.append(videosContainer);
        

        const audiosContainer=document.createElement("div");
        audiosContainer.className="audiosContainer";
        const audioTitle=document.createElement("p");
        audioTitle.textContent="Audios: ";

        audiosContainer.append(audioTitle);

        const audioList=result.data_media.filter(media => media.type==="audio");

        if(audioList.length===0){
            const noAudios=document.createElement("p");
            noAudios.textContent="No audios avaible.";
            noAudios.className="noAudios-text";
            audiosContainer.appendChild(noAudios);
        }
        else{
            const noAudiosText=audiosContainer.querySelector(".noAudios-text");
            if(noAudiosText)
                noAudiosText.remove();

            audioList.forEach(aud => {
                const audioPlayer=document.createElement("audio");
                audioPlayer.id="audioItem"
                audioPlayer.src=aud.path;
                audioPlayer.controls=true;
                audioPlayer.className="audiosContainer-class";

                audiosContainer.appendChild(audioPlayer);
            });
        }
        
        card.append(audiosContainer);


        const requestRow=document.createElement("div");
        requestRow.className="requestRow";

        const requestBtn=document.createElement("button");
        requestBtn.id="request-btn";
        requestBtn.textContent="Request Pet";

        const warnText=document.createElement("p");
        warnText.textContent="Are you sure?";
        warnText.id="warnText-field";
        warnText.style.display="none";

        const noBtn=document.createElement("button");
        noBtn.id="noBtn";
        noBtn.textContent="No";
        noBtn.style.display="none";

        const yesBtn=document.createElement("button");
        yesBtn.id="yesBtn";
        yesBtn.textContent="Yes";
        yesBtn.style.display="none";

        requestBtn.onclick=() => {
            if(warnText.style.display==="none")
                warnText.style.display="inline-block";
            else if(warnText.style.display==="inline-block")
                warnText.style.display="none";

            if(noBtn.style.display==="none")
                noBtn.style.display="inline-block";
            else if(noBtn.style.display==="inline-block")
                noBtn.style.display="none";

            if(yesBtn.style.display==="none")
                yesBtn.style.display="inline-block";
            else if(yesBtn.style.display==="inline-block")
                yesBtn.style.display="none";
        };

        noBtn.onclick=() => {
            if(warnText.style.display==="none")
                warnText.style.display="inline-block";
            else if(warnText.style.display==="inline-block")
                warnText.style.display="none";

            if(noBtn.style.display==="none")
                noBtn.style.display="inline-block";
            else if(noBtn.style.display==="inline-block")
                noBtn.style.display="none";

            if(yesBtn.style.display==="none")
                yesBtn.style.display="inline-block";
            else if(yesBtn.style.display==="inline-block")
                yesBtn.style.display="none";
        };

        yesBtn.onclick=() => {
            const formData=new FormData();
            formData.append("field", "request");
            formData.append("value", "");
            formData.append("id",pet.id);

            fetch("php/request_pet.php", {
                method: 'POST',
                body: formData
            }).then(response => response.json())
            .then(result => {
                if(result.success)
                {
                    alert(result.message);
                    pageNavigation("browse");
                }
                else
                {
                    alert(result.message);
                }
            })
            .catch(error => console.log('Error: ', error));
        };

        requestRow.append(requestBtn,warnText,noBtn,yesBtn);

        card.append(requestRow);


        display.appendChild(card);
    }
    else
    {
        const noPets=document.createElement("p");
        noPets.textContent="The Pet could not be found or you don't have permission to it.";
        noPets.className="noPets-class";
        display.appendChild(noPets);
    }
}).catch(error=> console.log('Error: ', error));

{
    let imageNumber=0;
    function changeImage(n){
        const allImages=document.querySelectorAll(".slideshow-image");

        if(allImages.length===0)
            return;
        allImages[imageNumber].style.display="none";
        imageNumber+=n;
        if(imageNumber>=allImages.length)
            imageNumber=0;
        if(imageNumber<0)
            imageNumber=allImages.length-1;

        allImages[imageNumber].style.display="block";

    }
}