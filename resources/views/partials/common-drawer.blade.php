<!-- COMMON FORM DRAWER -->
<div class="offcanvas offcanvas-end" id="commonDrawer" tabindex="-1" aria-labelledby="commonDrawerLabel">
    <div class="offcanvas-header border-bottom hstack">
        <h5 class="offcanvas-title fs-5 flex-grow-1 text-uppercase" id="commonDrawerLabel">Form</h5>
        <button type="button" class="close btn btn-text-primary icon-btn-sm flex-shrink-0" data-bs-dismiss="offcanvas"
            aria-label="Close">
            <i class="ri-close-large-line fw-semibold"></i>
        </button>
    </div>
    <div class="offcanvas-body" id="commonDrawerBody">
        <div class="text-center p-5">
            <div class="spinner-border text-primary avatar-sm" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>
    <div class="offcanvas-header border-top hstack gap-3 justify-content-end" id="commonDrawerFooter">
        <button type="button" class="btn btn-light" data-bs-dismiss="offcanvas">Close</button>
        <button type="submit" form="drawerForm" class="btn btn-primary" id="drawerSubmitBtn">Save Changes</button>
    </div>
</div>
