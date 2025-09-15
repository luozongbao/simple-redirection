jQuery(document).ready(function($) {
    
    // Create slug form submission
    $('#wsr-create-form').on('submit', function(e) {
        e.preventDefault();
        
        var $form = $(this);
        var $submitBtn = $form.find('input[type="submit"]');
        var originalText = $submitBtn.val();
        
        // Show loading state
        $submitBtn.val('Creating...').prop('disabled', true);
        
        $.ajax({
            url: wsr_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'wsr_create_slug',
                nonce: wsr_ajax.nonce,
                slug: $('#slug').val(),
                target_url: $('#target_url').val()
            },
            success: function(response) {
                if (response.success) {
                    // Reset form
                    $form[0].reset();
                    
                    // Show success message
                    showNotification('Redirect created successfully!', 'success');
                    
                    // Reload page to show new slug
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    showNotification(response.data, 'error');
                }
            },
            error: function() {
                showNotification('An error occurred. Please try again.', 'error');
            },
            complete: function() {
                $submitBtn.val(originalText).prop('disabled', false);
            }
        });
    });
    
    // Edit slug button
    $('.edit-slug').on('click', function() {
        var $row = $(this).closest('tr');
        var id = $(this).data('id');
        
        // Get current values from the row
        var slug = $row.find('td:first strong').text();
        var targetUrl = $row.find('td:nth-child(2) a').attr('href');
        
        // Populate edit form
        $('#edit-id').val(id);
        $('#edit-slug').val(slug);
        $('#edit-target_url').val(targetUrl);
        
        // Show modal
        $('#wsr-edit-modal').show();
    });
    
    // Edit form submission
    $('#wsr-edit-form').on('submit', function(e) {
        e.preventDefault();
        
        var $form = $(this);
        var $submitBtn = $form.find('input[type="submit"]');
        var originalText = $submitBtn.val();
        
        // Show loading state
        $submitBtn.val('Updating...').prop('disabled', true);
        
        $.ajax({
            url: wsr_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'wsr_update_slug',
                nonce: wsr_ajax.nonce,
                id: $('#edit-id').val(),
                slug: $('#edit-slug').val(),
                target_url: $('#edit-target_url').val()
            },
            success: function(response) {
                if (response.success) {
                    // Hide modal
                    $('#wsr-edit-modal').hide();
                    
                    // Show success message
                    showNotification('Redirect updated successfully!', 'success');
                    
                    // Reload page to show updated slug
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    showNotification(response.data, 'error');
                }
            },
            error: function() {
                showNotification('An error occurred. Please try again.', 'error');
            },
            complete: function() {
                $submitBtn.val(originalText).prop('disabled', false);
            }
        });
    });
    
    // Delete slug button
    $('.delete-slug').on('click', function() {
        if (!confirm('Are you sure you want to delete this redirect? This action cannot be undone.')) {
            return;
        }
        
        var $btn = $(this);
        var id = $btn.data('id');
        var $row = $btn.closest('tr');
        
        $btn.text('Deleting...').prop('disabled', true);
        
        $.ajax({
            url: wsr_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'wsr_delete_slug',
                nonce: wsr_ajax.nonce,
                id: id
            },
            success: function(response) {
                if (response.success) {
                    $row.fadeOut(function() {
                        $(this).remove();
                    });
                    showNotification('Redirect deleted successfully!', 'success');
                } else {
                    showNotification(response.data, 'error');
                    $btn.text('Delete').prop('disabled', false);
                }
            },
            error: function() {
                showNotification('An error occurred. Please try again.', 'error');
                $btn.text('Delete').prop('disabled', false);
            }
        });
    });
    
    // Toggle status button
    $('.toggle-status').on('click', function() {
        var $btn = $(this);
        var id = $btn.data('id');
        var currentStatus = $btn.data('status');
        var newStatus = currentStatus === 'active' ? 'inactive' : 'active';
        var $statusBadge = $btn.closest('tr').find('.status-badge');
        
        $btn.text('Updating...').prop('disabled', true);
        
        $.ajax({
            url: wsr_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'wsr_toggle_status',
                nonce: wsr_ajax.nonce,
                id: id,
                status: newStatus
            },
            success: function(response) {
                if (response.success) {
                    // Update button
                    $btn.data('status', newStatus);
                    $btn.text(newStatus === 'active' ? 'Disable' : 'Enable');
                    
                    // Update status badge
                    $statusBadge.removeClass('status-active status-inactive')
                               .addClass('status-' + newStatus)
                               .text(newStatus.charAt(0).toUpperCase() + newStatus.slice(1));
                    
                    showNotification('Status updated successfully!', 'success');
                } else {
                    showNotification(response.data, 'error');
                }
            },
            error: function() {
                showNotification('An error occurred. Please try again.', 'error');
            },
            complete: function() {
                $btn.prop('disabled', false);
            }
        });
    });
    
    // Test redirect button
    $('.test-redirect').on('click', function() {
        var $btn = $(this);
        var id = $btn.data('id');
        
        $btn.text('Testing...').prop('disabled', true);
        
        $.ajax({
            url: wsr_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'wsr_test_redirect',
                nonce: wsr_ajax.nonce,
                id: id
            },
            success: function(response) {
                if (response.success) {
                    // Open target URL in new tab
                    window.open(response.data.target_url, '_blank');
                    showNotification('Test redirect opened in new tab!', 'success');
                } else {
                    showNotification(response.data, 'error');
                }
            },
            error: function() {
                showNotification('An error occurred. Please try again.', 'error');
            },
            complete: function() {
                $btn.text('Test').prop('disabled', false);
            }
        });
    });
    
    // Copy slug URL
    $('.copy-slug').on('click', function(e) {
        e.preventDefault();
        
        var url = $(this).data('slug');
        
        // Create temporary input to copy text
        var $temp = $('<input>');
        $('body').append($temp);
        $temp.val(url).select();
        document.execCommand('copy');
        $temp.remove();
        
        // Show copy notification
        $('#wsr-copy-notification').show().delay(3000).fadeOut();
    });
    
    // Modal close functionality
    $('.wsr-modal-close').on('click', function() {
        $('#wsr-edit-modal').hide();
    });
    
    // Close modal when clicking outside
    $(window).on('click', function(e) {
        if ($(e.target).is('#wsr-edit-modal')) {
            $('#wsr-edit-modal').hide();
        }
    });
    
    // Utility function to show notifications
    function showNotification(message, type) {
        var $notification = $('<div class="notice notice-' + type + ' is-dismissible"><p>' + message + '</p></div>');
        $('.wrap h1').after($notification);
        
        // Auto dismiss after 5 seconds
        setTimeout(function() {
            $notification.fadeOut(function() {
                $(this).remove();
            });
        }, 5000);
        
        // Manual dismiss
        $notification.on('click', '.notice-dismiss', function() {
            $notification.fadeOut(function() {
                $(this).remove();
            });
        });
    }
    
    // Add dismiss button functionality
    $(document).on('click', '.notice-dismiss', function() {
        $(this).closest('.notice').fadeOut(function() {
            $(this).remove();
        });
    });
});