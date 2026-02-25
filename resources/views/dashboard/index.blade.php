@extends('layouts.base')

@section('content')
    <!-- Greeting -->
    <div class="greeting">
        <h1>สวัสดี</h1>
        <p>ยินดีต้อนรับสู่ระบบจัดการกิจกรรมไคเซ็น</p>
    </div>

    <!-- Stat Cards -->
    <div class="stat-cards">
        <!-- งานทั้งหมด -->
        <div class="stat-card">
            <div class="stat-card-left">
                <div class="stat-label">งานทั้งหมด</div>
                <div class="stat-value blue">{{ $counts['total'] ?? 0 }}</div>
            </div>
            <div class="stat-icon-wrap blue">
                <i class="fas fa-file-alt"></i>
            </div>
        </div>

        <!-- รออนุมัติ -->
        <div class="stat-card">
            <div class="stat-card-left">
                <div class="stat-label">รออนุมัติ</div>
                <div class="stat-value orange">{{ $counts['pending'] ?? 0 }}</div>
            </div>
            <div class="stat-icon-wrap orange">
                <i class="fas fa-clock"></i>
            </div>
        </div>

        <!-- ถูกปฏิเสธ -->
        <div class="stat-card">
            <div class="stat-card-left">
                <div class="stat-label">ถูกปฏิเสธ</div>
                <div class="stat-value red">{{ $counts['rejected'] ?? 0 }}</div>
            </div>
            <div class="stat-icon-wrap red">
                <i class="fas fa-times-circle"></i>
            </div>
        </div>

        <!-- อนุมัติแล้ว -->
        <div class="stat-card">
            <div class="stat-card-left">
                <div class="stat-label">อนุมัติแล้ว</div>
                <div class="stat-value green">{{ $counts['approved'] ?? 0 }}</div>
            </div>
            <div class="stat-icon-wrap green">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
    </div>

    <!-- Featured Kaizen Projects -->
    <div class="featured-section">
        <div class="featured-header">
            <div class="featured-header-left">
                <i class="fas fa-lightbulb trophy-icon"></i>
                <h2>ผลงาน Kaizen เด่น</h2>
            </div>
            <a href="#" class="view-all">ดูทั้งหมด <i class="fas fa-arrow-right"></i></a>
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
                            {{ $project->participants->pluck('name')->join(' · ') }}
                        </div>
                        <div class="project-result">
                            <span class="check-icon"><i class="fas fa-check"></i></span>
                            {{ $project->result }}
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <div class="empty-icon">🏆</div>
                    <div class="empty-title">ยังไม่มีผลงานเด่น</div>
                    <div class="empty-text">เมื่อมีกิจกรรมที่เสร็จสิ้นและบรรลุเป้าหมาย จะแสดงที่นี่</div>
                </div>
            @endforelse
        </div>
    </div>
@endsection