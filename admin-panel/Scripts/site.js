/* site.js - Custom Scripting for Government Scholarship Portal Admin Panel */

$(document).ready(function () {
    // 1. Sidebar Toggle for Mobile Devices
    $('#sidebar-toggle').on('click', function () {
        $('#sidebar').toggleClass('show-sidebar');
    });

    // Close sidebar when clicking outside on mobile
    $(document).on('click', function (e) {
        if ($(window).width() < 992) {
            var sidebar = $('#sidebar');
            var toggleBtn = $('#sidebar-toggle');
            if (!sidebar.is(e.target) && sidebar.has(e.target).length === 0 &&
                !toggleBtn.is(e.target) && toggleBtn.has(e.target).length === 0) {
                sidebar.removeClass('show-sidebar');
            }
        }
    });

    // 2. Scroll to Top Button
    $(window).scroll(function () {
        if ($(this).scrollTop() > 300) {
            $('#scroll-top').addClass('show');
        } else {
            $('#scroll-top').removeClass('show');
        }
    });

    $('#scroll-top').click(function () {
        $('html, body').animate({ scrollTop: 0 }, 'smooth');
        return false;
    });

    // 3. Loading Spinner on page navigation/form submissions
    // Show spinner when submitting forms (except AJAX)
    $('form').not('.ajax-form').on('submit', function () {
        showLoader();
    });

    // Show spinner for standard link navigation inside portal (excluding hash/modal triggers)
    $('.sidebar-menu a, .navbar-brand, .logout-link').on('click', function (e) {
        var href = $(this).attr('href');
        if (href && href !== '#' && !href.startsWith('javascript:')) {
            showLoader();
        }
    });

    // Auto-dismiss Alerts
    setTimeout(function () {
        $(".alert-dismissible").fadeTo(500, 0).slideUp(500, function () {
            $(this).remove();
        });
    }, 4000);

    // 4. Student Profile View Details AJAX Modal Load
    $('.view-student-profile').on('click', function () {
        var studentId = $(this).data('id');
        if (!studentId) return;

        // Show a brief loading state in modal body or show loader
        showLoader();

        $.ajax({
            url: '/Admin/GetStudentDetails/' + studentId,
            type: 'GET',
            dataType: 'json',
            success: function (data) {
                hideLoader();
                if (data) {
                    $('#profile-name').text(data.Name);
                    $('#profile-email').text(data.Email);
                    $('#profile-course').text(data.Course);
                    $('#profile-category').text(data.Category);
                    $('#profile-aadhaar').text(data.AadhaarNumber || 'N/A');
                    $('#profile-institution').text(data.Institution || 'N/A');
                    $('#profile-gpa').text(data.Gpa.toFixed(2));

                    var statusBadge = $('#profile-status');
                    statusBadge.removeClass().addClass('badge-status');
                    if (data.VerificationStatus === 'Verified') {
                        statusBadge.addClass('bg-approved').html('<i class="fas fa-check-circle"></i> Verified');
                    } else if (data.VerificationStatus === 'Pending') {
                        statusBadge.addClass('bg-pending').html('<i class="fas fa-clock"></i> Pending');
                    } else {
                        statusBadge.addClass('bg-rejected').html('<i class="fas fa-times-circle"></i> Rejected');
                    }

                    // Open the modal
                    var profileModal = new bootstrap.Modal(document.getElementById('studentProfileModal'));
                    profileModal.show();
                } else {
                    alert('Failed to retrieve student profile. Please try again.');
                }
            },
            error: function () {
                hideLoader();
                alert('Connection error occurred while loading profile.');
            }
        });
    });

    // 5. AJAX Application Approval
    $('.approve-application-btn').on('click', function () {
        var btn = $(this);
        var appId = btn.data('id');
        if (!confirm('Are you sure you want to APPROVE this scholarship application?')) return;

        showLoader();

        $.ajax({
            url: '/Admin/ApproveApplication/' + appId,
            type: 'POST',
            success: function (res) {
                hideLoader();
                if (res.success) {
                    // Update status column in UI
                    var row = $('#application-row-' + appId);
                    row.find('.status-cell').html('<span class="badge-status bg-approved"><i class="fas fa-check-circle"></i> Approved</span>');
                    // Hide/remove action buttons
                    row.find('.action-cell').html('<span class="text-success fw-bold"><i class="fas fa-check"></i> Processed</span>');
                    showToast('success', res.message);
                } else {
                    showToast('error', res.message);
                }
            },
            error: function () {
                hideLoader();
                showToast('error', 'Failed to approve application.');
            }
        });
    });

    // 6. AJAX Application Rejection
    $('.reject-application-btn').on('click', function () {
        var btn = $(this);
        var appId = btn.data('id');
        if (!confirm('Are you sure you want to REJECT this scholarship application?')) return;

        showLoader();

        $.ajax({
            url: '/Admin/RejectApplication/' + appId,
            type: 'POST',
            success: function (res) {
                hideLoader();
                if (res.success) {
                    var row = $('#application-row-' + appId);
                    row.find('.status-cell').html('<span class="badge-status bg-rejected"><i class="fas fa-times-circle"></i> Rejected</span>');
                    row.find('.action-cell').html('<span class="text-danger fw-bold"><i class="fas fa-times"></i> Rejected</span>');
                    showToast('success', res.message);
                } else {
                    showToast('error', res.message);
                }
            },
            error: function () {
                hideLoader();
                showToast('error', 'Failed to reject application.');
            }
        });
    });

    // 7. Toggle Announcement status
    $('.toggle-announcement-status').on('click', function () {
        var btn = $(this);
        var announcementId = btn.data('id');

        $.ajax({
            url: '/Admin/ToggleAnnouncement/' + announcementId,
            type: 'POST',
            success: function (res) {
                if (res.success) {
                    var badge = $('#announcement-badge-' + announcementId);
                    if (res.isActive) {
                        badge.removeClass('bg-rejected').addClass('bg-approved').text('Active');
                        btn.removeClass('btn-outline-success').addClass('btn-outline-warning').text('Deactivate');
                    } else {
                        badge.removeClass('bg-approved').addClass('bg-rejected').text('Inactive');
                        btn.removeClass('btn-outline-warning').addClass('btn-outline-success').text('Activate');
                    }
                    showToast('success', 'Announcement status updated.');
                }
            }
        });
    });

    // Helper functions for Loader Overlay
    function showLoader() {
        $('#loader-overlay').addClass('show');
    }

    function hideLoader() {
        $('#loader-overlay').removeClass('show');
    }

    // Helper to inject a simple Toast Notification dynamically
    function showToast(type, message) {
        var toastContainer = $('#toast-container');
        if (!toastContainer.length) {
            $('body').append('<div id="toast-container" class="position-fixed bottom-0 start-0 p-3" style="z-index: 1050;"></div>');
            toastContainer = $('#toast-container');
        }

        var icon = type === 'success' ? 'fa-check-circle text-success' : 'fa-exclamation-circle text-danger';
        var toastHtml = `
            <div class="toast align-items-center border-0 show" role="alert" aria-live="assertive" aria-atomic="true" style="background-color: white; box-shadow: 0 4px 12px rgba(0,0,0,0.15); border-radius: 8px;">
                <div class="d-flex">
                    <div class="toast-body d-flex align-items-center gap-2">
                        <i class="fas ${icon} fs-5"></i>
                        <span>${message}</span>
                    </div>
                    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;

        var toastElement = $(toastHtml).appendTo(toastContainer);
        setTimeout(function () {
            toastElement.fadeTo(500, 0).slideUp(500, function () {
                $(this).remove();
            });
        }, 3500);
    }
});
