-- phpMyAdmin SQL Dump
-- version 3.5.3
-- http://www.phpmyadmin.net
--
-- 主机: 10.10.168.211:3306
-- 生成日期: 2015 年 09 月 16 日 13:43
-- 服务器版本: 5.6.20-ucloudrel1-log
-- PHP 版本: 5.4.41

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `yeyeservice`
--

-- --------------------------------------------------------

--
-- 表的结构 `sellers`
--

CREATE TABLE IF NOT EXISTS `sellers` (
  `seller_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `hlj_id` int(10) unsigned NOT NULL,
  `seller_type` int(11) NOT NULL DEFAULT '1',
  `country_id` int(11) NOT NULL,
  `real_name` varchar(255) NOT NULL,
  `name_pinyin` varchar(255) NOT NULL,
  `name_abbreviation` varchar(255) NOT NULL,
  `seller_recent_area` varchar(255) DEFAULT NULL,
  `seller_memo` varchar(1000) DEFAULT NULL,
  `seller_receive_orders_num` int(11) NOT NULL DEFAULT '0',
  `seller_refuse_orders_num` int(11) NOT NULL DEFAULT '0',
  `seller_success_orders_num` int(11) NOT NULL DEFAULT '0',
  `seller_success_incoming` double NOT NULL DEFAULT '0',
  `seller_gmv` double NOT NULL DEFAULT '0',
  `is_available` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`seller_id`),
  KEY `sellers_hlj_id_foreign` (`hlj_id`),
  KEY `sellers_seller_type_index` (`seller_type`),
  KEY `sellers_country_id_index` (`country_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- 转存表中的数据 `sellers`
--

INSERT INTO `sellers` (`seller_id`, `hlj_id`, `seller_type`, `country_id`, `real_name`, `name_pinyin`, `name_abbreviation`, `seller_recent_area`, `seller_memo`, `seller_receive_orders_num`, `seller_refuse_orders_num`, `seller_success_orders_num`, `seller_success_incoming`, `seller_gmv`, `is_available`, `created_at`, `updated_at`) VALUES
(1, 1, 3, 11, '韦晓晴', 'weixiaoqing', 'wxq', NULL, NULL, 0, 0, 0, 0, 0, 1, '2015-09-10 14:03:17', '2015-09-11 10:00:43'),
(2, 2, 3, 11, '曹莅祥', 'caolixiang', 'clx', NULL, NULL, 0, 0, 0, 0, 0, 1, '2015-09-10 14:05:16', '2015-09-10 14:12:44'),
(3, 3, 3, 11, '王诗萌', 'wangshimeng', 'wsm', NULL, NULL, 0, 0, 0, 0, 0, 1, '2015-09-10 14:07:41', '2015-09-11 10:00:31'),
(4, 4, 3, 11, '王聪', 'wangcong', 'wc', NULL, NULL, 0, 0, 0, 0, 0, 1, '2015-09-10 14:11:56', '2015-09-11 10:00:23'),
(5, 5, 3, 11, '吴湘如', 'wuxiangru', 'wxr', NULL, NULL, 0, 0, 0, 0, 0, 1, '2015-09-10 14:14:27', '2015-09-11 10:00:08'),
(6, 6, 3, 11, '李文娟', 'liwenjuan', 'lwj', NULL, NULL, 0, 0, 0, 0, 0, 1, '2015-09-10 14:16:53', '2015-09-11 09:59:59'),
(7, 7, 3, 11, '陆烨', 'luye', 'ly', NULL, NULL, 0, 0, 0, 0, 0, 1, '2015-09-10 14:20:55', '2015-09-11 09:59:49'),
(8, 8, 3, 11, '王旖文', 'wangyiwen', 'wyw', NULL, NULL, 0, 0, 0, 0, 0, 1, '2015-09-10 14:24:32', '2015-09-11 09:59:26'),
(9, 9, 3, 11, '杨雯铄', 'yangwenshuo', 'yws', NULL, NULL, 0, 0, 0, 0, 0, 1, '2015-09-11 09:58:13', '2015-09-11 09:59:14');

--
-- 限制导出的表
--

--
-- 限制表 `sellers`
--
ALTER TABLE `sellers`
  ADD CONSTRAINT `sellers_hlj_id_foreign` FOREIGN KEY (`hlj_id`) REFERENCES `users` (`hlj_id`) ON DELETE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
