// assets/js/main.js - Interactive features for Digital Health Portal

document.addEventListener('DOMContentLoaded', function () {
  // 1. Mobile Off-Canvas Drawer Navigation
  const mobileToggle = document.getElementById('mobileToggle');
  const mobileDrawer = document.getElementById('mobileDrawer');
  const mobileDrawerBackdrop = document.getElementById('mobileDrawerBackdrop');
  const mobileDrawerClose = document.getElementById('mobileDrawerClose');

  function openMobileDrawer() {
    if (mobileDrawer && mobileDrawerBackdrop) {
      mobileDrawer.classList.add('open');
      mobileDrawerBackdrop.classList.add('open');
      document.body.style.overflow = 'hidden'; // prevent background scrolling
    }
  }

  function closeMobileDrawer() {
    if (mobileDrawer && mobileDrawerBackdrop) {
      mobileDrawer.classList.remove('open');
      mobileDrawerBackdrop.classList.remove('open');
      document.body.style.overflow = '';
    }
  }

  if (mobileToggle) {
    mobileToggle.addEventListener('click', openMobileDrawer);
  }
  if (mobileDrawerClose) {
    mobileDrawerClose.addEventListener('click', closeMobileDrawer);
  }
  if (mobileDrawerBackdrop) {
    mobileDrawerBackdrop.addEventListener('click', closeMobileDrawer);
  }
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && mobileDrawer && mobileDrawer.classList.contains('open')) {
      closeMobileDrawer();
    }
  });

  // 2. Government Accessibility Toolbar (ขนาดอักษร & ธีมการแสดงผล)
  const btnFontDec = document.getElementById('btnFontDec');
  const btnFontReset = document.getElementById('btnFontReset');
  const btnFontInc = document.getElementById('btnFontInc');
  const accessBtns = [btnFontDec, btnFontReset, btnFontInc];

  const btnThemeNormal = document.getElementById('btnThemeNormal');
  const btnThemeWarm = document.getElementById('btnThemeWarm');
  const btnThemeContrast = document.getElementById('btnThemeContrast');
  const themeBtns = [btnThemeNormal, btnThemeWarm, btnThemeContrast];

  // Load saved preferences
  const savedFontSize = localStorage.getItem('gov_font_size');
  if (savedFontSize === 'large') {
    document.body.classList.add('font-large');
    updateAccessBtnActive(btnFontInc);
  } else if (savedFontSize === 'small') {
    document.body.classList.add('font-small');
    updateAccessBtnActive(btnFontDec);
  }

  const savedTheme = localStorage.getItem('gov_theme');
  if (savedTheme === 'warm') {
    document.body.classList.add('theme-warm');
    updateThemeBtnActive(btnThemeWarm);
  } else if (savedTheme === 'contrast') {
    document.body.classList.add('theme-contrast');
    updateThemeBtnActive(btnThemeContrast);
  }

  function updateAccessBtnActive(activeBtn) {
    accessBtns.forEach(btn => btn && btn.classList.remove('active'));
    if (activeBtn) activeBtn.classList.add('active');
  }

  function updateThemeBtnActive(activeBtn) {
    themeBtns.forEach(btn => btn && btn.classList.remove('active'));
    if (activeBtn) activeBtn.classList.add('active');
  }

  if (btnFontDec) {
    btnFontDec.addEventListener('click', function () {
      document.body.classList.remove('font-large');
      document.body.classList.add('font-small');
      localStorage.setItem('gov_font_size', 'small');
      updateAccessBtnActive(btnFontDec);
    });
  }

  if (btnFontReset) {
    btnFontReset.addEventListener('click', function () {
      document.body.classList.remove('font-large', 'font-small');
      localStorage.removeItem('gov_font_size');
      updateAccessBtnActive(btnFontReset);
    });
  }

  if (btnFontInc) {
    btnFontInc.addEventListener('click', function () {
      document.body.classList.remove('font-small');
      document.body.classList.add('font-large');
      localStorage.setItem('gov_font_size', 'large');
      updateAccessBtnActive(btnFontInc);
    });
  }

  if (btnThemeNormal) {
    btnThemeNormal.addEventListener('click', function () {
      document.body.classList.remove('theme-warm', 'theme-contrast');
      localStorage.removeItem('gov_theme');
      updateThemeBtnActive(btnThemeNormal);
    });
  }

  if (btnThemeWarm) {
    btnThemeWarm.addEventListener('click', function () {
      document.body.classList.remove('theme-contrast');
      document.body.classList.add('theme-warm');
      localStorage.setItem('gov_theme', 'warm');
      updateThemeBtnActive(btnThemeWarm);
    });
  }

  if (btnThemeContrast) {
    btnThemeContrast.addEventListener('click', function () {
      document.body.classList.remove('theme-warm');
      document.body.classList.add('theme-contrast');
      localStorage.setItem('gov_theme', 'contrast');
      updateThemeBtnActive(btnThemeContrast);
    });
  }

  // 3. Hero Banner Carousel with Ken Burns Zoom & Progress Line
  const slides = document.querySelectorAll('.hero-slider .slide');
  const dots = document.querySelectorAll('.slider-dots .dot');
  const prevBtn = document.getElementById('prevSlide');
  const nextBtn = document.getElementById('nextSlide');
  const togglePlayBtn = document.getElementById('togglePlaySlide');
  const progressFill = document.getElementById('sliderProgressFill');

  let currentSlide = 0;
  let slideInterval = null;
  let progressInterval = null;
  const slideDuration = 6000; // 6 seconds per slide
  let isPlaying = true;

  if (slides.length > 0) {
    function goToSlide(index) {
      slides[currentSlide].classList.remove('active');
      if (dots[currentSlide]) dots[currentSlide].classList.remove('active');

      currentSlide = (index + slides.length) % slides.length;

      slides[currentSlide].classList.add('active');
      if (dots[currentSlide]) dots[currentSlide].classList.add('active');

      resetProgress();
    }

    function nextSlide() {
      goToSlide(currentSlide + 1);
    }

    function prevSlide() {
      goToSlide(currentSlide - 1);
    }

    function resetProgress() {
      if (!progressFill) return;
      progressFill.style.transition = 'none';
      progressFill.style.width = '0%';
      setTimeout(() => {
        if (isPlaying) {
          progressFill.style.transition = `width ${slideDuration}ms linear`;
          progressFill.style.width = '100%';
        }
      }, 50);
    }

    function startTimer() {
      if (slides.length > 1 && isPlaying) {
        clearInterval(slideInterval);
        resetProgress();
        slideInterval = setInterval(nextSlide, slideDuration);
      }
    }

    function stopTimer() {
      clearInterval(slideInterval);
      if (progressFill) {
        progressFill.style.transition = 'none';
        progressFill.style.width = '0%';
      }
    }

    if (nextBtn) {
      nextBtn.addEventListener('click', function () {
        nextSlide();
        if (isPlaying) startTimer();
      });
    }

    if (prevBtn) {
      prevBtn.addEventListener('click', function () {
        prevSlide();
        if (isPlaying) startTimer();
      });
    }

    if (togglePlayBtn) {
      togglePlayBtn.addEventListener('click', function () {
        isPlaying = !isPlaying;
        const icon = togglePlayBtn.querySelector('i');
        if (isPlaying) {
          icon.className = 'fa-solid fa-pause';
          togglePlayBtn.title = 'หยุดเล่นชั่วคราว';
          startTimer();
        } else {
          icon.className = 'fa-solid fa-play';
          togglePlayBtn.title = 'เริ่มเล่นสไลด์อัตโนมัติ';
          stopTimer();
        }
      });
    }

    dots.forEach((dot, idx) => {
      dot.addEventListener('click', function () {
        goToSlide(idx);
        if (isPlaying) startTimer();
      });
    });

    startTimer();

    // Pause on hover
    const heroWrap = document.querySelector('.hero-slider-wrap');
    if (heroWrap) {
      heroWrap.addEventListener('mouseenter', () => {
        if (isPlaying) stopTimer();
      });
      heroWrap.addEventListener('mouseleave', () => {
        if (isPlaying) startTimer();
      });
    }
  }

  // 4. Government News Category Tab Filter
  const newsTabs = document.querySelectorAll('#govNewsTabs .gov-tab-item');
  const newsCards = document.querySelectorAll('.news-grid .news-card');

  if (newsTabs.length > 0 && newsCards.length > 0) {
    newsTabs.forEach(tab => {
      tab.addEventListener('click', function () {
        newsTabs.forEach(t => t.classList.remove('active'));
        this.classList.add('active');

        const filter = this.getAttribute('data-filter');

        newsCards.forEach(card => {
          const catBadge = card.querySelector('.news-cat-badge');
          const catText = catBadge ? catBadge.textContent.trim() : '';

          if (filter === 'all') {
            card.style.display = 'flex';
          } else if (filter === 'general-pr' && catText.includes('ประชาสัมพันธ์')) {
            card.style.display = 'flex';
          } else if (filter === 'health-tech' && (catText.includes('นวัตกรรม') || catText.includes('เทคโนโลยี'))) {
            card.style.display = 'flex';
          } else if (filter === 'policy-security' && (catText.includes('นโยบาย') || catText.includes('ปลอดภัย') || catText.includes('อบรม'))) {
            card.style.display = 'flex';
          } else if (filter === 'procurement' && (catText.includes('จัดซื้อ') || catText.includes('ประกาศ'))) {
            card.style.display = 'flex';
          } else {
            card.style.display = 'none';
          }
        });
      });
    });
  }

  // 5. 2FA OTP 6-Digit Auto Focus Inputs
  const otpInputs = document.querySelectorAll('.otp-input-field');
  const fullOtpInput = document.getElementById('full_otp_code');

  if (otpInputs.length > 0) {
    otpInputs[0].focus();

    otpInputs.forEach((input, index) => {
      input.addEventListener('input', function () {
        this.value = this.value.replace(/[^0-9]/g, '');

        if (this.value.length >= 1) {
          if (index < otpInputs.length - 1) {
            otpInputs[index + 1].focus();
          }
        }
        updateFullCode();
      });

      input.addEventListener('keydown', function (e) {
        if (e.key === 'Backspace' && this.value === '' && index > 0) {
          otpInputs[index - 1].focus();
        }
      });

      input.addEventListener('paste', function (e) {
        e.preventDefault();
        const pastedData = (e.clipboardData || window.clipboardData).getData('text').trim();
        const cleanData = pastedData.replace(/[^0-9]/g, '').slice(0, 6);

        cleanData.split('').forEach((char, i) => {
          if (otpInputs[i]) {
            otpInputs[i].value = char;
          }
        });

        const nextIndex = Math.min(cleanData.length, otpInputs.length - 1);
        otpInputs[nextIndex].focus();
        updateFullCode();
      });
    });

    function updateFullCode() {
      let code = '';
      otpInputs.forEach(inp => code += inp.value);
      if (fullOtpInput) fullOtpInput.value = code;
    }
  }

  // 6. Copy OTP helper button
  const copyOtpBtn = document.getElementById('copyOtpBtn');
  if (copyOtpBtn) {
    copyOtpBtn.addEventListener('click', function () {
      const otpCode = this.getAttribute('data-otp');
      if (otpCode && otpInputs.length === 6) {
        otpCode.split('').forEach((char, idx) => {
          otpInputs[idx].value = char;
        });
        updateFullCode();
        this.innerHTML = '<i class="fa-solid fa-check"></i> วางรหัสแล้ว';
        setTimeout(() => {
          this.innerHTML = '<i class="fa-regular fa-copy"></i> นำรหัสไปกรอกอัตโนมัติ';
        }, 2000);
      }
    });
  }

  // 7. 2FA Countdown Timer
  const countdownEl = document.getElementById('otpCountdown');
  if (countdownEl) {
    let timeLeft = 600;
    const timerInterval = setInterval(function () {
      if (timeLeft <= 0) {
        clearInterval(timerInterval);
        countdownEl.innerHTML = '<span class="text-danger">รหัสหมดอายุแล้ว กรุณากดขอรหัสใหม่</span>';
      } else {
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        countdownEl.textContent = `รหัสจะหมดอายุในอีก ${minutes}:${seconds < 10 ? '0' : ''}${seconds} นาที`;
        timeLeft--;
      }
    }, 1000);
  }
});
