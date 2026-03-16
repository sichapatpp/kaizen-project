<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Sarabun', Arial, sans-serif; background-color: #f4f7f6; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .header { background-color: #0d6efd; color: white; padding: 20px; text-align: center; }
        .header h2 { margin: 0; font-size: 24px; }
        .content { padding: 30px; color: #333333; line-height: 1.6; }
        .status-box { background-color: #f8f9fa; padding: 20px; border-radius: 6px; margin: 20px 0; text-align: center; border: 1px solid #e9ecef; }
        .activity-title { font-size: 20px; font-weight: bold; margin-bottom: 10px; color: #212529; }
        .status-text { font-size: 18px; font-weight: bold; color: #0d6efd; display: inline-block; padding: 5px 15px; background-color: #e7f1ff; border-radius: 20px; }
        .button-container { text-align: center; margin-top: 35px; margin-bottom: 15px; }
        .button { display: inline-block; background-color: #0d6efd; color: #ffffff; text-decoration: none; padding: 12px 30px; border-radius: 5px; font-weight: bold; font-size: 16px; }
        .button:hover { background-color: #0b5ed7; }
        .footer { background-color: #f8f9fa; text-align: center; padding: 20px; font-size: 12px; color: #6c757d; border-top: 1px solid #e9ecef; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>อัปเดตสถานะโครงการ Kaizen</h2>
        </div>
        <div class="content">
            <p>เรียนคุณ <strong>{{ $project->user ? $project->user->name : 'ผู้ส่งกิจกรรม' }}</strong>,</p>
            <p>กิจกรรม Kaizen ของคุณมีการอัปเดตสถานะใหม่ โดยมีรายละเอียดดังนี้:</p>
            
            <div class="status-box">
                <div class="activity-title">{{ $project->title }}</div>
                <div style="font-size: 14px; color: #6c757d; margin-bottom: 5px;">สถานะปัจจุบัน</div>
                <div class="status-text">{{ $statusTh }}</div>
            </div>
            
            <p>คุณสามารถเข้าสู่ระบบเพื่อดูรายละเอียดเพิ่มเติม ตรวจสอบความคิดเห็น หรือดำเนินการขั้นต่อไปได้ที่ปุ่มด้านล่างนี้</p>
            
            <div class="button-container">
                <a href="{{ url('/activities/status') }}" class="button">เข้าสู่ระบบจัดการ Kaizen</a>
            </div>
        </div>
        <div class="footer">
            <p>อีเมลฉบับนี้เป็นการแจ้งเตือนอัตโนมัติจากระบบ กรุณาอย่าตอบกลับ</p>
            <p>&copy; {{ date('Y') }} ระบบกิจกรรม Kaizen</p>
        </div>
    </div>
</body>
</html>
