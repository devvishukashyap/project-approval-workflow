<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Project Dashboard</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

   <style>
    body {
        background-color: #f8fafc;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto,
            "Helvetica Neue", Arial, sans-serif;
        color: #334155;
    }

    .navbar-custom {
        background: #ffffff;
        border-bottom: 1px solid #e2e8f0;
        padding: 12px 24px;
    }

    .user-avatar {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background-color: #0d6efd;
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.95rem;
    }

    .dashboard-card {
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
        background: #ffffff;
    }

    .stat-card {
        border-left: 4px solid;
        background: #ffffff;
        padding: 20px;
        border-radius: 12px;
        border-top: 1px solid #e2e8f0;
        border-right: 1px solid #e2e8f0;
        border-bottom: 1px solid #e2e8f0;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
    }

    .stat-number {
        font-size: 1.75rem;
        font-weight: 700;
        line-height: 1.2;
        color: #0f172a;
    }

    .stat-label {
        font-size: 0.85rem;
        color: #64748b;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .table th {
        background: #f8fafc;
        color: #475569;
        font-weight: 600;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 12px 16px;
    }

    .table td {
        vertical-align: middle;
        padding: 16px;
    }

    .btn-sm {
        font-size: 0.75rem;
    }

    #toastContainer {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        max-width: 450px;
    }

    .toast {
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .toast .toast-body {
        padding: 12px 16px;
    }
</style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-custom sticky-top">
    <div class="container-fluid px-lg-4">
        <a class="navbar-brand d-flex align-items-center gap-2 fw-bold text-dark" href="#">
            <div class="bg-primary text-white rounded-3 p-1 d-flex align-items-center justify-content-center" style="width:32px;height:32px;">
                <i class="bi bi-kanban-fill fs-6"></i>
            </div>
            <span>Approval Workflow System</span>
        </a>
        <div class="d-flex align-items-center gap-3 ms-auto">
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle text-dark" id="userDropdown" data-bs-toggle="dropdown">
                    <div class="user-avatar me-2">{{ Auth::user()->initials ?? 'U' }}</div>
                    <div class="d-none d-md-block text-start me-2">
                        <div class="fw-semibold lh-1" style="font-size:0.9rem;">{{ Auth::user()->name }}</div>
                        <small class="text-muted" style="font-size:0.75rem;">{{ Auth::user()->email }}</small>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2">
                    <li><a class="dropdown-item py-2 text-danger" href="{{ route('logout') }}"><i class="bi bi-box-arrow-right me-2"></i>Sign Out</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<div class="container-fluid px-lg-4 py-4">

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4 d-flex align-items-center" role="alert">
            <i class="bi bi-check-circle-fill fs-5 me-2"></i>
            <div><strong>Success!</strong> {{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
        <div>
            <h3 class="fw-bold text-slate-800 mb-1">Project Dashboard</h3>
            <p class="text-muted mb-0 small">Track and manage your submitted project applications</p>
        </div>
        <div class="mt-3 mt-md-0">
            <a href="{{ route('projects.create') }}" class="btn btn-primary px-3 shadow-sm">
                <i class="bi bi-plus-lg me-1"></i> Submit New Project
            </a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card" style="border-left-color:#3b82f6;">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <div class="stat-label">Total Submitted</div>
                        <div class="stat-number mt-1" id="totalCount">{{ $total }}</div>
                    </div>
                    <div class="stat-icon"><i class="bi bi-folder2-open fs-4 text-muted"></i></div>
                </div>
                <div class="text-muted small"><span class="text-primary fw-semibold">100%</span> of total requests</div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card" style="border-left-color:#f59e0b;">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <div class="stat-label">Pending Review</div>
                        <div class="stat-number mt-1" id="pendingCount">{{ $pending }}</div>
                    </div>
                    <div class="stat-icon"><i class="bi bi-hourglass-split fs-4 text-muted"></i></div>
                </div>
                <div class="text-muted small"><span class="text-warning fw-semibold">{{ $pendingPercent }}%</span> awaiting response</div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card" style="border-left-color:#10b981;">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <div class="stat-label">Approved</div>
                        <div class="stat-number mt-1" id="approvedCount">{{ $approved }}</div>
                    </div>
                    <div class="stat-icon"><i class="bi bi-check-circle fs-4 text-muted"></i></div>
                </div>
                <div class="text-muted small"><span class="text-success fw-semibold">{{ $approvedPercent }}%</span> approval rate</div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card" style="border-left-color:#ef4444;">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <div class="stat-label">Rejected</div>
                        <div class="stat-number mt-1" id="rejectedCount">{{ $rejected }}</div>
                    </div>
                    <div class="stat-icon"><i class="bi bi-x-circle fs-4 text-muted"></i></div>
                </div>
                <div class="text-muted small"><span class="text-danger fw-semibold">{{ $rejectedPercent }}%</span> decline rate</div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-5 col-xl-4">
            <div class="dashboard-card p-3 h-100">
                <h6 class="fw-bold mb-3"><i class="bi bi-pie-chart me-2 text-primary"></i>Status Distribution</h6>
                <div class="d-flex align-items-center justify-content-center p-2" style="height:180px;">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-7 col-xl-8">
            <div class="dashboard-card p-4 h-100 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle">
                        <i class="bi bi-shield-check fs-3"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1">
                            @if(Auth::user()->role === 'admin') 🔐 Admin Access – Full Control @else 👤 Standard Contributor Access @endif
                        </h6>
                        <p class="text-muted small mb-0">
                            @if(Auth::user()->role === 'admin') You can approve/reject any project, view history, and perform bulk actions.
                            @else You are viewing data filtered specifically for your account ({{ Auth::user()->name }}). @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="dashboard-card p-3">
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-3 gap-2">
            <h6 class="fw-bold mb-0"><i class="bi bi-list-task me-2 text-primary"></i>
                @if(Auth::user()->role === 'admin') All Submitted Projects @else My Submitted Projects @endif
            </h6>
            <div class="d-flex gap-2 flex-wrap">
                <select id="statusFilter" class="form-select form-select-sm" style="width:140px;">
                    <option value="all">All Statuses</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                </select>
                @if(Auth::user()->role === 'admin')
                    <select id="submitterFilter" class="form-select form-select-sm" style="width:160px;">
                        <option value="all">All Submitters</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                        @endforeach
                    </select>
                @endif
            </div>
        </div>

        <div class="table-responsive">
            <table id="projectsTable" class="table table-hover align-middle w-100">
                <thead>
                    <tr>
                        @if(Auth::user()->role === 'admin')
                            <th style="width:30px;"><input class="form-check-input bulk-select-all" type="checkbox" id="selectAll"></th>
                        @endif
                        <th style="width:50px;">#</th>
                        <th>Project Name</th>
                        <th>Submitted By</th>
                        <th>Submission Date</th>
                        <th>Status</th>
                        <th>Last Updated</th>
                        <th class="text-center" style="width:220px;">Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

        @if(Auth::user()->role === 'admin')
            <div class="mt-3 d-flex gap-2 align-items-center">
                <button id="bulkApproveBtn" class="btn btn-success btn-sm"><i class="bi bi-check-all"></i> Approve Selected</button>
                <button id="bulkRejectBtn" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#bulkRejectModal"><i class="bi bi-x-circle"></i> Reject Selected</button>
                <span class="text-muted small ms-2">Select projects using checkboxes</span>
            </div>
        @endif
    </div>
</div>

<div class="modal fade" id="viewProjectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold"><i class="bi bi-folder2-open me-2 text-primary"></i>Project Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div id="modalLoader" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>
                </div>
                <div id="modalContent" class="d-none">
                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label class="text-muted small text-uppercase fw-semibold">Project Title</label>
                            <h5 id="viewTitle" class="fw-bold text-dark mt-1"></h5>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <label class="text-muted small text-uppercase fw-semibold d-block">Status</label>
                            <span id="viewStatus" class="mt-1 d-inline-block"></span>
                        </div>
                    </div>
                    <hr class="my-3 text-muted opacity-25">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="text-muted small text-uppercase fw-semibold">Submitted By</label>
                            <div id="viewSubmitter" class="fw-medium text-dark mt-1"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small text-uppercase fw-semibold">Submitted Date</label>
                            <div id="viewSubmittedAt" class="fw-medium text-dark mt-1"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small text-uppercase fw-semibold">Description</label>
                        <div id="viewDescription" class="p-3 bg-light rounded mt-1 border text-secondary" style="min-height:80px; white-space:pre-line;"></div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-muted small text-uppercase fw-semibold">Last Updated</label>
                            <div id="viewUpdatedAt" class="small text-muted mt-1"></div>
                        </div>
                        @if(Auth::user()->role === 'admin')
                            <div class="col-md-6">
                                <button class="btn btn-outline-secondary btn-sm" id="viewHistoryBtn" data-id=""><i class="bi bi-clock-history"></i> View History</button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@if(Auth::user()->role === 'admin')
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-x-circle text-danger me-2"></i>Reject Project</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="rejectProjectId">
                <div class="mb-3">
                    <label for="rejectReason" class="form-label fw-semibold">Reason <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="rejectReason" rows="3" placeholder="Provide reason..." required></textarea>
                    <div class="invalid-feedback">Please enter a reason.</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger btn-sm" id="confirmRejectBtn">
                    <span class="spinner-border spinner-border-sm me-1 d-none" id="rejectSpinner"></span>
                    Confirm Reject
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="bulkRejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-x-circle text-danger me-2"></i>Bulk Reject</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small">You are about to reject <span id="bulkRejectCount">0</span> selected project(s).</p>
                <div class="mb-3">
                    <label for="bulkRejectReason" class="form-label fw-semibold">Common Reason <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="bulkRejectReason" rows="3" placeholder="Provide reason for all..." required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger btn-sm" id="confirmBulkRejectBtn">
                    <span class="spinner-border spinner-border-sm me-1 d-none" id="bulkRejectSpinner"></span>
                    Confirm Bulk Reject
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="historyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-clock-history me-2"></i>Status Change History</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="historyLoader" class="text-center py-3">
                    <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>
                </div>
                <div id="historyContent" class="d-none">
                    <ul id="historyList" class="list-group list-group-flush"></ul>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endif

<div id="toastContainer"></div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<script>
    var statusChartInstance = null;

    // Toast Notification Utility Function
    function showToast(type, message) {
        const bgClass = type === 'success' ? 'bg-success' : type === 'danger' ? 'bg-danger' : type === 'warning' ? 'bg-warning text-dark' : 'bg-info';
        const icon = type === 'success' ? '✅' : type === 'danger' ? '❌' : type === 'warning' ? '⚠️' : 'ℹ️';
        const container = document.getElementById('toastContainer');
        if (!container) return;
        
        const toastHTML = `
            <div class="toast align-items-center text-white ${bgClass} border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body"><strong>${icon} ${message}</strong></div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', toastHTML);
        const toastEl = container.lastElementChild;
        setTimeout(() => { if (toastEl) toastEl.remove(); }, 5000);
    }

    // Chart Update Function
    function updateDashboardStats(stats) {
        if (!stats) return;

        // Update the stat numbers using IDs
        if (stats.total !== undefined) $('#totalCount').text(stats.total);
        if (stats.pending !== undefined) $('#pendingCount').text(stats.pending);
        if (stats.approved !== undefined) $('#approvedCount').text(stats.approved);
        if (stats.rejected !== undefined) $('#rejectedCount').text(stats.rejected);

        // Update chart
        if (statusChartInstance && stats.approved !== undefined && stats.pending !== undefined && stats.rejected !== undefined) {
            statusChartInstance.data.datasets[0].data = [stats.approved, stats.pending, stats.rejected];
            statusChartInstance.update();
        }
    }

    // Chart and Flash Messages Initialization
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('statusChart')?.getContext('2d');
        if (ctx) {
            statusChartInstance = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Approved', 'Pending', 'Rejected'],
                    datasets: [{ 
                        data: [{{ $approved ?? 0 }}, {{ $pending ?? 0 }}, {{ $rejected ?? 0 }}], 
                        backgroundColor: ['#10b981', '#f59e0b', '#ef4444'], 
                        borderWidth: 0, 
                        hoverOffset: 4 
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'right', labels: { boxWidth: 12, usePointStyle: true, font: { size: 12 } } } },
                    cutout: '75%'
                }
            });
        }

        @if (session('success')) showToast('success', "{{ session('success') }}"); @endif
        @if (session('failed')) showToast('danger', "{{ session('failed') }}"); @endif
    });

    $(document).ready(function() {
        // Global CSRF Header Setup for all jQuery AJAX Requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            }
        });

        // DataTable Initialization
        var table = $('#projectsTable').DataTable({
            processing: true,
            serverSide: true,
            searching: false,
            lengthChange: false,
            ajax: {
                url: "{{ route('dashboard.projects') }}",
                type: 'GET',
                data: function(d) {
                    d.status = $('#statusFilter').val();
                    @if(Auth::user()->role === 'admin') 
                        d.submitter = $('#submitterFilter').val(); 
                    @endif
                },
                dataSrc: function(json) {
                    
                    if (json.stats) {
                        updateDashboardStats(json.stats);
                    }
                    return json.data;
                },
                error: function(xhr) {
                    console.error('DataTable Fetch Error:', xhr.responseText);
                    showToast('danger', 'There was a problem loading the data.');
                }
            },
            columns: [
                @if(Auth::user()->role === 'admin')
                    { data: null, orderable: false, searchable: false, render: function(data, type, row) { 
                        return '<input class="form-check-input bulk-check" type="checkbox" value="' + row.id + '">'; 
                    }},
                @endif
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'title', name: 'title' },
                { data: 'submitter', name: 'submitter', orderable: false, searchable: false },
                { data: 'submitted_at', name: 'submitted_at' },
                { data: 'status', name: 'status' },
                { data: 'updated_at', name: 'updated_at' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center' }
            ],
            drawCallback: function() {
                $('#selectAll').prop('checked', false);
            }
        });

        // Filter Handlers
        $('#statusFilter, #submitterFilter').on('change', function() { 
            table.draw(); 
        });

        // Bulk Select All Checkbox Handler
        $('#selectAll').on('change', function() { 
            $('.bulk-check').prop('checked', this.checked); 
        });

        // 1. View Project Modal
        $(document).on('click', '.view-btn', function() {
            var id = $(this).data('id');
            $('#modalLoader').removeClass('d-none');
            $('#modalContent').addClass('d-none');
            $('#viewProjectModal').modal('show');

            $.ajax({
                url: "{{ url('/dashboard/projects') }}/" + id,
                type: 'GET',
                success: function(res) {
                    $('#viewTitle').text(res.title);
                    $('#viewSubmitter').html('<strong>' + (res.submitter_name || 'N/A') + '</strong><br><small class="text-muted">' + (res.submitter_email || '') + '</small>');
                    $('#viewSubmittedAt').text(res.submitted_at);
                    $('#viewDescription').text(res.description);
                    $('#viewUpdatedAt').text(res.updated_at);
                    
                    var badge = res.status === 'approved' ? 'bg-success' : res.status === 'pending' ? 'bg-warning text-dark' : 'bg-danger';
                    $('#viewStatus').html('<span class="badge ' + badge + ' px-3 py-2">' + (res.status ? res.status.toUpperCase() : '') + '</span>');
                    
                    @if(Auth::user()->role === 'admin') 
                        $('#viewHistoryBtn').data('id', res.id); 
                    @endif

                    $('#modalLoader').addClass('d-none');
                    $('#modalContent').removeClass('d-none');
                },
                error: function(xhr) {
                    console.error('View Details Error:', xhr.responseText);
                    $('#viewProjectModal').modal('hide');
                    showToast('danger', 'Project details load nahi ho payi.');
                }
            });
        });

        // 2. Single Approve Action
        $(document).on('click', '.approve-btn', function() {
            var btn = $(this);
            var id = btn.data('id');
            var originalText = btn.html();
            
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

            $.ajax({
                url: "{{ url('/projects') }}/" + id + "/approve",
                type: 'PATCH',
                success: function(res) {
                    showToast('success', res.message || 'Project approved.');
                    if (res.stats) updateDashboardStats(res.stats);
                    table.draw(false);
                },
                error: function(xhr) {
                    console.error('Approve Error:', xhr.responseText);
                    var msg = xhr.responseJSON && xhr.responseJSON.error ? xhr.responseJSON.error : 'Approval failed. Please try again.';
                    showToast('danger', msg);
                },
                complete: function() { 
                    btn.prop('disabled', false).html(originalText); 
                }
            });
        });

        // 3. Single Reject Modal Setup
        $(document).on('click', '.reject-btn', function() {
            var id = $(this).data('id');
            $('#rejectProjectId').val(id);
            $('#rejectReason').val('').removeClass('is-invalid');
            $('#rejectModal').modal('show');
        });

        // Single Reject Execute
        $('#confirmRejectBtn').off('click').on('click', function() {
            var id = $('#rejectProjectId').val();
            var reason = $('#rejectReason').val().trim();
            if (!reason) { 
                $('#rejectReason').addClass('is-invalid'); 
                return; 
            }
            
            var btn = $(this);
            var originalText = btn.html();
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Rejecting...');

            $.ajax({
                url: "{{ url('/projects') }}/" + id + "/reject",
                type: 'PATCH',
                data: { reason: reason },
                success: function(res) {
                    showToast('success', res.message || 'Project rejected.');
                    $('#rejectModal').modal('hide');
                    if (res.stats) updateDashboardStats(res.stats);
                    table.draw(false);
                },
                error: function(xhr) {
                    console.error('Reject Error:', xhr.responseText);
                    var msg = xhr.responseJSON && xhr.responseJSON.error ? xhr.responseJSON.error : 'Rejection failed.';
                    showToast('danger', msg);
                },
                complete: function() { 
                    btn.prop('disabled', false).html(originalText); 
                }
            });
        });

        // 4. Bulk Approve
        $('#bulkApproveBtn').off('click').on('click', function() {
            var ids = $('.bulk-check:checked').map(function() { return $(this).val(); }).get();
            if (ids.length === 0) { 
                showToast('warning', 'Please select at least one project.'); 
                return; 
            }
            
            if (!confirm(ids.length + ' Do you want to approve the selected projects?')) return;

            var btn = $(this);
            var originalText = btn.html();
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Approving...');

            $.ajax({
                url: "{{ route('projects.bulk.approve') }}",
                type: 'POST',
                data: { ids: ids },
                success: function(res) {
                    showToast('success', res.message || 'Selected projects approved.');
                    if (res.stats) updateDashboardStats(res.stats);
                    table.draw(false);
                },
                error: function(xhr) {
                    console.error('Bulk Approve Error:', xhr.responseText);
                    var msg = xhr.responseJSON && xhr.responseJSON.error ? xhr.responseJSON.error : 'Bulk approve failed.';
                    showToast('danger', msg);
                },
                complete: function() { 
                    btn.prop('disabled', false).html(originalText); 
                }
            });
        });

        // 5. Bulk Reject Open Modal
        $('#bulkRejectBtn').off('click').on('click', function() {
            var ids = $('.bulk-check:checked').map(function() { return $(this).val(); }).get();
            if (ids.length === 0) { 
                showToast('warning', 'Please select at least one project.'); 
                return; 
            }
            $('#bulkRejectCount').text(ids.length);
            $('#bulkRejectReason').val('');
            $('#bulkRejectModal').data('ids', ids).modal('show');
        });

        // Bulk Reject Confirm
        $('#confirmBulkRejectBtn').off('click').on('click', function() {
            var ids = $('#bulkRejectModal').data('ids') || [];
            var reason = $('#bulkRejectReason').val().trim();
            if (!reason) { 
                showToast('warning', 'Reason is required.'); 
                return; 
            }

            var btn = $(this);
            var originalText = btn.html();
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Rejecting...');

            $.ajax({
                url: "{{ route('projects.bulk.reject') }}",
                type: 'POST',
                data: { ids: ids, reason: reason },
                success: function(res) {
                    showToast('success', res.message || 'Selected projects have been rejected.');
                    $('#bulkRejectModal').modal('hide');
                    if (res.stats) updateDashboardStats(res.stats);
                    table.draw(false);
                },
                error: function(xhr) {
                    console.error('Bulk Reject Error:', xhr.responseText);
                    var msg = xhr.responseJSON && xhr.responseJSON.error ? xhr.responseJSON.error : 'Bulk reject failed.';
                    showToast('danger', msg);
                },
                complete: function() { 
                    btn.prop('disabled', false).html(originalText); 
                }
            });
        });

        // 6. Project History Modal
        @if(Auth::user()->role === 'admin')
        $(document).on('click', '.history-btn, #viewHistoryBtn', function() {
            var id = $(this).data('id');
            if (!id) return;
            
            $('#viewProjectModal').modal('hide');
            $('#historyLoader').removeClass('d-none');
            $('#historyContent').addClass('d-none');
            $('#historyModal').modal('show');

            $.ajax({
                url: "{{ url('/projects') }}/" + id + "/history",
                type: 'GET',
                success: function(data) {
                    var list = $('#historyList');
                    list.empty();
                    
                    if (!data || data.length === 0) {
                        list.append('<li class="list-group-item text-muted text-center">No history records found.</li>');
                    } else {
                        data.forEach(function(item) {
                            list.append(
                                '<li class="list-group-item d-flex justify-content-between align-items-center">' +
                                    '<span><span class="badge bg-secondary me-2">' + item.action + '</span> by ' + item.user + '</span>' +
                                    '<span class="text-muted small">' + item.time + '</span>' +
                                '</li>'
                            );
                        });
                    }
                    $('#historyLoader').addClass('d-none');
                    $('#historyContent').removeClass('d-none');
                },
                error: function(xhr) {
                    console.error('History Fetch Error:', xhr.responseText);
                    showToast('danger', 'History load nahi ho payi.');
                    $('#historyModal').modal('hide');
                }
            });
        });
        @endif
    });
</script>
</body>
</html>