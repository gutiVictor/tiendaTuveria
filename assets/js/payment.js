document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('payment-form');
    const cardNumber = document.getElementById('card_number');
    const cardExpMonth = document.getElementById('card_exp_month');
    const cardExpYear = document.getElementById('card_exp_year');
    const cardCvv = document.getElementById('card_cvv');

    // Format card number as user types
    cardNumber.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        let formattedValue = '';
        for (let i = 0; i < value.length; i++) {
            if (i > 0 && i % 4 === 0) {
                formattedValue += ' ';
            }
            formattedValue += value[i];
        }
        e.target.value = formattedValue.slice(0, 19);
    });

    // Format expiration month
    cardExpMonth.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 0) {
            value = Math.min(Math.max(parseInt(value), 1), 12).toString();
            if (value.length === 1) value = '0' + value;
        }
        e.target.value = value.slice(0, 2);
    });

    // Format expiration year
    cardExpYear.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        const currentYear = new Date().getFullYear();
        if (value.length === 4) {
            const year = parseInt(value);
            if (year < currentYear) {
                value = currentYear.toString();
            }
        }
        e.target.value = value.slice(0, 4);
    });

    // Format CVV
    cardCvv.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        e.target.value = value.slice(0, 4);
    });

    // Form submission validation
    form.addEventListener('submit', function(e) {
        const errors = [];

        // Validate card number (basic Luhn algorithm check)
        const cardVal = cardNumber.value.replace(/\s/g, '');
        if (!isValidCardNumber(cardVal)) {
            errors.push('Número de tarjeta inválido');
        }

        // Validate expiration date
        const currentDate = new Date();
        const expMonth = parseInt(cardExpMonth.value);
        const expYear = parseInt(cardExpYear.value);
        const expDate = new Date(expYear, expMonth - 1);

        if (expDate < currentDate) {
            errors.push('La tarjeta ha expirado');
        }

        // Validate CVV
        if (!/^\d{3,4}$/.test(cardCvv.value)) {
            errors.push('CVV inválido');
        }

        if (errors.length > 0) {
            e.preventDefault();
            alert(errors.join('\n'));
        }
    });

    // Luhn algorithm implementation
    function isValidCardNumber(number) {
        let sum = 0;
        let isEven = false;

        for (let i = number.length - 1; i >= 0; i--) {
            let digit = parseInt(number.charAt(i));

            if (isEven) {
                digit *= 2;
                if (digit > 9) {
                    digit -= 9;
                }
            }

            sum += digit;
            isEven = !isEven;
        }

        return sum % 10 === 0;
    }
});