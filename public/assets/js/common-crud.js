/**
 * Common CRUD JS for DataTables, Select2 AJAX, and Toasts
 * Matches the Herozi theme DataTable styling from datatable.init.js
 */

$(document).ready(function () {
    // CSRF Token for AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Initialize Select2 AJAX
    window.initSelect2Ajax = function (selector, url, placeholder = "Search...") {
        $(selector).select2({
            ajax: {
                url: url,
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term,
                        page: params.page
                    };
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.items,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
                cache: true
            },
            placeholder: placeholder,
            minimumInputLength: 1,
            width: '100%'
        });
    };

    // Initialize DataTable (matching Herozi theme style)
    window.initDataTable = function (selector, url, columns, options) {
        options = options || {};

        var tableConfig = {
            processing: true,
            serverSide: true,
            ajax: {
                url: typeof url === 'string' ? url : url.url,
                data: function (d) {
                    if (options && typeof options.ajaxData === 'function') {
                        options.ajaxData(d);
                    } else if (typeof url === 'object' && typeof url.data === 'function') {
                        url.data(d);
                    }
                }
            },
            columns: columns,
            order: [[0, 'desc']],
            scrollY: options.scrollY || '60vh',
            scrollX: true,
            scrollCollapse: true,
            fixedColumns: {
                left: 3,
                right: 1
            },
            dom:
                '<"card-header dt-head d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3"' +
                '<"d-flex align-items-center gap-2"l>' +
                '<"d-flex flex-column flex-sm-row align-items-center justify-content-sm-end gap-3 w-100"f<"add_button">>' +
                '>' +
                '<"table-responsive"t>' +
                '<"card-footer d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2"i' +
                '<"d-flex align-items-sm-center justify-content-end gap-4"p>' +
                '>',
            language: {
                sLengthMenu: 'Show _MENU_',
                search: '',
                searchPlaceholder: 'Search...',
                paginate: {
                    next: '<i class="ri-arrow-right-s-line"></i>',
                    previous: '<i class="ri-arrow-left-s-line"></i>'
                },
                processing: '<div class="spinner-border text-primary" role="status"></div>'
            },
            lengthMenu: [10, 20, 50],
            pageLength: 10,
            initComplete: function () {
                // Remove form-control-sm from search input
                var inputEl = $(selector).closest('.card').find('.dataTables_filter .form-control');
                if (inputEl.length) {
                    inputEl.removeClass('form-control-sm');
                }
                // Remove form-select-sm from length select
                var selectEl = $(selector).closest('.card').find('.dataTables_length .form-select');
                if (selectEl.length) {
                    selectEl.removeClass('form-select-sm');
                }
            }
        };

        var table = $(selector).DataTable(tableConfig);

        // Re-initialize Bootstrap tooltips on each draw (for AJAX-loaded rows)
        table.on('draw.dt', function () {
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            tooltipTriggerList.forEach(function (tooltipTriggerEl) {
                var existing = bootstrap.Tooltip.getInstance(tooltipTriggerEl);
                if (existing) {
                    existing.dispose();
                }
                new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });

        // Recalculate column widths on window resize & sidebar toggle
        var resizeTimer;
        $(window).on('resize', function () {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function () {
                table.columns.adjust();
                if (table.fixedColumns) { table.fixedColumns().relayout(); }
            }, 250);
        });

        // Handle sidebar toggle — recalculate after CSS transition ends
        $(document).on('click', '.sidebar-toggle, .hamburger-icon, [data-bs-toggle="collapse"]', function () {
            setTimeout(function () {
                table.columns.adjust();
                if (table.fixedColumns) { table.fixedColumns().relayout(); }
            }, 350);
        });

        return table;
    };

    // Toast Notification Handler
    window.showToast = function (message, type) {
        type = type || 'success';
        var toastContainer = document.getElementById('toast-container');
        if (!toastContainer) {
            var container = document.createElement('div');
            container.id = 'toast-container';
            container.className = 'toast-container position-fixed top-0 end-0 p-3';
            container.style.zIndex = '1080';
            document.body.appendChild(container);
        }

        var toastId = 'toast-' + Date.now();
        var bgClass = type === 'success' ? 'bg-success' : 'bg-danger';

        var toastHtml =
            '<div id="' + toastId + '" class="toast align-items-center text-white ' + bgClass + ' border-0" role="alert" aria-live="assertive" aria-atomic="true">' +
            '<div class="d-flex">' +
            '<div class="toast-body">' + message + '</div>' +
            '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>' +
            '</div>' +
            '</div>';

        document.getElementById('toast-container').insertAdjacentHTML('beforeend', toastHtml);
        var toastElement = document.getElementById(toastId);
        var toast = new bootstrap.Toast(toastElement);
        toast.show();

        toastElement.addEventListener('hidden.bs.toast', function () {
            toastElement.remove();
        });
    };

    // --- GLOBAL DELETE CONFIRMATION (SweetAlert2) ---

    $(document).on('submit', '.delete-form', function (e) {
        e.preventDefault();
        var $form = $(this);

        Swal.fire({
            title: 'Are you sure?',
            text: 'This action cannot be undone!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel',
            customClass: {
                popup: 'swal2-sm'
            }
        }).then(function (result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: $form.attr('action'),
                    method: 'POST',
                    data: $form.serialize(),
                    success: function (response) {
                        showToast(response.message || 'Deleted successfully!');
                        // Reload any DataTable on the page
                        if ($('.dataTable').length) {
                            $('.dataTable').each(function () {
                                $(this).DataTable().ajax.reload(null, false);
                            });
                        }
                    },
                    error: function (xhr) {
                        var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to delete.';
                        showToast(msg, 'danger');
                    }
                });
            }
        });
    });

    // --- DRAWER HANDLING LOGIC ---

    const commonDrawer = document.getElementById('commonDrawer');
    const drawerInstance = commonDrawer ? new bootstrap.Offcanvas(commonDrawer) : null;

    $(document).on('click', '[data-drawer="true"], .drawer-link', function (e) {
        e.preventDefault();
        const url = $(this).attr('href');
        const title = $(this).data('drawer-title') || $(this).text().trim() || 'Form';

        $('#commonDrawerLabel').text(title);
        $('#commonDrawerBody').html('<div class="text-center p-5"><div class="spinner-border text-primary" role="status"></div></div>');
        $('#commonDrawerFooter').addClass('d-none');

        drawerInstance.show();

        $.ajax({
            url: url,
            method: 'GET',
            success: function (html) {
                const $html = $('<div>').html(html);
                let $wrapper = $html.find('#drawer-form-content');
                let $content = null;

                if ($wrapper.length > 0) {
                    // Use the entire wrapper content (may include buttons, styles, scripts alongside the form)
                    $content = $wrapper;
                } else {
                    // Fallback: grab any form from the page
                    var $fallbackForm = $html.find('form').first();
                    if ($fallbackForm.length > 0) {
                        $content = $fallbackForm;
                    }
                }

                if ($content && $content.length > 0) {
                    // Check if there's a form
                    var $form = $content.is('form') ? $content : $content.find('form').first();

                    // Extract scripts before inserting HTML (jQuery strips them)
                    var scripts = [];
                    $content.find('script').each(function () {
                        scripts.push($(this).html());
                        $(this).remove();
                    });

                    $('#commonDrawerBody').html($content);

                    if ($form.length > 0) {
                        var formId = $form.attr('id') || 'drawerForm';
                        $form.attr('id', formId);
                        $('#commonDrawerFooter').removeClass('d-none');
                        $('#drawerSubmitBtn').attr('form', formId);
                    } else {
                        // It's just a view, hide footer
                        $('#commonDrawerFooter').addClass('d-none');
                    }

                    // Re-initialize Select2 inside drawer
                    var $select2 = $('#commonDrawer .select2');
                    if ($select2.length) {
                        $select2.select2({
                            dropdownParent: $('#commonDrawer'),
                            width: '100%'
                        });
                    }

                    // Execute embedded scripts
                    scripts.forEach(function (scriptContent) {
                        try { $.globalEval(scriptContent); } catch (e) { console.warn('Drawer script error:', e); }
                    });
                } else {
                    $('#commonDrawerBody').html('<div class="alert alert-warning m-0">Could not load content.</div>');
                }
            },
            error: function () {
                $('#commonDrawerBody').html('<div class="alert alert-danger m-0">Failed to load content. Please try again.</div>');
            }
        });
    });

    // Handle form submission inside drawer
    $(document).on('submit', '#commonDrawer form', function (e) {
        e.preventDefault();

        const $form = $(this);
        const url = $form.attr('action') || window.location.href;
        const formData = new FormData(this);
        const $btn = $('#drawerSubmitBtn');

        // Reset previous validation errors
        $form.find('.is-invalid').removeClass('is-invalid');

        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');

        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                showToast(response.message || 'Saved successfully!');
                drawerInstance.hide();
                $btn.prop('disabled', false).text('Save Changes');

                // Reload any DataTable on the page
                if ($('.dataTable').length) {
                    $('.dataTable').each(function () {
                        $(this).DataTable().ajax.reload(null, false);
                    });
                }
            },
            error: function (xhr) {
                $btn.prop('disabled', false).text('Save Changes');
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    var errors = xhr.responseJSON.errors;
                    Object.keys(errors).forEach(function (key) {
                        var field = $form.find('[name="' + key + '"], [name="' + key + '[]"]');
                        if (field.length) {
                            field.addClass('is-invalid');
                            let feedback = field.siblings('.invalid-feedback');
                            if (feedback.length) {
                                feedback.text(errors[key][0]);
                            } else {
                                field.after('<div class="invalid-feedback d-block">' + errors[key][0] + '</div>');
                            }
                        }
                    });
                } else {
                    showToast('An error occurred while saving.', 'danger');
                }
            }
        });
    });

    // Clean up drawer body when hidden
    if (commonDrawer) {
        commonDrawer.addEventListener('hidden.bs.offcanvas', function () {
            $('#commonDrawerBody').html('');
            $('#commonDrawerFooter').addClass('d-none');
        });
    }
});
