<?php
/**
 *
 * @package YouTube Mirror
 * @copyright (c) 2019 DrachenChronik <drachenlordchronik@gmail.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 */

require 'include/header.php';

$sql = 'CREATE TABLE `' . TABLE_VIDEOS . '` (
		`video_id` int(11) NOT NULL,
		`youtube_id` varchar(20) COLLATE utf8_bin NOT NULL,
		`channel_id` varchar(50) COLLATE utf8_bin NOT NULL,
		`title` varchar(255) COLLATE utf8_bin NOT NULL,
		`description` text COLLATE utf8_bin NOT NULL,
		`publish_date` varchar(14) COLLATE utf8_bin NOT NULL,
		`local_file` varchar(80) COLLATE utf8_bin NOT NULL
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;';
$result = sql::query($sql);

echo 'Installation abgeschlossen. <a href="cron.php">Videos suchen</a><br>';

require 'include/footer.php';