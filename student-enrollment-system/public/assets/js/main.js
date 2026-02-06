/**
 * Main JavaScript File
 * Student and Enrollment Management System
 */

(function() {
    'use strict';

    // ========================================================================
    // GLOBAL VARIABLES
    // ========================================================================
    
    const App = {
        baseUrl: window.location.origin + '/student-enrollment-system/public/',
        csrfToken: document.querySelector('meta[name="csrf-token"]')?.content || '',
        
        // Initialize application
        init: function() {
            this.setupEventListeners();
            this.initializeComponents();
            this.handleFlashMessages();
        },
        
        // Setup global event listeners
        setupEventListeners: function() {
            // Sidebar toggle for mobile
            const sidebarToggle = document.querySelector('.sidebar-toggle');
            const sidebar = document.querySelector('.sidebar');
            
            if (sidebarToggle && sidebar) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('active');
                });
            }
            
            // Alert dismiss buttons
            document.querySelectorAll('.alert-close').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    this.closest('.alert').remove();
                });
            });
            
            // Modal close buttons
            document.querySelectorAll('.modal-close, [data-dismiss="modal"]').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    this.closest('.modal').classList.remove('show');
                });
            });
            
            // Form confirmation
            document.querySelectorAll('form[data-confirm]').forEach(function(form) {
                form.addEventListener('submit', function(e) {
                    const message = this.getAttribute('data-confirm');
                    if (!confirm(message)) {
                        e.preventDefault();
                    }
                });
            });
            
            // Delete confirmation
            document.querySelectorAll('[data-confirm-delete]').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    if (!confirm('Are you sure you want to delete this item?')) {
                        e.preventDefault();
                    }
                });
            });
        },
        
        // Initialize components
        initializeComponents: function() {
            this.initDataTables();
            this.initDatePickers();
            this.initFileInputs();
            this.initTooltips();
        },
        
        // Initialize DataTables
        initDataTables: function() {
            if (typeof DataTable !== 'undefined') {
                document.querySelectorAll('.data-table').forEach(function(table) {
                    new DataTable(table, {
                        pageLength: 20,
                        responsive: true,
                        language: {
                            search: 'Search:',
                            lengthMenu: 'Show _MENU_ entries',
                            info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                            paginate: {
                                first: 'First',
                                last: 'Last',
                                next: 'Next',
                                previous: 'Previous'
                            }
                        }
                    });
                });
            }
        },
        
        // Initialize date pickers
        initDatePickers: function() {
            document.querySelectorAll('input[type="date"]').forEach(function(input) {
                // Add custom date picker styling
                input.classList.add('date-picker');
            });
        },
        
        // Initialize file inputs
        initFileInputs: function() {
            document.querySelectorAll('input[type="file"]').forEach(function(input) {
                input.addEventListener('change', function() {
                    const fileName = this.files[0]?.name || 'No file chosen';
                    const label = this.nextElementSibling;
                    if (label && label.classList.contains('file-label')) {
                        label.textContent = fileName;
                    }
                });
            });
        },
        
        // Initialize tooltips
        initTooltips: function() {
            document.querySelectorAll('[data-tooltip]').forEach(function(element) {
                element.addEventListener('mouseenter', function() {
                    const tooltip = document.createElement('div');
                    tooltip.className = 'tooltip';
                    tooltip.textContent = this.getAttribute('data-tooltip');
                    document.body.appendChild(tooltip);
                    
                    const rect = this.getBoundingClientRect();
                    tooltip.style.top = (rect.top - tooltip.offsetHeight - 5) + 'px';
                    tooltip.style.left = (rect.left + rect.width / 2 - tooltip.offsetWidth / 2) + 'px';
                });
                
                element.addEventListener('mouseleave', function() {
                    document.querySelectorAll('.tooltip').forEach(t => t.remove());
                });
            });
        },
        
        // Handle flash messages
        handleFlashMessages: function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                // Auto-dismiss after 5 seconds
                setTimeout(function() {
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 300);
                }, 5000);
            });
        }
    };
    
    // ========================================================================
    // AJAX HELPER
    // ========================================================================
    
    App.ajax = function(url, options = {}) {
        const defaults = {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.csrfToken
            },
            credentials: 'same-origin'
        };
        
        const config = { ...defaults, ...options };
        
        if (config.method !== 'GET' && config.body && typeof config.body === 'object') {
            config.body = JSON.stringify(config.body);
        }
        
        return fetch(url, config)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .catch(error => {
                console.error('AJAX Error:', error);
                this.showNotification('An error occurred. Please try again.', 'error');
                throw error;
            });
    };
    
    // ========================================================================
    // NOTIFICATION SYSTEM
    // ========================================================================
    
    App.showNotification = function(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} alert-dismissible`;
        notification.innerHTML = `
            ${message}
            <button type="button" class="alert-close">&times;</button>
        `;
        
        const container = document.querySelector('.main-content') || document.body;
        container.insertBefore(notification, container.firstChild);
        
        // Add close functionality
        notification.querySelector('.alert-close').addEventListener('click', function() {
            notification.remove();
        });
        
        // Auto-dismiss
        setTimeout(() => {
            notification.style.opacity = '0';
            setTimeout(() => notification.remove(), 300);
        }, 5000);
    };
    
    // ========================================================================
    // MODAL HELPER
    // ========================================================================
    
    App.showModal = function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('show');
        }
    };
    
    App.hideModal = function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('show');
        }
    };
    
    // ========================================================================
    // FORM VALIDATION
    // ========================================================================
    
    App.validateForm = function(formElement) {
        let isValid = true;
        
        // Clear previous errors
        formElement.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
        formElement.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        
        // Check required fields
        formElement.querySelectorAll('[required]').forEach(function(field) {
            if (!field.value.trim()) {
                isValid = false;
                field.classList.add('is-invalid');
                
                const error = document.createElement('div');
                error.className = 'invalid-feedback';
                error.textContent = 'This field is required';
                field.parentNode.appendChild(error);
            }
        });
        
        // Email validation
        formElement.querySelectorAll('input[type="email"]').forEach(function(field) {
            if (field.value && !App.isValidEmail(field.value)) {
                isValid = false;
                field.classList.add('is-invalid');
                
                const error = document.createElement('div');
                error.className = 'invalid-feedback';
                error.textContent = 'Please enter a valid email address';
                field.parentNode.appendChild(error);
            }
        });
        
        return isValid;
    };
    
    App.isValidEmail = function(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    };
    
    // ========================================================================
    // UTILITY FUNCTIONS
    // ========================================================================
    
    App.formatCurrency = function(amount) {
        return '₱' + parseFloat(amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
    };
    
    App.formatDate = function(dateString) {
        const options = { year: 'numeric', month: 'long', day: 'numeric' };
        return new Date(dateString).toLocaleDateString('en-US', options);
    };
    
    App.debounce = function(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    };
    
    // ========================================================================
    // LOADING INDICATOR
    // ========================================================================
    
    App.showLoading = function() {
        const loader = document.createElement('div');
        loader.id = 'loading-overlay';
        loader.innerHTML = `
            <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; 
                        background: rgba(0,0,0,0.5); display: flex; align-items: center; 
                        justify-content: center; z-index: 9999;">
                <div style="background: white; padding: 2rem; border-radius: 8px;">
                    <div class="spinner"></div>
                    <p style="margin-top: 1rem; text-align: center;">Loading...</p>
                </div>
            </div>
        `;
        document.body.appendChild(loader);
    };
    
    App.hideLoading = function() {
        const loader = document.getElementById('loading-overlay');
        if (loader) {
            loader.remove();
        }
    };
    
    // ========================================================================
    // EXPORT TO GLOBAL SCOPE
    // ========================================================================
    
    window.App = App;
    
    // ========================================================================
    // INITIALIZE ON DOM READY
    // ========================================================================
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            App.init();
        });
    } else {
        App.init();
    }
    
})();

// ========================================================================
// ADDITIONAL MODULE: FORM HELPERS
// ========================================================================

const FormHelper = {
    // Auto-format student number input
    formatStudentNumber: function(input) {
        input.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 4) {
                value = value.substring(0, 4) + '-' + value.substring(4, 9);
            }
            e.target.value = value;
        });
    },
    
    // Auto-format phone number
    formatPhoneNumber: function(input) {
        input.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            e.target.value = value.substring(0, 11);
        });
    },
    
    // Calculate GPA
    calculateGPA: function(grades) {
        if (grades.length === 0) return 0;
        const sum = grades.reduce((a, b) => a + b, 0);
        return (sum / grades.length).toFixed(2);
    }
};

window.FormHelper = FormHelper;

// ========================================================================
// ADDITIONAL MODULE: ENROLLMENT HELPER
// ========================================================================

const EnrollmentHelper = {
    // Calculate total units
    calculateTotalUnits: function(subjects) {
        return subjects.reduce((total, subject) => {
            return total + parseFloat(subject.units || 0);
        }, 0);
    },
    
    // Check if units exceed maximum
    checkUnitLimit: function(totalUnits, maxUnits = 24) {
        return totalUnits > maxUnits;
    },
    
    // Check prerequisites
    checkPrerequisites: function(subjectId, completedSubjects) {
        // Implementation depends on your prerequisite data structure
        return true; // Placeholder
    }
};

window.EnrollmentHelper = EnrollmentHelper;