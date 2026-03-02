@extends('layouts.base')

@section('content')

    {{-- Greeting + Year Filter --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div class="greeting mb-0">
            <h1 class="mb-1">สวัสดี</h1>
            <p class="mb-0">ยินดีต้อนรับสู่ระบบจัดการกิจกรรมไคเซ็น</p>
        </div>
        <div class="year-filter">
            <select
                id="fiscalYearSelect"
                class="form-select border-0 shadow-sm"
                style="border-radius:8px;min-width:180px;cursor:pointer;background-color:white;font-weight:500;color:#495057;padding:10px 15px;"
                onchange="window.location.href='?year='+this.value"
            >
                @foreach($years as $year)
                    <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                        ปีงบประมาณ {{ $year }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="stat-cards">
        <div class="stat-card">
            <div class="stat-card-left">
                <div class="stat-label">งานทั้งหมด</div>
                <div class="stat-value blue">{{ $counts['total'] ?? 0 }}</div>
            </div>
            <div class="stat-icon-wrap blue"><i class="fas fa-file-alt"></i></div>
        </div>
        <div class="stat-card">
            <div class="stat-card-left">
                <div class="stat-label">รออนุมัติ</div>
                <div class="stat-value orange">{{ $counts['pending'] ?? 0 }}</div>
            </div>
            <div class="stat-icon-wrap orange"><i class="fas fa-clock"></i></div>
        </div>
        <div class="stat-card">
            <div class="stat-card-left">
                <div class="stat-label">ถูกปฏิเสธ</div>
                <div class="stat-value red">{{ $counts['rejected'] ?? 0 }}</div>
            </div>
            <div class="stat-icon-wrap red"><i class="fas fa-times-circle"></i></div>
        </div>
        <div class="stat-card">
            <div class="stat-card-left">
                <div class="stat-label">อนุมัติแล้ว</div>
                <div class="stat-value green">{{ $counts['approved'] ?? 0 }}</div>
            </div>
            <div class="stat-icon-wrap green"><i class="fas fa-check-circle"></i></div>
        </div>
    </div>

    {{-- กราฟ + ตารางกิจกรรม --}}
    <div class="row g-4 mt-1">

        {{-- กราฟวงกลมประเภทการปรับปรุง --}}
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm h-100" style="border-radius:14px;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-1" style="color:#333;">
                        <i class="fas fa-chart-pie me-2 text-primary"></i>ประเภทการปรับปรุง
                    </h5>
                    <p class="text-muted small mb-3">คลิกที่กราฟเพื่อดูรายชื่อกิจกรรม</p>
                    <div style="position:relative;max-width:280px;margin:0 auto;">
                        <canvas id="typeChart" style="cursor:pointer;"></canvas>
                    </div>
                    <div id="chartEmpty" class="text-center py-5 text-muted d-none">
                        <i class="fas fa-chart-pie" style="font-size:2rem;opacity:.3;"></i>
                        <div class="mt-2">ไม่มีข้อมูลในปีนี้</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ตารางรายชื่อกิจกรรม --}}
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm h-100" style="border-radius:14px;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h5 class="fw-bold mb-0" style="color:#333;">
                                <i class="fas fa-list-ul me-2 text-success"></i>
                                <span id="activityListTitle">กิจกรรมทั้งหมด ปีงบประมาณ {{ $selectedYear }}</span>
                            </h5>
                            <p class="text-muted small mb-0 mt-1" id="activityListSubtitle">
                                แสดง {{ $allActivities->count() }} รายการ
                            </p>
                        </div>
                        <button id="resetFilterBtn"
                            class="btn btn-sm btn-outline-secondary d-none"
                            onclick="resetFilter()"
                            style="border-radius:8px;">
                            <i class="fas fa-times me-1"></i>ล้างตัวกรอง
                        </button>
                    </div>

                    <div style="max-height:320px;overflow-y:auto;">
                        @if($allActivities->count() > 0)
                            <table class="table table-hover table-sm mb-0">
                                <thead style="position:sticky;top:0;background:white;z-index:1;">
                                    <tr style="font-size:.82rem;color:#888;border-bottom:2px solid #f0f0f0;">
                                        <th class="fw-semibold pb-2" style="width:40px;">#</th>
                                        <th class="fw-semibold pb-2">ชื่อกิจกรรม</th>
                                        <th class="fw-semibold pb-2">ผู้ยื่น</th>
                                    </tr>
                                </thead>
                                <tbody id="activityTableBody">
                                    @foreach($allActivities as $i => $project)
                                        <tr style="font-size:.85rem;">
                                            <td class="text-muted">{{ $i + 1 }}</td>
                                            <td><span class="fw-medium">{{ $project->title ?? '(ไม่มีชื่อ)' }}</span></td>
                                            <td class="text-muted">{{ $project->user->name ?? 'ไม่ระบุ' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="text-center py-5 text-muted">
                                <i class="fas fa-inbox" style="font-size:2rem;opacity:.3;"></i>
                                <div class="mt-2">ไม่มีกิจกรรมในปีงบประมาณนี้</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ผลงาน Kaizen เด่น --}}
    <div class="featured-section mt-4">
        <div class="featured-header">
            <div class="featured-header-left">
                <i class="fas fa-lightbulb trophy-icon"></i>
                <h2>ผลงาน Kaizen เด่น</h2>
            </div>
           <!-- <a href="#" class="view-all">ดูทั้งหมด <i class="fas fa-arrow-right"></i></a> -->
        </div>
        <div class="project-cards">
            @forelse($featuredProjects as $project)
                <div class="project-card">
                    <div class="project-card-img img-placeholder-1">
                        <div class="bars">
                            <div class="bar" style="height:40px"></div>
                            <div class="bar" style="height:65px"></div>
                            <div class="bar" style="height:90px"></div>
                        </div>
                    </div>
                    <div class="project-card-body">
                        <h3>{{ $project->title }}</h3>
                        <div class="project-meta">
                            <i class="fas fa-user-circle me-1"></i> ผู้ยื่น: {{ $project->user->name ?? 'ไม่ระบุ' }}
                        </div>
                        <div class="project-result">
                            <span class="check-icon"><i class="fas fa-check"></i></span>
                            {{ $project->result }}
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty-state" style="grid-column:1/-1;text-align:center;padding:40px 20px;">
                    <div class="empty-icon text-muted" style="font-size:2.5rem;margin-bottom:15px;">📂</div>
                    <div class="empty-title" style="font-size:1.1rem;font-weight:600;">ไม่มีข้อมูลกิจกรรมในปีงบประมาณนี้</div>
                    <div class="empty-text text-muted">เมื่อมีกิจกรรมที่เสร็จสิ้นและบรรลุเป้าหมาย จะแสดงที่นี่</div>
                </div>
            @endforelse
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
    const typeCounts   = @json($typeCounts);
    const typeProjects = @json($typeProjects);
    const selectedYear = {{ $selectedYear }};

    const chartLabels = Object.keys(typeCounts);
    const chartData   = Object.values(typeCounts);

    const barColors = [
        '#4f86f7','#f7a44f','#4fc97e','#f75c5c',
        '#a44ff7','#f7e24f','#4fd5f7','#f74fa4',
        '#7bc67e','#ff9966',
    ];

    let originalRows = null;
    let typeChart    = null;

    if (chartLabels.length > 0) {
        const ctx = document.getElementById('typeChart').getContext('2d');
        typeChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: chartLabels,
                datasets: [{
                    data: chartData,
                    backgroundColor: chartLabels.map((_, i) => barColors[i % barColors.length] + 'cc'),
                    borderColor:     chartLabels.map((_, i) => barColors[i % barColors.length]),
                    borderWidth: 2,
                    hoverOffset: 12,
                }]
            },
            options: {
                responsive: true,
                cutout: '60%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 12,
                            font: { size: 11 },
                            boxWidth: 12,
                            boxHeight: 12,
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: ctx => ` ${ctx.label}: ${ctx.parsed} กิจกรรม`
                        }
                    }
                },
                onClick: (e) => {
                    const points = typeChart.getElementsAtEventForMode(e, 'nearest', { intersect: true }, true);
                    if (points.length > 0) {
                        filterByType(chartLabels[points[0].index]);
                    }
                }
            }
        });
    } else {
        document.getElementById('typeChart').classList.add('d-none');
        document.getElementById('chartEmpty').classList.remove('d-none');
    }

    function filterByType(typeName) {
        const projects = typeProjects[typeName] || [];
        const tbody    = document.getElementById('activityTableBody');
        const title    = document.getElementById('activityListTitle');
        const subtitle = document.getElementById('activityListSubtitle');
        const resetBtn = document.getElementById('resetFilterBtn');

        if (!originalRows) originalRows = tbody.innerHTML;

        title.textContent    = `ประเภท: ${typeName}`;
        subtitle.textContent = `แสดง ${projects.length} รายการ`;
        resetBtn.classList.remove('d-none');

        if (projects.length === 0) {
            tbody.innerHTML = `
                <tr><td colspan="3" class="text-center text-muted py-4">
                    <i class="fas fa-inbox me-2"></i>ไม่มีกิจกรรมในประเภทนี้
                </td></tr>`;
        } else {
            tbody.innerHTML = projects.map((p, i) => `
                <tr style="font-size:.85rem;">
                    <td class="text-muted">${i + 1}</td>
                    <td><span class="fw-medium">${p.title || '(ไม่มีชื่อ)'}</span></td>
                    <td class="text-muted">${p.user}</td>
                </tr>`).join('');
        }
    }

    function resetFilter() {
        const tbody    = document.getElementById('activityTableBody');
        const title    = document.getElementById('activityListTitle');
        const subtitle = document.getElementById('activityListSubtitle');
        const resetBtn = document.getElementById('resetFilterBtn');

        if (originalRows) tbody.innerHTML = originalRows;
        title.textContent    = `กิจกรรมทั้งหมด ปีงบประมาณ ${selectedYear}`;
        subtitle.textContent = `แสดง {{ $allActivities->count() }} รายการ`;
        resetBtn.classList.add('d-none');
        originalRows = null;
    }
    </script>

@endsection