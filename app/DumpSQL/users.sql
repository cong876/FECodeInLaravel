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
-- 表的结构 `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `hlj_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `wx_number` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mobile` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_available` tinyint(1) NOT NULL DEFAULT '1',
  `is_subscribed` tinyint(1) NOT NULL DEFAULT '0',
  `openid` varchar(60) COLLATE utf8_unicode_ci DEFAULT NULL,
  `unionid` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nickname` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `sex` enum('1','2') COLLATE utf8_unicode_ci NOT NULL DEFAULT '1',
  `province` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `city` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `country` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `headimgurl` varchar(600) COLLATE utf8_unicode_ci NOT NULL,
  `privilege` varchar(1000) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `secure_password` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`hlj_id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_mobile_unique` (`mobile`),
  UNIQUE KEY `users_openid_unique` (`openid`),
  UNIQUE KEY `users_unionid_unique` (`unionid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=10 ;

--
-- 转存表中的数据 `users`
--

INSERT INTO `users` (`hlj_id`, `wx_number`, `email`, `mobile`, `is_available`, `is_subscribed`, `openid`, `unionid`, `nickname`, `sex`, `province`, `city`, `country`, `headimgurl`, `privilege`, `password`, `secure_password`, `remember_token`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, '909984232', 'weixiaoqingyeah@yeah.net', '18258425758', 1, 0, 'olxLuv5iW-J-1xNhpYrFY87x7v8Q', 'onq6ws_KoxH55B5FQzVRHk4L1xH8', '韦小小小囧', '2', 'Ho Chi Minh City', '', '', 'http://wx.qlogo.cn/mmopen/paldhvvS4mfEmfAOgicO0RVzlZBAOEicSSMyWvFemV7XMtASneMpjNNnL0eqIAJiaNLefvvdjgsvWFLJxsv2KG2sPqTvVzOmv9l/0', '', '$2y$10$AG53ZRT/Q8JhFbscy23FAOkBNBdHdGhXD2JX.zRdwJfRl7tMrDObG', '$2y$10$Q3ck5ZrRhbRfpkFTA//dl.xYPEEn64t37Pw9iCh8H0aewhkjAPSYu', NULL, '2015-09-10 14:01:43', '2015-09-10 14:03:17', NULL),
(2, 'mumuzhenzhen', '568108047@qq.com', '18667175106', 1, 0, 'olxLuv7ftcxC48-YGe6go_E-0FMo', 'onq6ws_5rArE3N2xiMbxLGhyuHho', '慕慕珍珍@北京', '2', '北京', '海淀', '中国', 'http://wx.qlogo.cn/mmopen/fQSDnmuXpjHF1Sp546IucG9FsTubGeZY1JvGTDHfUcc3ib5cLKHw08Z5Upib1HQDIQiaMkkmDw5tE6LZfPwziaR6TQ/0', '', '$2y$10$/k0c7rg2aTR9QlomHcoow.AZgyAmfljC42UYt8xw0ZvOG/u7bFRpC', '$2y$10$rEoljXWYWMQupn1uyLYbUOfGDuNBfajYvRF/0APu5/0irlEvHMUEm', NULL, '2015-09-10 14:03:46', '2015-09-10 14:05:15', NULL),
(3, 'shimeng5d', 'shimeng.wang09@hotmail.com', '18311329339', 1, 0, 'olxLuvzv3O5dgGS0C-GHM2D7Ncrc', 'onq6wszm8OvRLvxl3UK766UkHS_Y', '大萌', '1', '辽宁', '沈阳', '中国', 'http://wx.qlogo.cn/mmopen/fQSDnmuXpjFYYSicbMI1let8OpUSLwBoD9KEs89FVwYqypE0DhMyEq53BkzLLOuLzloZ81U73vibmF4iaXydhzyiabam6WqVI8pW/0', '', '$2y$10$sSKg4SE5HkPzn91vhSHMou597gDNECw5idB8rqBgKFR3.adj66qEO', '$2y$10$ujajhwqc5hLbqWNh.cq75O4geIBIQVnCXXqanwjfgXa9t/aiGTDKe', NULL, '2015-09-10 14:05:28', '2015-09-10 14:07:41', NULL),
(4, 'wangcong1172', '736044622@qq.com', '18810541172', 1, 0, 'olxLuv1qP5tdb85xhNp2l-V9hOLI', 'onq6ws0UKrfp9cMFBAhE7MVllJc0', '王聪', '1', '北京', '海淀', '中国', 'http://wx.qlogo.cn/mmopen/Q3auHgzwzM62oxV7ZsnaYfhAxLtbeNmWcHcfJqlU0jQ12pCCicwoGAXexfNGyfD6f5BQ5BzyQ7yOjClZG9d0P2A/0', '', '$2y$10$qE9oOzw4tnnD17F9W1sRZOjaR11dZqzbTTB6CL2bOQCbizI249Wza', '$2y$10$xAVm7E4vH8q1hzT3LuOqTu5lvMBl7urPWxNeQh49ZirBRZVcoXscy', NULL, '2015-09-10 14:07:54', '2015-09-10 14:11:56', NULL),
(5, 'joannaaaaaaaaaa', 'xiangxi1989104@163.com', '13128281208', 1, 0, 'olxLuv2cwgP_UG5xBYJKFYhT6K20', 'onq6wsxdy5aK-D5093W3luFZMeDQ', 'Joanna', '2', '北京', '海淀', '中国', 'http://wx.qlogo.cn/mmopen/X5Q8KzdtCrRvEfloKjnAP79FVMSzPjNR6CTOMlicNWRSGzSSciamZ6IVBre1lfjLszF5J4hib9MCTcHaB0WqmgdXYV8vYfOSW3z/0', '', '$2y$10$LTtHoozkm0y0yXwZZdMNBOoPJZoLL6nPMEOBVkCGR23P8H7QpowPK', '$2y$10$IPZbDpcAhqwm866xiDGhueu/BRXWZ9t7jR8MADsd6NagNoioUMloS', NULL, '2015-09-10 14:12:24', '2015-09-10 14:14:27', NULL),
(6, 'wenjuanli_brave', '1012331948@qq.com', '18701133614', 1, 0, 'olxLuv3e-drYjItelNbkA89ErZoM', 'onq6ws5UZAnjuStfwUx7aydmRwtA', '李文娟', '2', '浙江', '杭州', '中国', 'http://wx.qlogo.cn/mmopen/fQSDnmuXpjErnoWvDuO2nKE1Eu1QxI1F1SUJ4xZvrstPBP8q05TiaWLb0MUG0cpicYTv9cUyicBo6HGEcSqbUic1lmuXdD03V6Un/0', '', '$2y$10$ynQL4KyhQlRVAWR0NdrA0OEgxc9W5cyOMvuX6z7lCXf7MKHD.YKG.', '$2y$10$2H.VEjITiLUDGAwsYPCEZeyBe23mKPAiRlAkCZACTMjjm6UtEhedS', NULL, '2015-09-10 14:14:49', '2015-09-10 14:16:53', NULL),
(7, '492098890', 'cokeguanguan@163.com', '18810681620', 1, 0, 'olxLuv2xGKQH2LRZm5-ybTW4ynzI', 'onq6ws8hO6-uAqo4-Kb7N9k7xrO8', '角角角角', '1', '', '', '中国', 'http://wx.qlogo.cn/mmopen/ajNVdqHZLLDK8jCxLHiaZicGGKCB4uCkia9Fibl98LicvbGShxybpQbUavHdTNDlEib84ly54Aics3v6PIOMNjNQSIohQ/0', '', '$2y$10$ylV8ascMvaB2ylDGvWoUcupPnOsXn/ZZN4xRu8WMZLDXHlLSSk2EK', '$2y$10$4JuVzMaabXF5nv4f7csiJOcBgkGtvhnI4JeJnVm3ohGqgHRPRt.iW', NULL, '2015-09-10 14:17:20', '2015-09-10 14:20:55', NULL),
(8, 'yesiyuan1', 'yesiyuan1@qq.com', '15267027123', 1, 0, 'olxLuv9NpN64Y7QoG6pliQJvqY74', 'onq6ws2GK34494c42PeKKQFrAAEI', ' 败笔Baby！', '1', '浙江', '杭州', '中国', 'http://wx.qlogo.cn/mmopen/X5Q8KzdtCrT2hdtmIh8K98Qsmf7mXZiawcaHaLa3iaCrBiao0Tbps9AOlzADibC3S1Rsg8ExOMrSGzZUUgLrNLkNgR7iaZibydfAyC/0', '', '$2y$10$DfR1DfhittDG4AvNPhWcAuuGeFfMncpuadRsI9lip0lrwTnr/hjM.', '$2y$10$YE1BnoEnUDoSAfovJt3CAOqQQOEkpUVObUTqqwDU2rC9JEZFr9Rly', NULL, '2015-09-10 14:21:22', '2015-09-10 14:24:32', NULL),
(9, 'Yang523294080', '523294080@qq.com', '15590389366', 1, 0, 'olxLuv9dXl-HAqRyUWtspjer2Oek', 'onq6ws-j4qbNi7dahKMV7nO0JkXE', '☀', '2', '吉林', '白山', '中国', 'http://wx.qlogo.cn/mmopen/X5Q8KzdtCrT2hdtmIh8K91q0reFodZibWJIRQjl7nlGIj6K6W2T7Oe4caFkExXeMpWUnxzVM6cYQNVyLS0MCaWl4hnPZOQNiaf/0', '', '$2y$10$bIod9JydhWDDVsmDIicoU.yxR2fsd2b77ltvnofJIbveBiu13J7K6', '$2y$10$tOp.1eBU.Nvy4bYRu8ZeK.1/XO9yGMDsr7b6d96DqW1Ljk439xjvm', NULL, '2015-09-11 09:56:13', '2015-09-11 09:58:13', NULL);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
