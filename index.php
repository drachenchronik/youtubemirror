<?php
/**
 *
 * @package YouTube Mirror
 * @copyright (c) 2019 DrachenChronik <drachenlordchronik@gmail.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 */

require 'include/header.php';

// Get channel ID to display
$channel_id = filter_input(INPUT_GET, 'c');
if (empty($channel_id))
{
	$channel_id = key(YOUTUBE_CHANNELS);
}
if (!isset(YOUTUBE_CHANNELS[$channel_id]))
{
	trigger_error('Invalide channel ID: ' . $channel_id, E_USER_ERROR);
}

echo '<h1>' . YOUTUBE_CHANNELS[$channel_id] . '</h1>';

// Display videos
$content = '';

$sql = 'SELECT *
	FROM ' . TABLE_VIDEOS . "
		WHERE channel_id = '" . sql::sql_escape($channel_id) . "'
	ORDER BY publish_date DESC";
$result = sql::query($sql);
while ($row = sql::fetch_array($result))
{
	$content .= youtube::display($row);
}

if (empty($content))
{
	echo 'Es gibt keine Videos die angezeigt werden können. <a href="cron.php">Videos suchen</a>.';
}
else
{
	echo '<div class="container">';
	echo $content;
	echo '</div>';
}
require 'include/footer.php';