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
        else if(result.message)
            alert(result.message);
    })
    .catch(error => console.log('Error: ', error));
});