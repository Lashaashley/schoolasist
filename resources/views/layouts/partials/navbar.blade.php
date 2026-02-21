<style>
    .notification-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }
    .payroll-types {
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    background-color: #f0f0f0;
    padding: 5px 10px;
    border-radius: 5px;
    font-size: 0.9em;
    max-width: 400px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
/* Blinking animation for no period alert */
@keyframes blink-warning {
    0%, 50%, 100% {
        opacity: 1;
        background-color: #f8d7da;
    }
    25%, 75% {
        opacity: 0.7;
        background-color: #dc3545;
        color: white;
    }
}

.no-period-alert {
    animation: blink-warning 2s infinite;
    padding: 8px 20px;
    border-radius: 5px;
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid #dc3545;
}

.no-period-alert:hover {
    animation: none;
    background-color: #dc3545;
    color: white;
    transform: scale(1.05);
}

.no-period-alert i {
    margin-right: 5px;
}

.period-active {
    padding: 8px 20px;
    border-radius: 5px;
    background-color: #d4edda;
    border: 2px solid #28a745;
}

.period-active i {
    color: #28a745;
    margin-right: 5px;
}
</style>

<div class="header">
    <div class="header-left">
        <div class="menu-icon dw dw-menu"></div>
        <div class="search-toggle-icon dw dw-search2" data-toggle="header_search"></div>
    </div>
    <div class="payroll-types d-flex align-items-center justify-content-center flex-grow-1">
        <span id="current-period" class="font-weight-bold text-dark">Loading current period...</span>
    </div>

    <div class="header-right">
        <div class="notification-icon">
            <div class="dropdown">
                <a class="dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                    <img src="{{ asset('images/bell.png') }}" style="width: 25px; height: 25px;" alt="Notifications" />
                </a>
                <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                    <a class="dropdown-item" href="#"><i class="dw dw-check"></i> New Task Assigned</a>
                </div>
            </div>
        </div>
        
        <div class="dashboard-setting user-notification">
            <div class="dropdown">
                <a class="dropdown-toggle no-arrow" href="javascript:;" data-toggle="right-sidebar">
                    <i class="dw dw-settings2"></i>
                </a>
            </div>
        </div>
        
        <div class="user-info-dropdown">
            <div class="dropdown">
                @if(Auth::check())
                <a class="dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                    <span class="user-icon">
                        <img src="{{ asset('storage/' . Auth::user()->profile_photo) ?? asset('images/NO-IMAGE-AVAILABLE.jpg') }}" alt="{{ Auth::user()->name }}">
                    </span>
                    <span class="user-name">{{ Auth::user()->name }}</span>
                </a>
                <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                    <a class="dropdown-item" href="{{ route('profile.edit') }}">
                        <i class="dw dw-user1"></i> Profile
                    </a>
                    
                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <a href="{{ route('logout') }}" 
                           class="dropdown-item" 
                           onclick="event.preventDefault(); this.closest('form').submit();">
                            <i class="dw dw-logout"></i> Log Out
                        </a>
                    </form>
                </div>
                 @else
            {{-- Redirect to login if user is not authenticated --}}
            <script>window.location.href = "{{ route('login') }}";</script>
        @endif
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="createPeriodModal" tabindex="-1" role="dialog" aria-labelledby="createPeriodModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="createPeriodModalLabel">
                    <i class="fa fa-calendar-plus-o"></i> Create New Period
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="createPeriodForm">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i> Create an active period to start managing students and fees.
                    </div>
                    
                    <div class="form-group">
                        <label for="periodname">Period Name <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control" 
                               id="periodname" 
                               name="periodname" 
                               placeholder="e.g., Term 1 2026, Q1 2026" 
                               required>
                        <small class="form-text text-muted">Enter a descriptive name for this period</small>
                        <span class="text-danger error-text periodname_error"></span>
                    </div>

                    <div class="form-group">
                        <label for="startdate">Start Date <span class="text-danger">*</span></label>
                        <input type="date" 
                               class="form-control" 
                               id="startdate" 
                               name="startdate" 
                               required>
                        <span class="text-danger error-text startdate_error"></span>
                    </div>

                    <div class="form-group">
                        <label for="enddate">End Date <span class="text-danger">*</span></label>
                        <input type="date" 
                               class="form-control" 
                               id="enddate" 
                               name="enddate" 
                               required>
                        <span class="text-danger error-text enddate_error"></span>
                    </div>

                    <div class="form-group">
                        <label for="pstatus">Status <span class="text-danger">*</span></label>
                        <select class="form-control" id="pstatus" name="pstatus" required>
                            <option value="Active" selected>Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                        <small class="form-text text-muted">Set to "Active" to use this period immediately</small>
                        <span class="text-danger error-text pstatus_error"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fa fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" id="savePeriodBtn">
                        <i class="fa fa-save"></i> Create Period
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
   $(document).ready(function () {
    // Load current period on page load
    loadCurrentPeriod();

    // Click event for no period alert
    $(document).on('click', '.no-period-alert', function() {
        $('#createPeriodModal').modal('show');
    });

    // Form validation and submission
    $('#createPeriodForm').on('submit', function(e) {
        e.preventDefault();
        
        // Clear previous errors
        $('.error-text').text('');
        
        // Validate dates
        var startDate = new Date($('#startdate').val());
        var endDate = new Date($('#enddate').val());
        
        if (endDate <= startDate) {
            $('.enddate_error').text('End date must be after start date');
            return false;
        }
        
        // Disable submit button
        $('#savePeriodBtn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Creating...');
        
        $.ajax({
            url: "{{ route('periods.store2') }}",
            type: "POST",
            data: $(this).serialize(),
            success: function(response) {
                // Show success message
                showAlert('success', 'Success!', response.message);
                
                // Close modal
                $('#createPeriodModal').modal('hide');
                
                // Reset form
                $('#createPeriodForm')[0].reset();
                
                // Reload current period
                loadCurrentPeriod();
                
                // Re-enable button
                $('#savePeriodBtn').prop('disabled', false).html('<i class="fa fa-save"></i> Create Period');
            },
            error: function(xhr) {
                // Re-enable button
                $('#savePeriodBtn').prop('disabled', false).html('<i class="fa fa-save"></i> Create Period');
                
                if (xhr.status === 422) {
                    // Validation errors
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        $('.' + key + '_error').text(value[0]);
                    });
                } else {
                    showAlert('danger', 'Error!', 'Failed to create period. Please try again.');
                }
            }
        });
    });

    // Clear errors when modal is closed
    $('#createPeriodModal').on('hidden.bs.modal', function () {
        $('#createPeriodForm')[0].reset();
        $('.error-text').text('');
        $('#savePeriodBtn').prop('disabled', false).html('<i class="fa fa-save"></i> Create Period');
    });
});

// Function to load current period
function loadCurrentPeriod() {
    $.ajax({
        url: "{{ route('periods.current') }}",
        type: "GET",
        success: function (response) {
            if (response.status === 'success') {
                $('#current-period').removeClass('no-period-alert').addClass('period-active');
                $('#current-period').html(`
                    <i class="fa fa-calendar-check-o"></i> 
                    Period: <strong>${response.periodname}</strong> | 
                    Ends: <strong>${response.enddate_formatted}</strong>
                `);
                $('#current-period').css('cursor', 'default');
                $('#current-period').off('click'); // Remove click event
            } else {
                $('#current-period').removeClass('period-active').addClass('no-period-alert');
                $('#current-period').html(`
                    <i class="fa fa-exclamation-triangle"></i> 
                    <strong>No Active Period - Click Here to Create One</strong>
                `);
                $('#current-period').css('cursor', 'pointer');
            }
        },
        error: function () {
            $('#current-period').removeClass('period-active').addClass('no-period-alert');
            $('#current-period').html(`
                <i class="fa fa-exclamation-triangle"></i> 
                <strong>Error Loading Period - Click to Create</strong>
            `);
            $('#current-period').css('cursor', 'pointer');
        }
    });
}

// Alert function (if not already defined)
function showAlert(type, title, message) {
    // You can use your existing alert system
    // This is a basic implementation using Bootstrap alerts
    var alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            <strong>${title}</strong> ${message}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    `;
    
    // Append to a container (adjust selector as needed)
    if ($('.alert-container').length) {
        $('.alert-container').html(alertHtml);
    } else {
        $('body').prepend('<div class="alert-container" style="position: fixed; top: 70px; right: 20px; z-index: 9999; min-width: 300px;">' + alertHtml + '</div>');
    }
    
    // Auto-dismiss after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow', function() {
            $(this).remove();
        });
    }, 5000);
}
</script>
