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

/**
 * Round given value with given decimals.
 *
 * @param value
 * @param decimals
 * @returns {number}
 */
let round = (value, decimals) => {
    return Number(Math.round(value + 'e' + decimals) + 'e-' + decimals);
};

document.querySelectorAll('.location-own-position').forEach(
    (e) => e.addEventListener('click', () => {
            navigator.geolocation.getCurrentPosition((position) => {
                window.location.href = window.location.protocol + '//' + window.location.host + window.location.pathname + '?q=' +
                    round(position.coords.latitude, 6) + ',' +
                    round(position.coords.longitude, 6);
            })
        }
    )
);

document.querySelectorAll('.location-position').forEach(
    (e) => e.addEventListener('click', (e) => {
        let target = e.target;

        let latitude = target.getAttribute('data-latitude');
        let longitude = target.getAttribute('data-longitude');

        window.location.href = window.location.protocol + '//' + window.location.host + window.location.pathname + '?q=' +
            latitude + ',' +
            longitude;
    })
);

document.querySelectorAll('.with-position').forEach(
    (e) => e.addEventListener('click', (e) => {
        e.stopPropagation();
        e.preventDefault();

        let target = e.target;

        navigator.geolocation.getCurrentPosition((position) => {

            /* Name of  */
            let name = 'l';

            /* Remove existing element */
            let element = document.getElementById(name);
            if (element !== null) {
                element.remove();
            }

            /* Create hidden element */
            let inputHidden = document.createElement('input');
            inputHidden.type = 'hidden';
            inputHidden.name = name;
            inputHidden.id = name;
            inputHidden.value = round(position.coords.latitude, 6) + ',' + round(position.coords.longitude, 6);

            /* Append hidden element */
            target.parentElement.appendChild(inputHidden);

            /* Submit form */
            document.getElementById('content-location-submit').click();
        });
    })
);

document.querySelectorAll('.location-id').forEach(
    (e) => e.addEventListener('click', (e) => {
        let target = e.target;

        let featureClass = target.getAttribute('data-feature-class');
        let id = target.getAttribute('data-id');

        let url = window.location.protocol + '//' + window.location.host + window.location.pathname + '?id=' + featureClass + ':' + id;

        window.location.href = url;
    })
);

let setDirection = (dir) => {

    let compassDisc = document.getElementById('compassDisc');
    compassDisc.style.transform = `rotate(${dir}deg)`;
    compassDisc.style.webkitTransform = `rotate(${dir}deg)`;
    compassDisc.style.MozTransform = `rotate(${dir}deg)`;

    let arrowDirection = document.getElementsByClassName('arrow-direction');
    for (let i = 0; i < arrowDirection.length; i++) {
        let item = arrowDirection.item(i);
        let dataDegree = parseFloat(item.getAttribute('data-degree'));
        let dirArrow = dataDegree + dir;

        item.style.transform = `rotate(${dirArrow}deg)`;
        item.style.webkitTransform = `rotate(${dirArrow}deg)`;
        item.style.MozTransform = `rotate(${dirArrow}deg)`;
    }
}

let displayCompass = () => {
    let compass = document.getElementById('compass');
    compass.style.display = 'block';

    let compassDirection = document.getElementsByClassName('compass-direction');
    for (let i = 0; i < compassDirection.length; i++) {
        compassDirection.item(i).style.display = 'block';
    }
}

document.addEventListener('DOMContentLoaded', function(event) {
    if (window.DeviceOrientationEvent && 'ontouchstart' in window) {
        displayCompass();
        setDirection(0);

        window.addEventListener('deviceorientationabsolute', (eventData) => {
            setDirection(eventData.alpha);
        }, true);
    }
});
