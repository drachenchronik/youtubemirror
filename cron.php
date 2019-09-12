<?php
/**
 *
 * @package YouTube Mirror
 * @copyright (c) 2019 DrachenChronik <drachenlordchronik@gmail.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 */

require 'include/header.php';

// Get videos from Youtube
$token = filter_input(INPUT_GET, 'token');
foreach (YOUTUBE_CHANNELS as $channel_id => $name)
{
	$next = youtube::get_videos($channel_id, $token);
	if ($next)
	{
		echo $name . ': <a href="?token=' . $next . '">Mehr Videos</a><br>';
	}
}

// Store Videos local
$sql = 'SELECT youtube_id
	FROM ' . TABLE_VIDEOS . "
		WHERE local_file = ''
		LIMIT 2";
$result = sql::query($sql);
while ($row = sql::fetch_array($result))
{
	youtube::store_video($row['youtube_id']);
}
echo '<br><a href="index.php">Zur Startseite</a>';

require 'include/footer.php';