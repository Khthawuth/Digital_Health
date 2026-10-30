// assets/js/admin.js - Admin Backoffice Utilities

document.addEventListener('DOMContentLoaded', function () {
  // 1. Mobile Sidebar Toggle
  const adminMenuBtn = document.getElementById('adminSidebarToggle');
  const adminSidebar = document.querySelector('.admin-sidebar');
  if (adminMenuBtn && adminSidebar) {
    adminMenuBtn.addEventListener('click', function () {
      adminSidebar.classList.toggle('show');
    });
  }

  // 2. Image File Preview
  const imageInputs = document.querySelectorAll('.image-upload-input');
  imageInputs.forEach(input => {
    input.addEventListener('change', function () {
      const targetPreviewId = this.getAttribute('data-preview-target');
      const previewImg = document.getElementById(targetPreviewId);
      if (previewImg && this.files && this.files[0]) {
        const reader = new FileReader();
        reader.onload = function (e) {
          previewImg.src = e.target.result;
          previewImg.style.display = 'block';
          const placeholder = previewImg.parentElement.querySelector('.preview-placeholder');
          if (placeholder) placeholder.style.display = 'none';
        };
        reader.readAsDataURL(this.files[0]);
      }
    });
  });

  // 3. Confirm Delete Prompts
  const deleteButtons = document.querySelectorAll('.confirm-delete');
  deleteButtons.forEach(btn => {
    btn.addEventListener('click', function (e) {
      const itemTitle = this.getAttribute('data-title') || 'รายการนี้';
      if (!confirm(`คุณแน่ใจหรือไม่ว่าต้องการลบ "${itemTitle}"? ข้อมูลที่ลบแล้วจะไม่สามารถกู้คืนได้`)) {
        e.preventDefault();
      }
    });
  });
});
