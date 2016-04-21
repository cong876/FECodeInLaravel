-- phpMyAdmin SQL Dump
-- version 3.5.3
-- http://www.phpmyadmin.net
--
-- 主机: 10.10.181.112:3306
-- 生成日期: 2015 年 09 月 11 日 18:01
-- 服务器版本: 5.6.20-ucloudrel1-log
-- PHP 版本: 5.4.41

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `homestead`
--

-- --------------------------------------------------------

--
-- 表的结构 `employees`
--

CREATE TABLE IF NOT EXISTS `employees` (
  `employee_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `hlj_id` int(10) unsigned NOT NULL,
  `ye_code` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `real_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `name_pinyin` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `name_abbreviation` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `identity_card_no` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` int(10) unsigned NOT NULL DEFAULT '0',
  `op_level` int(10) unsigned NOT NULL DEFAULT '0',
  `is_available` tinyint(1) NOT NULL DEFAULT '0',
  `entry_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `birthday` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`employee_id`),
  KEY `employees_hlj_id_foreign` (`hlj_id`),
  KEY `employees_ye_code_index` (`ye_code`),
  KEY `employees_real_name_index` (`real_name`),
  KEY `employees_name_pinyin_index` (`name_pinyin`),
  KEY `employees_name_abbreviation_index` (`name_abbreviation`),
  KEY `employees_identity_card_no_index` (`identity_card_no`),
  KEY `employees_type_index` (`type`),
  KEY `employees_op_level_index` (`op_level`),
  KEY `employees_is_available_index` (`is_available`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=10 ;

--
-- 转存表中的数据 `employees`
--

INSERT INTO `employees` (`employee_id`, `hlj_id`, `ye_code`, `real_name`, `name_pinyin`, `name_abbreviation`, `identity_card_no`, `type`, `op_level`, `is_available`, `entry_date`, `birthday`, `created_at`, `updated_at`) VALUES
(1, 1, '', '韦晓晴', 'weixiaoqing', 'wxq', '450221199001030023', 1, 4, 1, '0000-00-00 00:00:00', '1990-01-02 16:00:00', '2015-09-10 14:03:17', '2015-09-10 14:03:17'),
(2, 2, '', '曹莅祥', 'caolixiang', 'clx', '530103198908020314', 2, 4, 1, '0000-00-00 00:00:00', '1990-08-01 15:00:00', '2015-09-10 14:05:15', '2015-09-10 14:12:54'),
(3, 3, '', '王诗萌', 'wangshimeng', 'wsm', '210123199101090018', 2, 3, 1, '0000-00-00 00:00:00', '1991-01-08 16:00:00', '2015-09-10 14:07:41', '2015-09-11 10:00:31'),
(4, 4, '', '王聪', 'wangcong', 'wc', '230822199302246133', 2, 3, 1, '0000-00-00 00:00:00', '1993-02-23 16:00:00', '2015-09-10 14:11:56', '2015-09-11 10:00:22'),
(5, 5, '', '吴湘如', 'wuxiangru', 'wxr', '452226198910040025', 1, 3, 1, '0000-00-00 00:00:00', '1989-10-03 16:00:00', '2015-09-10 14:14:27', '2015-09-11 10:00:08'),
(6, 6, '', '李文娟', 'liwenjuan', 'lwj', '120222199202190621', 1, 3, 1, '0000-00-00 00:00:00', '1992-02-18 16:00:00', '2015-09-10 14:16:53', '2015-09-11 09:59:58'),
(7, 7, '', '陆烨', 'luye', 'ly', '320582199307183620', 1, 3, 1, '0000-00-00 00:00:00', '1993-07-17 16:00:00', '2015-09-10 14:20:55', '2015-09-11 09:59:48'),
(8, 8, '', '王旖文', 'wangyiwen', 'wyw', '321283199404249020', 1, 3, 1, '0000-00-00 00:00:00', '1994-04-23 16:00:00', '2015-09-10 14:24:32', '2015-09-11 09:59:39'),
(9, 9, '', '杨雯铄', 'yangwenshuo', 'yws', '220602199205200629', 3, 3, 1, '0000-00-00 00:00:00', '1992-05-19 16:00:00', '2015-09-11 09:58:13', '2015-09-11 09:59:17');

--
-- 限制导出的表
--

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
