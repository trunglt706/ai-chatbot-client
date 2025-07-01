<?php

if (!function_exists('format_size_units')) {
    /**
     * Helper function to format bytes into readable units.
     *
     * @param int $bytes
     * @return string
     */
    function format_size_units($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}

if (!function_exists('currency_to_number')) {
    /**
     * Chuyển đổi chuỗi tiền tệ đã định dạng (ví dụ: "1.234.567") thành số nguyên.
     * Hỗ trợ dấu chấm hoặc dấu phẩy làm dấu phân cách hàng nghìn.
     *
     * @param string $formattedCurrency
     * @return int|float
     */
    function currency_to_number(string $formattedCurrency)
    {
        // Xóa tất cả các dấu phân cách hàng nghìn (dấu chấm hoặc dấu phẩy)
        // và khoảng trắng không mong muốn.
        $numberString = str_replace(['.', ',', ' '], '', $formattedCurrency);

        // Kiểm tra nếu có dấu thập phân cuối cùng (không cần cho tiền VND thường)
        // Nếu có, có thể cần xử lý riêng hoặc ép kiểu float
        // Ví dụ: "1.234.567,89" -> "123456789"
        // Với tiền VND thông thường, ta chỉ cần int
        return (int) $numberString;
    }
}

if (!function_exists('number_to_currency')) {
    /**
     * Chuyển đổi số thành chuỗi tiền tệ đã định dạng (ví dụ: "1.234.567").
     *
     * @param int|float $number
     * @return string
     */
    function number_to_currency($number)
    {
        return number_format($number, 0, ',', '.'); // 0 số thập phân, dấu phẩy cho thập phân (không dùng), dấu chấm cho hàng nghìn
    }
}
