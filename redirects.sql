-- Adminer 4.2.5 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `redirects`;
CREATE TABLE `redirects` (
  `id` int(10) unsigned NOT NULL,
  `origin` varchar(500) NOT NULL,
  `target` varchar(500) NOT NULL,
  `owner` int(10) unsigned NOT NULL DEFAULT '0',
  `redirect_type` int(10) unsigned NOT NULL DEFAULT '1',
  `redirect_code` varchar(5) NOT NULL DEFAULT '301',
  `active` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `origin_redirect_type` (`origin`,`redirect_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `redirects` (`id`, `origin`, `target`, `owner`, `redirect_type`, `redirect_code`, `active`, `created_at`, `modified_at`) VALUES
(1,	'http://www.amarillo.com/123/123/123',	'http://www.amarilloc/4444444',	0,	1,	'301',	1,	'2016-10-05 06:03:20',	NULL),
(2,	'http://www.france.com/123/123',	'http://www.azul.com/pepito',	0,	1,	'301',	1,	'2016-10-05 06:04:54',	NULL),
(3,	'http://www.uno.com/1',	'http://www.dos.com/2',	0,	1,	'301',	1,	'2016-10-05 06:05:14',	NULL),
(4,	'http://www.black.com/mamba',	'http://www.worldofsnkaes.net/',	0,	3,	'301',	1,	'2016-10-05 06:05:40',	NULL),
(5,	'http://pepe.geocities.com/123.html',	'http://pepe.blogger.com/123/',	0,	1,	'301',	1,	'2016-10-05 06:06:14',	NULL),
(6,	'http://www.armadillo.net/cursos',	'http://www.armadillo.com/maestrias/',	0,	1,	'301',	1,	'2016-10-05 06:06:53',	NULL),
(7,	'http://www.booking.com/',	'http://www.destinia.com/',	0,	3,	'301',	1,	'2016-10-05 06:09:32',	NULL),
(8,	'http://www.armadillo.com/',	'http://www.test.com/',	0,	1,	'301',	1,	'2016-10-05 06:43:07',	NULL);

-- 2016-10-05 06:45:46
