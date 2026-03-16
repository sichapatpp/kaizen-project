@extends('layouts.base')

@section('content')

<!-- ── PAGE WRAPPER ── -->
<div class="kaizen-wrapper">
  <div class="kaizen-inner" style="max-width: 1400px;">

    <!-- Header -->
    <div class="kaizen-header" style="margin-bottom: 24px;">
      <div>
        <div class="topbar-title">ติดตามสถานะกิจกรรม</div>
        <div class="topbar-sub">ตรวจสอบความคืบหน้าและสถานะของกิจกรรม</div>
      </div>
      @if(auth()->user()->role->role_name == 'manager' || auth()->user()->role->role_name == 'chairman')
      <div>
        <a href="{{ route('activities.approve') }}" class="btn-primary" style="text-decoration: none;">
          ✅ อนุมัติกิจกรรม
        </a>
      </div>
      @endif
    </div>

    <!-- Filter & Search Section -->
    <div class="filter-section">
      <div class="filter-grid">
        <!-- ค้นหา -->
        <div class="filter-item" style="grid-column: span 2;">
          <label>🔍 ค้นหา</label>
          <input type="text" id="searchInput" placeholder="ค้นหาชื่อกิจกรรม, ผู้ยื่น..." oninput="filterActivities()" />
        </div>

        <!-- สถานะ -->
        <div class="filter-item">
          <label>📊 สถานะ</label>
          <select id="statusFilter" onchange="filterActivities()">
            <option value="">ทั้งหมด</option>
            <option value="pending">รอพิจารณา (รอบ 1)</option>
            <option value="in_progress">รอดำเนินการ (ช่วงรายงานผล)</option>
            <option value="waiting_for_manager_result_approval">รอหัวหน้าอนุมัติผล (รอบ 2)</option>
            <option value="waiting_for_chairman_approval">รอประธานอนุมัติ</option>
            <option value="completed">เสร็จสิ้น</option>
            <option value="rejected">ไม่อนุมัติ</option>
          </select>
        </div>

        <!-- ประเภท -->
        <div class="filter-item">
          <label>🏷️ ประเภท</label>
          <select id="typeFilter" onchange="filterActivities()">
            <option value="">ทั้งหมด</option>
            <option value="increase_revenue">เพิ่มรายได้</option>
            <option value="reduce_expenses">ลดรายจ่าย</option>
            <option value="reduce_steps">ลดขั้นตอน</option>
            <option value="reduce_time">ลดเวลาการทำงาน</option>
            <option value="improve_quality">ปรับปรุงคุณภาพ</option>
            <option value="reduce_risk">ลดความเสี่ยง</option>
            <option value="maintain_image">รักษาภาพลักษณ์</option>
            <option value="innovation">นวัตกรรม</option>
            <option value="new_service">เปิดบริการใหม่</option>
          </select>
        </div>
      </div>

      <!-- Active Filters -->
      <div id="activeFilters" class="active-filters" style="display: none;"></div>
    </div>

    <!-- Activities Table -->
    <div class="kaizen-card" style="padding: 0; overflow: hidden;">
      <div class="table-header">
        <h3 style="margin: 0;">รายการกิจกรรม</h3>
        <div class="table-info">
          <span id="filteredCount">0</span> รายการ
        </div>
      </div>

      <div class="table-container">
        <table class="kaizen-table" id="activitiesTable">
          <thead>
            <tr>
              <th style="width: 50px;">ลำดับ</th>
              <th style="width: 200px;">ชื่อกิจกรรม</th>
              <th style="width: 130px;">สถานะ</th>
              <th>ประเภท</th>
              <th style="width: 120px;">ผู้ยื่น</th>
              <th style="width: 120px; text-align: center;">จัดการ</th>
            </tr>
          </thead>
          <tbody id="activitiesTableBody">
            <!-- Data will be populated by JavaScript -->
          </tbody>
        </table>
      </div>

      <!-- Empty State -->
      <div id="emptyState" class="empty-state" style="display: none;">
        <div class="empty-icon">🔍</div>
        <div class="empty-title">ไม่พบข้อมูล</div>
        <div class="empty-text">ไม่พบกิจกรรมที่ตรงกับเงื่อนไขการค้นหา</div>
        <button class="btn-clear-filters" onclick="clearAllFilters()">ล้างตัวกรอง</button>
      </div>

      <!-- Pagination -->
      <div class="table-footer" id="tableFooter">
        <div class="pagination-info">
          แสดง <span id="showingFrom">0</span>-<span id="showingTo">0</span> จาก <span id="totalRecords">0</span> รายการ
        </div>
      </div>
    </div>

  </div>
</div>

<!-- ══════════════════════════════════════════
     Detail Modal
════════════════════════════════════════════ -->
<div class="modal-overlay" id="detailModal">
  <div class="modal-container">
    <div class="modal-header">
      <div>
        <h2 id="modalTitle" style="margin: 0; font-size: 20px; font-weight: 700; color: #1e293b;">รายละเอียดกิจกรรม</h2>
        <p id="modalSubtitle" style="margin: 4px 0 0 0; font-size: 14px; color: #64748b;"></p>
      </div>
      <button class="btn-close-modal" id="btnCloseDetailModal">✕</button>
    </div>

    <div class="modal-body">

      <!-- Status Badge -->
      <div style="margin-bottom: 24px;">
        <span id="modalStatus" class="status-badge status-pending">
          <span class="dot"></span>รอพิจารณา
        </span>
      </div>

      <!-- Activity Info Grid -->
      <div class="detail-grid">
        <div class="detail-item">
          <div class="detail-label">📋 ชื่อกิจกรรม</div>
          <div class="detail-value" id="detailActivityName">—</div>
        </div>
        <div class="detail-item">
          <div class="detail-label">🏷️ ประเภทการปรับปรุง</div>
          <div class="detail-value" id="detailTypes">—</div>
        </div>
        <div class="detail-item">
          <div class="detail-label">👤 ผู้ยื่นกิจกรรม</div>
          <div class="detail-value" id="detailSubmitter">—</div>
        </div>
        <div class="detail-item">
          <div class="detail-label">📊 สถานะ</div>
          <div class="detail-value" id="detailStatus">—</div>
        </div>
      </div>

      <hr class="modal-divider" />

      <!-- ส่วนที่ 1: ปัญหาที่พบ -->
      <div class="detail-section">
        <div class="section-title">❗ ปัญหาที่พบ</div>
        <div class="section-content" id="detailProblem">—</div>
        <div class="image-gallery" id="problemImages"></div>
      </div>

      <hr class="modal-divider" />

      <!-- ส่วนที่ 2: แนวทางการปรับปรุง -->
      <div class="detail-section">
        <div class="section-title">💡 แนวทางการปรับปรุง</div>
        <div class="section-content" id="detailSolution">—</div>
        <div class="image-gallery" id="solutionImages"></div>
      </div>

      <hr class="modal-divider" />

      <!-- ส่วนที่ 3: ผลที่คาดว่าจะได้รับ -->
      <div class="detail-section">
        <div class="section-title">🎯 ผลที่คาดว่าจะได้รับ</div>
        <div class="section-content" id="detailResult">—</div>
        <div class="image-gallery" id="resultImages"></div>
      </div>

      <hr class="modal-divider" />

      <!-- ส่วนที่ 4: รูปภาพประกอบผลงาน -->
      <div class="detail-section">
        <div class="section-title">🖼️ รูปภาพประกอบผลงาน</div>
        <div class="image-gallery" id="actualImages">
          <span id="actualImagesEmpty" style="color:#94a3b8; font-size:13px;">ยังไม่มีรูปภาพประกอบผลงาน</span>
        </div>
      </div>

      <!-- ══ ส่วนผลการดำเนินงาน (รอบที่ 2) ══ -->
      <div id="reportSection" style="display: none;">
        <hr class="modal-divider" />
        <div class="detail-section">
          <div class="section-title">📊 ผลการดำเนินงาน</div>

          <!-- ผลลัพธ์ที่เกิดขึ้นจริง -->
          <div id="actualResultBlock" style="display:none; margin-top:14px;">
            <div style="font-size:12px; font-weight:600; color:#64748b; margin-bottom:4px; text-transform:uppercase; letter-spacing:.5px;">ผลลัพธ์ที่เกิดขึ้นจริง</div>
            <div class="section-content" id="detailActualResult" style="background:#f0fdf4; border-left:3px solid #22c55e; padding:10px 14px; border-radius:0 6px 6px 0;">—</div>
          </div>

          <!-- รายละเอียดการดำเนินงานเพิ่มเติม -->
          <div id="performanceDetailBlock" style="display:none; margin-top:14px;">
            <div style="font-size:12px; font-weight:600; color:#64748b; margin-bottom:4px; text-transform:uppercase; letter-spacing:.5px;">รายละเอียดการดำเนินงานเพิ่มเติม</div>
            <div class="section-content" id="detailPerformanceDetail" style="background:#f8fafc; padding:10px 14px; border-radius:6px; border:1px solid #e2e8f0;">—</div>
          </div>

          <!-- ตัวชี้วัด -->
          <div id="indicatorsBlock" style="display:none; margin-top:14px;">
            <div style="font-size:12px; font-weight:600; color:#64748b; margin-bottom:8px; text-transform:uppercase; letter-spacing:.5px;">ตัวชี้วัด (Performance Indicators)</div>
            <div style="border:1px solid #e2e8f0; border-radius:10px; overflow:hidden;">
              <div style="display:grid; grid-template-columns:2.2fr 1fr 1fr 1.2fr 1fr; gap:8px; background:#f8fafc; padding:10px 14px; border-bottom:1px solid #e2e8f0; font-size:11px; font-weight:700; color:#475569; text-align:center;">
                <div style="text-align:left;">ชื่อตัวชี้วัด</div>
                <div>ก่อน</div>
                <div>หลัง</div>
                <div>ผลต่าง (%)</div>
                <div>หน่วย</div>
              </div>
              <div id="detailIndicatorRows"></div>
            </div>
          </div>

          <!-- งบประมาณ + เป้าหมาย -->
          <div id="budgetAchievedBlock" style="display:none; margin-top:14px; grid-template-columns:1fr 1fr; gap:12px;">
            <div style="background:#f8fafc; padding:12px 16px; border-radius:8px; border:1px solid #e2e8f0;">
              <div style="font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:.5px; margin-bottom:4px;">งบประมาณที่ใช้จริง</div>
              <div id="detailBudgetUsed" style="font-size:16px; font-weight:700; color:#1e293b;">—</div>
            </div>
            <div id="achievedBox" style="padding:12px 16px; border-radius:8px; border:1px solid #e2e8f0;">
              <div style="font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:.5px; margin-bottom:4px;">เป้าหมาย</div>
              <div id="detailAchieved" style="font-size:14px; font-weight:600;">—</div>
            </div>
          </div>

          <!-- เหตุผลที่ไม่บรรลุ -->
          <div id="notAchievedBlock" style="display:none; margin-top:12px;">
            <div style="font-size:12px; font-weight:600; color:#64748b; margin-bottom:4px; text-transform:uppercase; letter-spacing:.5px;">เหตุผลที่ไม่บรรลุเป้าหมาย</div>
            <div class="section-content" id="detailNotAchievedDetail" style="background:#fff7ed; border-left:3px solid #f97316; padding:10px 14px; border-radius:0 6px 6px 0;">—</div>
          </div>

        </div>
      </div>
      <!-- ══ จบส่วนผลการดำเนินงาน ══ -->

      <hr class="modal-divider" />

      <!-- Collaborators Section -->
      <div class="detail-section">
        <div class="section-title">👥 ผู้ร่วมงาน</div>
        <div id="detailCollaborators" class="collaborators-list">—</div>
      </div>

      <!-- Reviews Section -->
      <div id="reviewsSection" style="margin-top: 16px;">
        <div class="section-title">💬 ความเห็นจากผู้อนุมัติ</div>
        <div id="detailReviews"></div>
      </div>

    </div><!-- /.modal-body -->

    <div class="modal-footer">
      <a id="modalReportBtn" href="#"
         style="display:none; background:#f59e0b; color:white; text-decoration:none;
                padding:8px 20px; border-radius:8px; font-size:14px; font-weight:600;
                align-items:center; gap:6px;">
        📝 กรอกรายงานผล
      </a>
      <button class="btn-secondary" onclick="closeDetailModal()">ปิด</button>
    </div>
  </div>
</div>

<!-- Toast -->
<div class="kaizen-toast" id="toast">
  <span class="toast-icon">✅</span>
  <span id="toast-msg">ดำเนินการสำเร็จ</span>
</div>

<script>
const activitiesData = @json($activitiesData);
const currentUserId  = {{ auth()->id() }};

const statusLabels = {
  draft:                              'ฉบับร่าง',
  pending:                            'รอพิจารณา',
  in_progress:                        'รอดำเนินการ',
  waiting_for_manager_result_approval:'รอหัวหน้าอนุมัติผล',
  waiting_for_chairman_approval:      'รอประธานอนุมัติ',
  completed:                          'เสร็จสิ้น',
  rejected:                           'ไม่อนุมัติ'
};

const typeLabels = {
  increase_revenue: 'เพิ่มรายได้',
  reduce_expenses:  'ลดรายจ่าย',
  reduce_steps:     'ลดขั้นตอน',
  reduce_time:      'ลดเวลาการทำงาน',
  improve_quality:  'ปรับปรุงคุณภาพ',
  reduce_risk:      'ลดความเสี่ยง',
  maintain_image:   'รักษาภาพลักษณ์/ชื่อเสียงองค์กร',
  innovation:       'สิ่งประดิษฐ์/นวัตกรรม',
  new_service:      'เปิดบริการใหม่',
  others:           'อื่นๆ'
};

const REPORT_STATUSES = [
  'in_progress',
  'waiting_for_manager_result_approval',
  'waiting_for_chairman_approval',
  'completed'
];

let filteredData = [...activitiesData];

/* ══════════════════════════════════════════
   INIT
════════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', function () {
  renderTable();

  document.addEventListener('click', function (e) {
    const btn = e.target.closest('[data-action="view-detail"]');
    if (btn) {
      const id = parseInt(btn.getAttribute('data-id'), 10);
      if (!isNaN(id)) showDetail(id);
    }
  });

  const detailModal = document.getElementById('detailModal');
  if (detailModal) {
    detailModal.addEventListener('click', function (e) {
      if (e.target === this) closeDetailModal();
    });
  }

  const btnCloseModal = document.getElementById('btnCloseDetailModal');
  if (btnCloseModal) {
    btnCloseModal.addEventListener('click', closeDetailModal);
  }

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeDetailModal();
  });
});

/* ══════════════════════════════════════════
   RENDER TABLE
════════════════════════════════════════════ */
function renderTable() {
  const tbody       = document.getElementById('activitiesTableBody');
  const emptyState  = document.getElementById('emptyState');
  const tableFooter = document.getElementById('tableFooter');

  tbody.innerHTML = '';

  if (filteredData.length === 0) {
    emptyState.style.display  = 'block';
    tableFooter.style.display = 'none';
  } else {
    emptyState.style.display  = 'none';
    tableFooter.style.display = 'flex';

    filteredData.forEach((activity, index) => {
      const hasReport = REPORT_STATUSES.includes(activity.status);
      const canEdit   = (activity.status === 'draft' || activity.status === 'rejected') && activity.user_id === currentUserId;
      const isOwner   = activity.user_id === currentUserId;

      const row = document.createElement('tr');
      row.innerHTML = `
        <td style="color:#6b7280; font-weight:500; text-align:center;">${index + 1}</td>
        <td>
          <div class="activity-name"
               data-action="view-detail"
               data-id="${activity.id}"
               style="cursor:pointer; font-weight:500; color:#1e293b;">
            ${activity.name}
          </div>
        </td>
        <td>
          <span class="status-badge status-${activity.status}">
            <span class="dot"></span>${statusLabels[activity.status] || activity.status}
          </span>
        </td>
        <td>
          <div style="display:flex; flex-wrap:wrap; gap:3px;">
            ${activity.types.map(type =>
              `<span class="type-tag" style="font-size:11px; padding:2px 6px; white-space:nowrap;">${typeLabels[type] || type}</span>`
            ).join('')}
          </div>
        </td>
        <td style="color:#475569;">${activity.submitter}</td>
        <td style="white-space: nowrap; text-align: center;">
          <div style="display: inline-flex; gap: 6px; align-items:center; justify-content: center;">
            <button class="btn-action primary"
                    data-action="view-detail"
                    data-id="${activity.id}"
                    title="ดูรายละเอียด"
                    type="button">👁️</button>
            ${hasReport && isOwner && activity.status !== 'completed'
              ? `<a href="/activities/${activity.id}/report"
                    class="btn-action warning"
                    title="กรอกรายงานผล"
                    style="background:#f59e0b; color:white; border-color:#f59e0b; text-decoration:none;">📝</a>`
              : ''}
            ${canEdit
              ? `<a href="/activities/${activity.id}/edit"
                    class="btn-action primary"
                    title="แก้ไข"
                    style="text-decoration:none;">✏️</a>`
              : ''}
          </div>
        </td>
      `;
      tbody.appendChild(row);
    });
  }

  updatePaginationInfo();
}

/* ══════════════════════════════════════════
   SHOW DETAIL MODAL
════════════════════════════════════════════ */
function showDetail(id) {
  const activity = activitiesData.find(a => a.id === id);
  if (!activity) return;

  document.getElementById('modalTitle').textContent    = activity.name;
  document.getElementById('modalSubtitle').textContent = activity.code || '';
  document.getElementById('modalStatus').className     = `status-badge status-${activity.status}`;
  document.getElementById('modalStatus').innerHTML     = `<span class="dot"></span>${statusLabels[activity.status] || activity.status}`;

  document.getElementById('detailActivityName').textContent = activity.name;
  document.getElementById('detailTypes').innerHTML = activity.types.map(type =>
    `<span class="type-tag">${typeLabels[type] || type}</span>`
  ).join('');
  document.getElementById('detailSubmitter').textContent = activity.submitter;
  document.getElementById('detailStatus').textContent    = statusLabels[activity.status] || activity.status;

  document.getElementById('detailProblem').textContent  = activity.problem  || '—';
  document.getElementById('detailSolution').textContent = activity.solution || '—';
  document.getElementById('detailResult').textContent   = activity.result   || '—';

  renderImages('problemImages',  activity.problem_images);
  renderImages('solutionImages', activity.solution_images);
  renderImages('resultImages',   activity.result_images);

  const actualGallery = document.getElementById('actualImages');
  const actualEmpty   = document.getElementById('actualImagesEmpty');
  const actualImgs    = activity.actual_images || [];

  actualGallery.querySelectorAll('a').forEach(el => el.remove());

  if (actualImgs.length > 0) {
    actualEmpty.style.display = 'none';
    actualGallery.innerHTML += actualImgs.map(img =>
      `<a href="${img.url}" target="_blank">
         <img src="${img.url}" title="${img.name}"
              style="width:80px; height:80px; object-fit:cover; border-radius:6px; margin-right:5px; cursor:pointer;">
       </a>`
    ).join('');
  } else {
    actualEmpty.style.display = 'inline';
  }

  const reportSection  = document.getElementById('reportSection');
  const modalReportBtn = document.getElementById('modalReportBtn');
  const reportUrl      = `/activities/${activity.id}/report`;
  const hasReport      = REPORT_STATUSES.includes(activity.status);
  const isOwner        = activity.user_id === currentUserId;

  if (hasReport) {
    reportSection.style.display = 'block';

    const actualResultBlock = document.getElementById('actualResultBlock');
    if (activity.actual_result) {
      actualResultBlock.style.display = 'block';
      document.getElementById('detailActualResult').textContent = activity.actual_result;
    } else {
      actualResultBlock.style.display = 'none';
    }

    const perfBlock = document.getElementById('performanceDetailBlock');
    if (activity.performance_detail) {
      perfBlock.style.display = 'block';
      document.getElementById('detailPerformanceDetail').textContent = activity.performance_detail;
    } else {
      perfBlock.style.display = 'none';
    }

    const indicators = activity.indicators || [];
    const indBlock   = document.getElementById('indicatorsBlock');
    if (indicators.length > 0) {
      indBlock.style.display = 'block';
      document.getElementById('detailIndicatorRows').innerHTML = indicators.map(ind => {
        const before = parseFloat(ind.before_value);
        const after  = parseFloat(ind.after_value);
        let diffHtml = '<span style="color:#94a3b8;">—</span>';
        if (!isNaN(before) && !isNaN(after) && before !== 0) {
          const diff   = ((after - before) / Math.abs(before)) * 100;
          const color  = diff > 0 ? '#10b981' : diff < 0 ? '#ef4444' : '#64748b';
          const prefix = diff > 0 ? '+' : '';
          diffHtml = `<span style="color:${color}; font-weight:700;">${prefix}${diff.toFixed(1)}%</span>`;
        }
        return `
          <div style="display:grid; grid-template-columns:2.2fr 1fr 1fr 1.2fr 1fr; gap:8px; align-items:center;
                      padding:10px 14px; border-bottom:1px solid #f1f5f9; font-size:13px;">
            <div style="font-weight:500; color:#334155;">${ind.indicator_name || '—'}</div>
            <div style="text-align:center; color:#475569;">${ind.before_value ?? '—'}</div>
            <div style="text-align:center; color:#475569;">${ind.after_value ?? '—'}</div>
            <div style="text-align:center;">${diffHtml}</div>
            <div style="text-align:center; color:#64748b; font-size:12px;">${ind.unit || '—'}</div>
          </div>`;
      }).join('');
    } else {
      indBlock.style.display = 'none';
    }

    const budgetBlock     = document.getElementById('budgetAchievedBlock');
    const hasAchievedData = activity.is_achieved !== null && activity.is_achieved !== undefined;
    if (activity.status === 'completed' && (activity.budget_used || hasAchievedData)) {
      budgetBlock.style.display = 'grid';
      document.getElementById('detailBudgetUsed').textContent =
        activity.budget_used ? Number(activity.budget_used).toLocaleString('th-TH') + ' บาท' : '—';

      const achievedBox    = document.getElementById('achievedBox');
      const detailAchieved = document.getElementById('detailAchieved');
      if (activity.is_achieved == 1) {
        achievedBox.style.background  = '#f0fdf4';
        achievedBox.style.borderColor = '#86efac';
        detailAchieved.innerHTML = '<span style="color:#16a34a;">✅ บรรลุเป้าหมาย</span>';
      } else if (activity.is_achieved == 0) {
        achievedBox.style.background  = '#fff7ed';
        achievedBox.style.borderColor = '#fdba74';
        detailAchieved.innerHTML = '<span style="color:#ea580c;">⚠️ ไม่บรรลุเป้าหมาย</span>';
      } else {
        achievedBox.style.background  = '#f8fafc';
        achievedBox.style.borderColor = '#e2e8f0';
        detailAchieved.textContent = '—';
      }
    } else {
      budgetBlock.style.display = 'none';
    }

    const notAchievedBlock = document.getElementById('notAchievedBlock');
    if (activity.status === 'completed' && activity.is_achieved == 0 && activity.not_achieved_detail) {
      notAchievedBlock.style.display = 'block';
      document.getElementById('detailNotAchievedDetail').textContent = activity.not_achieved_detail;
    } else {
      notAchievedBlock.style.display = 'none';
    }

  } else {
    reportSection.style.display = 'none';
  }

  if (hasReport && isOwner && activity.status !== 'completed') {
    modalReportBtn.href          = reportUrl;
    modalReportBtn.style.display = 'inline-flex';
  } else {
    modalReportBtn.style.display = 'none';
  }

  const collabHtml = (activity.collaborators || []).map(collab => `
    <div class="collaborator-card">
      <div class="collaborator-info">
        <div class="collaborator-avatar">${collab.name.charAt(0)}</div>
        <div class="collaborator-name">${collab.name}</div>
      </div>
      <div class="collaborator-percent">${collab.percent}%</div>
    </div>
  `).join('');
  document.getElementById('detailCollaborators').innerHTML =
    collabHtml || '<span style="color:#94a3b8">ไม่มีผู้ร่วมงาน</span>';

  const reviews       = activity.reviews || [];
  const reviewSection = document.getElementById('reviewsSection');
  if (reviews.length === 0) {
    reviewSection.style.display = 'none';
  } else {
    reviewSection.style.display = 'block';
    const reviewHtml = reviews.map(r => {
      const isApprove   = r.action === 'approve';
      const approveWord = getReviewApproveLabel(r);
      const badge = isApprove
        ? `<span style="background:#dcfce7; color:#166534; padding:2px 10px; border-radius:12px; font-size:12px; font-weight:600;">✅ ${approveWord}</span>`
        : `<span style="background:#fee2e2; color:#991b1b; padding:2px 10px; border-radius:12px; font-size:12px; font-weight:600;">❌ ไม่อนุมัติ</span>`;
      return `
        <div style="padding:12px 14px; border:1px solid #e5e7eb; border-radius:8px; margin-bottom:10px; background:#f8fafc;">
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
            <div style="display:flex; align-items:center; gap:8px;">
              ${badge}
              <span style="font-size:13px; color:#374151; font-weight:500;">${r.reviewer}</span>
            </div>
            <span style="font-size:12px; color:#94a3b8;">${r.created_at}</span>
          </div>
          ${r.comment ? `<div style="font-size:13px; color:#4b5563; padding-top:4px; border-top:1px solid #e5e7eb; margin-top:6px;">"${r.comment}"</div>` : ''}
        </div>
      `;
    }).join('');
    document.getElementById('detailReviews').innerHTML = reviewHtml;
  }

  document.getElementById('detailModal').classList.add('show');
  document.body.style.overflow = 'hidden';
}

function getReviewApproveLabel(review) {
  if (review.review_round === 1) return 'เห็นชอบ';
  if (review.review_round === 2) return 'รับทราบ';
  if (review.review_round === 3) return 'รับทราบ';
  return 'เห็นชอบ';
}

function renderImages(containerId, images) {
  const container = document.getElementById(containerId);
  if (!container) return;
  if (images && images.length > 0) {
    container.innerHTML = images.map(img =>
      `<a href="${img.url}" target="_blank">
         <img src="${img.url}" title="${img.name}"
              style="width:80px; height:80px; object-fit:cover; border-radius:6px; margin-right:5px; cursor:pointer;">
       </a>`
    ).join('');
  } else {
    container.innerHTML = '';
  }
}

/* ══════════════════════════════════════════
   CLOSE MODAL
════════════════════════════════════════════ */
function closeDetailModal() {
  const modal = document.getElementById('detailModal');
  if (modal) modal.classList.remove('show');
  document.getElementById('modalTitle').textContent    = '';
  document.getElementById('modalSubtitle').textContent = '';
  document.getElementById('detailReviews').innerHTML   = '';
  document.body.style.overflow = '';
}

/* ══════════════════════════════════════════
   FILTER
════════════════════════════════════════════ */
function filterActivities() {
  const searchTerm   = document.getElementById('searchInput').value.toLowerCase().trim();
  const statusFilter = document.getElementById('statusFilter').value;
  const typeFilter   = document.getElementById('typeFilter').value;

  filteredData = activitiesData.filter(activity => {
    const matchSearch = !searchTerm ||
      activity.name.toLowerCase().includes(searchTerm) ||
      (activity.code || '').toLowerCase().includes(searchTerm) ||
      activity.submitter.toLowerCase().includes(searchTerm);
    const matchStatus = !statusFilter || activity.status === statusFilter;
    const matchType   = !typeFilter   || activity.types.includes(typeFilter);
    return matchSearch && matchStatus && matchType;
  });

  renderTable();
  updateActiveFilters();
}

function updateActiveFilters() {
  const container    = document.getElementById('activeFilters');
  const filters      = [];
  const searchTerm   = document.getElementById('searchInput').value;
  const statusFilter = document.getElementById('statusFilter').value;
  const typeFilter   = document.getElementById('typeFilter').value;

  if (searchTerm)   filters.push({ label: `ค้นหา: "${searchTerm}"`,              clear: 'search' });
  if (statusFilter) filters.push({ label: `สถานะ: ${statusLabels[statusFilter]}`, clear: 'status' });
  if (typeFilter)   filters.push({ label: `ประเภท: ${typeLabels[typeFilter]}`,    clear: 'type' });

  if (filters.length > 0) {
    container.style.display = 'flex';
    container.innerHTML = filters.map(f => `
      <div class="filter-chip">
        ${f.label}
        <span class="remove" onclick="clearFilter('${f.clear}')">✕</span>
      </div>
    `).join('') + `
      <div class="filter-chip" style="cursor:pointer; background:#fee2e2; color:#991b1b;" onclick="clearAllFilters()">
        ล้างทั้งหมด ✕
      </div>`;
  } else {
    container.style.display = 'none';
  }
}

function clearFilter(filterType) {
  if (filterType === 'search') document.getElementById('searchInput').value  = '';
  if (filterType === 'status') document.getElementById('statusFilter').value = '';
  if (filterType === 'type')   document.getElementById('typeFilter').value   = '';
  filterActivities();
}

function clearAllFilters() {
  document.getElementById('searchInput').value  = '';
  document.getElementById('statusFilter').value = '';
  document.getElementById('typeFilter').value   = '';
  filterActivities();
}

function updatePaginationInfo() {
  const count = filteredData.length;
  document.getElementById('filteredCount').textContent = count;
  document.getElementById('showingFrom').textContent   = count > 0 ? '1' : '0';
  document.getElementById('showingTo').textContent     = count;
  document.getElementById('totalRecords').textContent  = count;
}

function showToast(msg) {
  const toast = document.getElementById('toast');
  document.getElementById('toast-msg').textContent = msg;
  toast.classList.add('show');
  setTimeout(() => toast.classList.remove('show'), 2800);
}
</script>

@endsection