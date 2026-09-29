



    document.addEventListener('DOMContentLoaded', function () {
      const form = document.getElementById('contactForm');
      const nameInput = document.getElementById('name');
      const emailInput = document.getElementById('email');
      const phoneInput = document.getElementById('phone');
      const messageInput = document.getElementById('message');

      // Error පෙන්වන Function එක
      function setError(input, message) {
        const errorElement = document.getElementById(input.id + 'Error');
        input.classList.add('is-invalid');
        if (errorElement) {
          errorElement.innerText = message;
        }
      }

      // Error ඉවත් කරන Function එක
      function clearError(input) {
        const errorElement = document.getElementById(input.id + 'Error');
        input.classList.remove('is-invalid');
        if (errorElement) {
          errorElement.innerText = '';
        }
      }

      // 1. Name Field Validation
      function validateName() {
        nameInput.value = nameInput.value.replace(/[0-9]/g, '');

        const value = nameInput.value.trim();
        if (value === '') {
          setError(nameInput, 'Please enter your full name.');
          return false;
        } else if (value.length < 3) {
          setError(nameInput, 'Name must be at least 3 letters long.');
          return false;
        } else {
          clearError(nameInput);
          return true;
        }
      }

      // 2. Email Field Validation (Live check for '@' and valid format)
      function validateEmail() {
        const value = emailInput.value.trim();
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (value === '') {
          setError(emailInput, 'Please enter your email address.');
          return false;
        } else if (!value.includes('@')) {
          setError(emailInput, 'Email must contain an "@" symbol.');
          return false;
        } else if (!emailPattern.test(value)) {
          setError(emailInput, 'Please enter a valid email address (e.g., name@example.com).');
          return false;
        } else {
          clearError(emailInput);
          return true;
        }
      }

      // 3. Phone Field Validation (Only digits allowed)
      function validatePhone() {
        phoneInput.value = phoneInput.value.replace(/[^0-9]/g, '');

        const value = phoneInput.value.trim();
        if (value === '') {
          setError(phoneInput, 'Please enter your phone number.');
          return false;
        } else if (value.length !== 10) {
          setError(phoneInput, 'Phone number must be exactly 10 digits.');
          return false;
        } else {
          clearError(phoneInput);
          return true;
        }
      }

      // 4. Message Field Validation
      function validateMessage() {
        const value = messageInput.value.trim();
        if (value === '') {
          setError(messageInput, 'Please enter your message.');
          return false;
        } else if (value.length < 10) {
          setError(messageInput, 'Message must be at least 10 characters long.');
          return false;
        } else {
          clearError(messageInput);
          return true;
        }
      }

      // Submit කිරීමට පෙර ටයිප් කරන විටම (Live / Real-time) පරීක්ෂා කිරීම
      nameInput.addEventListener('input', validateName);
      emailInput.addEventListener('input', validateEmail);
      phoneInput.addEventListener('input', validatePhone);
      messageInput.addEventListener('input', validateMessage);

      // Form Submit Event (ඊමේල් යැවීමේ කොටස මෙතැනට එක් කර ඇත)
      form.addEventListener('submit', function (e) {
        e.preventDefault(); // පිටුව Refresh වීම වළක්වයි

        const isNameValid = validateName();
        const isEmailValid = validateEmail();
        const isPhoneValid = validatePhone();
        const isMessageValid = validateMessage();

        // සියලුම Validations සාර්ථක නම් පමණක් Email යැවීම සිදු කරයි
        if (isNameValid && isEmailValid && isPhoneValid && isMessageValid) {
          const submitBtn = form.querySelector('button[type="submit"]');
          const originalBtnText = submitBtn.innerHTML;

          // Status පෙන්වීමට Div එකක් තිබේදැයි බැලීම (නැතිනම් එකක් නිර්මාණය කරයි)
          let statusBox = document.getElementById('formStatus');
          if (!statusBox) {
            statusBox = document.createElement('div');
            statusBox.id = 'formStatus';
            statusBox.className = 'mt-3';
            form.appendChild(statusBox);
          }
          statusBox.innerHTML = '';

          // Button එක Disable කර 'Sending...' පෙන්වීම
          submitBtn.disabled = true;
          submitBtn.innerHTML = 'Sending... <i class="fas fa-spinner fa-spin ms-1"></i>';

          const formData = new FormData(form);

          // fetch API මගින් mail.php වෙත දත්ත යැවීම
          fetch('mail.php', {
            method: 'POST',
            body: formData
          })
            .then(response => response.json())
            .then(data => {
              if (data.status === 'success') {
                statusBox.innerHTML = '<div class="alert alert-success py-2">' + data.message + '</div>';
                form.reset(); // Form එක හිස් කරයි

                // Success message එක තත්පර 6කින් ඉවත් කිරීම
                setTimeout(() => {
                  statusBox.innerHTML = '';
                }, 6000);
              } else {
                statusBox.innerHTML = '<div class="alert alert-danger py-2">' + data.message + '</div>';
              }
            })
            .catch(error => {
              statusBox.innerHTML = '<div class="alert alert-danger py-2">Something went wrong. Please try again later.</div>';
            })
            .finally(() => {
              // Button එක නැවත සාමාන්‍ය තත්ත්වයට පත් කිරීම
              submitBtn.disabled = false;
              submitBtn.innerHTML = originalBtnText;
            });
        }
      });
    });

