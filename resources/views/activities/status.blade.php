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
            <option value="draft">ฉบับร่าง</option>
            <option value="pending">รอพิจารณา</option>
            <option value="approved">อนุมัติ</option>
            <option value="in_progress">กำลังดำเนินการ</option>
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
              <th style="min-width: 250px;">ชื่อกิจกรรม</th>
              <th style="width: 140px;">สถานะ</th>
              <th style="width: 200px;">ประเภท</th>
              <th style="width: 150px;">ผู้ยื่น</th>
              <th style="width: 120px;">จัดการ</th>
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

<!-- Detail Modal -->
<div class="modal-overlay" id="detailModal" onclick="closeModal(event)">
  <div class="modal-container" onclick="event.stopPropagation()">
    <div class="modal-header">
      <div>
        <h2 id="modalTitle" style="margin: 0; font-size: 20px; font-weight: 700; color: #1e293b;">รายละเอียดกิจกรรม</h2>
        <p id="modalSubtitle" style="margin: 4px 0 0 0; font-size: 14px; color: #64748b;">กิจกรรม #KZ-2026-001</p>
      </div>
      <button class="btn-close-modal" onclick="closeDetailModal()">✕</button>
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

      <!-- Problem Section -->
      <div class="detail-section">
        <div class="section-title">❗ ปัญหาที่พบ</div>
        <div class="section-content" id="detailProblem">—</div>
        <!-- Gallery สำหรับแสดงรูป -->
        <div class="image-gallery" id="problemImages"></div>
      </div>

      <hr class="modal-divider" />

      <!-- Solution Section -->
      <div class="detail-section">
        <div class="section-title">💡 แนวทางการปรับปรุง</div>
        <div class="section-content" id="detailSolution">—</div>
        <div class="image-gallery" id="solutionImages"></div>
      </div>

      <hr class="modal-divider" />

      <!-- Result Section -->
      <div class="detail-section">
        <div class="section-title">🎯 ผลที่คาดว่าจะได้รับ</div>
        <div class="section-content" id="detailResult">—</div>
        <div class="image-gallery" id="resultImages"></div>
      </div>

      <hr class="modal-divider" />

      <!-- Collaborators Section -->
      <div class="detail-section">
        <div class="section-title">👥 ผู้ร่วมงาน</div>
        <div id="detailCollaborators" class="collaborators-list">—</div>
      </div>
    </div>

    <div class="modal-footer">
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


const statusLabels = {
  draft: 'ฉบับร่าง',
  pending: 'รอพิจารณา',
  approved: 'อนุมัติ',
  in_progress: 'กำลังดำเนินการ',
  waiting_for_result_approval: 'รออนุมัติผล',
  waiting_for_chairman_approval: 'รอประธานอนุมัติ',
  completed: 'เสร็จสิ้น',
  rejected: 'ไม่อนุมัติ'
};

const typeLabels = {
  increase_revenue: 'เพิ่มรายได้',
  reduce_expenses: 'ลดรายจ่าย',
  reduce_steps: 'ลดขั้นตอน',
  reduce_time: 'ลดเวลาการทำงาน',
  improve_quality: 'ปรับปรุงคุณภาพ',
  reduce_risk: 'ลดความเสี่ยง',
  maintain_image: 'รักษาภาพลักษณ์',
  innovation: 'นวัตกรรม',
  new_service: 'เปิดบริการใหม่'
};

let filteredData = [...activitiesData];

document.addEventListener('DOMContentLoaded', function() {
  renderTable();
});

function renderTable() {
  const tbody = document.getElementById('activitiesTableBody');
  const emptyState = document.getElementById('emptyState');
  const tableFooter = document.getElementById('tableFooter');
  
  tbody.innerHTML = '';

  if (filteredData.length === 0) {
    emptyState.style.display = 'block';
    tableFooter.style.display = 'none';
  } else {
    emptyState.style.display = 'none';
    tableFooter.style.display = 'flex';

    filteredData.forEach((activity, index) => {
      const row = document.createElement('tr');
      row.innerHTML = `
        <td style="color: #6b7280; font-weight: 500;">${index + 1}</td>
        <td>
          <div class="activity-name" onclick="showDetail(${activity.id})">${activity.name}</div>
        </td>
        <td>
          <span class="status-badge status-${activity.status}">
            <span class="dot"></span>${statusLabels[activity.status] || activity.status}
          </span>
        </td>
        <td>
          ${activity.types.map(type => `<span class="type-tag">${typeLabels[type] || type}</span>`).join('')}
        </td>
        <td>${activity.submitter}</td>
        <td>
          <div class="action-buttons">
            <button class="btn-action primary" onclick="showDetail(${activity.id})" title="ดูรายละเอียด">
              👁️ 
            </button>
            ${activity.status === 'in_progress' ? 
              `<a href="/activities/${activity.id}/report" class="btn-action warning" title="รายงานผล" style="background:#f59e0b; color:white; text-decoration:none; padding:6px 12px; border-radius:6px; font-size:14px;">📝 รายงานผล</a>` 
              : ''}
            ${activity.status === 'draft' || activity.status === 'rejected' ? 
               // (Optional: Allow edit if rejected/draft)
              `<span style="font-size:12px; color:#ef4444;">ถูกส่งคืน/ร่าง</span>` 
              : ''}
          </div>
        </td>
      `;
      tbody.appendChild(row);
    });
  }

  updatePaginationInfo();
}

// Show detail modal
function showDetail(id) {
  const activity = activitiesData.find(a => a.id === id);
  if (!activity) return;

  // Update modal content
  document.getElementById('modalTitle').textContent = activity.name;
  document.getElementById('modalSubtitle').textContent = activity.code;
  document.getElementById('modalStatus').className = `status-badge status-${activity.status}`;
  document.getElementById('modalStatus').innerHTML = `<span class="dot"></span>${statusLabels[activity.status] || activity.status}`;

  document.getElementById('detailActivityName').textContent = activity.name;
  document.getElementById('detailTypes').innerHTML = activity.types.map(type => 
    `<span class="type-tag">${typeLabels[type] || type}</span>`
  ).join('');
  document.getElementById('detailSubmitter').textContent = activity.submitter;
  document.getElementById('detailStatus').textContent = statusLabels[activity.status] || activity.status;

  document.getElementById('detailProblem').textContent = activity.problem;
  document.getElementById('detailSolution').textContent = activity.solution;
  document.getElementById('detailResult').textContent = activity.result;


  // Problem Images
  const problemImgContainer = document.getElementById('problemImages');
  if (activity.problem_images && activity.problem_images.length > 0) {
      problemImgContainer.innerHTML = activity.problem_images.map(img => 
          `<a href="${img.url}" target="_blank"><img src="${img.url}" title="${img.name}" style="width:80px; height:80px; object-fit:cover; border-radius:6px; margin-right:5px; cursor:pointer;"></a>`
      ).join('');
  } else {
      problemImgContainer.innerHTML = '';
  }

  // Solution Images
  const solutionImgContainer = document.getElementById('solutionImages');
  if (activity.solution_images && activity.solution_images.length > 0) {
      solutionImgContainer.innerHTML = activity.solution_images.map(img => 
          `<a href="${img.url}" target="_blank"><img src="${img.url}" title="${img.name}" style="width:80px; height:80px; object-fit:cover; border-radius:6px; margin-right:5px; cursor:pointer;"></a>`
      ).join('');
  } else {
      solutionImgContainer.innerHTML = '';
  }

  // Result Images
  const resultImgContainer = document.getElementById('resultImages');
  if (activity.result_images && activity.result_images.length > 0) {
      resultImgContainer.innerHTML = activity.result_images.map(img => 
          `<a href="${img.url}" target="_blank"><img src="${img.url}" title="${img.name}" style="width:80px; height:80px; object-fit:cover; border-radius:6px; margin-right:5px; cursor:pointer;"></a>`
      ).join('');
  } else {
      resultImgContainer.innerHTML = '';
  }

  // Collaborators
  const collabHtml = activity.collaborators.map(collab => `
    <div class="collaborator-card">
      <div class="collaborator-info">
        <div class="collaborator-avatar">${collab.name.charAt(0)}</div>
        <div class="collaborator-name">${collab.name}</div>
      </div>
      <div class="collaborator-percent">${collab.percent}%</div>
    </div>
  `).join('');
  document.getElementById('detailCollaborators').innerHTML = collabHtml;

  // Show modal
  document.getElementById('detailModal').classList.add('show');
  document.body.style.overflow = 'hidden';
}

// Close modal
function closeDetailModal() {
  document.getElementById('detailModal').classList.remove('show');
  document.body.style.overflow = '';
}

function closeModal(event) {
  if (event.target === event.currentTarget) {
    closeDetailModal();
  }
}

// Filter activities
function filterActivities() {
  const searchTerm = document.getElementById('searchInput').value.toLowerCase().trim();
  const statusFilter = document.getElementById('statusFilter').value;
  const typeFilter = document.getElementById('typeFilter').value;

  filteredData = activitiesData.filter(activity => {
    // ตรวจสอบการค้นหา
    const matchSearch = !searchTerm || 
      activity.name.toLowerCase().includes(searchTerm) ||
      activity.code.toLowerCase().includes(searchTerm) ||
      activity.submitter.toLowerCase().includes(searchTerm);

    // ตรวจสอบสถานะ
    const matchStatus = !statusFilter || activity.status === statusFilter;
    
    // ตรวจสอบประเภท
    const matchType = !typeFilter || activity.types.includes(typeFilter);

    return matchSearch && matchStatus && matchType;
  });

  renderTable();
  updateActiveFilters();
}

// Update active filters display
function updateActiveFilters() {
  const container = document.getElementById('activeFilters');
  const filters = [];

  const searchTerm = document.getElementById('searchInput').value;
  const statusFilter = document.getElementById('statusFilter').value;
  const typeFilter = document.getElementById('typeFilter').value;

  if (searchTerm) filters.push({ label: `ค้นหา: "${searchTerm}"`, clear: 'search' });
  if (statusFilter) filters.push({ label: `สถานะ: ${statusLabels[statusFilter]}`, clear: 'status' });
  if (typeFilter) filters.push({ label: `ประเภท: ${typeLabels[typeFilter]}`, clear: 'type' });

  if (filters.length > 0) {
    container.style.display = 'flex';
    container.innerHTML = filters.map(f => `
      <div class="filter-chip">
        ${f.label}
        <span class="remove" onclick="clearFilter('${f.clear}')">✕</span>
      </div>
    `).join('') + `
      <div class="filter-chip" style="cursor: pointer; background: #fee2e2; color: #991b1b;" onclick="clearAllFilters()">
        ล้างทั้งหมด ✕
      </div>
    `;
  } else {
    container.style.display = 'none';
  }
}

// Clear specific filter
function clearFilter(filterType) {
  switch(filterType) {
    case 'search': 
      document.getElementById('searchInput').value = ''; 
      break;
    case 'status': 
      document.getElementById('statusFilter').value = ''; 
      break;
    case 'type': 
      document.getElementById('typeFilter').value = ''; 
      break;
  }
  filterActivities();
}

// Clear all filters
function clearAllFilters() {
  document.getElementById('searchInput').value = '';
  document.getElementById('statusFilter').value = '';
  document.getElementById('typeFilter').value = '';
  filterActivities();
}

// Update pagination info
function updatePaginationInfo() {
  const filteredCount = document.getElementById('filteredCount');
  const showingFrom = document.getElementById('showingFrom');
  const showingTo = document.getElementById('showingTo');
  const totalRecords = document.getElementById('totalRecords');

  const count = filteredData.length;
  
  filteredCount.textContent = count;
  showingFrom.textContent = count > 0 ? '1' : '0';
  showingTo.textContent = count;
  totalRecords.textContent = count;
}

// Toast notification
function showToast(msg) {
  const toast = document.getElementById('toast');
  document.getElementById('toast-msg').textContent = msg;
  toast.classList.add('show');
  setTimeout(() => toast.classList.remove('show'), 2800);
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
  // ESC to close modal
  if (e.key === 'Escape') {
    closeDetailModal();
  }
});
</script>

@endsection