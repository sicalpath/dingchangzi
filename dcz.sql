-- Adminer 4.1.0 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DELIMITER ;;

DROP PROCEDURE IF EXISTS `delLock`;;
CREATE PROCEDURE `delLock`()
delete from dcz_fstatus where status=-1;;

DROP PROCEDURE IF EXISTS `delVerify`;;
CREATE PROCEDURE `delVerify`()
delete from dcz_verifys where  (UNIX_TIMESTAMP(now()) - UNIX_TIMESTAMP(crtime)) > 600;;

DROP PROCEDURE IF EXISTS `unLocked`;;
CREATE PROCEDURE `unLocked`()
update dcz_order
inner join dcz_fstatus on dcz_order.oid = dcz_fstatus.oid
set dcz_order.status = -1,dcz_fstatus.status = -1
where dcz_order.status=0 and (UNIX_TIMESTAMP(now()) - UNIX_TIMESTAMP(ordertime)) > 900;;

DROP EVENT IF EXISTS `delF`;;
CREATE EVENT `delF` ON SCHEDULE EVERY 1 SECOND STARTS '2015-04-19 12:01:41' ON COMPLETION NOT PRESERVE ENABLE COMMENT '删除过期锁定' DO call delLock();;

DROP EVENT IF EXISTS `delO`;;
CREATE EVENT `delO` ON SCHEDULE EVERY 1 SECOND STARTS '2015-04-19 12:01:18' ON COMPLETION NOT PRESERVE ENABLE COMMENT '设置过期订单删除锁定' DO call unLocked();;

DROP EVENT IF EXISTS `delV`;;
CREATE EVENT `delV` ON SCHEDULE EVERY 1 SECOND STARTS '2015-04-19 10:17:25' ON COMPLETION NOT PRESERVE ENABLE COMMENT '定期删过期验证码' DO call delVerify();;

DELIMITER ;

DROP TABLE IF EXISTS `dcz_bind`;
CREATE TABLE `dcz_bind` (
  `uid` int(11) NOT NULL,
  `stuid` varchar(100) NOT NULL DEFAULT '0' COMMENT '学号',
  `utype` tinyint(4) DEFAULT NULL COMMENT '用户类型',
  `schid` int(10) DEFAULT NULL COMMENT '学校ID',
  `money` int(10) NOT NULL DEFAULT '0' COMMENT '对应学校余额',
  KEY `uid` (`uid`),
  CONSTRAINT `dcz_bind_ibfk_2` FOREIGN KEY (`uid`) REFERENCES `dcz_user` (`uid`) ON DELETE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `dcz_bindtosch`;
CREATE TABLE `dcz_bindtosch` (
  `schid` int(11) NOT NULL COMMENT '学校id',
  `stid` int(11) NOT NULL COMMENT '对应stid',
  KEY `stid` (`stid`),
  CONSTRAINT `dcz_bindtosch_ibfk_1` FOREIGN KEY (`stid`) REFERENCES `dcz_sportstype` (`stid`) ON DELETE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `dcz_buser`;
CREATE TABLE `dcz_buser` (
  `bid` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(20) NOT NULL,
  `password` varchar(32) NOT NULL,
  `name` varchar(50) NOT NULL,
  `phone` varchar(20) NOT NULL,
  PRIMARY KEY (`bid`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `dcz_feedbacks`;
CREATE TABLE `dcz_feedbacks` (
  `feid` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `content` varchar(500) NOT NULL,
  `contact` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`feid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `dcz_field`;
CREATE TABLE `dcz_field` (
  `ffid` int(11) NOT NULL AUTO_INCREMENT,
  `fid` int(11) NOT NULL,
  `stid` int(11) NOT NULL COMMENT '场馆ID',
  `fno` int(11) NOT NULL COMMENT '场地序号 7,24,60',
  `utype` tinyint(4) NOT NULL DEFAULT '0' COMMENT '用户类型(一般校外,学生，教职工)',
  `price` decimal(9,2) NOT NULL COMMENT '价格',
  `locked` tinyint(2) DEFAULT '0' COMMENT '是否锁定',
  PRIMARY KEY (`ffid`),
  KEY `sid` (`stid`),
  CONSTRAINT `dcz_field_ibfk_2` FOREIGN KEY (`stid`) REFERENCES `dcz_stadium` (`sid`) ON DELETE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `dcz_fstatus`;
CREATE TABLE `dcz_fstatus` (
  `fsid` int(11) NOT NULL AUTO_INCREMENT,
  `ffid` int(11) NOT NULL,
  `ftime` date NOT NULL COMMENT '消费时间',
  `status` tinyint(4) DEFAULT '0' COMMENT '状态',
  `oid` varchar(50) NOT NULL COMMENT '对于订单OID',
  PRIMARY KEY (`fsid`),
  KEY `ffid` (`ffid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `dcz_oauth`;
CREATE TABLE `dcz_oauth` (
  `uid` int(11) NOT NULL,
  `qq` varchar(500) DEFAULT NULL COMMENT 'qq登录 OPENID',
  `sina` varchar(500) DEFAULT NULL COMMENT '微博登陆OPENID',
  KEY `uid` (`uid`),
  CONSTRAINT `dcz_oauth_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `dcz_user` (`uid`) ON DELETE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `dcz_order`;
CREATE TABLE `dcz_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `stid` int(11) NOT NULL COMMENT '订单所属场馆',
  `oid` char(20) NOT NULL COMMENT '订单号,F+bid+',
  `uid` int(11) NOT NULL COMMENT '用户ID',
  `name` varchar(20) NOT NULL DEFAULT '佚名',
  `phone` varchar(20) DEFAULT NULL COMMENT '订单验证手机号',
  `fsids` varchar(4000) DEFAULT NULL,
  `usetimes` varchar(4000) DEFAULT NULL,
  `ordertime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '下单时间',
  `price` decimal(9,2) NOT NULL COMMENT '订单价格',
  `status` tinyint(4) NOT NULL COMMENT '订单状态 -1已过期0未付1已付2已付未验3已验',
  `operator` varchar(100) DEFAULT NULL COMMENT '操作人',
  `comment` varchar(100) DEFAULT NULL COMMENT '备注',
  `verify` int(11) DEFAULT NULL COMMENT '验证码',
  PRIMARY KEY (`id`),
  UNIQUE KEY `oid` (`oid`),
  UNIQUE KEY `verify` (`verify`),
  KEY `stid` (`stid`),
  CONSTRAINT `dcz_order_ibfk_2` FOREIGN KEY (`stid`) REFERENCES `dcz_sportstype` (`stid`) ON DELETE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `dcz_sportstype`;
CREATE TABLE `dcz_sportstype` (
  `stid` int(11) NOT NULL AUTO_INCREMENT,
  `sid` int(11) NOT NULL,
  `fnum` int(11) DEFAULT '0' COMMENT '场地数',
  `stype` int(11) DEFAULT NULL COMMENT '运动类型 1 羽毛球 2足球3网球',
  `comment` varchar(500) DEFAULT NULL,
  `detail` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`stid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `dcz_stadium`;
CREATE TABLE `dcz_stadium` (
  `sid` int(11) NOT NULL AUTO_INCREMENT,
  `bid` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `pics` varchar(4000) DEFAULT NULL COMMENT '图片数组',
  `location` point DEFAULT NULL COMMENT '经纬度',
  `address` varchar(100) DEFAULT NULL,
  `address2` varchar(100) DEFAULT NULL,
  `detail` varchar(500) DEFAULT NULL,
  `detail2` varchar(500) DEFAULT NULL,
  `totstar` int(10) unsigned DEFAULT '5' COMMENT '总星数',
  `totvoter` int(10) unsigned DEFAULT '1' COMMENT '总评星人数',
  `url` varchar(200) DEFAULT NULL COMMENT '独立URL',
  PRIMARY KEY (`sid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `dcz_ufavorites`;
CREATE TABLE `dcz_ufavorites` (
  `uid` int(11) NOT NULL,
  `stid` int(11) NOT NULL,
  KEY `stid` (`stid`),
  KEY `uid` (`uid`),
  CONSTRAINT `dcz_ufavorites_ibfk_4` FOREIGN KEY (`stid`) REFERENCES `dcz_sportstype` (`stid`) ON DELETE NO ACTION,
  CONSTRAINT `dcz_ufavorites_ibfk_5` FOREIGN KEY (`uid`) REFERENCES `dcz_user` (`uid`) ON DELETE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `dcz_user`;
CREATE TABLE `dcz_user` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(20) DEFAULT NULL,
  `password` varchar(32) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`uid`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `dcz_userinfo`;
CREATE TABLE `dcz_userinfo` (
  `uid` int(11) NOT NULL,
  `nickname` varchar(200) DEFAULT NULL,
  `realname` varchar(200) DEFAULT NULL,
  `sex` tinyint(4) DEFAULT '0',
  `birthday` varchar(200) DEFAULT NULL,
  `address` varchar(200) DEFAULT NULL,
  `qq` varchar(200) DEFAULT NULL,
  KEY `uid` (`uid`),
  CONSTRAINT `dcz_userinfo_ibfk_2` FOREIGN KEY (`uid`) REFERENCES `dcz_user` (`uid`) ON DELETE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `dcz_verifys`;
CREATE TABLE `dcz_verifys` (
  `phone` varchar(15) NOT NULL,
  `code` int(11) NOT NULL,
  `crtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- 2015-05-10 09:43:50
