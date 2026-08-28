$(".owl-carousel2").owlCarousel({

    // Most important owl features
    items: 4,
    itemsCustom: false,
    itemsDesktop: [1199, 4],
    itemsDesktopSmall: [980, 4],
    itemsTablet: [768, 3],
    itemsTabletSmall: [690, 2],
    itemsMobile: [479, 1],
    singleItem: false,
    itemsScaleUp: false,

    //Basic Speeds
    slideSpeed: 1000,
    paginationSpeed: 3000,
    rewindSpeed: 1000,

    //Autoplay
    autoPlay: true,
    stopOnHover: false,

    // Navigation
    navigation: true,
    navigationText: ["", ""],
    rewindNav: true,
    scrollPerPage: false,

    //Pagination
    pagination: true,
    paginationNumbers: false,

    // Responsive 
    responsive: true,
    responsiveRefreshRate: 200,
    responsiveBaseWidth: window,


});


$(".owl-carousel1").owlCarousel({

    // Most important owl features
    items: 3,
    itemsCustom: false,
    itemsDesktop: [1199, 4],
    itemsDesktopSmall: [980, 4],
    itemsTablet: [768, 3],
    itemsTabletSmall: [690, 2],
    itemsMobile: [479, 1],
    singleItem: false,
    itemsScaleUp: false,

    //Basic Speeds
    slideSpeed: 1000,
    paginationSpeed: 3000,
    rewindSpeed: 1000,

    //Autoplay
    autoPlay: true,
    stopOnHover: false,

    // Navigation
    navigation: true,
    navigationText: ["", ""],
    rewindNav: true,
    scrollPerPage: false,

    //Pagination
    pagination: true,
    paginationNumbers: false,

    // Responsive 
    responsive: true,
    responsiveRefreshRate: 200,
    responsiveBaseWidth: window,


});





document.getElementById('mybutton').onclick = function() {
    document.getElementsByClassName('mob-menu')[0].classList.toggle("in");
};


$(window).scroll(function() {
    if ($(window).scrollTop() >= 80) {
        $('.mid-box').addClass('fixed-header');

    } else {
        $('.mid-box').removeClass('fixed-header');
    }
});