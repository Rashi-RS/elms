<div class="mb-4">
    <h3 class="fw-bold mb-0 text-dark">Analytics & Enterprise Reports</h3>
    <p class="text-muted small">Generate exportable audit-ready operational summaries in CSV and print formats</p>
</div>

<div class="row g-4">
    <!-- Attendance Report Card -->
    <div class="col-md-4">
        <div class="card h-100 p-4 text-center">
            <div class="kpi-icon bg-success bg-opacity-10 text-success mx-auto mb-3" style="width: 58px; height: 58px; font-size: 1.8rem;">
                <i class="bi bi-clock-history"></i>
            </div>
            <h5 class="fw-bold text-dark mb-1">Attendance Report</h5>
            <p class="text-muted small mb-4">Historical attendance records, working hours, punctuality tracking, and absence audits by department or date range.</p>
            <div class="mt-auto">
                <a href="<?= \App\helpers\ViewHelper::url('admin/reports/attendance') ?>" class="btn btn-primary w-100 btn-sm">
                    Generate Attendance Report &rarr;
                </a>
            </div>
        </div>
    </div>

    <!-- Leave Report Card -->
    <div class="col-md-4">
        <div class="card h-100 p-4 text-center">
            <div class="kpi-icon bg-primary bg-opacity-10 text-primary mx-auto mb-3" style="width: 58px; height: 58px; font-size: 1.8rem;">
                <i class="bi bi-calendar2-range"></i>
            </div>
            <h5 class="fw-bold text-dark mb-1">Leave Utilization Report</h5>
            <p class="text-muted small mb-4">Leave application history, category breakdowns, approval ratios, and balance consumption trends.</p>
            <div class="mt-auto">
                <a href="<?= \App\helpers\ViewHelper::url('admin/reports/leaves') ?>" class="btn btn-primary w-100 btn-sm">
                    Generate Leave Report &rarr;
                </a>
            </div>
        </div>
    </div>

    <!-- Employee Directory Report Card -->
    <div class="col-md-4">
        <div class="card h-100 p-4 text-center">
            <div class="kpi-icon bg-warning bg-opacity-10 text-warning mx-auto mb-3" style="width: 58px; height: 58px; font-size: 1.8rem;">
                <i class="bi bi-people-fill"></i>
            </div>
            <h5 class="fw-bold text-dark mb-1">Employee Directory Report</h5>
            <p class="text-muted small mb-4">Comprehensive headcount census, department distribution, salary payroll overview, and tenure records.</p>
            <div class="mt-auto">
                <a href="<?= \App\helpers\ViewHelper::url('admin/reports/employees') ?>" class="btn btn-primary w-100 btn-sm">
                    Generate Employee Report &rarr;
                </a>
            </div>
        </div>
    </div>
</div>
