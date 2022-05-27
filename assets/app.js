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

/* Start the Stimulus application */
import './bootstrap';

/* Import bs5-lightbox */
import Lightbox from 'bs5-lightbox'

/* Save last added classes to lightbox. */
let modalAddedClasses = null;

/* Saves the lightbox query. */
let lightboxQuery = 'div.modal.lightbox';

/**
 * Adds classes to modal of lightbox.
 *
 */
let addLightboxClasses = () => {

    /* Get some elements of lightbox. */
    let modal = $(lightboxQuery);
    let dataGallery = modal.data('gallery');

    let items = modal.find('.carousel-inner .carousel-item');
    let links = $('*[data-gallery="' + dataGallery + '"]');

    /* Add modal classes. */
    links.each((index) => {
        let item = $(items[index]);

        if (item.hasClass('active')) {

            let dataAddClass = item.data('addClass');

            /* Remove classes added before. */
            if (modalAddedClasses !== null) {
                modal.removeClass(modalAddedClasses);
                modalAddedClasses = null;
            }

            /* Add new classes.  */
            if (typeof dataAddClass !== 'undefined') {
                modalAddedClasses = dataAddClass;
                modal.addClass(modalAddedClasses);
            }
        }
    });
}

/**
 * Builds the lightbox.
 *
 * @param e
 */
let buildLightbox = (e) => {

    /* Start lightbox. */
    e.preventDefault();
    const lightbox = new Lightbox(e.currentTarget);
    lightbox.show();

    /* Get properties. */
    let currentTarget = $(e.currentTarget);
    let dataGallery = currentTarget.data('gallery');

    /* Get some lightbox elements. */
    let modal = $(lightboxQuery);
    let links = $('a[data-gallery="' + dataGallery + '"]');
    let items = modal.find('.carousel-inner .carousel-item');

    /* Add image classes */
    links.each((index, linkElement) => {
        let link = $(linkElement);
        let item = $(items[index]);
        let ratio = item.find('.ratio');

        let imageSource = link.find('img');
        let imageTarget = ratio.find('img');

        imageTarget.addClass(imageSource.data('addClass'));

        let style = 'background-color: transparent;';
        let width = parseInt(imageSource.data('width'));
        let height = parseInt(imageSource.data('height'));

        if (!isNaN(width) && width > 0 && !isNaN(height) && height > 0) {
            style += ' --bs-aspect-ratio: ' + String(Math.round(height / width * 10000) / 100) + '%;';
        }

        ratio.attr('style', style);
        ratio.removeClass('.ratio-16x9');

        /* Transfer data attributes. */
        item.attr('data-add-class', link.data('addClass'));
        modal.attr('data-gallery', link.data('gallery'));
    });

    /* Add modal classes */
    addLightboxClasses();

    modal.find('.carousel-control-next').click(() => { addLightboxClasses(); });
    modal.find('.carousel-control-prev').click(() => { addLightboxClasses(); });
}

document.querySelectorAll('.lightbox-own').forEach(
    (e) => e.addEventListener('click', buildLightbox)
);

/**
 * Submits given position.
 *
 * @param position
 */
let submitPosition = (position) => {
    document.getElementById('full_location_locationFull').value = position;
    document.getElementById('content-location-submit').click();
};

document.querySelectorAll('.location-own-position').forEach(
    (e) => e.addEventListener('click', () => {
            navigator.geolocation.getCurrentPosition((position) => {
                let positionFull = position.coords.latitude + ' ' + position.coords.longitude;

                submitPosition(positionFull);
            })
        }
    )
);

document.querySelectorAll('.location-position').forEach(
    (e) => e.addEventListener('click', (e) => {
        let target = e.target;

        let latitude = target.getAttribute('data-latitude');
        let longitude = target.getAttribute('data-longitude');

        let position = latitude + ' ' + longitude;

        submitPosition(position);
    })
);
