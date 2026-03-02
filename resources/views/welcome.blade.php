<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kaizen System - ระบบจัดการกิจกรรม Kaizen</title>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --secondary: #64748b;
            --success: #10b981;
            --warning: #f59e0b;
            --bg: #f8fafc;
            --text-main: #1e293b;
            --text-sub: #475569;
            --white: #ffffff;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Prompt', sans-serif;
        }

        html, body {
            height: 100%;
            overflow: hidden;
            background-color: var(--bg);
            color: var(--text-main);
        }

        .page-wrapper {
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ─── Hero Section ─── */
        .hero {
            flex: 0 0 auto;
            position: relative;
            padding: 36px 0 28px;
            text-align: center;
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            overflow: hidden;
        }

        .hero::before {
            content: "";
            position: absolute;
            top: -80px;
            right: -80px;
            width: 240px;
            height: 240px;
            background: rgba(37, 99, 235, 0.05);
            border-radius: 50%;
            z-index: 0;
        }

        .hero-content {
            position: relative;
            z-index: 1;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
        }

        .hero-icon {
            font-size: 42px;
            margin-bottom: 10px;
            display: inline-block;
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }

        .hero h1 {
            font-size: 2rem;
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .hero p {
            font-size: 0.95rem;
            color: var(--text-sub);
            max-width: 520px;
            margin: 0 auto 20px;
            line-height: 1.6;
        }

        .btn-login {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background-color: var(--primary);
            color: var(--white);
            padding: 11px 30px;
            border-radius: 50px;
            font-size: 15px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.3);
        }

        .btn-login:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 12px 24px rgba(37, 99, 235, 0.4);
        }

        /* ─── Stats Section ─── */
        .stats {
            flex: 0 0 auto;
            padding: 16px 24px;
            background: var(--bg);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
        }

        .stat-card {
            background: var(--white);
            padding: 18px 22px;
            border-radius: 16px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.07);
            display: flex;
            align-items: center;
            gap: 16px;
            transition: transform 0.3s ease;
        }

        .stat-card:hover { transform: translateY(-3px); }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }

        .stat-info .count {
            font-size: 24px;
            font-weight: 700;
            color: var(--text-main);
            display: block;
        }

        .stat-info .label {
            font-size: 13px;
            color: var(--text-sub);
            font-weight: 500;
        }

        .icon-total     { background: #eff6ff; color: var(--primary); }
        .icon-completed { background: #ecfdf5; color: var(--success); }
        .icon-progress  { background: #fffbeb; color: var(--warning); }

        /* ─── Features Section ─── */
        .features {
            flex: 1 1 auto;
            padding: 16px 24px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .section-header {
            text-align: center;
            margin-bottom: 16px;
        }

        .section-header h2 {
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .section-header p {
            font-size: 0.88rem;
            color: var(--text-sub);
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
        }

        .feature-card {
            padding: 22px 24px;
            background: var(--white);
            border-radius: 18px;
            border: 1px solid #f1f5f9;
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            border-color: var(--primary);
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.05);
        }

        .f-icon {
            width: 40px;
            height: 40px;
            background: var(--primary);
            color: var(--white);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 17px;
            margin-bottom: 14px;
        }

        .feature-card h3 {
            font-size: 0.95rem;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .feature-card p {
            color: var(--text-sub);
            line-height: 1.55;
            font-size: 0.83rem;
        }

        /* ─── Footer ─── */
        footer {
            flex: 0 0 auto;
            padding: 12px 0;
            text-align: center;
            color: var(--text-sub);
            font-size: 12px;
            border-top: 1px solid #e2e8f0;
            background: var(--white);
        }

        @media (max-width: 768px) {
            html, body { overflow: auto; }
            .stats-grid, .feature-grid { grid-template-columns: 1fr; }
            .hero h1 { font-size: 1.6rem; }
        }
    </style>
</head>
<body>

<div class="page-wrapper">

    <!-- Hero -->
    <section class="hero">
        <div class="hero-content">
            <div class="hero-icon">🏆</div>
            <h1>Kaizen System</h1>
            <p>ระบบจัดการกิจกรรม Kaizen สำหรับองค์กร เพื่อขับเคลื่อนนวัตกรรมและพัฒนาประสิทธิภาพการทำงานอย่างต่อเนื่อง</p>
            <a href="{{ route('login') }}" class="btn-login">
                เข้าสู่ระบบ <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </section>

    <!-- Stats -->
    <section class="stats">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon icon-total">
                        <i class="fas fa-lightbulb"></i>
                    </div>
                    <div class="stat-info">
                        <span class="count">{{ number_format($stats['total']) }}</span>
                        <span class="label">กิจกรรมทั้งหมด</span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon icon-completed">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-info">
                        <span class="count">{{ number_format($stats['completed']) }}</span>
                        <span class="label">เสร็จสิ้นแล้ว</span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon icon-progress">
                        <i class="fas fa-spinner"></i>
                    </div>
                    <div class="stat-info">
                        <span class="count">{{ number_format($stats['in_progress']) }}</span>
                        <span class="label">กำลังดำเนินการ</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section class="features">
        <div class="container">
            <div class="section-header">
                <h2>ยกระดับองค์กรด้วย Kaizen</h2>
                <p>ตัวช่วยที่จะทำให้การพัฒนาองค์กรเป็นเรื่องง่ายและมีประสิทธิภาพ</p>
            </div>
            <div class="feature-grid">
                <div class="feature-card">
                    <div class="f-icon"><i class="fas fa-paper-plane"></i></div>
                    <h3>ส่งกิจกรรมได้ง่าย</h3>
                    <p>อินเทอร์เฟซที่ออกแบบมาให้ใช้งานง่าย ช่วยให้พนักงานทุกคนสามารถส่งไอเดียการปรับปรุงได้เพียงไม่กี่ขั้นตอน</p>
                </div>
                <div class="feature-card">
                    <div class="f-icon"><i class="fas fa-chart-line"></i></div>
                    <h3>ติดตามสถานะ Real-time</h3>
                    <p>ตรวจสอบความคืบหน้าของกิจกรรมได้ตลอดเวลา ตั้งแต่ขั้นตอนการเสนอไอเดียจนถึงการนำไปปฏิบัติจริง</p>
                </div>
                <div class="feature-card">
                    <div class="f-icon"><i class="fas fa-shield-alt"></i></div>
                    <h3>ระบบอนุมัติเป็นขั้นตอน</h3>
                    <p>กระบวนการพิจารณาที่เป็นระบบจากหัวหน้างานและผู้บริหาร มั่นใจได้ว่าทุกไอเดียจะได้รับการดูแลอย่างเหมาะสม</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            &copy; {{ date('Y') + 543 }} Kaizen System. All rights reserved.
        </div>
    </footer>

</div>

</body>
</html>