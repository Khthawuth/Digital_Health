# กลุ่มงานสุขภาพดิจิทัล (Hospital Software & Digital Health Division)

ระบบเว็บไซต์พอร์ทัลข้อมูลและนวัตกรรมสุขภาพดิจิทัล พัฒนาสำหรับ **กลุ่มงานสุขภาพดิจิทัล (ทีมโปรแกรมเมอร์และพัฒนาระบบสารสนเทศโรงพยาบาล)** มาพร้อมระบบประชาสัมพันธ์ข่าวสาร บริการสุขภาพดิจิทัล ปฏิทินตารางเวรเจ้าหน้าที่ ผังบริหารองค์กร และระบบจัดการหลังบ้าน (Admin Dashboard) พร้อมระบบความปลอดภัย 2-Factor Authentication (2FA)

---

## ✨ จุดเด่นและฟังก์ชันการทำงาน (Features)

### 🌐 ฝั่งผู้ใช้งานทั่วไป (Public Portal)
1. **หน้าแรก (Homepage)**:
   - **Hero Banner Slider**: สไลด์แบนเนอร์ภาพขนาดใหญ่ พร้อมเอฟเฟกต์ Ken Burns Zoom เข้า-ออก และระบบสลับภาพอัตโนมัติ
   - **Government Ticker**: แถบข่าวประกาศราชการแบบวิ่ง (Marquee Ticker) แสดงข้อมูลข่าวสารด่วนและวันที่ภาษาไทย
   - **Bento Grid News Showcase**: การจัดวางข่าวประชาสัมพันธ์สไตล์ Bento Box ทันสมัย พร้อมตัวนับแยกหมวดหมู่และยอดผู้เข้าชม
   - **Quick Digital Services**: การ์ดบริการสุขภาพดิจิทัลยอดนิยม
2. **ข่าวประชาสัมพันธ์ (News & Announcements)**:
   - แสดงรายการข่าวสาร แบ่งหมวดหมู่ กรองข่าวตามหมวด และแสดงจำนวนผู้เข้าชม (Views)
   - หน้าอ่านข่าวฉบับเต็ม พร้อมรูปภาพและเนื้อหา
3. **ผังโครงสร้างการบริหารงาน (Executive Organization Chart)**:
   - แสดงผังผู้บริหารสูงสุด รองผู้อำนวยการ และหัวหน้ากลุ่มงาน/ฝ่ายอย่างเป็นทางการ พร้อมช่องทางติดต่อ
4. **ปฏิทินตารางเวรเจ้าหน้าที่ (Interactive Duty Schedule Calendar)**:
   - ปฏิทินแสดงตารางเวรปฏิบัติงานของเจ้าหน้าที่ (เวรเช้า, เวรบ่าย, เวรดึก, เวร On-call)
   - คลิกเพื่อดูรายละเอียดและผู้ร่วมเวรในแต่ละวัน
5. **บริการสุขภาพดิจิทัล (Digital Health Services)**:
   - รวบรวมบริการสุขภาพดิจิทัล เช่น บัตรสุขภาพดิจิทัล (Digital Health ID), การแพทย์ทางไกล (Telemedicine), สมุดสุขภาพ และระบบคิวอัจฉริยะ
6. **การรองรับทุกอุปกรณ์ (Fully Responsive)**:
   - ใช้งานได้สมบูรณ์แบบบนสมาร์ตโฟน, iPad, แท็บเล็ต, โน้ตบุ๊ก และจอคอมพิวเตอร์ขนาดใหญ่
   - มีระบบ Mobile Off-Canvas Navigation Drawer ใช้งานสะดวกบนหน้าจอขนาดเล็ก

---

### 🔐 ระบบความปลอดภัยและการจัดการหลังบ้าน (Admin System)
1. **ระบบยืนยันตัวตน 2 ขั้นตอน (2-Factor Authentication - 2FA)**:
   - เมื่อเข้าสู่ระบบด้วยสิทธิ์ผู้ดูแลระบบ (Admin) ระบบจะส่งรหัสความปลอดภัย OTP 6 หลักไปยังอีเมลผู้ดูแล
2. **แดชบอร์ดผู้ดูแลระบบ (Admin Dashboard)**:
   - สรุปภาพรวมสถิติ ข่าวสาร ยอดเข้าชม ข้อความติดต่อ และตารางเวร
3. **ระบบจัดการข้อมูลหลังบ้าน**:
   - **จัดการแบนเนอร์สไลด์ (Hero Banners)**: เพิ่ม ลบ แก้ไข อัปโหลดรูปภาพ ปรับลำดับการแสดงผล
   - **จัดการข่าวสาร (News & Articles)**: ระบบจัดการบทความข่าว หมวดหมู่ กำหนดข่าวเด่น (Featured) และอัปโหลดภาพประกอบ
   - **จัดการผังบริหาร (Executives)**: จัดการรายชื่อ ตำแหน่ง ฝ่าย ระดับชั้น และภาพถ่ายผู้บริหาร
   - **จัดการปฏิทินตารางเวร (Duty Schedules)**: เพิ่ม แก้ไข และลบตารางเวรเจ้าหน้าที่ตามวันที่และกะการทำงาน
   - **จัดการบริการดิจิทัล (Services)**: จัดการรายการบริการสุขภาพ
   - **กล่องข้อความติดต่อ (Contact Inquiries)**: ตรวจสอบข้อความสอบถามจากประชาชน
   - **ตั้งค่าระบบ (System Settings)**: ปรับแต่งชื่อหน่วยงาน ข้อมูลติดต่อ และอีเมลรับ 2FA

---

## 🔑 ข้อมูลเข้าสู่ระบบเริ่มต้น (Default Credentials)

* **URL ล็อคอิน**: `http://localhost/digital_health/login.php`
* **ชื่อผู้ใช้งาน (Username)**: `admin`
* **รหัสผ่าน (Password)**: `admin1234`
* **อีเมลรับรหัส 2FA (2-Factor Authentication)**: `Khthawuth.leo@gmail.com`
*(ระบบมีการบันทึกสำเนารหัส OTP ในกรณีทดสอบที่ไฟล์ `logs/otp_mail.log`)*

---

## 🛠️ ความต้องการของระบบ (Prerequisites)

* **เว็บเซิร์ฟเวอร์**: Apache (ผ่าน XAMPP หรือ Docker)
* **ภาษาที่ใช้**: PHP 8.0 ขึ้นไป (พร้อมเปิดใช้งาน PDO MySQL และ mbstring)
* **ฐานข้อมูล**: MySQL / MariaDB 10.4 ขึ้นไป

---

## 🚀 วิธีการติดตั้งและใช้งาน (Installation)

1. **โคลนโปรเจกต์** ไปยังโฟลเดอร์ `htdocs` ของ XAMPP:
   ```bash
   cd c:/xampp/htdocs
   git clone https://github.com/Khthawuth/Digital_Health.git digital_health
   ```

2. **เปิดบริการในโปรแกรม XAMPP Control Panel**:
   - กด **Start** ที่บริการ **Apache**
   - กด **Start** ที่บริการ **MySQL**

3. **การติดตั้งฐานข้อมูล (Database)**:
   * **วิธีที่ 1 (อัตโนมัติ)**: เพียงเปิดหน้าเว็บ `http://localhost/digital_health/` ระบบจะทำการสร้างฐานข้อมูล `digital_health_db` ตาราง และข้อมูลตั้งต้นให้โดยอัตโนมัติ (Auto-Migration)
   * **วิธีที่ 2 (นำเข้าไฟล์ SQL)**: นำเข้าไฟล์ `database.sql` ผ่าน phpMyAdmin (`http://localhost/phpmyadmin`)

4. **เข้าใช้งาน**:
   - หน้าเว็บไซต์หลัก: [http://localhost/digital_health/](http://localhost/digital_health/)
   - ระบบจัดการหลังบ้าน: [http://localhost/digital_health/admin/](http://localhost/digital_health/admin/)

---

## 📂 โครงสร้างโฟลเดอร์ (Project Structure)

```text
digital_health/
├── admin/                 # ระบบจัดการหลังบ้าน (Admin Panel)
│   ├── includes/          # ส่วนประกอบ Header, Sidebar, Footer ของ Admin
│   ├── banners.php        # จัดการแบนเนอร์สไลด์
│   ├── news.php           # จัดการข่าวประชาสัมพันธ์
│   ├── executives.php     # จัดการผังผู้บริหาร
│   ├── duty-schedules.php # จัดการปฏิทินตารางเวร
│   ├── services.php       # จัดการบริการดิจิทัล
│   ├── settings.php       # ตั้งค่าระบบ
│   └── index.php          # แดชบอร์ดสรุปผล
├── assets/                # ไฟล์ Assets (CSS, JS, Fonts)
│   ├── css/               # สไตล์ชีตหลัก (style.css, admin.css)
│   └── js/                # สคริปต์ JavaScript (main.js, admin.js)
├── config/                # การตั้งค่าระบบและฐานข้อมูล
│   ├── database.php       # เชื่อมต่อ DB และ Auto-Schema Migration
│   └── functions.php      # ฟังก์ชันช่วยเหลือและระบบความปลอดภัย
├── includes/              # ส่วนประกอบสากล (Header, Footer, Navigation)
├── uploads/               # โฟลเดอร์เก็บไฟล์ภาพอัปโหลด
├── database.sql           # ไฟล์สำรองฐานข้อมูลฉบับสมบูรณ์
├── index.php              # หน้าแรกของพอร์ทัล
├── news.php               # หน้ารวมข่าวสาร
├── organization.php       # หน้าผังบริหารองค์กร
├── duty-schedule.php      # หน้าปฏิทินตารางเวร
├── services.php           # หน้าบริการสุขภาพดิจิทัล
├── about.php              # หน้าเกี่ยวกับเรา วิสัยทัศน์ พันธกิจ
├── contact.php            # หน้าติดต่อสอบถาม
├── login.php              # หน้าเข้าสู่ระบบ
├── register.php           # หน้าสมัครสมาชิก
├── verify-2fa.php         # หน้ายืนยันรหัส OTP 2FA
└── logout.php             # ออกจากระบบ
```

---

## 📄 ลิขสิทธิ์และการพัฒนา (License & Author)
พัฒนาสำหรับ **กลุ่มงานสุขภาพดิจิทัล (Digital Health Innovation & Hospital Software Division)**
รองรับมาตรฐานการคุ้มครองข้อมูลส่วนบุคคล (PDPA) และมาตรฐานระบบความมั่นคงปลอดภัยสารสนเทศภาครัฐ
