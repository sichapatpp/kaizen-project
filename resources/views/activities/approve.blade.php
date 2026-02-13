@extends('layouts.base')

@section('content')

<div class="kaizen-wrapper">
  <div class="kaizen-inner" style="max-width: 1400px;">

    <div class="kaizen-header" style="margin-bottom: 24px;">
      <div>
        <div class="topbar-title">อนุมัติและรายงาน</div>
        <div class="topbar-sub">ตรวจสอบและอนุมัติกิจกรรมที่รอดำเนินการ</div>
      </div>
      <div>
        <a href="{{ route('activities.status') }}" class="btn-secondary" style="text-decoration: none;">
            📊 ติดตามสถานะ
        </a>
      </div>
    </div>

    <div class="tab-navigation" style="margin-bottom: 24px;">
      <button class="tab-btn active" onclick="switchTab('pending')">
        รออนุมัติ <span class="badge" id="pendingCount">1</span>
      </button>
      <button class="tab-btn" onclick="switchTab('history')">
        รายงาน
      </button>
    </div>

    <div id="pendingTab" class="tab-content active">
      <div id="pendingList">
        </div>
      
      <div id="emptyPending" class="empty-state" style="display: none;">
        <div class="empty-icon">✅</div>
        <div class="empty-title">ไม่มีรายการรออนุมัติ</div>
        <div class="empty-text">ไม่พบกิจกรรมที่รอการอนุมัติในขณะนี้</div>
      </div>
    </div>

    <div id="historyTab" class="tab-content" style="display: none;">
      <div class="filter-section" style="margin-bottom: 24px;">
        <div class="filter-grid">
          <div class="filter-item" style="grid-column: span 2;">
            <label>🔍 ค้นหา</label>
            <input type="text" id="historySearch" placeholder="ค้นหากิจกรรม..." oninput="filterHistory()" />
          </div>

          <div class="filter-item">
            <label>📊 สถานะการอนุมัติ</label>
            <select id="historyStatusFilter" onchange="filterHistory()">
              <option value="">ทั้งหมด</option>
              <option value="approved">อนุมัติ</option>
              <option value="rejected">ไม่อนุมัติ</option>
            </select>
          </div>
        </div>
      </div>

      <div class="kaizen-card" style="padding: 0; overflow: hidden;">
        <div class="table-header">
          <h3 style="margin: 0;">ประวัติการอนุมัติ</h3>
          <div class="table-info">
            <span id="historyCount">0</span> รายการ
          </div>
        </div>

        <div class="table-container">
          <table class="kaizen-table" id="historyTable">
            <thead>
              <tr>
                <th style="min-width: 250px;">ชื่อกิจกรรม</th>
                <th style="width: 140px;">ผู้ยื่น</th>
                <th style="width: 140px;">สถานะ</th>
                <th style="width: 150px;">วันที่อนุมัติ</th>
                <th style="width: 100px;">จัดการ</th>
              </tr>
            </thead>
            <tbody id="historyTableBody">
              </tbody>
          </table>
        </div>

        <div id="emptyHistory" class="empty-state" style="display: none;">
          <div class="empty-icon">📋</div>
          <div class="empty-title">ไม่พบข้อมูล</div>
          <div class="empty-text">ไม่มีประวัติการอนุมัติในขณะนี้</div>
        </div>
      </div>
    </div>

  </div>
</div>

<div class="modal-overlay" id="detailModal" onclick="closeModal(event)">
  <div class="modal-container" onclick="event.stopPropagation()">
    <div class="modal-header">
      <div>
        <h2 id="modalTitle" style="margin: 0; font-size: 20px; font-weight: 700; color: #1e293b;">รายละเอียดกิจกรรม</h2>
        <p id="modalSubtitle" style="margin: 4px 0 0 0; font-size: 14px; color: #64748b;"></p>
      </div>
      <button class="btn-close-modal" onclick="closeDetailModal()">✕</button>
    </div>

    <div class="modal-body">
      <div style="margin-bottom: 24px;">
        <span id="modalStatus" class="status-badge status-pending">
          <span class="dot"></span>รอพิจารณา
        </span>
      </div>

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
          <div class="detail-value" id="detailSubmitDate">—</div>
        </div>
      </div>

      <hr class="modal-divider" />

      <div class="detail-section">
        <div class="section-title">❗ ปัญหาที่พบ</div>
        <div class="section-content" id="detailProblem">—</div>
      </div>

      <hr class="modal-divider" />

      <div class="detail-section">
        <div class="section-title">💡 วิธีการ</div>
        <div class="section-content" id="detailSolution">—</div>
      </div>

      <hr class="modal-divider" />

      <div class="detail-section">
        <div class="section-title">🎯 ผลที่คาดว่าจะได้รับ</div>
        <div class="section-content" id="detailResult">—</div>
      </div>

      <hr class="modal-divider" />

      <div class="detail-section">
        <div class="section-title">👥 ผู้ร่วมงาน</div>
        <div id="detailCollaborators" class="collaborators-list">—</div>
      </div>
    </div>

    <div class="modal-footer" id="modalFooter">
      <button class="btn-secondary" onclick="closeDetailModal()">ปิด</button>
    </div>
  </div>
</div>

<div class="modal-overlay" id="approvalModal" onclick="closeApprovalModal(event)">
  <div class="modal-container" style="max-width: 600px;" onclick="event.stopPropagation()">
    <div class="modal-header">
      <div>
        <h2 style="margin: 0; font-size: 20px; font-weight: 700; color: #1e293b;">
          <span id="approvalAction">อนุมัติ</span>กิจกรรม
        </h2>
        <p id="approvalActivityName" style="margin: 4px 0 0 0; font-size: 14px; color: #64748b;"></p>
      </div>
      <button class="btn-close-modal" onclick="closeApprovalModal()">✕</button>
    </div>

    <div class="modal-body">
      <div style="margin-bottom: 20px;">
        <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #334155;">
          💬 หมายเหตุ <span style="color: #94a3b8; font-weight: 400;">(ไม่บังคับ)</span>
        </label>
        <textarea 
          id="approvalNote" 
          rows="4" 
          style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; resize: vertical;"
          placeholder="ระบุหมายเหตุหรือข้อเสนอแนะ..."></textarea>
      </div>
    </div>

    <div class="modal-footer">
      <button class="btn-secondary" onclick="closeApprovalModal()">ยกเลิก</button>
      <button id="confirmApprovalBtn" class="btn-primary" onclick="confirmApproval()">
        ยืนยัน
      </button>
    </div>
  </div>
</div>

<div class="kaizen-toast" id="toast">
  <span class="toast-icon">✅</span>
  <span id="toast-msg">ดำเนินการสำเร็จ</span>
</div>

<script>
// ✅ ดึงข้อมูลจาก Controller มาแสดง (แทนที่ Sample Data เดิม)
const activitiesData = @json($activitiesData);

// Labels
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

// State
let currentApprovalActivity = null;
let currentApprovalAction = null;
let filteredHistory = [];

// Initialize
document.addEventListener('DOMContentLoaded', function() {
  renderPendingActivities();
  renderHistory();
});

// Switch Tab
function switchTab(tab) {
  // Update tab buttons
  document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
  event.target.classList.add('active');

  // Update tab content
  document.querySelectorAll('.tab-content').forEach(content => {
    content.style.display = 'none';
  });

  if (tab === 'pending') {
    document.getElementById('pendingTab').style.display = 'block';
  } else if (tab === 'history') {
    document.getElementById('historyTab').style.display = 'block';
  }
}

// Render Pending Activities
function renderPendingActivities() {
  const userRole = "{{ auth()->user()->role->role_name }}";
  
  const pendingActivities = activitiesData.filter(a => {
      if (userRole === 'manager') {
          return a.status === 'pending' || a.status === 'waiting_for_result_approval';
      } else if (userRole === 'chairman') {
          return a.status === 'waiting_for_chairman_approval';
      } else if (userRole === 'admin') {
          return a.status === 'pending' || a.status === 'waiting_for_result_approval' || a.status === 'waiting_for_chairman_approval';
      }
      return false; 
  });
  
  const container = document.getElementById('pendingList');
  const emptyState = document.getElementById('emptyPending');
  const countBadge = document.getElementById('pendingCount');

  countBadge.textContent = pendingActivities.length;

  if (pendingActivities.length === 0) {
    container.innerHTML = '';
    emptyState.style.display = 'block';
    return;
  }

  emptyState.style.display = 'none';
  
  container.innerHTML = pendingActivities.map(activity => `
    <div class="approval-card">
      <div class="approval-header">
        <div class="approval-info">
          <h3>${activity.name}</h3>
        </div>
      </div>

      <div class="approval-meta">
        <div class="meta-item">
          <div class="meta-label">ผู้ยื่น</div>
          <div class="meta-value">${activity.submitter}</div>
        </div>
        <div class="meta-item">
          <div class="meta-label">ประเภท</div>
          <div class="meta-value">
            ${activity.types.map(type => `<span class="type-tag">${typeLabels[type]}</span>`).join(' ')}
          </div>
        </div>
      </div>

      <div class="approval-sections">
        <div class="section-box problem">
          <div class="section-box-title">❗ปัญหา</div>
          <div class="section-box-content">${activity.problem}</div>
        </div>

        <div class="section-box solution">
          <div class="section-box-title">💡วิธีการ</div>
          <div class="section-box-content">${activity.solution}</div>
        </div>

        <div class="section-box result">
          <div class="section-box-title">🎯ผลที่คาดว่าจะได้รับ</div>
          <div class="section-box-content">${activity.result}</div>
        </div>
      </div>

      <div class="approval-actions">
        <button class="btn-view-detail" onclick="showDetail(${activity.id})">
          👁️ ดูรายละเอียด
        </button>
        <button class="btn-approve" onclick="openApprovalModal(${activity.id}, 'approve')">
          ✅ อนุมัติ
        </button>
        <button class="btn-reject" onclick="openApprovalModal(${activity.id}, 'reject')">
          ❌ ไม่อนุมัติ
        </button>
      </div>
    </div>
  `).join('');
}

// Render History
function renderHistory() {
  const approvedActivities = activitiesData.filter(a => a.status === 'approved' || a.status === 'rejected' || a.status === 'completed');
  filteredHistory = [...approvedActivities];
  updateHistoryTable();
}

// Update History Table
function updateHistoryTable() {
  const tbody = document.getElementById('historyTableBody');
  const emptyState = document.getElementById('emptyHistory');
  const countElement = document.getElementById('historyCount');

  countElement.textContent = filteredHistory.length;

  if (filteredHistory.length === 0) {
    tbody.innerHTML = '';
    emptyState.style.display = 'block';
    return;
  }

  emptyState.style.display = 'none';

  tbody.innerHTML = filteredHistory.map(activity => `
    <tr>
      <td style="font-weight: 600; color: #3b82f6;">${activity.name}</td>
      <td>${activity.submitter}</td>
      <td>
        <span class="status-badge status-${activity.status}">
          <span class="dot"></span>${statusLabels[activity.status]}
        </span>
      </td>
      <td>${activity.approvalDate || '—'}</td>
      <td>
        <button class="btn-action primary" onclick="showDetail(${activity.id})" title="ดูรายละเอียด">
          👁️
        </button>
      </td>
    </tr>
  `).join('');
}

// Filter History
function filterHistory() {
  const searchTerm = document.getElementById('historySearch').value.toLowerCase().trim();
  const statusFilter = document.getElementById('historyStatusFilter').value;

  const allHistory = activitiesData.filter(a => a.status === 'approved' || a.status === 'rejected' || a.status === 'completed');

  filteredHistory = allHistory.filter(activity => {
    const matchSearch = !searchTerm || 
      activity.name.toLowerCase().includes(searchTerm) ||
      activity.submitter.toLowerCase().includes(searchTerm);

    const matchStatus = !statusFilter || activity.status === statusFilter;

    return matchSearch && matchStatus;
  });

  updateHistoryTable();
}

// Show Detail Modal
function showDetail(id) {
  const activity = activitiesData.find(a => a.id === id);
  if (!activity) return;

  document.getElementById('modalTitle').textContent = activity.name;
  document.getElementById('modalStatus').className = `status-badge status-${activity.status}`;
  document.getElementById('modalStatus').innerHTML = `<span class="dot"></span>${statusLabels[activity.status]}`;

  document.getElementById('detailActivityName').textContent = activity.name;
  document.getElementById('detailTypes').innerHTML = activity.types.map(type => 
    `<span class="type-tag">${typeLabels[type]}</span>`
  ).join('');
  document.getElementById('detailSubmitter').textContent = activity.submitter;
  document.getElementById('detailSubmitDate').textContent = activity.submitDate;

  document.getElementById('detailProblem').textContent = activity.problem;
  document.getElementById('detailSolution').textContent = activity.solution;
  document.getElementById('detailResult').textContent = activity.result;

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

  document.getElementById('detailModal').classList.add('show');
  document.body.style.overflow = 'hidden';
}

// Close Detail Modal
function closeDetailModal() {
  document.getElementById('detailModal').classList.remove('show');
  document.body.style.overflow = '';
}

function closeModal(event) {
  if (event.target === event.currentTarget) {
    closeDetailModal();
  }
}

// Open Approval Modal
function openApprovalModal(activityId, action) {
  const activity = activitiesData.find(a => a.id === activityId);
  if (!activity) return;

  currentApprovalActivity = activity;
  currentApprovalAction = action;

  const actionText = action === 'approve' ? 'อนุมัติ' : 'ไม่อนุมัติ';
  const actionColor = action === 'approve' ? '#10b981' : '#ef4444';

  document.getElementById('approvalAction').textContent = actionText;
  document.getElementById('approvalActivityName').textContent = activity.name;
  document.getElementById('approvalNote').value = '';
  
  const confirmBtn = document.getElementById('confirmApprovalBtn');
  confirmBtn.textContent = `ยืนยัน${actionText}`;
  confirmBtn.style.background = actionColor;

  document.getElementById('approvalModal').classList.add('show');
  document.body.style.overflow = 'hidden';
}

// Close Approval Modal
function closeApprovalModal(event) {
  if (event && event.target !== event.currentTarget) return;
  
  document.getElementById('approvalModal').classList.remove('show');
  document.body.style.overflow = '';
  currentApprovalActivity = null;
  currentApprovalAction = null;
}

// Confirm Approval
function confirmApproval() {
  if (!currentApprovalActivity || !currentApprovalAction) return;

  const note = document.getElementById('approvalNote').value.trim();
  const newStatus = currentApprovalAction === 'approve' ? 'approved' : 'rejected';
  const actionText = currentApprovalAction === 'approve' ? 'อนุมัติ' : 'ไม่อนุมัติ';

  // 1. ส่งข้อมูลไปบันทึง
  fetch(`/activities/${currentApprovalActivity.id}/update-status`, {
      method: 'POST',
      headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content // ดึง token จาก meta tag
      },
      body: JSON.stringify({
          status: newStatus,
          note: note
      })
  })
  .then(async response => {
      const isJson = response.headers.get('content-type')?.includes('application/json');
      const data = isJson ? await response.json() : null;

      if (!response.ok) {
          const error = (data && data.message) || response.statusText;
          throw new Error(error);
      }
      
      return data;
  })
  .then(data => {
      if(data && data.success) {
          // 2. อัปเดตข้อมูลในหน้าจอ
          const activity = activitiesData.find(a => a.id === currentApprovalActivity.id);
          if (activity) {
              activity.status = newStatus;
              activity.approvalDate = new Date().toLocaleDateString('th-TH');
              activity.approvalNote = note;
          }

          closeApprovalModal();
          
          // 3. รีเฟรชตาราง
          renderPendingActivities();
          renderHistory();
      } else {
          alert('เกิดข้อผิดพลาด: ' + ((data && data.message) || 'ได้รับข้อมูลที่ไม่ถูกต้องจาก Server'));
      }
  })
  .catch(error => {
      console.error('Error:', error);
      alert('เกิดข้อผิดพลาดในการเชื่อมต่อ (อาจเป็นเพราะหมดอายุ Session กรุณา Refresh หน้าเว็บ)');
  });
}
</script>

@endsection