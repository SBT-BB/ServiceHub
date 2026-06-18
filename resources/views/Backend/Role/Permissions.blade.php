@extends('partials.layouts.master')

@section('title')
    {{ ucfirst($role->name) }} Role's Permissions | Herozi
@endsection

@section('sub-title')
    {{ ucfirst($role->name) }} Role's Permissions
@endsection

@section('pagetitle', 'Dashboard')

@section('css')
    <style>
        .permission-accordion .accordion-item {
            border: 1px solid var(--bs-border-color);
            border-radius: 8px !important;
            margin-bottom: 10px;
            overflow: hidden;
        }

        .permission-accordion .accordion-button {
            background: var(--bs-card-bg);
            font-weight: 600;
            font-size: 15px;
            padding: 14px 20px;
            box-shadow: none;
        }

        .permission-accordion .accordion-button:not(.collapsed) {
            background: var(--bs-light);
            color: var(--bs-heading-color);
        }

        .permission-accordion .accordion-body {
            padding: 15px 20px;
            background: var(--bs-card-bg);
            border-top: 1px solid var(--bs-border-color);
        }

        .permission-badge {
            font-size: 11px;
            padding: 3px 8px;
            border-radius: 4px;
        }

        .module-checkbox {
            width: 20px;
            height: 20px;
            cursor: pointer;
        }

        .perm-check-item {
            padding: 8px 15px;
            border-radius: 6px;
            transition: background 0.2s;
        }

        .perm-check-item:hover {
            background: var(--bs-light);
        }
    </style>
@endsection

@section('content')

    <div class="row g-4">
        <div class="col-12">
            <!-- Breadcrumb navigation -->
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('role.index') }}">Roles</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Permissions</li>
                </ol>
            </nav>
        </div>
        <div class="col-12">
            <div class="card mb-0">
                <div class="card-body">
                    <div id="drawer-form-content">
                        <style>
                            #commonDrawer .permission-accordion .accordion-item {
                                border: 1px solid var(--bs-border-color);
                                border-radius: 8px !important;
                                margin-bottom: 10px;
                                overflow: hidden;
                            }

                            #commonDrawer .permission-accordion .accordion-button {
                                background: var(--bs-card-bg);
                                font-weight: 600;
                                font-size: 14px;
                                padding: 12px 16px;
                                box-shadow: none;
                            }

                            #commonDrawer .permission-accordion .accordion-button:not(.collapsed) {
                                background: var(--bs-light);
                                color: var(--bs-heading-color);
                            }

                            #commonDrawer .permission-accordion .accordion-body {
                                padding: 12px 16px;
                                background: var(--bs-card-bg);
                                border-top: 1px solid var(--bs-border-color);
                            }

                            #commonDrawer .permission-badge {
                                font-size: 11px;
                                padding: 3px 8px;
                                border-radius: 4px;
                            }

                            #commonDrawer .module-checkbox {
                                width: 18px;
                                height: 18px;
                                cursor: pointer;
                            }

                            #commonDrawer .perm-check-item {
                                padding: 6px 12px;
                                border-radius: 6px;
                                transition: background 0.2s;
                            }

                            #commonDrawer .perm-check-item:hover {
                                background: var(--bs-light);
                            }
                        </style>

                        <div class="d-flex gap-2 flex-wrap mb-8">
                            <button type="button" class="btn btn-sm btn-info" id="expandAllBtn">Expand All</button>
                            <button type="button" class="btn btn-sm btn-secondary" id="collapseAllBtn">Collapse
                                All</button>
                            <button type="button" class="btn btn-sm btn-success" id="selectAllBtn">Select All</button>
                            <button type="button" class="btn btn-sm btn-danger" id="unselectAllBtn">Unselect All</button>
                        </div>

                        <form id="permissionsForm" action="{{ route('role.permissions.update', $role->id) }}"
                            method="POST">
                            @csrf
                            @method('PUT')

                            <div class="accordion permission-accordion" id="permissionsAccordion">
                                @foreach ($modules as $moduleName => $permissions)
                                    @php
                                        $moduleSlug = Str::slug($moduleName);
                                        $permCount = count($permissions);
                                        $checkedCount = count(array_intersect($permissions, $rolePermissions));
                                        $allChecked = $checkedCount === $permCount;
                                    @endphp
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="heading_{{ $moduleSlug }}">
                                            <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#collapse_{{ $moduleSlug }}"
                                                aria-expanded="false" aria-controls="collapse_{{ $moduleSlug }}">
                                                <div class="d-flex align-items-center gap-3 w-100">
                                                    <input type="checkbox"
                                                        class="form-check-input module-checkbox select-all-module m-0"
                                                        data-module="{{ $moduleSlug }}" {{ $allChecked ? 'checked' : '' }}
                                                        onclick="event.stopPropagation();">
                                                    <span>{{ $moduleName }}</span>
                                                    <span
                                                        class="badge bg-primary-subtle text-primary permission-badge">{{ $permCount }}</span>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="collapse_{{ $moduleSlug }}" class="accordion-collapse collapse"
                                            aria-labelledby="heading_{{ $moduleSlug }}">
                                            <div class="accordion-body">
                                                <div class="row g-2">
                                                    @foreach ($permissions as $permission)
                                                        <div class="col-12">
                                                            <div class="perm-check-item d-flex align-items-center gap-2">
                                                                <input type="checkbox"
                                                                    class="form-check-input permission-checkbox m-0 perm-{{ $moduleSlug }}"
                                                                    name="permissions[]" value="{{ $permission }}"
                                                                    id="perm_{{ Str::slug($permission) }}"
                                                                    {{ in_array($permission, $rolePermissions) ? 'checked' : '' }}>
                                                                <label class="form-check-label mb-0"
                                                                    for="perm_{{ Str::slug($permission) }}"
                                                                    style="cursor:pointer;">
                                                                    {{ ucwords(str_replace('-', ' ', $permission)) }}
                                                                </label>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </form>

                        <script>
                            (function() {
                                // Expand All
                                $(document).off('click', '#expandAllBtn').on('click', '#expandAllBtn', function() {
                                    $('#commonDrawer .permission-accordion .accordion-collapse, .permission-accordion .accordion-collapse')
                                        .each(function() {
                                            new bootstrap.Collapse(this, {
                                                toggle: false
                                            }).show();
                                        });
                                });
                                // Collapse All
                                $(document).off('click', '#collapseAllBtn').on('click', '#collapseAllBtn', function() {
                                    $('#commonDrawer .permission-accordion .accordion-collapse, .permission-accordion .accordion-collapse')
                                        .each(function() {
                                            new bootstrap.Collapse(this, {
                                                toggle: false
                                            }).hide();
                                        });
                                });
                                // Select All
                                $(document).off('click', '#selectAllBtn').on('click', '#selectAllBtn', function() {
                                    var $ctx = $(this).closest('#drawer-form-content, .card-body');
                                    $ctx.find('.permission-checkbox').prop('checked', true);
                                    $ctx.find('.select-all-module').prop('checked', true);
                                });
                                // Unselect All
                                $(document).off('click', '#unselectAllBtn').on('click', '#unselectAllBtn', function() {
                                    var $ctx = $(this).closest('#drawer-form-content, .card-body');
                                    $ctx.find('.permission-checkbox').prop('checked', false);
                                    $ctx.find('.select-all-module').prop('checked', false);
                                });
                                // Module "Select All" checkbox
                                $(document).off('change', '.select-all-module').on('change', '.select-all-module', function() {
                                    var moduleSlug = $(this).data('module');
                                    $(this).closest('#drawer-form-content, .card-body').find('.perm-' + moduleSlug).prop('checked',
                                        this.checked);
                                });
                                // Update module checkbox when individual changes
                                $(document).off('change', '.permission-checkbox').on('change', '.permission-checkbox', function() {
                                    var classes = $(this).attr('class').split(' ');
                                    var moduleClass = classes.find(function(c) {
                                        return c.startsWith('perm-') && c !== 'permission-checkbox';
                                    });
                                    if (moduleClass) {
                                        var moduleSlug = moduleClass.replace('perm-', '');
                                        var $ctx = $(this).closest('#drawer-form-content, .card-body');
                                        var total = $ctx.find('.perm-' + moduleSlug).length;
                                        var checked = $ctx.find('.perm-' + moduleSlug + ':checked').length;
                                        $ctx.find('[data-module="' + moduleSlug + '"]').prop('checked', total === checked);
                                    }
                                });
                            })();
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
