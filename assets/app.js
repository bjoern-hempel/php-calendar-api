/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.sass';

const $ = require('jquery');
require('bootstrap');

/**
 * Function: Add classes depending on page position.
 */
let navbarChanger = () => {
    let y = $(window).scrollTop();

    if (y > 20) {
        $('header')
            .addClass('--not-top')
            .find('.navbar')
            .removeClass('navbar-dark')
            .addClass('navbar-light');
    } else {
        $('header')
            .removeClass('--not-top')
            .find('.navbar')
            .removeClass('navbar-light')
            .addClass('navbar-dark');
    }
}

if ($('body').hasClass('content-index')) {
    $(window).scroll(navbarChanger);
    navbarChanger();
}

// start the Stimulus application
import './bootstrap';
