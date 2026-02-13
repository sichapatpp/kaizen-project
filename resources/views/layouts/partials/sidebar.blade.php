 <aside class="sidebar">
    <div class="sidebar-section-label">หลัก</div>
    
    @if(auth()->user()->role->role_name === 'admin')
        <a href="{{ route('dashboard') }}" class="sidebar-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fas fa-th-large si-icon"></i> Dashboard
        </a>
    @else
        <a href="{{ route('activities.index') }}" class="sidebar-item {{ request()->routeIs('activities.index') ? 'active' : '' }}">
            <i class="fas fa-home si-icon"></i> หน้าหลัก
        </a>
    @endif

    <div class="sidebar-section-label" style="margin-top:8px">กิจกรรม</div>
    
    <a href="{{ route('activities.index') }}" class="sidebar-item {{ request()->routeIs('activities.index') ? 'active' : '' }}">
        <i class="fas fa-plus-circle si-icon"></i> สร้างกิจกรรม
    </a>
    
    <a href="{{ route('activities.status') }}" class="sidebar-item {{ request()->routeIs('activities.status') ? 'active' : '' }}">
        <i class="fas fa-list-alt si-icon"></i> ติดตามสถานะ 
        {{-- <span class="si-badge">2</span> --}}
    </a>

    @if(in_array(auth()->user()->role->role_name, ['manager', 'chairman', 'admin']))
        <a href="{{ route('activities.approve') }}" class="sidebar-item {{ request()->routeIs('activities.approve') ? 'active' : '' }}">
            <i class="fas fa-trophy si-icon"></i> อนุมัติและรายงาน
        </a>
    @endif

    @if(auth()->user()->role->role_name === 'admin')
        <div class="sidebar-section-label" style="margin-top:8px">ระบบ</div>
        
        <a href="{{ route('roles.index') }}" class="sidebar-item {{ request()->routeIs('roles.*') ? 'active' : '' }}">
            <i class="fas fa-cog si-icon"></i> จัดการสิทธิ์ผู้ใช้งาน
        </a>
        
        <a href="{{ route('user.index') }}" class="sidebar-item {{ request()->routeIs('user.*') ? 'active' : '' }}">
            <i class="fas fa-users si-icon"></i> จัดการผู้ใช้
        </a>
    @endif
  </aside> 