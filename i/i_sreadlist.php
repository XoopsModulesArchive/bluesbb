<?php

ini_set('mbstring.language', 'Japanese');
ini_set('mbstring.http_output', 'pass');
require dirname(__DIR__, 3) . '/mainfile.php';
require dirname(__DIR__) . '/functions.php';
require dirname(__DIR__) . '/config.php';
$topic = (int)$_GET['topic'];
$more = isset($_GET['more']) ? (int)$_GET['more'] : 0;
if (empty($topic)) {
    redirect_header('index.php', 2, 'トピックが選択されていません。');

    exit();
}
$sql = 'SELECT topic_name, topic_access FROM ' . $xoopsDB->prefix('bluesbb_topic') . ' WHERE topic_id = ' . $topic;
if (!$result = $xoopsDB->query($sql)) {
    redirect_header('index.php', 2, 'データベースとアクセスすることができません。');

    exit();
}
if (!$topicdata = $xoopsDB->fetchArray($result)) {
    redirect_header('index.php', 2, 'データベースとアクセスすることができません。');

    exit();
}
$accesserror = 1;
if ('1' == $topicdata['topic_access'] || '2' == $topicdata['topic_access'] || '5' == $topicdata['topic_access']) {
    $accesserror = 0;
}
if (1 == $accesserror) {
    redirect_header('index.php', 3, 'このトピックへのアクセスは許可されていません。');

    exit();
}
$myts = MyTextSanitizer::getInstance();
header('Content-type: text/html; charset=Shift-jis');
?>
<html>
<head>
    <title><?php echo sjisconvert(htmlspecialchars($xoopsConfig['sitename'], ENT_QUOTES | ENT_HTML5)); ?></title>
    <META HTTP-EQUIV="Content-Type" CONTENT="text/html;CHARSET=Shift-jis">
</head>
<body bgcolor="#f0f0f0" text="#000000">
<?php
if (eregi('mozilla', $_SERVER['HTTP_USER_AGENT'])) {
    echo "<div align=\"center\">\r\n";

    echo "<table width=\"200\" border=\"0\" cellspacing=\"1\" cellpadding=\"5\" bgcolor=\"#696969\">\r\n";

    echo "<tr>\r\n";

    echo "<td style=\"font-family: monospace\" bgcolor=\"#f0f0f0\">\r\n";
}
?>
<center><font color="#000000"><?php echo sjisconvert(htmlspecialchars($topicdata['topic_name'], ENT_QUOTES | ENT_HTML5)); ?></font></center>
<HR>
<?php
if ('1' == $topicdata['topic_access']) {
    ?>
    <center><a href="i_form.php?topic=<?php echo $topic; ?>&amp;mode=1">新規スレッド作成</a></center>
    <HR>
    <?php
}
//スレッド一覧の抽出
$list_more = 30 * $more;
$sql = 'SELECT sread_id, title, res_time FROM ' . $xoopsDB->prefix('bluesbb') . ' WHERE topic_id = ' . $topic . ' AND res_id = 0 ORDER BY res_time DESC LIMIT ' . $list_more . ', 30';
if (!$result = $xoopsDB->query($sql)) {
    redirect_header('index.php', 2, 'データベースとアクセスすることができません。');

    exit();
}
$now_time = time();
$srn = 1;
while (false !== ($myrow = $xoopsDB->fetchArray($result))) {
    $count_sql = 'SELECT COUNT(post_id) FROM ' . $xoopsDB->prefix('bluesbb') . ' WHERE sread_id = ' . $myrow['sread_id'];

    if (!$count_result = $xoopsDB->query($count_sql)) {
        redirect_header('index.php', 2, 'データベースとアクセスすることができません。');

        exit();
    }

    [$count] = $xoopsDB->fetchRow($count_result);    //投稿数をカウント

    $GLOBALS['xoopsDB']->freeRecordSet($count_result);

    if (($now_time - $myrow['res_time']) <= 60 * 60 * 24) {
        echo $srn
             . ':<a href="i_message.php?topic='
             . $topic
             . '&amp;sread_id='
             . $myrow['sread_id']
             . '&amp;display=1&amp;number=l10">省</a><a href="i_message.php?topic='
             . $topic
             . '&amp;sread_id='
             . $myrow['sread_id']
             . '&amp;display=2&amp;number=l10">全</a><font color="#FF0000">New</font>'
             . sjisconvert(htmlspecialchars($myrow['title'], ENT_QUOTES | ENT_HTML5))
             . '('
             . $count
             . ')<br>';
    } else {
        echo $srn . ':<a href="i_message.php?topic=' . $topic . '&amp;sread_id=' . $myrow['sread_id'] . '&amp;display=1&amp;number=l10">省</a><a href="i_message.php?topic=' . $topic . '&amp;sread_id=' . $myrow['sread_id'] . '&amp;display=2&amp;number=l10">全</a>' . sjisconvert(
                htmlspecialchars($myrow['title'], ENT_QUOTES | ENT_HTML5)
        ) . '(' . $count . ')<br>';
    }

    $srn++;
}
$GLOBALS['xoopsDB']->freeRecordSet($result);
?>
<HR>
<center>
    <?php
    if ($more > 0) {
        $back = $more - 1;

        echo '<a href="i_sreadlist.php?topic=' . $topic . '&amp;more=' . $back . '">Back</a>&nbsp;';
    }
    echo '<a href="index.php">Top</a>&nbsp;';
    ++$more;
    echo '<a href="i_sreadlist.php?topic=' . $topic . '&amp;more=' . $more . '">Next</a>';
    ?>
</center>
<HR>
<center><?php echo sjisconvert(BLUESBB_COPYRIGHT); ?></center>
<?php
if (eregi('mozilla', $_SERVER['HTTP_USER_AGENT'])) {
        echo "</td>\r\n";

        echo "</tr>\r\n";

        echo "</table>\r\n";

        echo "</div>\r\n";
    }
?>
</body>
</html>
