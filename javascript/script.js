document.addEventListener("DOMContentLoaded", function() {
    // Success alert logic
    const showSuccessAlert = () => {
        if (document.querySelector('.alert.success')) {
            setTimeout(function() {
                document.querySelector('.alert.success').classList.add('alert__active');
            }, 10);

            setTimeout(function() {
                document.querySelector('.alert.success').classList.add('alert__extended');
            }, 500);

            setTimeout(function() {
                var alert = document.querySelector('.alert.success');
                alert.classList.remove('alert__extended');
                setTimeout(function() {
                    alert.classList.remove('alert__active');
                }, 500);
                setTimeout(function() {
                    alert.remove();
                }, 1000);
            }, 5000);
        }
    };

    // Error alert logic
    const showErrorAlert = () => {
        if (document.querySelector('.alert.error')) {
            setTimeout(function() {
                document.querySelector('.alert.error').classList.add('alert__active');
            }, 10);

            setTimeout(function() {
                document.querySelector('.alert.error').classList.add('alert__extended');
            }, 500);

            setTimeout(function() {
                var alert = document.querySelector('.alert.error');
                alert.classList.remove('alert__extended');
                setTimeout(function() {
                    alert.classList.remove('alert__active');
                }, 500);
                setTimeout(function() {
                    alert.remove();
                }, 1000);
            }, 5000);
        }
    };

    showSuccessAlert();
    showErrorAlert();

    // Sign up/in panel toggle
    const signUpButton = document.getElementById('signUp');
    const signInButton = document.getElementById('signIn');
    const container = document.getElementById('container');

    if (signUpButton && signInButton && container) {
        signUpButton.addEventListener('click', () => {
            container.classList.add("right-panel-active");
        });

        signInButton.addEventListener('click', () => {
            container.classList.remove("right-panel-active");
        });
    }

    // Profile dropdown logic
    const profileDropdown = document.querySelector('.profile-dropdown span');
    if (profileDropdown) {
        profileDropdown.addEventListener('click', function() {
            this.parentElement.classList.toggle('show');
        });

        document.addEventListener('click', function(event) {
            if (!profileDropdown.contains(event.target)) {
                profileDropdown.parentElement.classList.remove('show');
            }
        });
    }
});

document.addEventListener("DOMContentLoaded", function() {
    const faqItems = document.querySelectorAll('.faq-list li');

    faqItems.forEach(item => {
        item.addEventListener('click', () => {
            const answer = item.querySelector('.answer');
            if (answer.classList.contains('show')) {
                answer.style.maxHeight = null;
                answer.classList.remove('show');
            } else {
                answer.style.maxHeight = answer.scrollHeight + 'px';
                answer.classList.add('show');
            }
        });
    });

   
});