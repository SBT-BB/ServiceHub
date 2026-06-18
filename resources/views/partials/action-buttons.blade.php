<div class="hstack gap-2 fs-15">
    @if (isset($permission_route))
        <a href="{{ $permission_route }}" class="btn icon-btn-sm btn-light-success" data-bs-toggle="tooltip"
            data-bs-placement="bottom" data-bs-custom-class="tooltip-dark" data-bs-title="Permissions" data-drawer="true"
            data-drawer-title="Permissions">
            <i class="ri-shield-keyhole-line"></i>
        </a>
    @endif

    @if (isset($edit_route))
        <a href="{{ $edit_route }}" class="btn icon-btn-sm btn-light-primary" data-bs-toggle="tooltip"
            data-bs-placement="bottom" data-bs-custom-class="tooltip-dark" data-bs-title="Edit" data-drawer="true"
            data-drawer-title="Edit">
            <i class="ri-pencil-line"></i>
        </a>
    @endif

    @if (isset($delete_route))
        <form action="{{ $delete_route }}" method="POST" class="delete-form" style="display:inline;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn icon-btn-sm btn-light-danger delete-item" data-bs-toggle="tooltip"
                data-bs-placement="bottom" data-bs-custom-class="tooltip-dark" data-bs-title="Delete">
                <i class="ri-delete-bin-line"></i>
            </button>
        </form>
    @endif
</div>
