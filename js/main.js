const validPages = ["login","register","dashboard","addpets","mypets","browse","requests","panel","notfound"];

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
    const pageHash = location.hash.substring(1);
    if (validPages.includes(pageHash))
      loadPage(pageHash);
    else
      loadPage("notfound");
  }
};

window.onhashchange = () => {
  const pageHash=location.hash.substring(1);
  if(validPages.includes(pageHash))
    loadPage(pageHash);
  else
    loadPage("notfound");
};

function pageNavigation(pageName){
  if(validPages.includes(pageName))
    location.hash=pageName;
  else
    location.hash="notfound";
};

function loadPage(pageName){
  fetch(`partials/${pageName}.html`).then(result => result.text()).then(html => {
    document.getElementById("main-content").innerHTML = html;

    if(pageName==="dashboard")
    {
      const home=document.getElementById("home-btn");
      if(home)
        home.classList.add("active-btn");
    }
    else if(pageName==="addpets")
    {
      const addpets=document.getElementById("addpets-btn");
      if(addpets)
        addpets.classList.add("active-btn");
    }
    else if(pageName==="mypets")
    {
      const mypets=document.getElementById("mypets-btn");
      if(mypets)
        mypets.classList.add("active-btn");
    }
    else if(pageName==="browse")
    {
      const browse=document.getElementById("browse-btn");
      if(browse)
        browse.classList.add("active-btn");
    }
    else if(pageName==="requests")
    {
      const requests=document.getElementById("requests-btn");
      if(requests)
        requests.classList.add("active-btn");
    }
    else if(pageName==="panel")
    {
      const panel=document.getElementById("panel-btn");
      if(panel)
        panel.classList.add("active-btn");
    }

    const oldStyle=document.getElementById("main-style");
    if(oldStyle)
      oldStyle.remove();

    const link=document.createElement("link");
    link.href=`css/${pageName}.css`;
    link.rel="stylesheet";
    link.id="main-style";
    document.head.appendChild(link);

    const oldScript=document.getElementById("page-script");
    if(oldScript)
      oldScript.remove();

    if(pageName!=="notfound")
    {const script=document.createElement("script");
    script.src=`js/${pageName}.js`;
    script.id="page-script";
    document.body.appendChild(script);}
  });
};