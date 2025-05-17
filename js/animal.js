fetch("partials/header.html").then(response => response.text()).
then(html => {
    document.getElementById("animal-header").innerHTML=html;

    const allButtons=document.querySelectorAll(".topnav button");
    allButtons.forEach(btn => btn.classList.remove("active-btn"));
    const animal=document.getElementById("animal-btn");
    if(animal)
        animal.classList.add("active-btn");

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
    document.getElementById("animal-footer").innerHTML=html;

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

fetch(`php/animal.php?id=${getParams()}`).then(response => response.json())
.then(result => {
    const display=document.getElementById("animal");
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

        const editNameBtn=document.createElement("button");
        editNameBtn.textContent="Edit";

        const nameInput=document.createElement("input");
        nameInput.type="text";
        nameInput.style.display="none";
        nameInput.placeholder="Enter a new name";

        const saveNameBtn=document.createElement("button");
        saveNameBtn.textContent="Save";
        saveNameBtn.style.display="none";

        editNameBtn.onclick=() => {
            if(nameInput.style.display==="none")
                nameInput.style.display="inline-block";
            else if(nameInput.style.display==="inline-block")
                nameInput.style.display="none";

            if(saveNameBtn.style.display==="none")
                saveNameBtn.style.display="inline-block";
            else if(saveNameBtn.style.display==="inline-block")
                saveNameBtn.style.display="none";
        };

        saveNameBtn.onclick=() => {
            const formData=new FormData();
            formData.append("field", "name");
            formData.append("value", nameInput.value);
            formData.append("id",pet.id);

            fetch("php/update_animal.php", {
                method: 'POST',
                body: formData
            }).then(response => response.json())
            .then(result => {
                if(result.success)
                {
                    alert(result.message);
                    location.reload();
                }
                else
                {
                    alert(result.message);
                }
            })
            .catch(error => console.log('Error: ', error));
        };

        nameRow.append(name,editNameBtn,nameInput,saveNameBtn);
        containerForTexts.appendChild(nameRow);


        const birthRow=document.createElement("div");
        birthRow.className="birthRow";

        const birth=document.createElement("p");
        birth.textContent="Birth Date: "+pet.birth_date;
        birth.id="birth-field";

        const editBirthBtn=document.createElement("button");
        editBirthBtn.textContent="Edit";

        const birthInput=document.createElement("input");
        birthInput.type="date";
        birthInput.style.display="none";

        const saveBirthBtn=document.createElement("button");
        saveBirthBtn.textContent="Save";
        saveBirthBtn.style.display="none";

        editBirthBtn.onclick=() => {
            if(birthInput.style.display==="none")
                birthInput.style.display="inline-block";
            else if(birthInput.style.display==="inline-block")
                birthInput.style.display="none";

            if(saveBirthBtn.style.display==="none")
                saveBirthBtn.style.display="inline-block";
            else if(saveBirthBtn.style.display==="inline-block")
                saveBirthBtn.style.display="none";
        };

        saveBirthBtn.onclick=() => {
            const formData=new FormData();
            formData.append("field", "birth");
            formData.append("value", birthInput.value);
            formData.append("id",pet.id);

            fetch("php/update_animal.php", {
                method: 'POST',
                body: formData
            }).then(response => response.json())
            .then(result => {
                if(result.success)
                {
                    alert(result.message);
                    location.reload();
                }
                else
                {
                    alert(result.message);
                }
            })
            .catch(error => console.log('Error: ', error));
        };

        birthRow.append(birth,editBirthBtn,birthInput,saveBirthBtn);
        containerForTexts.appendChild(birthRow);


        const speciesRow=document.createElement("div");
        speciesRow.className="speciesRow";

        const species=document.createElement("p");
        species.textContent="Species: "+pet.species;
        species.id="species-field";

        const editSpeciesBtn=document.createElement("button");
        editSpeciesBtn.textContent="Edit";

        const speciesInput=document.createElement("input");
        speciesInput.type="text";
        speciesInput.style.display="none";
        speciesInput.placeholder="Enter a new species";

        const saveSpeciesBtn=document.createElement("button");
        saveSpeciesBtn.textContent="Save";
        saveSpeciesBtn.style.display="none";

        editSpeciesBtn.onclick=() => {
            if(speciesInput.style.display==="none")
                speciesInput.style.display="inline-block";
            else if(speciesInput.style.display==="inline-block")
                speciesInput.style.display="none";

            if(saveSpeciesBtn.style.display==="none")
                saveSpeciesBtn.style.display="inline-block";
            else if(saveSpeciesBtn.style.display==="inline-block")
                saveSpeciesBtn.style.display="none";
        };

        saveSpeciesBtn.onclick=() => {
            const formData=new FormData();
            formData.append("field", "species");
            formData.append("value", speciesInput.value);
            formData.append("id",pet.id);

            fetch("php/update_animal.php", {
                method: 'POST',
                body: formData
            }).then(response => response.json())
            .then(result => {
                if(result.success)
                {
                    alert(result.message);
                    location.reload();
                }
                else
                {
                    alert(result.message);
                }
            })
            .catch(error => console.log('Error: ', error));
        };

        speciesRow.append(species,editSpeciesBtn,speciesInput,saveSpeciesBtn);
        containerForTexts.appendChild(speciesRow);
        

        const breedRow=document.createElement("div");
        breedRow.className="breedRow";

        const breed=document.createElement("p");
        breed.textContent="Breed: "+pet.breed;
        breed.id="breed-field";

        const editBreedBtn=document.createElement("button");
        editBreedBtn.textContent="Edit";

        const breedInput=document.createElement("input");
        breedInput.type="text";
        breedInput.style.display="none";
        breedInput.placeholder="Enter a new breed";

        const saveBreedBtn=document.createElement("button");
        saveBreedBtn.textContent="Save";
        saveBreedBtn.style.display="none";

        editBreedBtn.onclick=() => {
            if(breedInput.style.display==="none")
                breedInput.style.display="inline-block";
            else if(breedInput.style.display==="inline-block")
                breedInput.style.display="none";

            if(saveBreedBtn.style.display==="none")
                saveBreedBtn.style.display="inline-block";
            else if(saveBreedBtn.style.display==="inline-block")
                saveBreedBtn.style.display="none";
        };

        saveBreedBtn.onclick=() => {
            const formData=new FormData();
            formData.append("field", "breed");
            formData.append("value", breedInput.value);
            formData.append("id",pet.id);

            fetch("php/update_animal.php", {
                method: 'POST',
                body: formData
            }).then(response => response.json())
            .then(result => {
                if(result.success)
                {
                    alert(result.message);
                    location.reload();
                }
                else
                {
                    alert(result.message);
                }
            })
            .catch(error => console.log('Error: ', error));
        };

        breedRow.append(breed,editBreedBtn,breedInput,saveBreedBtn);
        containerForTexts.appendChild(breedRow);


        const healthRow=document.createElement("div");
        healthRow.className="healthRow";

        const health_status=document.createElement("p");
        health_status.textContent="Health Status: "+pet.health_status;
        health_status.id="health-field";

        const editHealthBtn=document.createElement("button");
        editHealthBtn.textContent="Edit";

        const healthSelect=document.createElement("select");
        healthSelect.style.display="none";

        ["","healthy","injured","sick"].forEach(version =>{
            const option=document.createElement("option");
            option.value=version;
            if(version==="")
            {
                option.disabled=true;
                option.selected=true;
                option.textContent="Select health status:";
            }
            else
            {
                if(version==="healthy")
                    option.textContent="Healthy";
                else if(version==="injured")
                    option.textContent="Injured";
                else if(version==="sick")
                    option.textContent="Sick";
            }
            healthSelect.appendChild(option);
        });

        const saveHealthBtn=document.createElement("button");
        saveHealthBtn.textContent="Save";
        saveHealthBtn.style.display="none";

        editHealthBtn.onclick=() => {
            if(healthSelect.style.display==="none")
                healthSelect.style.display="inline-block";
            else if(healthSelect.style.display==="inline-block")
                healthSelect.style.display="none";

            if(saveHealthBtn.style.display==="none")
                saveHealthBtn.style.display="inline-block";
            else if(saveHealthBtn.style.display==="inline-block")
                saveHealthBtn.style.display="none";
        };

        saveHealthBtn.onclick=() => {
            const formData=new FormData();
            formData.append("field", "health_status");
            formData.append("value", healthSelect.value);
            formData.append("id",pet.id);

            fetch("php/update_animal.php", {
                method: 'POST',
                body: formData
            }).then(response => response.json())
            .then(result => {
                if(result.success)
                {
                    alert(result.message);
                    location.reload();
                }
                else
                {
                    alert(result.message);
                }
            })
            .catch(error => console.log('Error: ', error));
        };

        healthRow.append(health_status,editHealthBtn,healthSelect,saveHealthBtn);
        containerForTexts.appendChild(healthRow);


        const regionRow=document.createElement("div");
        regionRow.className="regionRow";

        const region_status=document.createElement("p");
        region_status.textContent="Region: "+pet.region;
        region_status.id="region-field";

        const editRegionBtn=document.createElement("button");
        editRegionBtn.textContent="Edit";

        const regionSelect=document.createElement("select");
        regionSelect.style.display="none";

        ["","Botosani","Bacau","Galati","Iasi","Neamt","Suceava"].forEach(version =>{
            const option=document.createElement("option");
            option.value=version;
            if(version==="")
            {
                option.disabled=true;
                option.selected=true;
                option.textContent="Select region:";
            }
            else
            {
                if(version==="Botosani")
                    option.textContent="Botosani";
                else if(version==="Bacau")
                    option.textContent="Bacau";
                else if(version==="Galati")
                    option.textContent="Galati";
                else if(version==="Iasi")
                    option.textContent="Iasi";
                else if(version==="Neamt")
                    option.textContent="Neamt";
                else if(version==="Suceava")
                    option.textContent="Suceava";
            }
            regionSelect.appendChild(option);
        });

        const saveRegionBtn=document.createElement("button");
        saveRegionBtn.textContent="Save";
        saveRegionBtn.style.display="none";

        editRegionBtn.onclick=() => {
            if(regionSelect.style.display==="none")
                regionSelect.style.display="inline-block";
            else if(regionSelect.style.display==="inline-block")
                regionSelect.style.display="none";

            if(saveRegionBtn.style.display==="none")
                saveRegionBtn.style.display="inline-block";
            else if(saveRegionBtn.style.display==="inline-block")
                saveRegionBtn.style.display="none";
        };

        saveRegionBtn.onclick=() => {
            const formData=new FormData();
            formData.append("field", "region_status");
            formData.append("value", regionSelect.value);
            formData.append("id",pet.id);

            fetch("php/update_animal.php", {
                method: 'POST',
                body: formData
            }).then(response => response.json())
            .then(result => {
                if(result.success)
                {
                    alert(result.message);
                    location.reload();
                }
                else
                {
                    alert(result.message);
                }
            })
            .catch(error => console.log('Error: ', error));
        };

        regionRow.append(region_status,editRegionBtn,regionSelect,saveRegionBtn);
        containerForTexts.appendChild(regionRow);
            

        const pickupRow=document.createElement("div");
        pickupRow.className="pickupRow";

        const pickup_address=document.createElement("p");
        pickup_address.textContent="Pickup Address: "+pet.pickup_address;
        pickup_address.id="pickup-field";

        const editPickupBtn=document.createElement("button");
        editPickupBtn.textContent="Edit";

        const pickupInput=document.createElement("input");
        pickupInput.type="text";
        pickupInput.style.display="none";
        pickupInput.placeholder="Enter a new pickup address";

        const savePickupBtn=document.createElement("button");
        savePickupBtn.textContent="Save";
        savePickupBtn.style.display="none";

        editPickupBtn.onclick=() => {
            if(pickupInput.style.display==="none")
                pickupInput.style.display="inline-block";
            else if(pickupInput.style.display==="inline-block")
                pickupInput.style.display="none";

            if(savePickupBtn.style.display==="none")
                savePickupBtn.style.display="inline-block";
            else if(savePickupBtn.style.display==="inline-block")
                savePickupBtn.style.display="none";
        };

        savePickupBtn.onclick=() => {
            const formData=new FormData();
            formData.append("field", "pickup_address");
            formData.append("value", pickupInput.value);
            formData.append("id",pet.id);

            fetch("php/update_animal.php", {
                method: 'POST',
                body: formData
            }).then(response => response.json())
            .then(result => {
                if(result.success)
                {
                    alert(result.message);
                    location.reload();
                }
                else
                {
                    alert(result.message);
                }
            })
            .catch(error => console.log('Error: ', error));
        };

        pickupRow.append(pickup_address,editPickupBtn,pickupInput,savePickupBtn);
        containerForTexts.appendChild(pickupRow);


        const descriptionRow=document.createElement("div");
        descriptionRow.className="descriptionRow";

        const description=document.createElement("p");
        description.textContent="Description: "+(pet.description||"No information avaible.");
        description.id="description-field";

        const editDescriptionBtn=document.createElement("button");
        editDescriptionBtn.textContent="Edit";

        const descriptionInput=document.createElement("input");
        descriptionInput.type="text";
        descriptionInput.style.display="none";
        descriptionInput.placeholder="Enter a new description";

        const saveDescriptionBtn=document.createElement("button");
        saveDescriptionBtn.textContent="Save";
        saveDescriptionBtn.style.display="none";

        editDescriptionBtn.onclick=() => {
            if(descriptionInput.style.display==="none")
                descriptionInput.style.display="inline-block";
            else if(descriptionInput.style.display==="inline-block")
                descriptionInput.style.display="none";

            if(saveDescriptionBtn.style.display==="none")
                saveDescriptionBtn.style.display="inline-block";
            else if(saveDescriptionBtn.style.display==="inline-block")
                saveDescriptionBtn.style.display="none";
        };

        saveDescriptionBtn.onclick=() => {
            const formData=new FormData();
            formData.append("field", "description");
            formData.append("value", descriptionInput.value);
            formData.append("id",pet.id);

            fetch("php/update_animal.php", {
                method: 'POST',
                body: formData
            }).then(response => response.json())
            .then(result => {
                if(result.success)
                {
                    alert(result.message);
                    location.reload();
                }
                else
                {
                    alert(result.message);
                }
            })
            .catch(error => console.log('Error: ', error));
        };

        descriptionRow.append(description,editDescriptionBtn,descriptionInput,saveDescriptionBtn);
        containerForTexts.appendChild(descriptionRow);


        const feedingRow=document.createElement("div");
        feedingRow.className="feedingRow";

        const feeding_schedule=document.createElement("p");
        feeding_schedule.textContent="Feeding Schedule: "+(pet.feeding_schedule||"No information avaible.");
        feeding_schedule.id="feeding-field";

        const editFeedingBtn=document.createElement("button");
        editFeedingBtn.textContent="Edit";

        const feedingInput=document.createElement("input");
        feedingInput.type="text";
        feedingInput.style.display="none";
        feedingInput.placeholder="Enter a new feeding schedule";

        const saveFeedingBtn=document.createElement("button");
        saveFeedingBtn.textContent="Save";
        saveFeedingBtn.style.display="none";

        editFeedingBtn.onclick=() => {
            if(feedingInput.style.display==="none")
                feedingInput.style.display="inline-block";
            else if(feedingInput.style.display==="inline-block")
                feedingInput.style.display="none";

            if(saveFeedingBtn.style.display==="none")
                saveFeedingBtn.style.display="inline-block";
            else if(saveFeedingBtn.style.display==="inline-block")
                saveFeedingBtn.style.display="none";
        };

        saveFeedingBtn.onclick=() => {
            const formData=new FormData();
            formData.append("field", "feeding_schedule");
            formData.append("value", feedingInput.value);
            formData.append("id",pet.id);

            fetch("php/update_animal.php", {
                method: 'POST',
                body: formData
            }).then(response => response.json())
            .then(result => {
                if(result.success)
                {
                    alert(result.message);
                    location.reload();
                }
                else
                {
                    alert(result.message);
                }
            })
            .catch(error => console.log('Error: ', error));
         };

        feedingRow.append(feeding_schedule,editFeedingBtn,feedingInput,saveFeedingBtn);
        containerForTexts.appendChild(feedingRow);

        
        const restrictionsRow=document.createElement("div");
        restrictionsRow.className="restrictionsRow";

        const restrictions=document.createElement("p");
        restrictions.textContent="Restrictions: "+(pet.restrictions||"No information avaible.");
        restrictions.id="restrictions-field";

        const editRestrictionsBtn=document.createElement("button");
        editRestrictionsBtn.textContent="Edit";

        const restrictionsInput=document.createElement("input");
        restrictionsInput.type="text";
        restrictionsInput.style.display="none";
        restrictionsInput.placeholder="Enter new restrictions";

        const saveRestrictionsBtn=document.createElement("button");
        saveRestrictionsBtn.textContent="Save";
        saveRestrictionsBtn.style.display="none";

        editRestrictionsBtn.onclick=() => {
            if(restrictionsInput.style.display==="none")
                restrictionsInput.style.display="inline-block";
            else if(restrictionsInput.style.display==="inline-block")
                restrictionsInput.style.display="none";

            if(saveRestrictionsBtn.style.display==="none")
                saveRestrictionsBtn.style.display="inline-block";
            else if(saveRestrictionsBtn.style.display==="inline-block")
                saveRestrictionsBtn.style.display="none";
        };

        saveRestrictionsBtn.onclick=() => {
            const formData=new FormData();
            formData.append("field", "restrictions");
            formData.append("value", restrictionsInput.value);
            formData.append("id",pet.id);

            fetch("php/update_animal.php", {
                method: 'POST',
                body: formData
            }).then(response => response.json())
            .then(result => {
                if(result.success)
                {
                    alert(result.message);
                    location.reload();
                }
                else
                {
                    alert(result.message);
                }
            })
            .catch(error => console.log('Error: ', error));
        };

        restrictionsRow.append(restrictions,editRestrictionsBtn,restrictionsInput,saveRestrictionsBtn);
        containerForTexts.appendChild(restrictionsRow);


        const relationshipRow=document.createElement("div");
        relationshipRow.className="relationshipRow";

        const relationship=document.createElement("p");
        relationship.textContent="Relationship: "+(pet.relationship||"No information avaible.");
        relationship.id="relationship-field";

        const editRelationshipBtn=document.createElement("button");
        editRelationshipBtn.textContent="Edit";

        const relationshipInput=document.createElement("input");
        relationshipInput.type="text";
        relationshipInput.style.display="none";
        relationshipInput.placeholder="Enter new relationships";

        const saveRelationshipBtn=document.createElement("button");
        saveRelationshipBtn.textContent="Save";
        saveRelationshipBtn.style.display="none";

        editRelationshipBtn.onclick=() => {
            if(relationshipInput.style.display==="none")
                relationshipInput.style.display="inline-block";
            else if(relationshipInput.style.display==="inline-block")
                relationshipInput.style.display="none";

            if(saveRelationshipBtn.style.display==="none")
                saveRelationshipBtn.style.display="inline-block";
            else if(saveRelationshipBtn.style.display==="inline-block")
                saveRelationshipBtn.style.display="none";
        };

        saveRelationshipBtn.onclick=() => {
            const formData=new FormData();
            formData.append("field", "relationship");
            formData.append("value", relationshipInput.value);
            formData.append("id",pet.id);

            fetch("php/update_animal.php", {
                method: 'POST',
                body: formData
            }).then(response => response.json())
            .then(result => {
                if(result.success)
                {
                    alert(result.message);
                    location.reload();
                }
                else
                {
                    alert(result.message);
                }
            })
            .catch(error => console.log('Error: ', error));
        };

        relationshipRow.append(relationship,editRelationshipBtn,relationshipInput,saveRelationshipBtn);
        containerForTexts.appendChild(relationshipRow);


        const medicalRow=document.createElement("div");
        medicalRow.className="medicalRow";

        medicalTitle=document.createElement("p");
        medicalTitle.textContent="Medical History: ";
        medicalRow.append(medicalTitle);

        const addMedicalBtn=document.createElement("button");
        addMedicalBtn.textContent="Add";

        const removeMedicalBtn=document.createElement("button");
        removeMedicalBtn.textContent="Remove all";

        const dateMedicalInput=document.createElement("input");
        dateMedicalInput.type="date";
        dateMedicalInput.style.display="none";

        const descriptionMedicalInput=document.createElement("input");
        descriptionMedicalInput.type="text";
        descriptionMedicalInput.style.display="none";
        descriptionMedicalInput.placeholder="Enter description";

        const treatmentMedicalInput=document.createElement("input");
        treatmentMedicalInput.type="text";
        treatmentMedicalInput.style.display="none";
        treatmentMedicalInput.placeholder="Enter treatment";

        const saveMedicalBtn=document.createElement("button");
        saveMedicalBtn.textContent="Save";
        saveMedicalBtn.style.display="none";

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

        addMedicalBtn.onclick=() => {
            if(dateMedicalInput.style.display==="none")
                dateMedicalInput.style.display="inline-block";
            else if(dateMedicalInput.style.display==="inline-block")
                dateMedicalInput.style.display="none";

            if(descriptionMedicalInput.style.display==="none")
                descriptionMedicalInput.style.display="inline-block";
            else if(descriptionMedicalInput.style.display==="inline-block")
                descriptionMedicalInput.style.display="none";

            if(treatmentMedicalInput.style.display==="none")
                treatmentMedicalInput.style.display="inline-block";
            else if(treatmentMedicalInput.style.display==="inline-block")
                treatmentMedicalInput.style.display="none";

            if(saveMedicalBtn.style.display==="none")
                saveMedicalBtn.style.display="inline-block";
            else if(saveMedicalBtn.style.display==="inline-block")
                saveMedicalBtn.style.display="none";
        };

        saveMedicalBtn.onclick=() => {
            const formData=new FormData();
            formData.append("field", "saveMedical");
            formData.append("value", "");
            formData.append("id",pet.id);
            formData.append("date",dateMedicalInput.value);
            formData.append("description",descriptionMedicalInput.value);
            formData.append("treatment",treatmentMedicalInput.value);

            fetch("php/update_animal.php", {
                method: 'POST',
                body: formData
            }).then(response => response.json())
            .then(result => {
                if(result.success)
                {
                    alert(result.message);
                    location.reload();
                }
                else
                {
                    alert(result.message);
                }
            })
            .catch(error => console.log('Error: ', error));
        };

        removeMedicalBtn.onclick=() => {
            const formData=new FormData();
            formData.append("field", "removeMedical");
            formData.append("value", "");
            formData.append("id",pet.id);

            fetch("php/update_animal.php", {
                method: 'POST',
                body: formData
            }).then(response => response.json())
            .then(result => {
                if(result.success)
                {
                    alert(result.message);
                    location.reload();
                }
                else
                {
                    alert(result.message);
                }
            })
            .catch(error => console.log('Error: ', error));
        };

        medicalRow.append(addMedicalBtn,removeMedicalBtn,dateMedicalInput,descriptionMedicalInput,treatmentMedicalInput,saveMedicalBtn);
        containerForTexts.appendChild(medicalRow);



        const mediaTitle=document.createElement("p");
        mediaTitle.textContent="Media: ";

        containerForTexts.append(mediaTitle);
        card.append(containerForTexts);
        

        const slideShowImages=document.createElement("div");
        slideShowImages.className="slideShowImages";
        const imageTitle=document.createElement("p");
        imageTitle.textContent="Images: ";

        const uploadForm=document.createElement("form");
        uploadForm.className="uploadForm";
        uploadForm.enctype="multipart/form-data";

        const fileInput=document.createElement("input");
        fileInput.type="file";
        fileInput.accept="image/*";
        fileInput.multiple=true;

        const uploadBtn=document.createElement("button");
        uploadBtn.textContent="Upload new Images";
        uploadBtn.type="submit";

        const deleteImagesBtn=document.createElement("button");
        deleteImagesBtn.textContent="Remove images";

        uploadForm.onsubmit=(event) => {
            event.preventDefault();

            const files=fileInput.files;
            let totalSize=0;

            const formData=new FormData();
            formData.append("field", "images");
            formData.append("value", "");
            formData.append("id",pet.id);

            for(let i=0;i<files.length;i++)
            {
                totalSize=totalSize+files[i].size;
                formData.append("images[]",files[i]);
            }

            if(totalSize>90*1024*1024)
            {
                alert("Total size for photos exceeds 90MB! Remove some files.");
                return;
            }

            fetch("php/update_animal.php", {
                method: 'POST',
                body: formData
            }).then(response => response.json())
            .then(result => {
                if(result.success)
                {
                    alert(result.message);
                    location.reload();
                }
                else
                {
                    alert(result.message);
                }
            })
            .catch(error => console.log('Error: ', error));
        };

        const photoList=document.createElement("ol");
        photoList.className="photo-list";

        fileInput.addEventListener("change", function (){
            photoList.innerHTML="";
            const files=fileInput.files;
            for(let i=0; i<files.length;i++)
            {
                const item=document.createElement("li");
                item.textContent=files[i].name;
                photoList.appendChild(item);
            }
        });

        deleteImagesBtn.onclick=() => {
            const formData=new FormData();
            formData.append("field", "removeImages");
            formData.append("value", "");
            formData.append("id",pet.id);

            fetch("php/update_animal.php", {
                method: 'POST',
                body: formData
            }).then(response => response.json())
            .then(result => {
                if(result.success)
                {
                    alert(result.message);
                    location.reload();
                }
                else
                {
                    alert(result.message);
                }
            })
            .catch(error => console.log('Error: ', error));
        };

        uploadForm.append(fileInput,uploadBtn,photoList);
        slideShowImages.append(imageTitle,uploadForm,deleteImagesBtn);

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

        const upload2Form=document.createElement("form");
        upload2Form.className="upload2Form";
        upload2Form.enctype="multipart/form-data";

        const file2Input=document.createElement("input");
        file2Input.type="file";
        file2Input.accept="video/*";
        file2Input.multiple=true;

        const upload2Btn=document.createElement("button");
        upload2Btn.textContent="Upload new Videos";
        upload2Btn.type="submit";

        const deleteVideosBtn=document.createElement("button");
        deleteVideosBtn.textContent="Remove videos";

        upload2Form.onsubmit=(event) => {
            event.preventDefault();

            const files=file2Input.files;
            let totalSize=0;

            const formData=new FormData();
            formData.append("field", "videos");
            formData.append("value", "");
            formData.append("id",pet.id);

            for(let i=0;i<files.length;i++)
            {
                totalSize=totalSize+files[i].size;
                formData.append("videos[]",files[i]);
            }

            if(totalSize>90*1024*1024)
            {
                alert("Total size for videos exceeds 90MB! Remove some files.");
                return;
            }

            fetch("php/update_animal.php", {
                method: 'POST',
                body: formData
            }).then(response => response.json())
            .then(result => {
                if(result.success)
                {
                    alert(result.message);
                    location.reload();
                }
                else
                {
                    alert(result.message);
                }
            })
            .catch(error => console.log('Error: ', error));
        };

        const videoclipsList=document.createElement("ol");
        videoclipsList.className="video-list";

        file2Input.addEventListener("change", function (){
            videoclipsList.innerHTML="";
            const files=file2Input.files;
            for(let i=0; i<files.length;i++)
            {
                const item=document.createElement("li");
                item.textContent=files[i].name;
                videoclipsList.appendChild(item);
            }
        });

        deleteVideosBtn.onclick=() => {
            const formData=new FormData();
            formData.append("field", "removeVideos");
            formData.append("value", "");
            formData.append("id",pet.id);

            fetch("php/update_animal.php", {
                method: 'POST',
                body: formData
            }).then(response => response.json())
            .then(result => {
                if(result.success)
                {
                    alert(result.message);
                    location.reload();
                }
                else
                {
                    alert(result.message);
                }
            })
            .catch(error => console.log('Error: ', error));
        };

        upload2Form.append(file2Input,upload2Btn,videoclipsList);
        videosContainer.append(videoTitle,upload2Form,deleteVideosBtn);

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

        const upload3Form=document.createElement("form");
        upload3Form.className="upload3Form";
        upload3Form.enctype="multipart/form-data";

        const file3Input=document.createElement("input");
        file3Input.type="file";
        file3Input.accept="audio/*";
        file3Input.multiple=true;

        const upload3Btn=document.createElement("button");
        upload3Btn.textContent="Upload new Audios";
        upload3Btn.type="submit";

        const deleteAudiosBtn=document.createElement("button");
        deleteAudiosBtn.textContent="Remove audios";

        upload3Form.onsubmit=(event) => {
            event.preventDefault();

            const files=file3Input.files;
            let totalSize=0;

            const formData=new FormData();
            formData.append("field", "audios");
            formData.append("value", "");
            formData.append("id",pet.id);

            for(let i=0;i<files.length;i++)
            {
                totalSize=totalSize+files[i].size;
                formData.append("audios[]",files[i]);
            }

            if(totalSize>20*1024*1024)
            {
                alert("Total size for audios exceeds 20MB! Remove some files.");
                return;
            }

            fetch("php/update_animal.php", {
                method: 'POST',
                body: formData
            }).then(response => response.json())
            .then(result => {
                if(result.success)
                {
                    alert(result.message);
                    location.reload();
                }
                else
                {
                    alert(result.message);
                }
            })
            .catch(error => console.log('Error: ', error));
        };

        const audiosList=document.createElement("ol");
        audiosList.className="audio-list";

        file3Input.addEventListener("change", function (){
            audiosList.innerHTML="";
            const files=file3Input.files;
            for(let i=0; i<files.length;i++)
            {
                const item=document.createElement("li");
                item.textContent=files[i].name;
                audiosList.appendChild(item);
            }
        });

        deleteAudiosBtn.onclick=() => {
            const formData=new FormData();
            formData.append("field", "removeAudios");
            formData.append("value", "");
            formData.append("id",pet.id);

            fetch("php/update_animal.php", {
                method: 'POST',
                body: formData
            }).then(response => response.json())
            .then(result => {
                if(result.success)
                {
                    alert(result.message);
                    location.reload();
                }
                else
                {
                    alert(result.message);
                }
            })
            .catch(error => console.log('Error: ', error));
        };

        upload3Form.append(file3Input,upload3Btn,audiosList);
        audiosContainer.append(audioTitle,upload3Form,deleteAudiosBtn);

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


        const deleteRow=document.createElement("div");
        deleteRow.className="deleteRow";

        const deleteBtn=document.createElement("button");
        deleteBtn.id="delete-btn";
        deleteBtn.textContent="Delete Pet";

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

        deleteBtn.onclick=() => {
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
            formData.append("field", "delete");
            formData.append("value", "");
            formData.append("id",pet.id);

            fetch("php/update_animal.php", {
                method: 'POST',
                body: formData
            }).then(response => response.json())
            .then(result => {
                if(result.success)
                {
                    alert(result.message);
                    pageNavigation("mypets");
                }
                else
                {
                    alert(result.message);
                }
            })
            .catch(error => console.log('Error: ', error));
        };

        deleteRow.append(deleteBtn,warnText,noBtn,yesBtn);

        card.append(deleteRow);


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