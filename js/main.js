const validPages = ["login","register","dashboard","addpets","mypets","browse","requests","panel","animal","animal_public","notfound"];

window.onload = () => {
  if (!location.hash) {
    fetch("php/dashboard.php").then(response => response.json())
      .then(result => {
        if (result.success) 
        { location.hash = "dashboard"; loadPage("dashboard"); } 
        else 
        { location.hash = "login"; loadPage("login"); }
      });
  } 
  else
  {
    const fullHash = location.hash.substring(1);
    const pageHash = fullHash.split('?')[0];
    if (validPages.includes(pageHash))
      loadPage(fullHash);
    else
      loadPage("notfound");
  }
};

window.onhashchange = () => {
  const fullHash = location.hash.substring(1);
  const pageHash = fullHash.split('?')[0];
  if(validPages.includes(pageHash))
    loadPage(fullHash);
  else
    loadPage("notfound");
};

function pageNavigation(pageName){
  const baseHash=pageName.split('?')[0];
  if(validPages.includes(baseHash))
    location.hash=pageName;
  else
    location.hash="notfound";
};

function loadPage(pageName){
  const baseHash=pageName.split('?')[0];

  fetch(`partials/${baseHash}.html`).then(result => result.text()).then(html => {
    document.getElementById("main-content").innerHTML = html;

    if(baseHash==="dashboard")
    {
      const home=document.getElementById("home-btn");
      if(home)
        home.classList.add("active-btn");
    }
    else if(baseHash==="addpets")
    {
      const addpets=document.getElementById("addpets-btn");
      if(addpets)
        addpets.classList.add("active-btn");
    }
    else if(baseHash==="mypets")
    {
      const mypets=document.getElementById("mypets-btn");
      if(mypets)
        mypets.classList.add("active-btn");
    }
    else if(baseHash==="browse")
    {
      const browse=document.getElementById("browse-btn");
      if(browse)
        browse.classList.add("active-btn");
    }
    else if(baseHash==="requests")
    {
      const requests=document.getElementById("requests-btn");
      if(requests)
        requests.classList.add("active-btn");
    }
    else if(baseHash==="panel")
    {
      const panel=document.getElementById("panel-btn");
      if(panel)
        panel.classList.add("active-btn");
    }

    const oldStyle=document.getElementById("main-style");
    if(oldStyle)
      oldStyle.remove();

    const link=document.createElement("link");
    link.href=`css/${baseHash}.css`;
    link.rel="stylesheet";
    link.id="main-style";
    document.head.appendChild(link);

    const oldScript=document.getElementById("page-script");
    if(oldScript)
      oldScript.remove();

    if(baseHash!=="notfound")
    {const script=document.createElement("script");
    script.src=`js/${baseHash}.js`;
    script.id="page-script";
    document.body.appendChild(script);}
  });
};