fetch("php/dashboard.php").then(response => response.json())
.then(result => {
    if(result.success)
        document.getElementById("nume-dash").textContent=`Welcome, ${result.user_name}`;
    else
        pageNavigation("login");
})
.catch(error=> console.log('Error: ', error));