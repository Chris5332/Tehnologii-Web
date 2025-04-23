document.getElementById("signin-btn").addEventListener("click", () => {
    pageNavigation("login");
});

(() => {
    const checkbutton= document.getElementById("family-checkbox");
    const surnameInput= document.getElementById("surname-input");

    if(checkbutton && surnameInput){
        checkbutton.addEventListener("change", () => {
            if(checkbutton.checked===true)
                {
                    surnameInput.disabled=true;
                    surnameInput.value="";
                }
            else
                {
                    surnameInput.disabled=false;
                }
        });
    }
})();

document.getElementById("register-form").addEventListener("submit", async function(event) {
    event.preventDefault();

    const form=document.getElementById("register-form");
    const formData= new FormData(form);

    fetch('php/register.php', {
        method: 'POST',
        body: formData
    }).then(response => response.json())
    .then(result => {
        if(result.success)
            {
                pageNavigation("dashboard");
            }
        else if(result.errors)
            alert(result.errors.join("\n"));
    })
    .catch(error => console.log('Error: ', error));
});