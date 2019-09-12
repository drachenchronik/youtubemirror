<?php
/**
 *
 * @package YouTube Mirror
 * @copyright (c) 2019 DrachenChronik <drachenlordchronik@gmail.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 */

// Page name
define('PAGE_NAME', 'Drachenlord Videos');

// Youtube channels
define('YOUTUBE_CHANNELS', array(
	'UCm3_j4RLEzgMovQTRPx47MQ'		=> 'Hauptkanal',
	'UCMl7TI-xQ6Qv_H7FsEX7pRQ'		=> 'LP Kanal',
	'UCQmszFsjBzPvvBelia4pBOw'		=> 'Phoenix',
));

// Youtube API Key
define('YOUTUBE_API_KEY', '');

// SQL Conection
define('SQL_USER', '');
define('SQL_PASSWD', '');
define('SQL_DBNAME', '');
define('SQL_HOST', '127.0.0.1');
define('SQL_PORT', '3306');


// DO NOT CHANGE
define('THUMBNAIL_STORE', __DIR__ . '/thumbnails/');
define('VIDEO_STORE', __DIR__ . '/videos/');

define('TABLE_VIDEOS', 'videos');
