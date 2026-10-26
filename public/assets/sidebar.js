(()=>{'use strict';
const body=document.body,button=document.querySelector('.mt-sidebar-toggle'),sidebar=document.getElementById('mt-sidebar'),backdrop=document.querySelector('[data-sidebar-close]');if(!button||!sidebar)return;
const mobile=()=>matchMedia('(max-width:760px)').matches;
const refresh=()=>{const open=mobile()?body.classList.contains('mt-sidebar-mobile-open'):!body.classList.contains('mt-sidebar-collapsed');button.setAttribute('aria-expanded',String(open));if(backdrop)backdrop.hidden=!(mobile()&&open);};
try{if(localStorage.getItem('mt_sidebar_collapsed')==='1')body.classList.add('mt-sidebar-collapsed');}catch(e){}
function closeMobile(){body.classList.remove('mt-sidebar-mobile-open');refresh();button.focus();}
button.addEventListener('click',()=>{if(mobile()){body.classList.toggle('mt-sidebar-mobile-open');}else{body.classList.toggle('mt-sidebar-collapsed');try{localStorage.setItem('mt_sidebar_collapsed',body.classList.contains('mt-sidebar-collapsed')?'1':'0');}catch(e){}}refresh();});
backdrop?.addEventListener('click',closeMobile);
document.addEventListener('keydown',event=>{if(event.key==='Escape'&&body.classList.contains('mt-sidebar-mobile-open'))closeMobile();});
window.addEventListener('resize',()=>{body.classList.remove('mt-sidebar-mobile-open');refresh();});
refresh();
})();
