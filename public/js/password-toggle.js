/**
 * Password Toggle Functionality
 * Adds eye icon to toggle password visibility
 */

document.addEventListener('DOMContentLoaded', function() {
    // Find all password input fields
    const passwordFields = document.querySelectorAll('input[type="password"]');

    passwordFields.forEach(function(passwordInput) {
        const inputGroup = passwordInput.closest('.input-group');

        if (!inputGroup) return;

        // Check if toggle already exists
        if (inputGroup.querySelector('.password-toggle')) return;

        // Keep the lock icon visible and move it further right
        const existingIcon = inputGroup.querySelector('.input-icon');
        if (existingIcon) {
            existingIcon.style.right = '15px'; // Lock icon on the far right
        }

        // Create toggle button (eye icon to the left of lock icon)
        const toggleButton = document.createElement('button');
        toggleButton.type = 'button';
        toggleButton.className = 'password-toggle';
        toggleButton.setAttribute('aria-label', 'Toggle password visibility');
        toggleButton.innerHTML = '<i class="fas fa-eye"></i>';

        // Add styles - eye icon to the left of lock icon
        toggleButton.style.cssText = `
            position: absolute;
            right: ${existingIcon ? '45px' : '12px'};
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #64748b;
            font-size: 18px;
            padding: 4px;
            transition: color 0.2s ease;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: center;
        `;

        // Hover effect - simple color change to blue
        toggleButton.addEventListener('mouseenter', function() {
            this.style.color = '#6366f1';
        });

        toggleButton.addEventListener('mouseleave', function() {
            this.style.color = '#64748b';
        });

        // Toggle functionality
        toggleButton.addEventListener('click', function() {
            const icon = this.querySelector('i');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
                this.setAttribute('aria-label', 'Hide password');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
                this.setAttribute('aria-label', 'Show password');
            }
        });

        // Adjust password input padding to make room for icon(s) on the right
        if (existingIcon) {
            passwordInput.style.paddingRight = '80px'; // Space for both eye and lock icons
        } else {
            passwordInput.style.paddingRight = '45px'; // Space for eye icon only
        }

        // Append toggle button to input group
        inputGroup.style.position = 'relative';
        inputGroup.appendChild(toggleButton);
    });
});
