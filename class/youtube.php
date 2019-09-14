<?php
/**
 *
 * @package YouTube Mirror
 * @copyright (c) 2019 DrachenChronik <drachenlordchronik@gmail.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 */

class youtube
{

	/**
	 * Display video as HTML
	 * 
	 * @param array $row
	 * @return string
	 */
	public static function display($row)
	{
		$html = '<div class="video">';
		$html .= '<img class="image" src="./thumbnails/' . $row['youtube_id'] . '.jpg">';
		$html .= '<a href="./video.php?v=' . $row['youtube_id'] . '" class="title">' . $row['title'] . '</a>';
		$html .= '<span class="date">' . strftime('%d. %B %G %H:%M:%S', $row['publish_date']) . '</span>';
		$html .= '</div>';
		
		return $html;
	}

	
	/**
	 * Get new videos from Youtube
	 * 
	 * @param string $channel_id
	 * @param string $page_token
	 * @return string
	 */
	public static function get_videos($channel_id, $page_token = '')
	{
		if (empty(YOUTUBE_API_KEY))
		{
			trigger_error('Kein Youtube API Key in der config.php angegeben.', E_USER_WARNING);
			return;
		}
		
		// Get channel info
		$url = 'https://www.googleapis.com/youtube/v3/channels?part=contentDetails&id=' . $channel_id . '&key=' . YOUTUBE_API_KEY;
		
		// Check if URL is valide
		$headers = get_headers($url, 1);
		if ($headers[0] != 'HTTP/1.0 200 OK')
		{
			return;
		}

		// Get playlist ID with all videos
		$channel_info = json_decode(file_get_contents($url), true);
		if (!isset($channel_info['items'][0]['contentDetails']['relatedPlaylists']['uploads']))
		{
			return;
		}
		$playlist_id = $channel_info['items'][0]['contentDetails']['relatedPlaylists']['uploads'];

		// Get videos from playlist
		$addon = !empty($page_token) ? '&pageToken=' . $page_token : '';
		$url = 'https://www.googleapis.com/youtube/v3/playlistItems?part=snippet,contentDetails&playlistId=' . $playlist_id . '&maxResults=50&key=' . YOUTUBE_API_KEY . $addon;
		
		// Check if URL is valide
		$headers = get_headers($url, 1);
		if ($headers[0] != 'HTTP/1.0 200 OK')
		{
			return;
		}

		// Get JSON from youtube
		$youtube_info = json_decode(file_get_contents($url), true);
		if(!isset($youtube_info['items']))
		{
			return;
		}
		
		print_r($youtube_info);die;
		
		// More than one page?
		$next = isset($youtube_info['nextPageToken']) ? $youtube_info['nextPageToken'] : '';
		
		foreach($youtube_info['items'] as $video)
		{
			$data = $video['snippet'];
			
			// Check if video is already in DB?
			$sql = 'SELECT video_id
				FROM ' . TABLE_VIDEOS . "
					WHERE youtube_id = '" . sql::sql_escape($data['resourceId']['videoId']) . "'";
			$result = sql::query($sql);
			$row = sql::fetch_array($result);
			
			// Insert new Video
			if (!isset($row['video_id']))
			{	
				$sql_array = array(
					'youtube_id'		=> $data['resourceId']['videoId'],
					'publish_date'		=> strtotime($data['publishedAt']),
					'channel_id'		=> $data['channelId'],
					'title'				=> $data['title'],
					'description'		=> $data['description'],
					'local_file'		=> '',
				);
				sql::insert_array(TABLE_VIDEOS, $sql_array);

				self::copy_thumbnail($data['resourceId']['videoId'], $data['thumbnails']['default']['url']);
			}
		}
		return $next;
	}
	
	
	/**
	 * Generate local copy
	 * 
	 * @param string $video_id
	 */
	public static function store_video($video_id)
	{
		// Get array with source files
		$sources = self::get_youtube_sources($video_id);
		foreach ($sources as $data)
		{
			if (substr_count($data['type'], 'video/mp4;'))
			{
				$file_ext = 'mp4';
				$url = $data['url'];
			}
			else if (substr_count($data['type'], 'video/webm;'))
			{
				$file_ext = 'webm';
				$url = $data['url'];
			}
		}
		if (empty($url))
		{
			trigger_error('Failed find valide source video', E_USER_ERROR);
		}
		
		// Store video file local
		$video = fopen(html_entity_decode($url),'r');
		$file = fopen(VIDEO_STORE . $video_id . '.' . $file_ext, 'w');
		stream_copy_to_stream($video, $file);
		fclose($video);
		fclose($file);
		
		// Update database
		$sql = 'UPDATE ' . TABLE_VIDEOS . "
			SET local_file = '" . sql::sql_escape($video_id . '.' . $file_ext)  . "'
			WHERE youtube_id = '" . sql::sql_escape($video_id) . "'";
		sql::query($sql);
	}
	
	
	/**
	 * Get file source from youtube
	 * 
	 * @param string $video_id
	 * @return array
	 */
	private static function get_youtube_sources($video_id)
	{
		$dt = file_get_contents('https://www.youtube.com/get_video_info?video_id=' . $video_id . '&el=embedded&ps=default&eurl=&gl=US&hl=en');
		if (strpos($dt, 'status=fail') !== false)
		{
			trigger_error('Failed query source' , E_USER_ERROR);
		}

		// Get list of video streams
		$x = explode('&', $dt);
		$t = $g = $h = array();
		foreach($x as $r)
		{
			$c = explode('=', $r);
			$t[$c[0]] = $c[1];
		}
		
		$streams = explode(',', urldecode($t['url_encoded_fmt_stream_map']));
	//	$streams = explode(',', urldecode($t['adaptive_fmts']));
		
		foreach($streams as $dt)
		{
			$x = explode('&', $dt);
			foreach($x as $r)
			{
				$c = explode('=', $r);
				$h[$c[0]] = urldecode($c[1]);
			}
			$g[] = $h;
		}	

		return $g;
	}
	
	
	/**
	 * Get thumbnail from youtube and store it local
	 * 
	 * @param string $video_id
	 * @param string $source
	 */
	private static function copy_thumbnail($video_id, $source)
	{
		if (!is_file(THUMBNAIL_STORE . $video_id . '.jpg'))
		{
			copy($source, THUMBNAIL_STORE . $video_id . '.jpg');
		}
	}

}
