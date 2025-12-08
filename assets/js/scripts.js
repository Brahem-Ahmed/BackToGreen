/*!
* Start Bootstrap - Small Business v5.0.6 (https://startbootstrap.com/template/small-business)
* Copyright 2013-2023 Start Bootstrap
* Licensed under MIT (https://github.com/StartBootstrap/startbootstrap-small-business/blob/master/LICENSE)
*/

// BacktoGreen Custom JavaScript
document.addEventListener('DOMContentLoaded', function() {
    
    // ========================================
    // USER FORM VALIDATION
    // ========================================
    
    // Initialize user form validation if present
    const userForm = document.querySelector('form[name="user"]');
    if (userForm) {
        initUserFormValidation();
    }
    
    function initUserFormValidation() {
        const form = document.querySelector('form[name="user"]');
        
        // Password visibility toggle
        initPasswordToggle();
        
        // Password strength indicator
        initPasswordStrength();
        
        // Real-time validation
        initRealTimeValidation();
        
        // Form submission validation
        initFormSubmissionValidation();
    }
    
    function initPasswordToggle() {
        const togglePassword = document.getElementById('togglePassword');
        const passwordField = document.querySelector('input[type="password"]');
        const eyeIcon = document.getElementById('eyeIcon');
        
        if (togglePassword && passwordField && eyeIcon) {
            togglePassword.addEventListener('click', function() {
                const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordField.setAttribute('type', type);
                
                if (type === 'password') {
                    eyeIcon.classList.remove('fa-eye-slash');
                    eyeIcon.classList.add('fa-eye');
                } else {
                    eyeIcon.classList.remove('fa-eye');
                    eyeIcon.classList.add('fa-eye-slash');
                }
            });
        }
    }
    
    function initPasswordStrength() {
        const passwordField = document.querySelector('input[type="password"]');
        const passwordStrength = document.getElementById('passwordStrength');
        
        if (passwordField && passwordStrength) {
            passwordField.addEventListener('input', function() {
                const password = this.value;
                let strength = 0;
                let messages = [];
                
                if (password.length >= 8) strength++;
                else messages.push('At least 8 characters');
                
                if (/[a-z]/.test(password)) strength++;
                else messages.push('Lowercase letter');
                
                if (/[A-Z]/.test(password)) strength++;
                else messages.push('Uppercase letter');
                
                if (/\d/.test(password)) strength++;
                else messages.push('Number');
                
                if (/[^A-Za-z0-9]/.test(password)) strength++;
                else messages.push('Special character');
                
                let strengthText = '';
                let strengthClass = '';
                
                switch(strength) {
                    case 0:
                    case 1:
                        strengthText = 'Very Weak';
                        strengthClass = 'text-danger';
                        break;
                    case 2:
                        strengthText = 'Weak';
                        strengthClass = 'text-warning';
                        break;
                    case 3:
                        strengthText = 'Fair';
                        strengthClass = 'text-info';
                        break;
                    case 4:
                        strengthText = 'Strong';
                        strengthClass = 'text-success';
                        break;
                    case 5:
                        strengthText = 'Very Strong';
                        strengthClass = 'text-success fw-bold';
                        break;
                }
                
                if (password.length > 0) {
                    passwordStrength.innerHTML = `
                        <small class="${strengthClass}">Password Strength: ${strengthText}</small>
                        ${messages.length > 0 ? '<br><small class="text-muted">Missing: ' + messages.join(', ') + '</small>' : ''}
                    `;
                } else {
                    passwordStrength.innerHTML = '';
                }
            });
        }
    }
    
    function initRealTimeValidation() {
        const form = document.querySelector('form[name="user"]');
        if (!form) return;
        
        // Email validation
        const emailField = form.querySelector('input[type="email"]');
        if (emailField) {
            emailField.addEventListener('blur', function() {
                const email = this.value;
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                
                if (email && !emailRegex.test(email)) {
                    this.classList.add('is-invalid');
                    this.classList.remove('is-valid');
                } else if (email) {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                } else {
                    this.classList.remove('is-invalid', 'is-valid');
                }
            });
        }
        
        // Required field validation
        const requiredFields = form.querySelectorAll('input[required], select[required]');
        requiredFields.forEach(function(field) {
            field.addEventListener('blur', function() {
                validateField(this);
            });
            
            // Also validate on input for better UX
            field.addEventListener('input', function() {
                if (this.classList.contains('is-invalid')) {
                    validateField(this);
                }
            });
        });
        
        // Name field validation (letters only)
        const nameFields = form.querySelectorAll('input[name*="nom"], input[name*="prenom"]');
        nameFields.forEach(function(field) {
            field.addEventListener('input', function() {
                const nameRegex = /^[A-Za-zÀ-ÿ\s'-]+$/;
                if (this.value && !nameRegex.test(this.value)) {
                    this.classList.add('is-invalid');
                    this.classList.remove('is-valid');
                } else if (this.value && this.value.length >= 2) {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                }
            });
        });
        
        // Phone field validation (numbers only)
        const phoneField = form.querySelector('input[name*="telephone"]');
        if (phoneField) {
            phoneField.addEventListener('input', function() {
                const phoneRegex = /^\d+$/;
                if (this.value && !phoneRegex.test(this.value)) {
                    this.classList.add('is-invalid');
                    this.classList.remove('is-valid');
                } else if (this.value && this.value.length >= 8 && this.value.length <= 20) {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                }
            });
        }
    }
    
    function validateField(field) {
        const value = field.value.trim();
        let isValid = true;
        
        // Required field check
        if (field.hasAttribute('required') && !value) {
            isValid = false;
        }
        
        // Email specific validation
        if (field.type === 'email' && value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
                isValid = false;
            }
        }
        
        // Length validation
        const minLength = field.getAttribute('minlength');
        const maxLength = field.getAttribute('maxlength');
        
        if (minLength && value.length < parseInt(minLength)) {
            isValid = false;
        }
        
        if (maxLength && value.length > parseInt(maxLength)) {
            isValid = false;
        }
        
        // Pattern validation
        const pattern = field.getAttribute('pattern');
        if (pattern && value) {
            const regex = new RegExp(pattern);
            if (!regex.test(value)) {
                isValid = false;
            }
        }
        
        // Apply validation classes
        if (isValid) {
            field.classList.remove('is-invalid');
            field.classList.add('is-valid');
        } else {
            field.classList.add('is-invalid');
            field.classList.remove('is-valid');
        }
        
        return isValid;
    }
    
    function initFormSubmissionValidation() {
        const form = document.querySelector('form[name="user"]');
        if (!form) return;
        
        form.addEventListener('submit', function(e) {
            let isFormValid = true;
            const inputs = form.querySelectorAll('input[required], select[required]');
            
            inputs.forEach(function(input) {
                const fieldValid = validateField(input);
                if (!fieldValid) {
                    isFormValid = false;
                }
            });
            
            if (!isFormValid) {
                e.preventDefault();
                
                // Scroll to first invalid field
                const firstInvalid = form.querySelector('.is-invalid');
                if (firstInvalid) {
                    firstInvalid.scrollIntoView({ 
                        behavior: 'smooth', 
                        block: 'center' 
                    });
                    
                    // Focus after scroll animation
                    setTimeout(() => {
                        firstInvalid.focus();
                    }, 500);
                }
                
                // Show validation alert
                showValidationAlert();
            }
        });
    }
    
    function showValidationAlert() {
        // Remove existing alert if present
        const existingAlert = document.querySelector('.validation-alert');
        if (existingAlert) {
            existingAlert.remove();
        }
        
        // Create new alert
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-danger alert-dismissible fade show validation-alert';
        alertDiv.innerHTML = `
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Form Validation Error:</strong> Please correct the highlighted fields before submitting.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        // Insert at top of form
        const form = document.querySelector('form[name="user"]');
        if (form) {
            form.insertBefore(alertDiv, form.firstChild);
        }
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (alertDiv && alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }
    
    // ========================================
    // GENERAL UTILITIES
    // ========================================
    
    // Auto-dismiss alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            if (alert && alert.parentNode) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        }, 5000);
    });
    
    // Smooth scrolling for anchor links
    const anchorLinks = document.querySelectorAll('a[href^="#"]');
    anchorLinks.forEach(function(link) {
        link.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);
            
            if (targetElement) {
                e.preventDefault();
                targetElement.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });
    
});