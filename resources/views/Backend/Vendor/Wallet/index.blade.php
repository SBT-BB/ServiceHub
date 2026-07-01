@extends('partials.layouts.master')

@section('title', 'My Wallet | ServiceHub')

@section('sub-title', 'Wallet')
@section('pagetitle', 'My Wallet Dashboard')

@section('content')
    <div class="row g-4">
        <!-- Wallet Stats Cards -->
        <!-- Card 1: Wallet Balance -->
        <div class="col-xl-4 col-md-6">
            <div class="card border-0 shadow-sm overflow-hidden h-100 mb-0" style="border-radius: 12px;">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="avatar-md rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center me-3" style="width: 56px; height: 56px; min-width: 56px;">
                        <i class="ri-wallet-3-fill fs-24"></i>
                    </div>
                    <div class="flex-grow-1">
                        <span class="text-muted fs-13 d-block mb-1 text-uppercase fw-medium tracking-wide">Wallet Balance</span>
                        <h3 class="fw-bold mb-0 text-primary">₹ {{ number_format($currentBalance, 2) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: Total Earnings (Credit) -->
        <div class="col-xl-4 col-md-6">
            <div class="card border-0 shadow-sm overflow-hidden h-100 mb-0" style="border-radius: 12px;">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="avatar-md rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center me-3" style="width: 56px; height: 56px; min-width: 56px;">
                        <i class="ri-arrow-left-down-fill fs-24"></i>
                    </div>
                    <div class="flex-grow-1">
                        <span class="text-muted fs-13 d-block mb-1 text-uppercase fw-medium tracking-wide">Total Earnings (80%)</span>
                        <h3 class="fw-bold mb-0 text-success">₹ {{ number_format($totalEarned, 2) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 3: Platform Commission (Debit) -->
        <div class="col-xl-4 col-md-12">
            <div class="card border-0 shadow-sm overflow-hidden h-100 mb-0" style="border-radius: 12px;">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="avatar-md rounded-circle bg-danger bg-opacity-10 text-danger d-flex align-items-center justify-content-center me-3" style="width: 56px; height: 56px; min-width: 56px;">
                        <i class="ri-percent-fill fs-24"></i>
                    </div>
                    <div class="flex-grow-1">
                        <span class="text-muted fs-13 d-block mb-1 text-uppercase fw-medium tracking-wide">Commission Deducted (20%)</span>
                        <h3 class="fw-bold mb-0 text-danger">₹ {{ number_format($totalCommissionPaid, 2) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Info/payout note banner -->
        <div class="col-12">
            <div class="alert alert-info border-0 mb-0 d-flex align-items-center p-3" role="alert" style="border-radius: 10px; background-color: rgba(53, 119, 241, 0.08);">
                <i class="ri-info-card-fill text-info fs-22 me-3"></i>
                <div>
                    <span class="fw-semibold text-dark d-block">Platform Payout & Commission Policy</span>
                    <span class="text-muted fs-12">Every completed booking is subject to a 20% platform fee. The remaining 80% is automatically credited to your wallet balance. Wallet payouts are settled automatically to your registered bank account.</span>
                </div>
            </div>
        </div>

        <!-- Transaction History Table -->
        <div class="col-12">
            <div class="card border-0 shadow-sm mb-0" style="border-radius: 12px;">
                <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0 fw-bold">Transaction History</h5>
                    <button class="btn btn-sm btn-light d-flex align-items-center gap-1 btn-refresh-table">
                        <i class="ri-refresh-line"></i> Refresh
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="vendor-wallet-table" class="table table-hover align-middle table-nowrap w-100">
                            <thead class="bg-light bg-opacity-50">
                                <tr>
                                    <th>Booking No.</th>
                                    <th>Description</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Date & Time</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function () {
            var table = initDataTable('#vendor-wallet-table', '{{ route('vendor.wallet.index') }}', [
                { data: 'booking_number', name: 'booking.booking_number', orderable: false },
                { data: 'description', name: 'description' },
                { data: 'type', name: 'type' },
                { data: 'amount', name: 'amount' },
                { data: 'created_at', name: 'created_at' }
            ]);

            // Refresh table trigger
            $('.btn-refresh-table').on('click', function() {
                table.ajax.reload(null, false);
                showToast('Transaction list refreshed', 'success');
            });
        });
    </script>
@endsection
