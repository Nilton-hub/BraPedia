const body = document.querySelector('body'),
    btnToggleMenu = document.querySelector('#btn-toggle-menu'),
    mainsidebar = document.querySelector('#main-sidebar-menu'),
    searchContainer = document.querySelector('.search-container'),
    btnSearch = document.getElementById('btn-search');

searchContainer.style.display = 'flex';
const showSearchContainer = evt => {
    searchContainer.classList.toggle('search-show');
    const toggleSearchContainer = document.getElementById('toggle-search-container');
    toggleSearchContainer.querySelector('input');
    toggleSearchContainer.classList.toggle('icon-x');
    toggleSearchContainer.classList.toggle('icon-search');
}

btnSearch.addEventListener('click', showSearchContainer);

window.addEventListener('click', evt => {
    if (evt.srcElement !== searchContainer && evt.srcElement !== btnSearch && evt.target !== searchContainer && evt.target !== btnSearch) {
        // console.log(evt.srcElement);
    }
});

const toggleMenu = etv => {
    mainsidebar.classList.toggle('active');
    if (mainsidebar.classList.contains('active')) {
        
    } else {
        
    }
};
btnToggleMenu.addEventListener('click', toggleMenu);
