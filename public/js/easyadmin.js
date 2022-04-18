document.addEventListener("DOMContentLoaded", () => {
    let calendarImageRadioButtons = document.querySelectorAll('#CalendarImage_month .form-check-input');

    calendarImageRadioButtons.forEach((calendarImageRadioButton) => {
        calendarImageRadioButton.addEventListener('click', function(event) {
            document.querySelector('#CalendarImage_title').value = event.target.parentElement.querySelector('label').textContent;
        });
    });
});