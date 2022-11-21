document.addEventListener("DOMContentLoaded", () => {

    let imageTitle = document.querySelector('#CalendarImage_title');
    let imagePosition = document.querySelector('#CalendarImage_position');

    let calendarImageMonthRadioButtons = document.querySelectorAll('#CalendarImage_month .form-check-input');
    calendarImageMonthRadioButtons.forEach((calendarImageMonthRadioButton) => {
        calendarImageMonthRadioButton.addEventListener('click', function(event) {
            let month = event.target.parentElement.querySelector('label').textContent;
            if (month !== '' && imageTitle.value === '') {
                imageTitle.value = month;
            }
        });
    });

    let calendarImageImageRadioButtons = document.querySelectorAll('#CalendarImage_image .form-check-input');
    calendarImageImageRadioButtons.forEach((calendarImageImageRadioButton) => {
        calendarImageImageRadioButton.addEventListener('click', function(event) {
            let imagePreview = event.target.parentElement.querySelector('img.preview');
            let title = imagePreview.getAttribute('data-title');
            if (title !== '') {
                imageTitle.value = title;
            }
            let position = imagePreview.getAttribute('data-position');
            if (position !== '') {
                imagePosition.value = position;
            }
        });
    });
});