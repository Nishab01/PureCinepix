const emailInput = document.getElementById('email');
const emailError = document.getElementById('emailError');
const btnSubmit = document.getElementById('createacc');

const passInput = document.getElementById('password');
const passError = document.getElementById('passError');
const passHint = document.getElementById('passHint');

emailInput.addEventListener('input', function() {
    const emailValue = emailInput.value;
    // Standard Email Regex
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (emailPattern.test(emailValue)) {
        // VALID: Reset border and hide error
        emailInput.classList.replace('border-red-500', 'border-blue-500');
        emailError.classList.replace('text-red-500', 'text-gray-400');
        // btnSubmit.classList.replace('cursor-not-allowed', 'cursor-pointer');

        passInput.addEventListener('input', function() {
            const passValue = passInput.value;

            if (passValue.length >= 8) {
                // VALID: 8 or more characters
                passInput.classList.replace('border-red-500', 'border-blue-500');
                passError.classList.add('hidden');
                passHint.classList.remove('hidden'); // Show original hint
                btnSubmit.classList.replace('cursor-not-allowed', 'cursor-pointer');
                btnSubmit.disabled = false;
            } else if (passValue.length > 0) {
                // INVALID: Between 1 and 7 characters
                passInput.classList.replace('border-blue-500', 'border-red-500');
                passError.classList.remove('hidden');
                passHint.classList.add('hidden'); // Hide hint to save space
                btnSubmit.classList.replace('cursor-pointer', 'cursor-not-allowed');
                btnSubmit.disabled = true;
            } else {
                // EMPTY: Reset to default
                passInput.classList.replace('border-red-500', 'border-blue-500');
                passError.classList.add('hidden');
                passHint.classList.remove('hidden');
                btnSubmit.classList.replace('cursor-pointer', 'cursor-not-allowed');
                btnSubmit.disabled = true;
            }
        });
    }
    else {
        // INVALID: Show red border and error message
        // Only show error if there is actually text in the box
        if (emailValue.length > 0) {
            emailInput.classList.replace('border-blue-500', 'border-red-500');
            emailError.classList.replace('text-gray-400', 'text-red-500');
            btnSubmit.classList.replace('cursor-pointer', 'cursor-not-allowed');
            btnSubmit.disabled = true;
        } else {
            // Reset if empty
            emailInput.classList.replace('border-red-500', 'border-blue-500');
            emailError.classList.replace('text-red-500', 'text-gray-400');
            btnSubmit.classList.replace('cursor-pointer', 'cursor-not-allowed');
            btnSubmit.disabled = true;
        }
    }
});

function togglePassword(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);

    if (input.type === 'password') {
        input.type = 'text';
        // Eye-Slash Icon
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />';
    }
    else {
        input.type = 'password';
        // Normal Eye Icon
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />';
    }
}