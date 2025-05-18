document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("contactForm");

  const validateEmail = (email) => {
    return email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/);
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

    const name = document.getElementById("name");
    const email = document.getElementById("email");
    const subject = document.getElementById("subject");
    const message = document.getElementById("message");

    // Validate all fields
    if (!name.value.trim()) {
      showError(name, "Name is required");
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

    if (!subject.value.trim()) {
      showError(subject, "Subject is required");
      return;
    }

    if (!message.value.trim()) {
      showError(message, "Message is required");
      return;
    }

    // If validation passes
    // console.log("Form submitted");
    alert("Form submitted");
    form.reset();
  });
});
