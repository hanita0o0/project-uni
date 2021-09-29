-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 11, 2021 at 10:34 PM
-- Server version: 10.4.20-MariaDB
-- PHP Version: 8.0.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `project-uni`
--

-- --------------------------------------------------------

--
-- Table structure for table `auctions`
--

CREATE TABLE `auctions` (
  `id` int(11) NOT NULL,
  `code` varchar(50) COLLATE utf8_persian_ci NOT NULL,
  `price` int(11) NOT NULL,
  `wage` int(11) NOT NULL,
  `base_price` int(11) NOT NULL,
  `start_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `winner` varchar(50) COLLATE utf8_persian_ci DEFAULT NULL,
  `registration_cost` int(11) DEFAULT NULL,
  `capacity_participants` int(11) NOT NULL,
  `final_price` int(11) DEFAULT NULL,
  `final_time` varchar(50) COLLATE utf8_persian_ci DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `status_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

--
-- Dumping data for table `auctions`
--

INSERT INTO `auctions` (`id`, `code`, `price`, `wage`, `base_price`, `start_at`, `winner`, `registration_cost`, `capacity_participants`, `final_price`, `final_time`, `product_id`, `status_id`, `created_at`) VALUES
(1, '2020', 4000000, 40000, 100000, '2021-09-30 09:30:00', NULL, 30000, 10, NULL, NULL, 1, 2, '2021-09-11 20:23:49'),
(2, '2021', 948000, 30000, 50000, '2021-09-24 20:30:00', NULL, 10000, 10, NULL, NULL, 12, 2, '2021-09-11 20:26:40'),
(3, '2022', 350000, 20000, 50000, '2021-09-24 20:30:00', NULL, 10000, 10, NULL, NULL, 11, 2, '2021-09-11 20:27:43');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(11) NOT NULL,
  `migration` varchar(255) COLLATE utf8_persian_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `created_at`) VALUES
(1, 'm00_create_users_table.php', '2021-09-10 20:08:26'),
(2, 'm01_create_products_table.php', '2021-09-10 20:08:26'),
(3, 'm02_create_status_table.php', '2021-09-10 20:08:26'),
(4, 'm03_create_auctions_table.php', '2021-09-10 20:08:26'),
(5, 'm04_create_participants_table.php', '2021-09-10 20:08:26');

-- --------------------------------------------------------

--
-- Table structure for table `participants`
--

CREATE TABLE `participants` (
  `id` int(11) NOT NULL,
  `bid_capacity` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `auction_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

--
-- Dumping data for table `participants`
--

INSERT INTO `participants` (`id`, `bid_capacity`, `user_id`, `auction_id`, `created_at`) VALUES
(1, 10, 2, 1, '2021-09-11 20:30:33'),
(2, 20, 1, 1, '2021-09-11 20:31:50');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(50) COLLATE utf8_persian_ci NOT NULL,
  `model` varchar(50) COLLATE utf8_persian_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_persian_ci DEFAULT NULL,
  `image` varchar(50) COLLATE utf8_persian_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `model`, `description`, `image`, `created_at`) VALUES
(1, 'گوشی موبایل سامسونگ', 'مدل Galaxy A21S SM-A217F/DS', 'حافظه داخلی:\n64 گیگابایت\nشبکه های ارتباطی:\n4G، 3G، 2G\nدوربین‌های پشت گوشی:\n4 ماژول دوربین\nسیستم عامل:\nAndroid\nتوضیحات سیم کارت:\nسایز نانو (8.8 × 12.3 میلی‌متر)\nمقدار RAM:\n4 گیگابایت\nنسخه سیستم عامل:\nAndroid 10\nفناوری صفحه‌نمایش:\nPLS TFT\nوی', '/upload/products/mobile/GalaxyA21S-SM-A217F-DS.jpg', '2021-09-11 19:32:29'),
(2, 'گوشی موبایل اپل', 'iPhone 12 A2404 ', 'حافظه داخلی:\r\n128 گیگابایت\r\nشبکه های ارتباطی:\r\n5G، 4G، 3G، 2G\r\nدوربین‌های پشت گوشی:\r\n2 ماژول دوربین\r\nسیستم عامل:\r\niOS\r\nتوضیحات سیم کارت:\r\nسایز نانو (8.8 × 12.3 میلی‌متر)\r\nمقدار RAM:\r\n4 گیگابایت\r\nرزولوشن عکس:\r\n12 مگاپیکسل\r\nنسخه سیستم عامل:\r\niOS 14\r\nفناوری ', '/upload/products/mobile/iPhone-12-A2404 .jpg', '2021-09-11 19:39:38'),
(3, 'ماشین اصلاح صورت فیلیپس', 'مدل AT890/20', 'منبع انرژی\r\nآداپتور برق\r\nسایر مشخصات\r\n- ابعاد: 99 × 157 × 48 میلی‌متر\r\n- نوع باتری: لیتیوم یونی\r\n- وجود سیستم Quick Rinse که باعث می شود بتوانید تیغه ها را زیر آب بشویید و یا از آن زیر دوش استفاده کنید\r\n- سیستم تیغه های خیلی دقیق 2 دقته\r\nمدت زمان استفاده ', '/upload/products/beauty/philipsAT890-20.jpg', '2021-09-11 19:46:58'),
(4, 'ادو پرفیوم زنانه رووناکس', 'مدل گود گرل حجم 100 میلی لیتر', 'نت آغازی\r\nبادام و قهوه\r\nنت پایانی\r\nدانه‌ی تونکا و کاکائو\r\nنت میانی\r\nگل مریم و یاسمین\r\nرایحه\r\nشیرین , گرم , شرقی , گل\r\nمناسب برای\r\nبانوان\r\nساختار رایحه\r\nشکلات , میوه , گیاهان معطر , گل\r\nحجم\r\n100', '/upload/products/beauty/perfume.jpg', '2021-09-11 19:53:14'),
(5, 'کفش مخصوص پیاده روی زنانه', ' رامیلا کد 7332', 'جنس زیره:\r\nپلی اورتان\r\nکفی:\r\nقابل تعویض\r\nنحوه بسته شدن کفش:\r\nبندی\r\nویژگی‌های تخصصی کفش:\r\nمقاوم در برابر سایش\r\nویژگی‌های زیره:\r\nکاهش فشار وارده\r\nکشور تولید کننده:\r\nایران', '/upload/products/fashion/kafsh.jpg', '2021-09-11 19:58:11'),
(6, 'بوت زنانه گابور', 'مدل 31.642.28', 'فرم نوک کفش:\r\nنوک پهن\r\nارتفاع ساق:\r\nبالای مچ پا\r\nمدل پاشنه:\r\nپهن\r\nنحوه بسته شدن کفش:\r\nزیپی\r\nمورد استفاده:\r\nروزمره\r\nکشور مبدا برند:\r\nآلمان', '/upload/products/fashion/boot.jpg', '2021-09-11 20:00:23'),
(7, 'کرم ضد آفتاب جوان کننده پریم ', 'مدل Matex Tinted SPF50', 'کشور مبدا برند\r\nایران\r\nسایر مشخصات\r\nحفاظت برتر پوست در برابر اشعه های UVA، UVB، IR\r\nجوان کننده ی پوست از طریق مکانیسم شبه بوتاکس و شبه رتینوئیدی توسط عصاره ی معجزه آسای تاک\r\nضد چروک و مرطوب کننده\r\nسرشار از ویتامین E با خواص آنتی اکسیدانی قوی\r\nرنگی (بژ) با', '/upload/products/beauty/cream.jpg', '2021-09-11 20:05:01'),
(8, 'مسواک سنسوداین', 'مدل Multicare با برس متوسط بسته دو عددی', 'سایز\r\nمتوسط\r\nکشور مبدا برند\r\nانگلستان\r\nسایز سری\r\n2 × 1\r\nمیزان زبری برس\r\nمتوسط\r\nصادر کننده مجوز\r\nسازمان غذا و دارو', '/upload/products/beauty/brush.jpg', '2021-09-11 20:07:15'),
(9, 'لپ تاپ 15.6 اینچی اچ پی', 'مدل HP 15-DA2989NIA - D', 'ظرفیت حافظه RAM:\r\n8 گیگابایت\r\nظرفیت حافظه داخلی:\r\nیک ترابایت\r\nسازنده پردازنده گرافیکی:\r\nNVIDIA\r\nاندازه صفحه نمایش:\r\n15.6 اینچ\r\nطبقه‌بندی:\r\nکاربری عمومی\r\nسری پردازنده:\r\nCore i5\r\nنوع حافظه RAM:\r\nDDR4\r\nدقت صفحه نمایش:\r\nHD|1366x768\r\nصفحه نمایش مات:\r\nبله\r\nصفحه', '/upload/products/laptop/hplaptop.jpg', '2021-09-11 20:10:17'),
(10, 'لپ تاپ 15 اینچی لنوو ', 'Ideapad 330 - E  مدل', 'ظرفیت حافظه RAM:\r\n4 گیگابایت\r\nظرفیت حافظه داخلی:\r\nیک ترابایت\r\nسازنده پردازنده گرافیکی:\r\nIntel\r\nاندازه صفحه نمایش:\r\n15.6 اینچ\r\nطبقه‌بندی:\r\nباریک و سبک، کاربری عمومی\r\nسری پردازنده:\r\nCeleron\r\nنوع حافظه RAM:\r\nDDR4\r\nدقت صفحه نمایش:\r\nHD|1366x768\r\nصفحه نمایش مات', '/upload/products/laptop/lenovo.jpg', '2021-09-11 20:13:39'),
(11, 'سرویس خواب 4 تکه کودک ', 'طرح کوالا کد 200', 'قابلیت شست‌وشو\r\nتوسط دست , توسط ماشین لباس‌شویی\r\nوزن لحاف\r\n325\r\nوزن قنداق\r\n275\r\nوزن تشک\r\n1025\r\nوزن بسته‌بندی\r\n1800\r\nوزن بالش\r\n125\r\nابعاد لحاف\r\n60x80\r\nابعاد قنداق\r\n35x45\r\nابعاد تشک\r\n45x75\r\nابعاد بسته‌بندی\r\n15x48x75\r\nابعاد بالش\r\n20x30\r\nشامل\r\nتشک , بالش , لح', '/upload/products/child/child.jpg', '2021-09-11 20:16:20'),
(12, 'سرویس خواب 8 تکه کودک', 'مدل Smart Rabbit', 'قابلیت شست‌وشو\r\nتوسط دست , توسط ماشین لباس‌شویی\r\nوزن بسته‌بندی\r\n4850\r\nابعاد ملحفه\r\n120*220\r\nابعاد لحاف\r\n3*100*140\r\nابعاد دور تختی\r\n10*40*40\r\nابعاد بسته‌بندی\r\n60*70*120\r\nشامل\r\nملحفه , رو بالشی , دور تختی , لحاف', '/upload/products/child/child1.jpg', '2021-09-11 20:18:37');

-- --------------------------------------------------------

--
-- Table structure for table `status`
--

CREATE TABLE `status` (
  `id` int(11) NOT NULL,
  `name` varchar(50) COLLATE utf8_persian_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

--
-- Dumping data for table `status`
--

INSERT INTO `status` (`id`, `name`) VALUES
(1, 'برگزارشده'),
(2, 'برگزارنشده'),
(3, 'درحال برگزاری');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `mobile` varchar(15) COLLATE utf8_persian_ci NOT NULL,
  `verification_code` varchar(10) COLLATE utf8_persian_ci DEFAULT NULL,
  `verified` tinyint(1) NOT NULL DEFAULT 0,
  `username` varchar(50) COLLATE utf8_persian_ci DEFAULT NULL,
  `firstname` varchar(50) COLLATE utf8_persian_ci DEFAULT NULL,
  `lastname` varchar(50) COLLATE utf8_persian_ci DEFAULT NULL,
  `email` varchar(50) COLLATE utf8_persian_ci DEFAULT NULL,
  `introduced_no` varchar(15) COLLATE utf8_persian_ci DEFAULT NULL,
  `wallet` int(11) DEFAULT 0,
  `image` varchar(50) COLLATE utf8_persian_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `mobile`, `verification_code`, `verified`, `username`, `firstname`, `lastname`, `email`, `introduced_no`, `wallet`, `image`, `created_at`) VALUES
(1, '09906771338', '5208', 1, 'hanita0o0', 'shahla', 'alamir', 'shahi.alamir@gmail.com', '', 140000, NULL, '2021-09-11 19:05:01'),
(2, '09369689538', '1112', 1, 'hosein0o0', 'hosein', 'derakhshani', 'hosein.derakhshani76@gmail.com', '', 170000, NULL, '2021-09-11 19:18:27');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `auctions`
--
ALTER TABLE `auctions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `status_id` (`status_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `participants`
--
ALTER TABLE `participants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `auction_id` (`auction_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `status`
--
ALTER TABLE `status`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `auctions`
--
ALTER TABLE `auctions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `participants`
--
ALTER TABLE `participants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `status`
--
ALTER TABLE `status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `auctions`
--
ALTER TABLE `auctions`
  ADD CONSTRAINT `auctions_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `auctions_ibfk_2` FOREIGN KEY (`status_id`) REFERENCES `status` (`id`);

--
-- Constraints for table `participants`
--
ALTER TABLE `participants`
  ADD CONSTRAINT `participants_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `participants_ibfk_2` FOREIGN KEY (`auction_id`) REFERENCES `auctions` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
