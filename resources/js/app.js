import '../css/app.css';
import './bootstrap';
import Alpine from 'alpinejs';
import { ModelManager } from './utils/modelManager';
import { ActivityFeedService } from './services/ActivityFeedService';

window.Alpine = Alpine;
Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    // Initialize model manager if we're on a page that needs it
    if (document.getElementById('model-categories')) {
        const modelManager = new ModelManager();
        modelManager.initialize();
    }

    // Initialize activity feed if we're on a page that needs it
    if (document.getElementById('activity-feed')) {
        const activityFeedService = new ActivityFeedService();
        activityFeedService.initialize();
    }

    // Initialize notifications
    setupNotifications();
});

function setupNotifications() {
    const markAllReadButton = document.getElementById('mark-all-read');
    if (markAllReadButton) {
        markAllReadButton.addEventListener('click', async () => {
            try {
                await fetch('/api/notifications/mark-all-read', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                // Update UI to reflect all notifications being read
                document.querySelectorAll('.notification-unread').forEach(el => {
                    el.classList.remove('notification-unread');
                });

                // Update notification badge
                const badge = document.querySelector('.notification-badge');
                if (badge) {
                    badge.classList.add('hidden');
                }
            } catch (error) {
                console.error('Error marking notifications as read:', error);
            }
        });
    }

    // Handle keyboard navigation for dropdowns
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            // Close all dropdowns when Escape is pressed
            document.querySelectorAll('[x-data]').forEach(dropdown => {
                if (dropdown.__x) {
                    dropdown.__x.$data.open = false;
                }
            });
        }
    });

    // Add focus trap to dropdowns
    document.querySelectorAll('[x-data]').forEach(dropdown => {
        if (dropdown.__x) {
            dropdown.addEventListener('keydown', (e) => {
                if (e.key === 'Tab') {
                    const focusableElements = dropdown.querySelectorAll(
                        'a[href], button, input, textarea, select, details, [tabindex]:not([tabindex="-1"])'
                    );
                    const firstFocusable = focusableElements[0];
                    const lastFocusable = focusableElements[focusableElements.length - 1];

                    if (e.shiftKey) {
                        if (document.activeElement === firstFocusable) {
                            lastFocusable.focus();
                            e.preventDefault();
                        }
                    } else {
                        if (document.activeElement === lastFocusable) {
                            firstFocusable.focus();
                            e.preventDefault();
                        }
                    }
                }
            });
        }
    });
}
