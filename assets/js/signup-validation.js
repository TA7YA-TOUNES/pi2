document.addEventListener("DOMContentLoaded", () => {
  const form = document.querySelector("form");

  const validateEmail = (email) => {
    return email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/);
  };

  const validatePassword = (password) => {
    return (
      password.length >= 6 &&
      password.match(/[A-Za-z]/) &&
      password.match(/[0-9]/)
    );
  };

  const validateName = (name) => {
    return name.trim().length >= 2 && /^[A-Za-z\s]+$/.test(name);
  };

  const showError = (element, message) => {
    const error = document.createElement("div");
    error.className = "error-message";
    error.textContent = message;
    element.parentElement.appendChild(error);
    element.classList.add("error");
  };

  form.addEventListener("submit", (e) => {
    e.preventDefault();

    // Clear previous errors
    document.querySelectorAll(".error-message").forEach((err) => err.remove());
    document
      .querySelectorAll(".error")
      .forEach((el) => el.classList.remove("error"));

    const fullname = document.getElementById("fullname");
    const email = document.getElementById("email");
    const password = document.getElementById("password");
    const confirmPassword = document.getElementById("confirm-password");

    if (!fullname.value.trim()) {
      showError(fullname, "Name is required");
      return;
    }

    if (!validateName(fullname.value)) {
      showError(
        fullname,
        "Please enter a valid name (letters and spaces only)"
      );
      return;
    }

    if (!email.value.trim()) {
      showError(email, "Email is required");
      return;
    }

    if (!validateEmail(email.value)) {
      showError(email, "Please enter a valid email");
      return;
    }

    if (!validatePassword(password.value)) {
      showError(
        password,
        "Password must be at least 6 characters with letters and numbers"
      );
      return;
    }

    if (password.value !== confirmPassword.value) {
      showError(confirmPassword, "Passwords do not match");
      return;
    }

    alert("Signup successful");
  });
});
