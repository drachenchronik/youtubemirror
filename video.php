<?php
/**
 *
 * @package YouTube Mirror
 * @copyright (c) 2019 DrachenChronik <drachenlordchronik@gmail.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 */

require 'include/header.php';

$video_id = filter_input(INPUT_GET, 'v');

$sql = 'SELECT *
	FROM ' . TABLE_VIDEOS . "
	WHERE youtube_id = '" . sql::sql_escape($video_id) . "'";
$result = sql::query($sql);
$row = sql::fetch_array($result);

// Video not in DB
if (!isset($row['video_id']))
{
	trigger_error('Video not found', E_USER_ERROR);
}

// No local file
if (empty($row['local_file']) || !is_file(VIDEO_STORE . $row['local_file']))
{
	youtube::store_video($row['youtube_id']);
}

// Display page
echo '<h1>' . $row['title'] . '</h1>';
echo '<video width="100%" controls><source src="./videos/' . $row['local_file'] . '" type="video/mp4"></video>';
echo '<h2>Video Beschreibung</h2>';
echo nl2br($row['description']);

require 'include/footer.php';