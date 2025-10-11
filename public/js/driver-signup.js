const unmatchedPasswordErr = document.getElementById('unmatchedPasswordErr');

const updateUnmatchedPasswordErrState = () => {
    if(confirmPassword.value.length === 0) {
        return unmatchedPasswordErr.classList.add('d-none');
    }

    if(!doesPasswordMatch()) {
        return unmatchedPasswordErr.classList.remove('d-none');
    }

    unmatchedPasswordErr.classList.add('d-none');
}

const isPasswordFormatValid = (pw) => {
    // password should contain atleast one uppercase, lowercase letter, and a special char
    const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*(),.?":{}|<>]).+$/
    return regex.test(pw);
}

const togglePasswordVisibility = (inputId, toggleBtnId) => {
    const input = document.getElementById(inputId);
    const toggleBtn = document.getElementById(toggleBtnId);
    const icon = toggleBtn.querySelector("i");

    toggleBtn.addEventListener("click", function () {
      const type = input.getAttribute("type") === "password" ? "text" : "password";
      input.setAttribute("type", type);

      // toggle eye / eye-slash icon
      if (type === "text") {
        icon.classList.remove("bi-eye");
        icon.classList.add("bi-eye-slash");
      } else {
        icon.classList.remove("bi-eye-slash");
        icon.classList.add("bi-eye");
      }
    });
}

togglePasswordVisibility("password", "togglePassword");
togglePasswordVisibility("confirmPassword", "toggleConfirmPassword");