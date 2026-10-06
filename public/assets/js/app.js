/**
 * ELMS - Core Client JavaScript Engine
 * Vanilla JS, Fetch API, CSRF Injection & Micro-interactions
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Mobile Sidebar Toggle
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const sidebar = document.getElementById('sidebar');
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('show');
        });
    }

    // 2. CSRF Token Helper
    const getCsrfToken = () => {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    };

    // 3. Reusable Toast Notification Generator
    window.showToast = (message, type = 'info') => {
        let container = document.getElementById('toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container';
            document.body.appendChild(container);
        }

        const bgClass = type === 'success' ? 'bg-success text-white' :
                        type === 'error' ? 'bg-danger text-white' :
                        type === 'warning' ? 'bg-warning text-dark' : 'bg-primary text-white';

        const toastEl = document.createElement('div');
        toastEl.className = `toast align-items-center ${bgClass} border-0 show mb-2 shadow`;
        toastEl.setAttribute('role', 'alert');
        toastEl.innerHTML = `
            <div class="d-flex">
                <div class="toast-body font-monospace">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;

        container.appendChild(toastEl);
        setTimeout(() => {
            toastEl.remove();
        }, 4000);
    };

    // 4. Dynamic Leave Days Calculation in Apply Form
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    const daysPreview = document.getElementById('calculated_days_preview');

    function updateCalculatedDays() {
        if (!startDateInput || !endDateInput || !daysPreview) return;

        const sVal = startDateInput.value;
        const eVal = endDateInput.value;

        if (sVal && eVal) {
            const start = new Date(sVal);
            const end = new Date(eVal);

            if (end < start) {
                daysPreview.innerHTML = '<span class="text-danger"><i class="bi bi-exclamation-triangle"></i> End date cannot be prior to start date</span>';
                return;
            }

            const diffTime = Math.abs(end - start);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
            daysPreview.innerHTML = `<span class="badge bg-primary fs-6">${diffDays} Day(s) requested</span>`;
        } else {
            daysPreview.innerHTML = '<span class="text-muted">Select start and end dates to calculate duration</span>';
        }
    }

    if (startDateInput && endDateInput) {
        startDateInput.addEventListener('change', updateCalculatedDays);
        endDateInput.addEventListener('change', updateCalculatedDays);
    }

    // 5. AJAX Leave Approval & Rejection Handler
    document.querySelectorAll('.ajax-leave-action').forEach(btn => {
        btn.addEventListener('click', async (e) => {
            const action = btn.dataset.action; // 'approve' or 'reject'
            const requestId = btn.dataset.id;
            let rejectionReason = '';

            if (action === 'reject') {
                rejectionReason = prompt('Please specify the reason for rejecting this leave request:');
                if (rejectionReason === null) return; // User cancelled
                if (rejectionReason.trim() === '') {
                    alert('Rejection reason cannot be blank.');
                    return;
                }
            } else {
                if (!confirm(`Are you sure you want to approve leave request #${requestId}?`)) {
                    return;
                }
            }

            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

            try {
                const endpoint = action === 'approve' 
                    ? `/admin/leaves/${requestId}/approve` 
                    : `/admin/leaves/${requestId}/reject`;

                const formData = new FormData();
                formData.append('_csrf_token', getCsrfToken());
                if (action === 'reject') {
                    formData.append('rejection_reason', rejectionReason);
                }

                const response = await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': getCsrfToken(),
                    },
                    body: formData,
                });

                const data = await response.json();

                if (data.success) {
                    window.showToast(data.message, 'success');
                    // Update table row badge smoothly
                    const row = document.getElementById(`leave-row-${requestId}`);
                    if (row) {
                        const statusCell = row.querySelector('.leave-status-cell');
                        if (statusCell) {
                            statusCell.innerHTML = action === 'approve'
                                ? '<span class="badge bg-success px-2.5 py-1.5">Approved</span>'
                                : '<span class="badge bg-danger px-2.5 py-1.5">Rejected</span>';
                        }
                        const actionCell = row.querySelector('.leave-actions-cell');
                        if (actionCell) {
                            actionCell.innerHTML = '<span class="badge bg-secondary-subtle text-secondary py-1 px-2">Reviewed</span>';
                        }
                    } else {
                        setTimeout(() => location.reload(), 800);
                    }
                } else {
                    window.showToast(data.message || 'Action failed', 'error');
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                }
            } catch (err) {
                console.error(err);
                window.showToast('Network error during request processing', 'error');
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        });
    });

    // 6. AJAX Notification Read Handler
    document.querySelectorAll('.ajax-mark-notif-read').forEach(btn => {
        btn.addEventListener('click', async (e) => {
            e.preventDefault();
            const notifId = btn.dataset.id;

            try {
                const formData = new FormData();
                formData.append('_csrf_token', getCsrfToken());

                const response = await fetch(`/notifications/${notifId}/read`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': getCsrfToken(),
                    },
                    body: formData,
                });

                const data = await response.json();
                if (data.success) {
                    const item = document.getElementById(`notif-item-${notifId}`);
                    if (item) {
                        item.classList.remove('bg-light', 'fw-semibold');
                        item.classList.add('opacity-75');
                        btn.remove();
                    }
                    updateUnreadCounter();
                }
            } catch (err) {
                console.error(err);
            }
        });
    });

    // 7. Polling / Fetching Unread Notification Counter
    async function updateUnreadCounter() {
        try {
            const res = await fetch('/notifications/unread-count', {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();
            const counter = document.getElementById('unread-notif-counter');
            if (counter) {
                if (data.count > 0) {
                    counter.textContent = data.count;
                    counter.style.display = 'inline-block';
                } else {
                    counter.style.display = 'none';
                }
            }
        } catch (e) {
            // Silently ignore polling errors
        }
    }

    if (document.getElementById('unread-notif-counter')) {
        updateUnreadCounter();
    }
});
