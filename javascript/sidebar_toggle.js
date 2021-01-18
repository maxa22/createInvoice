const toggle = document.getElementById('sidebar-toggle');
let sidebar = document.querySelector('.sidebar');
let main = document.querySelector('main');
let navigation = document.querySelector('.navigation-container');
let sidebarOverlay = document.querySelector('.sidebar-overlay');
let sidebarMenuDropdowns = document.querySelectorAll('.sidebar__dropdown-toggle');

toggle.addEventListener('click', () => {
    toggleActiveClass();
});

sidebarOverlay.addEventListener('click', () => {
    toggleActiveClass();
});

for(let sidebarMenuDropdown of sidebarMenuDropdowns) {
    sidebarMenuDropdown.addEventListener('click', e => {
        sidebarMenuDropdown.classList.toggle('sidebar__dropdown-active');
    });
}

function toggleActiveClass() {
    sidebar.classList.toggle('active');
    main.classList.toggle('active');
    navigation.classList.toggle('active');
    toggle.classList.toggle('active');
    sidebarOverlay.classList.toggle('active');
}


