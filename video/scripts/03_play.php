<?php
	echo "<?xml version=\"1.0\" encoding=\"UTF8\" ?>\r\n";
	echo "<rss version=\"2.0\" xmlns:dc=\"http://purl.org/dc/elements/1.1/\">\r\n";
?>
<?php
	require('00_prefix.php');
	$myName = basename($myScriptName, '.php');
	$myBaseName = basename($myName, '.link');
?>
<?php
	include('06_get.link.inc');

	ini_set('user_agent', $userAgent);

	if (isset($extra)) {
		// $extra may be changed by included scripts
		$extra_03_play = $extra;
	}
	else {
		unset($extra_03_play);
	}

	include($myName . '.inc');

	$titleComponents = explode('.', $myBaseName);
	$pageTitleBase = $titleComponents[0];
	$pageTitle = $pageTitleBase;
	if (isset($title)) {
		$pageTitle = $pageTitle . ': ' . $title;
	}

	// Create my own link
	$params  = str_replace('&', '&amp;', $_SERVER['QUERY_STRING']);
	$currUrl = $scriptsURLprefix . '/' . $myName . '.php?' . $params;
?>

<onEnter>
	inputNumCount = 0;
	inputNumVal = -1;
	curNumVal = -1;

	m = 0;
	if (linkArray == null) {
		postMessage("return");
	}
	setRefreshTime(100);
	startVideo = 1;
	n = readStringFromFile(storagePath);
	if (n == null) {
		n = <?php echo (isset($iStart) ? $iStart : 0) ;?>;
	}
	else {
		n = Integer(n);
	}

	dataBrowse   = "<?php echo $fileBrowse; ?>";
	dataWatch    = "<?php echo $fileWatch; ?>";
	dataFavorite = "<?php echo $fileFavorite; ?>";

	dataBrowseMax   = <?php echo $maxBrowse; ?>;
	dataWatchMax    = <?php echo $maxWatch; ?>;
	dataFavoriteMax = <?php echo $maxFavorite; ?>;

	history = <?php echo $history; ?>;
	if (history == 0) {
		/* Parameters */
		dataFile   = dataWatch;
		dataMax    = dataWatchMax;
		dataType   = "1";
		dataTitle  = "<?php echo $pageTitle; ?>";
		dataLink   = "<?php echo $currUrl; ?>";
		<?php include('08_history.record.inc'); ?>
	}

	itemCount = itemSize;

	x = itemCount;
	<?php include('00_utils.digits.inc'); ?>
	itemCountDigits = y;

	runningHead = "";
	runningHeadWidthPC = 0;
	displayRunningHeadWidthPC = 100;
</onEnter>

<onExit>
	playItemURL(-1, 1);
	setRefreshTime(-1);
</onExit>

<onRefresh>
	pbStatus = getPlaybackStatus();
	pbCur = getStringArrayAt(pbStatus, 0);
	pbMax = getStringArrayAt(pbStatus, 1);
	if ((pbCur == "0") &amp;&amp; (pbMax == "100")) {
		runningHead = "";
	}
	else {
		pbMaxInt = Integer(pbMax);
		pbMaxH = Integer(pbMaxInt / 3600);
		pbMaxM = Integer((pbMaxInt % 3600) / 60);
		if (pbMaxM &lt; 10) pbMaxM = "0" + pbMaxM;
		pbMaxS = pbMaxInt % 60;
		if (pbMaxS &lt; 10) pbMaxS = "0" + pbMaxS;
		pbCurInt = Integer(pbCur);
		pbCurH = Integer(pbCurInt / 3600);
		pbCurM = Integer((pbCurInt % 3600) / 60);
		if (pbCurM &lt; 10) pbCurM = "0" + pbCurM;
		pbCurS = pbCurInt % 60;
		if (pbCurS &lt; 10) pbCurS = "0" + pbCurS;
		runningHead = pbCurH + ":" + pbCurM + ":" + pbCurS + " / " + pbMaxH + ":" + pbMaxM + ":" + pbMaxS + " [" + now + "/" + itemCount + "] " + currentTitle;
	}

	if ((n &lt; 0) || (n &gt; (itemCount-1))) {
		postMessage("return");
	}
	else {
		vidProgress = getPlaybackStatus();
		bufProgress = getCachedStreamDataSize(0, bufferSize);
		playElapsed = getStringArrayAt(vidProgress, 0);
		playStatus  = getStringArrayAt(vidProgress, 3);

		if (startVideo == 1) {
			currentUrl = getStringArrayAt(linkArray, n);
			if ((currentUrl == null) || (currentUrl == "")) {
				postMessage("return");
			}
			else {
				playItemURL(currentUrl, 0, "mediaDisplay", "previewWindow");
			}
			currentTitle = getStringArrayAt(titleArray, n);
			if (currentTitle == null) {
				currentTitle = "";
			}
			writeStringToFile(storagePath, n);
			setRefreshTime(100);
			startVideo = 0;
			updatePlaybackProgress(bufProgress, "mediaDisplay", "progressBar");
		}
		else {
			if (playElapsed != 0) {
				if (startVideo == 0) {
					updatePlaybackProgress("delete", "mediaDisplay", "progressBar");
					startVideo = 2;
				}
			}
			else if (playStatus == 0) {
				if ((n+1) &gt; (itemCount-1)) {
					playItemURL(-1, 1);
					setRefreshTime(-1);
					postMessage("return");
				}
				else {
					n = Add(n, 1);
					if (n &lt; 0) {
						n = 0;
					}
					startVideo = 1;
				}
			}
			else {
				updatePlaybackProgress(bufProgress, "mediaDisplay", "progressBar");
			}
		}
	}
</onRefresh>

<mediaDisplay name="threePartsView" viewAreaWidthPC="100" viewAreaHeightPC="100">

	<previewWindow windowColor="-1:-1:-1"
		offsetXPC="0" offsetYPC="0"
		widthPC="100" heightPC="100">
	</previewWindow>

	<text redraw="yes" align="left" fontSize="20"
		offsetXPC="0" offsetYPC="6"
		widthPC="0" heightPC="6"
		backgroundColor="0:0:0" foregroundColor="255:255:255">
		<script>
			runningHead;
		</script>
		<widthPC>
			<script>
				runningHeadWidthPC;
			</script>
		</widthPC>
	</text>

	<progressBar backgroundColor="-1:-1:-1"
		offsetXPC="10" offsetYPC="64"
		widthPC="80" heightPC="24">

		<bar offsetXPC="45" offsetYPC="46"
			widthPC="55" heightPC="10"
			barColor="200:200:200"
			progressColor="26:129:211"
			bufferColor="-1:-1:-1"
			cornerRounding="10" />

		<text align="left" fontSize="20"
			offsetXPC="3" offsetYPC="0"
			widthPC="97" heightPC="24"
			backgroundColor="-1:-1:-1" foregroundColor="255:255:255">
			<script>
				currentTitle;
			</script>
		</text>

		<text align="left" fontSize="20"
			offsetXPC="5" offsetYPC="40"
			widthPC="40" heightPC="24"
			backgroundColor="-1:-1:-1" foregroundColor="255:255:255">
			<script>
				if (playStatus == 2) {
					showstr = "讀取資料中...";
				}
				else {
					now = Add(n, 1);
					showstr = "正在載入第 " + now + " / " + itemCount + " 段";
				}
				showstr;
			</script>
		</text>

		<text align="left" fontSize="20"
			offsetXPC="3" offsetYPC="80"
			widthPC="97" heightPC="24"
			backgroundColor="-1:-1:-1" foregroundColor="255:255:255">
			<script>
				if ((inputNumCount == 0) ||
						((inputNumCount == itemCountDigits) &amp;&amp;
						((curNumVal &lt; 1) || (curNumVal &gt; itemCount)))) {
					str = "[↔]±1;  [上下頁]±10;  [數字直選]+[信息]";
				}
				else {
					str = "[↔]±1;  [上下頁]±10;  [信息]播放第 " + curNumVal + " 項";
				}
				str;
			</script>
		</text>

		<destructor offsetXPC="0" offsetYPC="0"
			widthPC="100" heightPC="100"
			Color="-1:-1:-1">
		</destructor>

	</progressBar>

	<onUserInput>
		<script>
			ret = "false";
			userInput = currentUserInput();

			if (userInput == "video_stop") {
				startVideo = 0;
				postMessage("return");
				ret = "true";
			}
			else if (userInput == "display") {
				/* Toggle the playback status display */
				runningHeadWidthPC = displayRunningHeadWidthPC - runningHeadWidthPC;
				ret = "true";
			}
			else if (
				(userInput == "pagedown") ||
				(userInput == "pageup") ||
				(userInput == "right") ||
				(userInput == "left")
			) {
				if (userInput == "pagedown") {
					n = Add(n, 10);
				}
				else if (userInput == "pageup") {
					n = Minus(n, 10);
				}
				else if (userInput == "right") {
					n = Add(n, 1);
				}
				else if (userInput == "left") {
					n = Minus(n, 1);
				}

				/* Make n valid */
				if (n &lt; 0)
					n = 0;
				else if (n &gt; (itemCount-1))
					n = (itemCount-1);

				startVideo = 1;
				if(itemCount &gt; 0)
					postMessage("video_stop");
				setRefreshTime(100);
				ret = "true";
			}
			else if (
				(userInput == "one") ||
				(userInput == "two") ||
				(userInput == "three") ||
				(userInput == "four") ||
				(userInput == "five") ||
				(userInput == "six") ||
				(userInput == "seven") ||
				(userInput == "eight") ||
				(userInput == "nine") ||
				(userInput == "zero")
			) {
				if (userInput == "one") {
					inputNumVal = 1;
				}
				else if (userInput == "two") {
					inputNumVal = 2;
				}
				else if (userInput == "three") {
					inputNumVal = 3;
				}
				else if (userInput == "four") {
					inputNumVal = 4;
				}
				else if (userInput == "five") {
					inputNumVal = 5;
				}
				else if (userInput == "six") {
					inputNumVal = 6;
				}
				else if (userInput == "seven") {
					inputNumVal = 7;
				}
				else if (userInput == "eight") {
					inputNumVal = 8;
				}
				else if (userInput == "nine") {
					inputNumVal = 9;
				}
				else if (userInput == "zero") {
					inputNumVal = 0;
				}

				if ((inputNumCount == 0) || (inputNumCount == itemCountDigits)) {
					inputNumCount = 1;
					curNumVal = inputNumVal;
				}
				else {
					inputNumCount = inputNumCount + 1;
					curNumVal = (10*curNumVal) + inputNumVal;
				}

				if ((curNumVal &gt;= 1) &amp;&amp; (curNumVal &lt;= itemCount)) {
					/* n = (curNumVal - 1); */
				}
				else if ((inputNumVal &gt;= 1) &amp;&amp; (inputNumVal &lt;= itemCount)) {
					/* Keep the last digit which makes the value out of range unless invalid */
					inputNumCount = 1;
					curNumVal = inputNumVal;
					/* n = (curNumVal - 1); */
				}
				else {
					inputNumCount = 0;
					inputNumVal = -1;
					curNumVal = -1;
				}
				ret = "true";
			}
			ret;
		</script>
	</onUserInput>
</mediaDisplay>

<channel>
	<title>Media Play</title>
</channel>

</rss>
<?php
	require('00_suffix.php');
?>
