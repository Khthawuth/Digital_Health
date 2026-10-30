<?php
// index.php - Digital Health Portal Homepage
require_once __DIR__ . '/config/functions.php';

$pageTitle = "หน้าแรก - กลุ่มงานสุขภาพดิจิทัล | Hospital Programmer & Software Division";
$pageDesc = "กลุ่มงานสุขภาพดิจิทัล ทีมโปรแกรมเมอร์และพัฒนาระบบสารสนเทศโรงพยาบาล ข่าวสารเทคโนโลยีสุขภาพ ระบบเวชระเบียน ผังโครงสร้าง และบริการดิจิทัล";

$pdo = getDBConnection();

// Fetch Active Hero Banners
$stmtBanners = $pdo->query("SELECT * FROM hero_banners WHERE is_active = 1 ORDER BY sort_order ASC, id DESC");
$banners = $stmtBanners->fetchAll();

// Fetch Latest Featured & Recent News
$stmtFeaturedNews = $pdo->query("SELECT n.*, c.name as category_name, c.slug as category_slug 
    FROM news n 
    LEFT JOIN news_categories c ON n.category_id = c.id 
    WHERE n.is_featured = 1 
    ORDER BY n.published_at DESC LIMIT 5");
$featuredNews = $stmtFeaturedNews->fetchAll();

$stmtRecentNews = $pdo->query("SELECT n.*, c.name as category_name, c.slug as category_slug 
    FROM news n 
    LEFT JOIN news_categories c ON n.category_id = c.id 
    ORDER BY n.published_at DESC LIMIT 10");
$recentNews = $stmtRecentNews->fetchAll();

// Spotlight News (ข่าวเด่นสำคัญอันดับหนึ่ง)
$spotlightNews = !empty($featuredNews) ? $featuredNews[0] : (!empty($recentNews) ? $recentNews[0] : null);

// Trending Feed (ข่าวล่าสุด 4 รายการ)
$trendingNews = [];
foreach ($recentNews as $r) {
    if ($spotlightNews && $r['id'] === $spotlightNews['id']) continue;
    $trendingNews[] = $r;
    if (count($trendingNews) >= 4) break;
}

// Category lists with counts
$stmtCatCounts = $pdo->query("SELECT c.*, COUNT(n.id) as news_count 
    FROM news_categories c 
    LEFT JOIN news n ON n.category_id = c.id 
    GROUP BY c.id ORDER BY c.id ASC");
$newsCategories = $stmtCatCounts->fetchAll();

// Fetch Services
$stmtServices = $pdo->query("SELECT * FROM services WHERE is_active = 1 ORDER BY sort_order ASC LIMIT 4");
$services = $stmtServices->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<!-- Hero Banner Slider with Ken Burns Zoom & Smooth Transition -->
<section class="hero-slider-wrap">
    <div class="hero-slider">
        <?php if (!empty($banners)): ?>
            <?php foreach ($banners as $idx => $b): ?>
                <?php 
                    $hasText = !empty(trim($b['title'])) && trim($b['title']) !== '.';
                    $imgUrl = $b['image_url'];
                ?>
                <div class="slide <?= $idx === 0 ? 'active' : '' ?>">
                    <!-- Smooth Zoom Background Image -->
                    <div class="slide-bg" style="background-image: url('<?= htmlspecialchars($imgUrl) ?>');"></div>
                    
                    <?php if ($hasText): ?>
                        <!-- Only render subtle overlay & caption if title text is present -->
                        <div class="slide-overlay-gradient"></div>
                        <div class="container slide-container">
                            <div class="slide-caption-card">
                                <?php if (!empty($b['badge_text'])): ?>
                                    <div class="slide-badge">
                                        <i class="fa-solid fa-sparkles"></i> <?= htmlspecialchars($b['badge_text']) ?>
                                    </div>
                                <?php endif; ?>
                                <h2 class="slide-title"><?= htmlspecialchars($b['title']) ?></h2>
                                <?php if (!empty($b['subtitle'])): ?>
                                    <p class="slide-subtitle"><?= htmlspecialchars($b['subtitle']) ?></p>
                                <?php endif; ?>
                                <div class="slide-buttons">
                                    <?php if (!empty($b['button_text']) && $b['button_link'] !== '#'): ?>
                                        <a href="<?= htmlspecialchars($b['button_link']) ?>" class="btn btn-primary">
                                            <?= htmlspecialchars($b['button_text']) ?> <i class="fa-solid fa-arrow-right"></i>
                                        </a>
                                    <?php endif; ?>
                                    <a href="about.php" class="btn btn-light">
                                        <i class="fa-solid fa-circle-info"></i> เกี่ยวกับศูนย์สุขภาพดิจิทัล
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="slide active">
                <div class="slide-bg" style="background: linear-gradient(135deg, #0f172a, #0d9488);"></div>
                <div class="container slide-container">
                    <div class="slide-caption-card">
                        <div class="slide-badge">นวัตกรรมสุขภาพ 2026</div>
                        <h2 class="slide-title">ศูนย์ข้อมูลและนวัตกรรมสุขภาพดิจิทัล</h2>
                        <p class="slide-subtitle">ยกระดับบริการสาธารณสุขไทยด้วยเทคโนโลยีและเวชระเบียนดิจิทัล</p>
                        <a href="services.php" class="btn btn-primary">เข้าสู่บริการ</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Slide Timer Progress Bar -->
    <div class="slider-progress-line">
        <div class="slider-progress-fill" id="sliderProgressFill"></div>
    </div>

    <!-- Carousel Controls -->
    <?php if (count($banners) > 1): ?>
        <div class="slider-controls">
            <button class="slider-btn" id="prevSlide" aria-label="Previous Slide" title="ภาพก่อนหน้า"><i class="fa-solid fa-chevron-left"></i></button>
            <button class="slider-btn" id="togglePlaySlide" aria-label="Pause/Play Slider" title="หยุด/เล่น อัตโนมัติ"><i class="fa-solid fa-pause"></i></button>
            <button class="slider-btn" id="nextSlide" aria-label="Next Slide" title="ภาพถัดไป"><i class="fa-solid fa-chevron-right"></i></button>
        </div>
        <div class="slider-dots">
            <?php foreach ($banners as $idx => $b): ?>
                <div class="dot <?= $idx === 0 ? 'active' : '' ?>" title="สไลด์ที่ <?= $idx + 1 ?>"></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<!-- Official Government Announcement Marquee Ticker -->
<div class="gov-ticker-wrap">
    <div class="container gov-ticker-inner">
        <div class="gov-ticker-badge">
            <span class="live-dot-pulse"></span>
            <i class="fa-solid fa-bullhorn"></i> ข่าวประกาศราชการ
        </div>
        <div class="gov-ticker-marquee">
            <div class="ticker-track">
                <span>กระทรวงสาธารณสุข เดินหน้านโยบาย "30 บาทรักษาทุกที่ด้วยบัตรประชาชนใบเดียว" ผ่านระบบสุขภาพดิจิทัล</span>
                <span class="ticker-divider">★</span>
                <span>เปิดจุดบริการการแพทย์ทางไกล (Telemedicine) ใน รพ.สต. กว่า 480 แห่ง ลดความแออัดในโรงพยาบาลศูนย์</span>
                <span class="ticker-divider">★</span>
                <span>ประกาศแนวปฏิบัติด้านความมั่นคงปลอดภัยสารสนเทศและคุ้มครองข้อมูลสุขภาพ (PDPA) มาตรฐานสากล ISO/IEC 27001</span>
                <span class="ticker-divider">★</span>
                <span>เตือนประชาชน: ศูนย์สุขภาพดิจิทัลไม่มีนโยบายโทรสอบถามรหัส OTP หรือขอข้อมูลการเงินผ่านโทรศัพท์เด็ดขาด</span>
            </div>
        </div>
        <div class="gov-ticker-date">
            <i class="fa-regular fa-calendar-check"></i> วันนี้: <?= thaiDate(date('Y-m-d')) ?>
        </div>
    </div>
</div>

<!-- Quick Services Row -->
<section class="section quick-services-section">
    <div class="container">
        <div class="services-grid">
            <?php foreach ($services as $srv): ?>
                <div class="service-card">
                    <div class="service-header">
                        <div class="service-icon">
                            <i class="<?= htmlspecialchars($srv['icon']) ?>"></i>
                        </div>
                        <span class="service-badge"><?= htmlspecialchars($srv['badge']) ?></span>
                    </div>
                    <h3 class="service-title"><?= htmlspecialchars($srv['title']) ?></h3>
                    <p class="service-desc"><?= htmlspecialchars($srv['description']) ?></p>
                    <a href="<?= htmlspecialchars($srv['link_url']) ?>" class="service-link">
                        เข้าใช้บริการ <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- News & Announcements (ข่าวสารและกิจกรรมประชาสัมพันธ์ภาครัฐล้ำสมัย) -->
<section class="section" style="background: #ffffff;" id="news-section">
    <div class="container">
        <div class="section-title-wrap">
            <span class="badge-pill"><i class="fa-regular fa-newspaper"></i> ศูนย์ข่าวสารและกิจกรรมสุขภาพดิจิทัล</span>
            <h2 class="section-title">ข่าวประชาสัมพันธ์และประกาศสำคัญ</h2>
            <p class="section-subtitle">ติดตามความก้าวหน้านวัตกรรมสุขภาพ นโยบายกระทรวงสาธารณสุข และประกาศมาตรการความปลอดภัยสารสนเทศ</p>
        </div>

        <?php if ($spotlightNews): ?>
            <!-- 1. BENTO SHOWCASE: ข่าวเด่นทางการ (ซ้าย) + ข่าวล่าสุดด่วน (ขวา) -->
            <div class="bento-news-showcase">
                <!-- ข่าวเด่นทางการระดับนโยบาย (Spotlight Card) -->
                <article class="spotlight-news-card">
                    <div class="spotlight-thumb-wrap">
                        <img src="<?= htmlspecialchars($spotlightNews['image_url'] ?: 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?auto=format&fit=crop&w=1200&q=80') ?>" 
                             alt="<?= htmlspecialchars($spotlightNews['title']) ?>" 
                             class="spotlight-thumb"
                             loading="lazy">
                        <span class="spotlight-badge-official">
                            <i class="fa-solid fa-star"></i> ข่าวเด่นราชการ
                        </span>
                        <span class="spotlight-cat-tag">
                            <?= htmlspecialchars($spotlightNews['category_name'] ?: 'นโยบายและเทคโนโลยี') ?>
                        </span>
                    </div>
                    <div class="spotlight-body">
                        <div class="spotlight-meta-row">
                            <span class="spotlight-meta-item">
                                <i class="fa-regular fa-calendar-check"></i> <?= thaiDate($spotlightNews['published_at']) ?>
                            </span>
                            <span class="spotlight-meta-item">
                                <i class="fa-regular fa-eye"></i> <?= number_format($spotlightNews['views']) ?> ครั้ง
                            </span>
                            <span class="spotlight-meta-item">
                                <i class="fa-regular fa-clock"></i> ใช้เวลาอ่าน 3 นาที
                            </span>
                        </div>
                        <h3 class="spotlight-title">
                            <a href="news-detail.php?id=<?= $spotlightNews['id'] ?>">
                                <?= htmlspecialchars($spotlightNews['title']) ?>
                            </a>
                        </h3>
                        <p class="spotlight-excerpt">
                            <?= htmlspecialchars($spotlightNews['excerpt'] ?: mb_substr(strip_tags($spotlightNews['content']), 0, 150) . '...') ?>
                        </p>
                        <div class="spotlight-footer">
                            <span style="font-size: 12.5px; color: #64748b;">
                                <i class="fa-solid fa-shield-halved text-primary"></i> <?= htmlspecialchars($spotlightNews['author'] ?: 'ศูนย์สุขภาพดิจิทัล') ?>
                            </span>
                            <a href="news-detail.php?id=<?= $spotlightNews['id'] ?>" class="btn btn-primary btn-sm">
                                อ่านแถลงการณ์ฉบับเต็ม <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </article>

                <!-- ลิสต์ข่าวล่าสุด & ประกาศทันเหตุการณ์ (Trending Feed) -->
                <div class="trending-feed-wrap">
                    <div class="trending-feed-header">
                        <span class="trending-feed-title">
                            <i class="fa-solid fa-bolt text-warning"></i> ประกาศและข่าวสารด่วน
                        </span>
                        <a href="news.php" class="trending-feed-more">
                            ดูทั้งหมด <i class="fa-solid fa-chevron-right"></i>
                        </a>
                    </div>
                    <div class="trending-feed-list">
                        <?php foreach ($trendingNews as $trend): ?>
                            <a href="news-detail.php?id=<?= $trend['id'] ?>" class="trending-item">
                                <div class="trending-thumb-wrap">
                                    <img src="<?= htmlspecialchars($trend['image_url'] ?: 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?auto=format&fit=crop&w=400&q=80') ?>" 
                                         alt="<?= htmlspecialchars($trend['title']) ?>" 
                                         class="trending-thumb"
                                         loading="lazy">
                                </div>
                                <div class="trending-body">
                                    <span class="trending-cat"><?= htmlspecialchars($trend['category_name'] ?: 'ประชาสัมพันธ์') ?></span>
                                    <h4 class="trending-title"><?= htmlspecialchars($trend['title']) ?></h4>
                                    <div class="trending-meta">
                                        <span><i class="fa-regular fa-calendar"></i> <?= thaiDate($trend['published_at']) ?></span>
                                        <span><i class="fa-regular fa-eye"></i> <?= number_format($trend['views']) ?></span>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- 2. หมวดหมู่สำหรับกรองข่าวสาร (Category Filter Tabs with Real Counts) -->
        <div style="margin-top: 35px; margin-bottom: 25px;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px; margin-bottom: 16px;">
                <h3 style="font-size: 18px; font-weight: 700; color: var(--dark-navy);">
                    <i class="fa-solid fa-layer-group text-primary"></i> ข่าวสารและกิจกรรมทั้งหมด
                </h3>
                <div class="gov-tab-filter" id="govNewsTabs" style="margin: 0;">
                    <button class="gov-tab-item active" data-filter="all">ทั้งหมด</button>
                    <?php foreach ($newsCategories as $cat): ?>
                        <button class="gov-tab-item" data-filter="<?= htmlspecialchars($cat['slug']) ?>">
                            <?= htmlspecialchars($cat['name']) ?> (<?= $cat['news_count'] ?>)
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- 3. การ์ดข่าวสารแบบ Grid (Uniform Grid - ตัวหนังสือไม่ซ้อนทับกัน) -->
        <div class="news-grid">
            <?php foreach ($recentNews as $item): ?>
                <article class="news-card" data-category="<?= htmlspecialchars($item['category_slug'] ?? '') ?>">
                    <div class="news-thumb-wrap">
                        <img src="<?= htmlspecialchars($item['image_url'] ?: 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?auto=format&fit=crop&w=800&q=80') ?>" 
                             alt="<?= htmlspecialchars($item['title']) ?>" 
                             class="news-thumb"
                             loading="lazy">
                        <span class="news-cat-badge"><?= htmlspecialchars($item['category_name'] ?: 'ประชาสัมพันธ์') ?></span>
                        <?php if ($item['is_featured']): ?>
                            <span class="news-featured-tag"><i class="fa-solid fa-thumbtack"></i> ข่าวเด่น</span>
                        <?php endif; ?>
                    </div>
                    <div class="news-body">
                        <div class="news-meta">
                            <span><i class="fa-regular fa-calendar"></i> <?= thaiDate($item['published_at']) ?></span>
                            <span><i class="fa-regular fa-eye"></i> <?= number_format($item['views']) ?> ครั้ง</span>
                        </div>
                        <h3 class="news-title">
                            <a href="news-detail.php?id=<?= $item['id'] ?>"><?= htmlspecialchars($item['title']) ?></a>
                        </h3>
                        <p class="news-excerpt"><?= htmlspecialchars($item['excerpt']) ?></p>
                        <div class="news-footer">
                            <span style="font-size: 12px; color: #64748b;"><i class="fa-solid fa-user-pen"></i> <?= htmlspecialchars($item['author']) ?></span>
                            <a href="news-detail.php?id=<?= $item['id'] ?>" class="read-more-link">
                                อ่านต่อ <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <div style="text-align: center; margin-top: 40px;">
            <a href="news.php" class="btn btn-primary" style="padding: 12px 32px; font-size: 15px;">
                <i class="fa-solid fa-book-open"></i> เข้าสู่คลังข่าวสารและกิจกรรมทั้งหมด
            </a>
        </div>
    </div>
</section>

<!-- Statistics / Impact Numbers -->
<section class="stats-section">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-number">1.5M+</div>
                <div class="stat-label">ประชาชนใช้งาน Digital Health ID</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">480+</div>
                <div class="stat-label">โรงพยาบาลและ รพ.สต. เชื่อมโยงระบบ</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">95,000+</div>
                <div class="stat-label">ครั้งการรักษาผ่าน Telemedicine</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">99.98%</div>
                <div class="stat-label">ความปลอดภัยตามเกณฑ์ ISO/IEC 27001</div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="section" style="background: linear-gradient(135deg, #0d9488 0%, #0284c7 100%); color: white; text-align: center;">
    <div class="container">
        <h2 style="color: white; font-size: 32px; font-weight: 700; margin-bottom: 16px;">
            เริ่มต้นใช้บริการสุขภาพดิจิทัลเพื่อตัวคุณและครอบครัว
        </h2>
        <p style="color: #e0f2fe; max-width: 680px; margin: 0 auto 30px auto; font-size: 16px;">
            ลงทะเบียนยืนยันตัวตนวันนี้ เพื่อเข้าถึงประวัติการรักษา นัดหมายแพทย์ และปรึกษาอาการเบื้องต้นผ่านระบบออนไลน์
        </p>
        <div style="display: flex; justify-content: center; gap: 16px; flex-wrap: wrap;">
            <a href="register.php" class="btn btn-light" style="padding: 12px 30px; font-weight: 600; color: #0f766e;">
                <i class="fa-solid fa-user-plus"></i> สมัครสมาชิกรับสิทธิ์
            </a>
            <a href="contact.php" class="btn btn-outline-primary" style="padding: 12px 30px; color: white; border-color: white;">
                <i class="fa-solid fa-phone"></i> สอบถามข้อมูลเพิ่มเติม
            </a>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
