<?php
	$siteURLprefix = '';
	foreach($videos as $video) {

		$link  = $siteURLprefix . trim(str_between($video, '" href="', '"'));
		$title = trim(str_between($video, '">', '</a>'));
		$title = convertUnicodePoints($title);
		$extra = '';

		$image  = $imageAll;
		$note   = array();
		$note[] = '';
		$note[] = '';
		$note[] = '';
		$note[] = '';

		$clickPlay = 'no';
		include('04_query.items.link.inc');
	}
?>
