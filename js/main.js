const validPages = ["login","register","dashboard","notfound"];

window.onload = () => {
  if(!location.hash)
    location.hash="login";  

  const pageHash=location.hash.substring(1);

  if(validPages.includes(pageHash))
    loadPage(pageHash);
  else
    loadPage("notfound");
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

    const script=document.createElement("script");
    script.src=`js/${pageName}.js`;
    script.id="page-script";
    document.body.appendChild(script);
  });
};