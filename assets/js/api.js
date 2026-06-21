/**
 * SDO FAST API Wrapper and Global JS Utilities.
 * Handles Fetch requests, CSRF injection, API loading spinners, and Toast notifications.
 */

const API = {
    /**
     * Centralized fetch wrapper that automatically handles JSON parsing, CSRF headers, and errors.
     */
    async request(url, options = {}) {
        // Default headers
        options.headers = options.headers || {};
        
        // Add CSRF Token for state-modifying requests
        const method = (options.method || 'GET').toUpperCase();
        if (['POST', 'PUT', 'DELETE', 'PATCH'].includes(method)) {
            const csrfToken = this.getCSRFToken();
            if (csrfToken) {
                options.headers['X-CSRF-Token'] = csrfToken;
            }
        }

        // Show spinner / loading state if requested
        if (options.showLoader) {
            this.showSpinner();
        }

        try {
            const response = await fetch(url, options);
            
            // Check for authentication redirects
            if (response.status === 401 || response.status === 403) {
                const data = await response.json().catch(() => ({}));
                this.showToast(data.message || 'Unauthorized action.', 'danger');
                if (data.redirect) {
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 1500);
                }
                return data;
            }

            const data = await response.json();
            
            if (options.showLoader) {
                this.hideSpinner();
            }

            return data;
        } catch (error) {
            console.error('API Request Failure:', error);
            if (options.showLoader) {
                this.hideSpinner();
            }
            this.showToast('Network error occurred. Please try again.', 'danger');
            return { success: false, message: 'Network or system error.' };
        }
    },

    /**
     * Reads CSRF token from the global metadata tag.
     */
    getCSRFToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : null;
    },

    /**
     * Displays a centered popup notification modal.
     */
    showToast(message, type = 'success', duration = 4000) {
        const iconMap = {
            success: 'success',
            danger: 'error',
            error: 'error',
            warning: 'warning',
            info: 'info',
        };

        const titleMap = {
            success: 'Success',
            danger: 'Error',
            error: 'Error',
            warning: 'Notice',
            info: 'Information',
        };

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: titleMap[type] || 'Notification',
                text: message,
                icon: iconMap[type] || 'info',
                position: 'center',
                showConfirmButton: true,
                confirmButtonText: 'OK',
                confirmButtonColor: '#1b4a9a',
                timer: duration > 0 ? duration : undefined,
                timerProgressBar: duration > 0,
                customClass: {
                    popup: 'fast-notification-popup',
                    confirmButton: 'fast-notification-confirm',
                },
            });
            return;
        }

        // Fallback if SweetAlert2 is not loaded
        alert(message);
    },

    /**
     * Global spinner helpers
     */
    showSpinner() {
        let spinner = document.getElementById('api-loading-spinner');
        if (!spinner) {
            spinner = document.createElement('div');
            spinner.id = 'api-loading-spinner';
            spinner.style.position = 'fixed';
            spinner.style.top = '0';
            spinner.style.left = '0';
            spinner.style.width = '100vw';
            spinner.style.height = '100vh';
            spinner.style.backgroundColor = 'rgba(255, 255, 255, 0.6)';
            spinner.style.display = 'flex';
            spinner.style.alignItems = 'center';
            spinner.style.justifyContent = 'center';
            spinner.style.zIndex = '2000';
            spinner.innerHTML = `
                <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            `;
            document.body.appendChild(spinner);
        }
        spinner.style.display = 'flex';
    },

    hideSpinner() {
        const spinner = document.getElementById('api-loading-spinner');
        if (spinner) {
            spinner.style.display = 'none';
        }
    },

    /**
     * Global Confirmation Dialog using SweetAlert2
     */
    async confirmAction(title = 'Are you sure?', text = 'This action cannot be undone.', confirmButtonText = 'Yes, proceed', type = 'warning') {
        if (typeof Swal !== 'undefined') {
            const result = await Swal.fire({
                title: title,
                text: text,
                icon: type,
                position: 'center',
                showCancelButton: true,
                confirmButtonColor: '#1b4a9a',
                cancelButtonColor: '#6c757d',
                confirmButtonText: confirmButtonText,
                cancelButtonText: 'Cancel',
                reverseButtons: true,
                focusCancel: false,
                customClass: {
                    popup: 'fast-notification-popup',
                    confirmButton: 'fast-notification-confirm',
                    cancelButton: 'fast-notification-cancel',
                },
            });
            return result.isConfirmed;
        }

        // Fallback to native confirm if SweetAlert2 fails to load
        return window.confirm(`${title}\n\n${text}`);
    }
};
