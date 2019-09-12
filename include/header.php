<?php
/**
 *
 * @package YouTube Mirror
 * @copyright (c) 2019 DrachenChronik <drachenlordchronik@gmail.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 */

set_error_handler('error_handler');

require 'config.php';
require 'class/sql.php';
require 'class/youtube.php';

sql::connect();

?>
<!DOCTYPE html>
<html lang="de-de">
<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="style.css">
	<title>Drachenlord Videos</title>
</head>
<body>
	<header>
		<a href="./"><?php echo PAGE_NAME ?></a>
	</header>
	<nav>
		<?php
		foreach (YOUTUBE_CHANNELS as $channel_id => $name)
		{
			echo '<a href="./index.php?c=' . $channel_id . '">' . $name . '</a>';
		}
		?>
	</nav>
	<main>
<?php

// Is store path writable?
if (!is_dir(THUMBNAIL_STORE) || !is_writable(THUMBNAIL_STORE))
{
	trigger_error('Der Speicherort für Thumbnails (' . THUMBNAIL_STORE . ') existiert nicht oder kann nicht beschrieben werden.');
}
if (!is_dir(VIDEO_STORE) || !is_writable(VIDEO_STORE))
{
	trigger_error('Der Speicherort für Videos existiert nicht oder kann nicht beschrieben werden.');
}

// Error handler
function error_handler($errno, $errstr, $errfile, $errline, $errcontext)
{
	switch($errno)
	{
		case E_USER_ERROR:
			echo '<p class="error"><b>Error:</b> ' . $errstr . '</p>';
			require 'include/footer.php';
			break;
		default:
			echo '<p class="warning">' . $errstr . '</p>';
			break;
	}
	
}