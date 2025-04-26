fetch("php/dashboard.php").then(response => response.json())
    .then(result => {
    if(result.success)
    {
        pageNavigation("dashboard");//redirect in case of accessing register/login
    }
});
    
document.getElementById("signup-btn").addEventListener("click", () => {
    pageNavigation("register");
});

document.getElementById("login-form").addEventListener("submit", async function(event){
    event.preventDefault();

    const form=document.getElementById("login-form");
    const formData= new FormData(form);

    fetch('php/login.php', {
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