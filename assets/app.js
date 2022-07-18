/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.sass';

/* Include jquery */
const $ = require('jquery');
Window.prototype.$ = $;

/* Include bootstrap */
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
 * Function: Adds classes to modal of lightbox.
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
 * Function: Builds the lightbox.
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

/**
 * Function: Round given value with given decimals.
 *
 * @param value
 * @param decimals
 * @returns {number}
 */
let round = (value, decimals) => {
    return Number(Math.round(value + 'e' + decimals) + 'e-' + decimals);
};

/**
 * Function: Shows the main and detail compasses.
 */
let displayCompass = () => {
    let compass = document.getElementById('compass');
    if (compass !== null) {
        compass.style.display = 'block';
    }

    let compassDirection = document.getElementsByClassName('compass-direction');
    for (let i = 0; i < compassDirection.length; i++) {
        compassDirection.item(i).style.display = 'block';
    }
}

/**
 * Function: Set direction to main compass and detail compasses.
 *
 * @param dir
 */
let setDirection = (dir) => {

    let compassDisc = document.getElementById('compassDisc');
    if (compassDisc) {
        compassDisc.style.transform = `rotate(${dir}deg)`;
        compassDisc.style.webkitTransform = `rotate(${dir}deg)`;
        compassDisc.style.MozTransform = `rotate(${dir}deg)`;
    }

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

/**
 * Function: Sets hidden field value.
 *
 * @param id
 * @param value
 */
let setHiddenFieldValue = (id, value) => {
    document.getElementById(id).value = value;
}

/**
 * Function: Adds hidden field to form.
 *
 * @param name
 * @param value
 */
let addHiddenFieldToSearchForm = (name, value) => {

    let form = document.getElementsByName('full_location')[0];

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
    inputHidden.value = value;

    /* Append hidden element */
    form.appendChild(inputHidden);
}

/**
 * Function: Submits the search form.
 */
let submitSearchForm = () => {

    /* Submit form */
    document.getElementById('content-location-submit-hidden').click();
}

/**
 * Function: Submits the search form.
 */
let submitSearchFormReset = () => {

    /* Submit form */
    document.getElementById('content-location-submit').click();
}

/**
 * Function: Returns the full location from position.
 *
 * @param GeolocationPosition position
 * @returns {string}
 */
let getPosition = (position) => {
    return round(position.coords.latitude, 6) + ',' + round(position.coords.longitude, 6);
}

/**
 * Shows app loader.
 *
 * @param message
 */
let showAppLoader = (message) => {
    let appLoader = document.getElementById('app-loader');
    let appLoaderMessage = document.getElementById('app-loader-message');

    appLoader.style.display = 'block';
    appLoaderMessage.innerText = message;
}

/**
 * Function: Stops button or link propagation.
 *
 * @param triggeredEvent
 */
let stopPropagation = (triggeredEvent) => {
    triggeredEvent.stopPropagation();
    triggeredEvent.preventDefault();
}

/**
 * Images: Starts the lightbox.
 */
document.querySelectorAll('.lightbox-own').forEach(
    (e) => e.addEventListener('click', buildLightbox)
);

/**
 * Search form: Reset page and search.
 */
document.getElementById('content-location-submit').addEventListener('click', (e) => {

    /* Stop propagation */
    stopPropagation(e);

    /* Write location. */
    setHiddenFieldValue('p', 1);

    /* Submit form. */
    submitSearchForm();
});

/**
 * Example link list: Search for current location.
 */
document.querySelectorAll('.location-own-position').forEach(
    (e) => e.addEventListener('click', (e) => {

            /* Stop propagation */
            stopPropagation(e);

            /* Get clicked element. */
            let target = e.target;

            /* Get message. */
            let message = target.getAttribute('data-app-loader-message');

            /* Show app loader */
            showAppLoader(message);

            navigator.geolocation.getCurrentPosition((position) => {
                location.href = '/location/' + round(position.coords.latitude, 6) + '/' + round(position.coords.longitude, 6);
            })
        }
    )
);

/**
 * Example link list: Search location examples.
 */
document.querySelectorAll('.location-position').forEach(
    (e) => e.addEventListener('click', (e) => {

        /* Get clicked element. */
        let target = e.target;

        /* Get latitude and longitude. */
        let latitude = target.getAttribute('data-latitude');
        let longitude = target.getAttribute('data-longitude');

        /* Write location. */
        setHiddenFieldValue('q', latitude + ',' + longitude);

        /* Submit form. */
        submitSearchFormReset();
    })
);

/**
 * Search form: Add current position to search request.
 */
document.querySelectorAll('.search-with-position').forEach(
    (e) => e.addEventListener('click', (e) => {

        /* Stop propagation */
        stopPropagation(e);

        /* Get clicked element. */
        let target = e.target;

        /* Get message. */
        let message = target.getAttribute('data-app-loader-message');

        /* Show app loader */
        showAppLoader(message);

        navigator.geolocation.getCurrentPosition((position) => {

            /* Name and value of hidden field. */
            let name = 'l';
            let value = getPosition(position);

            /* Add hidden field. */
            addHiddenFieldToSearchForm(name, value);

            /* Submit form */
            submitSearchFormReset();
        });
    })
);

/**
 * Search form: Search for current location.
 */
document.querySelectorAll('.search-current-position').forEach(
    (e) => e.addEventListener('click', (e) => {

        /* Stop propagation */
        stopPropagation(e);

        /* Get clicked element. */
        let target = e.target;

        /* Get message. */
        let message = target.getAttribute('data-app-loader-message');

        /* Show app loader */
        showAppLoader(message);

        navigator.geolocation.getCurrentPosition((position) => {
            location.href = '/location/' + round(position.coords.latitude, 6) + '/' + round(position.coords.longitude, 6);
        });
    })
);

/**
 * Location result list: Open direct element (via id).
 */
document.querySelectorAll('.location-id').forEach(
    (e) => e.addEventListener('click', (e) => {
        let target = e.target;

        let featureClass = target.getAttribute('data-feature-class');
        let id = target.getAttribute('data-id');

        /* Write location. */
        setHiddenFieldValue('q', featureClass + ':' + id);

        /* Submit form. */
        submitSearchFormReset();
    })
);

/**
 * Location result list: Next page.
 */
document.querySelectorAll('a.next-page').forEach(
    (e) => e.addEventListener('click', (e) => {

        /* Stop propagation */
        stopPropagation(e);

        let target = e.target;

        let nextPage = target.getAttribute('data-next-page');

        /* Write location. */
        setHiddenFieldValue('p', nextPage);

        /* Submit form. */
        submitSearchForm();
    })
);


/**
 * Location result list: Order list (via class).
 */
document.querySelectorAll('p.sort-area .sort-by').forEach(
    (e) => e.addEventListener('click', (e) => {

        /* Get the target. */
        let target = e.target;

        /* Name and value for hidden input field. */
        let name = 's';
        let value = 'r';

        /* Get the value depending on the clicked sort link. */
        switch (true) {
            case target.classList.contains('sort-by-current-location'):
                value = 'l';
                break;
            case target.classList.contains('sort-by-name'):
                value = 'n';
                break;
            case target.classList.contains('sort-by-relevance-current-location'):
                value = 'rl';
                break;
            case target.classList.contains('sort-by-relevance'):
                value = 'r';
                break;
        }

        /* Add hidden field. */
        addHiddenFieldToSearchForm(name, value);

        if (value === 'l' || value === 'rl') {

            /* Stop propagation */
            stopPropagation(e);

            /* Get clicked element. */
            let target = e.target;

            /* Get message. */
            let message = target.getAttribute('data-app-loader-message');

            /* Show app loader */
            showAppLoader(message);

            /* Request position. */
            navigator.geolocation.getCurrentPosition((position) => {

                /* Name and value of hidden field. */
                let name = 'l';
                let value = getPosition(position);

                /* Add hidden field. */
                addHiddenFieldToSearchForm(name, value);

                /* Submit form */
                submitSearchFormReset();
            });
        } else {

            /* Submit form. */
            submitSearchFormReset();
        }
    })
);

/**
 * Start/Page Init.
 */
document.addEventListener('DOMContentLoaded', function(event) {
    if (window.DeviceOrientationEvent && 'ontouchstart' in window) {
        displayCompass();
        setDirection(0);

        window.addEventListener('deviceorientationabsolute', (eventData) => {
            setDirection(eventData.alpha);
        }, true);
    }
});
