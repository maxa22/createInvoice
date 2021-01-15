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
        removeActiveClasses();
        let dropdown = sidebarMenuDropdown.querySelector('ul');
        if(dropdown.offsetHeight == '0') {
            sidebarMenuDropdown.classList.add('sidebar__dropdown-active');
            let dropdownItem = sidebarMenuDropdown.querySelector('li');
            let dropdownItemsCount = sidebarMenuDropdown.querySelectorAll('li').length;
            let dropdownHeight = dropdownItem.offsetHeight * dropdownItemsCount;
            dropdown.style.height = `${dropdownHeight}px`;
        } else {
            sidebarMenuDropdown.classList.remove('sidebar__dropdown-active');
            dropdown.style.height = '0';
        }
    });
}

function toggleActiveClass() {
    sidebar.classList.toggle('active');
    main.classList.toggle('active');
    navigation.classList.toggle('active');
    toggle.classList.toggle('active');
    sidebarOverlay.classList.toggle('active');
}

function removeActiveClasses() {
    for(let sidebarMenuDropdown of sidebarMenuDropdowns) {
        sidebarMenuDropdown.classList.remove('sidebar__dropdown-active');
        let dropdown = sidebarMenuDropdown.querySelector('ul');
        dropdown.style.height = '0';
    }
}