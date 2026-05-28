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
     * Displays a dynamic Bootstrap 5 Toast alert.
     */
    showToast(message, type = 'success', duration = 4000) {
        // Ensure container exists
        let container = document.getElementById('toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container';
            container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
            container.style.zIndex = '1090';
            document.body.appendChild(container);
        }

        const toastId = 'toast_' + Date.now();
        const icon = type === 'success' 
            ? '<i class="bi bi-check-circle-fill text-success me-2"></i>' 
            : '<i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>';
            
        const borderClass = type === 'success' ? 'border-success' : 'border-danger';

        const toastHTML = `
            <div id="${toastId}" class="toast align-items-center bg-white border-0 border-start border-4 ${borderClass} shadow" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body d-flex align-items-center">
                        ${icon}
                        <div>${message}</div>
                    </div>
                    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;

        container.insertAdjacentHTML('beforeend', toastHTML);
        const toastEl = document.getElementById(toastId);
        
        if (typeof bootstrap !== 'undefined') {
            const toast = new bootstrap.Toast(toastEl, { delay: duration });
            toast.show();
            
            // Remove from DOM after hidden
            toastEl.addEventListener('hidden.bs.toast', function() {
                toastEl.remove();
            });
        } else {
            // Fallback if bootstrap JS is not loaded
            toastEl.classList.add('show');
            setTimeout(() => {
                toastEl.classList.remove('show');
                setTimeout(() => toastEl.remove(), 500);
            }, duration);
        }
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
    }
};
