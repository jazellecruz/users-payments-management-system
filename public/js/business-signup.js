const password = document.getElementById('password');
const confirmPassword = document.getElementById('confirmPassword');
const unmatchedPasswordErr = document.getElementById('unmatchedPasswordErr');
const passwordCriteriaMsg = document.getElementById('passwordCriteriaMsg');
const signupBtn = document.getElementById('signupBtn');
const businessSignUpForm = document.getElementById('businessSignUpForm');

password.addEventListener('input', () => {
    updateSubmitBtnState();
    updatePasswordCriteriaMsgState();
    updateUnmatchedPasswordErrState();
});

confirmPassword.addEventListener('input', () => {
    updateSubmitBtnState();
    updatePasswordCriteriaMsgState();
    updateUnmatchedPasswordErrState();
});

document.querySelectorAll('.togglePassword').forEach(button => {
    button.addEventListener('click', function () {
        const targetInput = document.getElementById(this.dataset.target);
        const icon = this.querySelector('i');

        if (targetInput.type === 'password') {
            targetInput.type = 'text';
            icon.classList.remove('bi-eye-slash-fill');
            icon.classList.add('bi-eye-fill');
        } else {
            targetInput.type = 'password';
            icon.classList.remove('bi-eye-fill');
            icon.classList.add('bi-eye-slash-fill');
        }
    }); 
});

const doesPasswordMatch = () => {
    return confirmPassword.value === password.value; 
}

const isPasswordFormatValid = pw => {
    // password should contain atleast one uppercase, lowercase letter, and a special char
    const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*(),.?":{}|<>]).+$/
    return regex.test(pw);
}

const updateSubmitBtnState = () => {
    if(!isPasswordFormatValid(password.value)) { return signupBtn.disabled = true; } 
    if(!doesPasswordMatch()) { return signupBtn.disabled = true; }
    signupBtn.disabled = false;
}

const updatePasswordCriteriaMsgState = () => {
    passwordCriteriaMsg.classList.remove('text-secondary', 'text-danger', 'text-success');
    if(password.value.length === 0) return passwordCriteriaMsg.classList.add('text-secondary');
    if(!isPasswordFormatValid(password.value)) return passwordCriteriaMsg.classList.add('text-danger');
    return passwordCriteriaMsg.classList.add('text-success');
}

const updateUnmatchedPasswordErrState = () => {
    if(confirmPassword.value.length === 0) {
        return unmatchedPasswordErr.classList.add('d-none');
    }

    if(!doesPasswordMatch()) {
        return unmatchedPasswordErr.classList.remove('d-none');
    }

    unmatchedPasswordErr.classList.add('d-none');
}

businessSignUpForm.addEventListener('submit', () => {
    const spinner = signupBtn.querySelector('.loading-spinner');
    spinner.classList.remove('d-none');
    signupBtn.disabled = true;
});