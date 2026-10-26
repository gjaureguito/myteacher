(()=>{'use strict';
const menus=[...document.querySelectorAll('.topbar .mt-nav-dropdown')];if(!menus.length)return;
const closeAll=(except=null)=>menus.forEach(m=>{if(m!==except)m.open=false;});
menus.forEach(menu=>{menu.addEventListener('toggle',()=>{if(menu.open)closeAll(menu);});});
document.addEventListener('pointerdown',event=>{if(!menus.some(menu=>menu.contains(event.target)))closeAll();});
document.addEventListener('keydown',event=>{if(event.key==='Escape'){const active=menus.find(menu=>menu.open);if(active){active.open=false;active.querySelector('summary')?.focus();}}});
})();
