const toggle = document.getElementById('sidebar-toggle');
let sidebar = document.querySelector('.sidebar');
let main = document.querySelector('main');
let navigation = document.querySelector('.navigation-container');
let sidebarOverlay = document.querySelector('.sidebar-overlay');

toggle.addEventListener('click', () => {
    toggleActiveClass();
});

sidebarOverlay.addEventListener('click', () => {
    toggleActiveClass();
});

function toggleActiveClass() {
    sidebar.classList.toggle('active');
    main.classList.toggle('active');
    navigation.classList.toggle('active');
    toggle.classList.toggle('active');
    sidebarOverlay.classList.toggle('active');
}