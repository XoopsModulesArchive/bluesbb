<?php

// Author: Sting_Band
// URL: http://www.bluish.jp/

ini_set('mbstring.language', 'Japanese');
ini_set('mbstring.http_output', 'pass');
require dirname(__DIR__, 3) . '/mainfile.php';
require __DIR__ . '/i_header.php';
require_once BLUESBB_ROOT . '/include/btickets.php';
$topic = isset($_GET['top']) ? (int)$_GET['top'] : 0;
$thread_id = isset($_GET['thr']) ? (int)$_GET['thr'] : 0;
$mode = isset($_GET['mod']) ? (int)$_GET['mod'] : 0;
$display = isset($_GET['dis']) ? (int)$_GET['dis'] : 0;
if (empty($topic)) {
    redirect_header(BLUESBB_URL . '/i/', 2, 'トピックが選択されていません。');

    exit();
}
if (empty($mode)) {
    redirect_header(BLUESBB_URL . '/i/i_threadlist.php?top=' . $topic, 2, 'モードが選択されていません。');

    exit();
}
$sql = 'SELECT topic_name, topic_access, res_limit FROM ' . $xoopsDB->prefix('bluesbb_topic') . ' WHERE topic_id = ' . $topic;
if (!$result = $xoopsDB->query($sql)) {
    redirect_header(BLUESBB_URL . '/i/', 3, 'Error!! ' . basename(__FILE__) . ' Line ' . __LINE__);

    exit();
}
if (!$topicdata = $xoopsDB->fetchArray($result)) {
    redirect_header(BLUESBB_URL . '/i/', 3, 'Error!! ' . basename(__FILE__) . ' Line ' . __LINE__);

    exit();
}
$accesserror = 1;
if ('1' == $topicdata['topic_access']) {
    $accesserror = 0;
}
if (1 == $accesserror) {
    redirect_header(BLUESBB_URL . '/i/', 3, 'このトピックへのアクセスは許可されていません。');

    exit();
}
$myts = MyTextSanitizer::getInstance();
$pagetitle = '';
$def_subject = '';
if ('1' == $mode) {
    $pagetitle = '<center><font color="#000080">' . sjisconvert(htmlspecialchars($topicdata['topic_name'], ENT_QUOTES | ENT_HTML5)) . "</font>へ新規スレッド作成</center>\r\n";
} elseif ('2' == $mode) {
    if (empty($thread_id)) {
        redirect_header(BLUESBB_URL . '/i/i_threadlist.php?top=' . $topic, 2, 'スレッドが選択されていません。');

        exit();
    }

    $sql = 'SELECT MAX(res_id) FROM ' . $xoopsDB->prefix('bluesbb') . ' WHERE thread_id =' . $thread_id;

    if (!$r = $xoopsDB->query($sql)) {
        redirect_header(BLUESBB_URL . '/', 1);

        exit();
    }

    [$max_res] = $xoopsDB->fetchRow($r);

    if ($max_res >= $topicdata['res_limit']) {
        redirect_header(BLUESBB_URL . '/i/i_threadlist.php?top=' . $topic, 3, 'このスレッドは規定投稿数を超えていますので、書込むことができません。');

        exit();
    }

    $sql = 'SELECT title FROM ' . $xoopsDB->prefix('bluesbb') . ' WHERE thread_id = ' . $thread_id . ' AND res_id = 0';

    if (!$result = $xoopsDB->query($sql)) {
        redirect_header(BLUESBB_URL . '/i/', 3, 'Error!! ' . basename(__FILE__) . ' Line ' . __LINE__);

        exit();
    }

    if (!$resdata = $xoopsDB->fetchArray($result)) {
        redirect_header(BLUESBB_URL . '/i/', 3, 'Error!! ' . basename(__FILE__) . ' Line ' . __LINE__);

        exit();
    }

    $pagetitle = '<center><font color="#000080">' . sjisconvert(htmlspecialchars($resdata['title'], ENT_QUOTES | ENT_HTML5)) . "</font>へ返信</center>\r\n";

    $def_subject = 'Re: ' . sjisconvert(htmlspecialchars($resdata['title'], ENT_QUOTES | ENT_HTML5));
} else {
    echo 'このページは直接アクセスできません！';

    exit;
}
header('Content-type: text/html; charset=Shift-jis');
?>
<html>
<head>
    <title>POST_FORM</title>
    <META HTTP-EQUIV="Content-Type" CONTENT="text/html;CHARSET=Shift-jis">
</head>
<body bgcolor="#F0F0F0" text="#000000">
<?php
if (eregi('mozilla', $_SERVER['HTTP_USER_AGENT'])) {
    echo "<div align=\"center\">\r\n";

    echo "<table width=\"200\" border=\"0\" cellspacing=\"1\" cellpadding=\"5\" bgcolor=\"#696969\">\r\n";

    echo "<tr>\r\n";

    echo "<td style=\"font-family: monospace\" bgcolor=\"#F0F0F0\">\r\n";
}
echo $pagetitle;
?>
<hr>
<font color="#FF0000">*</font>の項目は必須
<form method="POST" ACTION="<?php echo BLUESBB_URL; ?>/i/i_post.php">
    ▼名前<br>
    <input type="text" name="name" size="14" maxlength="50" value=""><br>
    ▼ﾀｲﾄﾙ<font color="#FF0000">*</font><br>
    <input name="title" value="<?php echo $def_subject; ?>" size="14" maxlength="100"><br>
    ▼ﾒｯｾｰｼﾞ<font color="#FF0000">*</font><br>
    <textarea name="message" rows="4" cols="14"></textarea><br>
    ▼mail<br>
    <input name="mail" size="14" maxlength="100" istyle="3" value=""><br>
    ▼site<br>
    <input name="url" size="14" maxlength="255" istyle="3" value="http://"><br>
    ▼ﾊﾟｽﾜｰﾄﾞ<br>
    <input type="password" name="pass" size="8" maxlength="12" istyle="4"><br>
    <center><input type="submit" name="icontents_submit" value=" 送信 "></center>
    <?php
    echo $GLOBALS['xoopsBluesTicket']->getTicketHtml(__LINE__);
    if (mb_strstr($_SERVER['HTTP_USER_AGENT'], 'DoCoMo') || mb_strstr($_SERVER['HTTP_USER_AGENT'], 'J-PHONE') || mb_strstr($_SERVER['HTTP_USER_AGENT'], 'UP.Browser')) {
        echo '<input type="hidden" name="' . session_name() . '" value="' . session_id() . '">';
    }
    if ('2' == $mode) {
        echo '<input type="hidden" name="thread_id" value="' . $thread_id . '">';
    }
    ?>
    <input type="hidden" name="topic" value="<?php echo $topic; ?>">
    <input type="hidden" name="mode" value="<?php echo $mode; ?>">
</form>
<hr>
<center><a href="<?php echo BLUESBB_URL; ?>/i/index.php">Top</a>&nbsp;
    <?php
    if ('1' == $mode) {
        echo '<a href="' . BLUESBB_URL . '/i/i_threadlist.php?top=' . $topic . "\">Back</a></center>\r\n<hr>\r\n";
    } elseif ('2' == $mode) {
        echo '<a href="' . BLUESBB_URL . '/i/i_message.php?top=' . $topic . '&amp;thr=' . $thread_id . '&amp;dis=' . $display . "&amp;num=l10\">Back</a></center>\r\n<hr>\r\n";
    }
    ?>
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
