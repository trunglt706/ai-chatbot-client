import "./bootstrap";
import * as Trix from "trix";
import "trix/dist/trix.css"; // Import CSS của Trix

import Alpine from "alpinejs";
import $ from "jquery"; // PHẢI IMPORT jQuery TRƯỚC
window.$ = window.jQuery = $; // Đảm bảo jQuery có sẵn toàn cục

import "select2"; // PHẢI IMPORT Select2 SAU jQuery

// Các dòng Alpine.js
window.Alpine = Alpine;
Alpine.start();

// KHÔNG NÊN KHỞI TẠO SELECT2 Ở ĐÂY NẾU BẠN DÙNG @push/@stack
// Đoạn code này chỉ nên có nếu bạn muốn áp dụng Select2 cho MỌI .select2 element trên MỌI TRANG
/*
$(document).ready(function () {
    console.log("Document ready for Select2 initialization from app.js.");
    $(".select2").select2({
        placeholder: "Select data",
        theme: "bootstrap-5",
    });
});
*/

console.log("app.js loaded, jQuery and Select2 should be available.");
if ($.fn.select2) {
    console.log("Select2 function is available on jQuery from app.js.");
} else {
    console.log(
        "Select2 function is NOT available on jQuery from app.js. Check import."
    );
}

if (window.Echo && window.Laravel.user) {
    // Kiểm tra nếu Echo và thông tin user có sẵn
    window.Echo.private(`users.${window.Laravel.user.id}`).listen(
        ".user.blocked",
        (e) => {
            console.log("User Blocked Event Received:", e);
            alert(e.message); // Hiển thị thông báo
            window.location.href = e.redirect_url; // Chuyển hướng
        }
    );
}
