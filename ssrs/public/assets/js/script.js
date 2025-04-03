document.addEventListener('DOMContentLoaded', () => {
    console.log('Smart Service Request System loaded successfully.');

    // Example: Add interactivity for form validation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', (event) => {
            const inputs = form.querySelectorAll('input[required]');
            let isValid = true;

            inputs.forEach(input => {
                if (!input.value.trim()) {
                    isValid = false;
                    input.style.borderColor = 'red';
                } else {
                    input.style.borderColor = '#ccc';
                }
            });

            if (!isValid) {
                event.preventDefault();
                alert('Please fill out all required fields.');
            }
        });
    });
});
