<?php

// Author: Sting_Band
// URL: http://www.bluish.jp/

ini_set('mbstring.language', 'Japanese');
ini_set('mbstring.http_output', 'pass');
require dirname(__DIR__, 3) . '/mainfile.php';
require __DIR__ . '/i_header.php';
$thread_id = isset($_GET['thr']) ? (int)$_GET['thr'] : 0;
$display = isset($_GET['dis']) ? (int)$_GET['dis'] : 0;
if (empty($thread_id)) {
    redirect_header(BLUESBB_URL . '/i/i_threadlist.php?top=' . $topic, 2, 'スレッドが選択されていません。');

    exit();
}
if (empty($display)) {
    redirect_header(BLUESBB_URL . '/i/i_threadlist.php?top=' . $topic, 2, '表示形式が選択されていません。');

    exit();
}
$sql = 'SELECT topic_id FROM ' . $xoopsDB->prefix('bluesbb') . ' WHERE thread_id = ' . $thread_id . ' AND res_id = 0';
if (!$result = $xoopsDB->query($sql)) {
    redirect_header(BLUESBB_URL . '/', 3, 'Error!! ' . basename(__FILE__) . ' Line ' . __LINE__);

    exit();
}
if (!$topic_id = $xoopsDB->fetchArray($result)) {
    redirect_header(BLUESBB_URL . '/', 3, 'Error!! ' . basename(__FILE__) . ' Line ' . __LINE__);

    exit();
}
$topic = '';
$topic = $topic_id['topic_id'];
$sql = 'SELECT topic_access FROM ' . $xoopsDB->prefix('bluesbb_topic') . ' WHERE topic_id = ' . $topic;
if (!$result = $xoopsDB->query($sql)) {
    redirect_header(BLUESBB_URL . '/i/', 3, 'Error!! ' . basename(__FILE__) . ' Line ' . __LINE__);

    exit();
}
if (!$topicdata = $xoopsDB->fetchArray($result)) {
    redirect_header(BLUESBB_URL . '/i/', 3, 'Error!! ' . basename(__FILE__) . ' Line ' . __LINE__);

    exit();
}
$accesserror = 1;
if ('1' == $topicdata['topic_access'] || '2' == $topicdata['topic_access'] || '5' == $topicdata['topic_access']) {
    $accesserror = 0;
}
if (1 == $accesserror) {
    redirect_header(BLUESBB_URL . '/i/', 3, 'このトピックへのアクセスは許可されていません。');

    exit();
}
$number = $_GET['num'];
if (empty($number)) {
    redirect_header(BLUESBB_URL . '/i/', 2, '投稿が指定されていません。');

    exit();
}
$where = '';
$topnum = '0';
if ('l' == mb_substr($number, 0, 1)) {
    $ls = mb_substr($number, 1);

    $ls = (int)$ls;

    $where = 'b.res_id > 0 ORDER BY b.post_time DESC LIMIT 0, ' . $ls;

    $topnum = '1';
} elseif (preg_match("/\-/", $number)) {
    [$st, $to] = explode('-', $number);

    if (!$st || $st < 2) {
        $st = 1;
    }

    $st2 = (int)$st - 1;

    $to2 = (int)$to - 1;

    $where = '(b.res_id BETWEEN ' . $st2 . ' AND ' . $to2 . ') ORDER BY b.post_time DESC';
} else {
    $st = (int)$number;

    $to = (int)$number;

    $st2 = $st - 1;

    $where = 'b.res_id = ' . $st2;
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
$sql = 'SELECT title FROM ' . $xoopsDB->prefix('bluesbb') . ' WHERE thread_id = ' . $thread_id . ' AND res_id = 0';
if (!$result = $xoopsDB->query($sql)) {
    redirect_header(BLUESBB_URL . '/i/', 3, 'Error!! ' . basename(__FILE__) . ' Line ' . __LINE__);

    exit();
}
if (!$threaddata = $xoopsDB->fetchArray($result)) {
    redirect_header(BLUESBB_URL . '/i/', 3, 'Error!! ' . basename(__FILE__) . ' Line ' . __LINE__);

    exit();
}
?>
<center><font color="#000000"><?php echo sjisconvert(htmlspecialchars($threaddata['title'], ENT_QUOTES | ENT_HTML5)); ?></font></center>
<hr>
<?php
$now_time = time();
if ('1' == $topnum) {
    $sql = 'SELECT b.*, u.uname FROM ' . $xoopsDB->prefix('bluesbb') . ' b LEFT JOIN ' . $xoopsDB->prefix('users') . ' u ON u.uid = b.uid WHERE b.thread_id = ' . $thread_id . ' AND b.res_id = 0';

    if (!$result = $xoopsDB->query($sql)) {
        redirect_header(BLUESBB_URL . '/i/', 3, 'Error!! ' . basename(__FILE__) . ' Line ' . __LINE__);

        exit();
    }

    while (false !== ($myrow = $xoopsDB->fetchArray($result))) {
        ++$myrow['res_id'];

        if (($now_time - $myrow['post_time']) <= 60 * 60 * 24) {
            echo '<font color="#c00000">' . $myrow['res_id'] . '</font>:<font color="#FF0000">New</font>&nbsp;<font color="#000080">' . sjisconvert(htmlspecialchars($myrow['title'], ENT_QUOTES | ENT_HTML5)) . '</font><br>';
        } else {
            echo '<font color="#c00000">' . $myrow['res_id'] . '</font>:<font color="#000080">' . sjisconvert(htmlspecialchars($myrow['title'], ENT_QUOTES | ENT_HTML5)) . '</font><br>';
        }

        $ptime = date('m/d H:i', xoops_getUserTimestamp($myrow['post_time']));

        if ($myrow['uid'] > 0) {
            echo '<font color="#008000">' . sjisconvert(htmlspecialchars($myrow['uname'], ENT_QUOTES | ENT_HTML5)) . '</font>&nbsp;<font color="#696969">' . $ptime . '</font><br>';
        } else {
            echo '<font color="#008000">' . sjisconvert(htmlspecialchars($myrow['name'], ENT_QUOTES | ENT_HTML5)) . '</font>&nbsp;<font color="#696969">' . $ptime . '</font><br>';
        }

        echo gt_link_i(sjisconvert($myts->displayTarea($myrow['message'], 0, 1, 1, 1, 1)), $topic, $myrow['thread_id'], BLUESBB_URL) . '<br><hr>';
    }

    $GLOBALS['xoopsDB']->freeRecordSet($result);
}
$res_array = [];
$sql = 'SELECT b.*, u.uname FROM ' . $xoopsDB->prefix('bluesbb') . ' b LEFT JOIN ' . $xoopsDB->prefix('users') . ' u ON u.uid = b.uid WHERE b.thread_id = ' . $thread_id . ' AND ' . $where;
if (!$result = $xoopsDB->query($sql)) {
    redirect_header(BLUESBB_URL . '/i/', 3, 'Error!! ' . basename(__FILE__) . ' Line ' . __LINE__);

    exit();
}
while (false !== ($myrow = $xoopsDB->fetchArray($result))) {
    $res_array[] = $myrow;
}
$GLOBALS['xoopsDB']->freeRecordSet($result);
$res_array = array_reverse($res_array);
foreach ($res_array as $log) {
    ++$log['res_id'];

    if (1 == $display) {
        if (($now_time - $log['post_time']) <= 60 * 60 * 24) {
            echo '<font color="#c00000">' . $log['res_id'] . '</font>:<font color="#FF0000">New</font>&nbsp;<a href="i_message.php?thr=' . $thread_id . '&amp;dis=3&amp;num=' . $log['res_id'] . '">' . sjisconvert(htmlspecialchars($log['title'], ENT_QUOTES | ENT_HTML5)) . '</a><br>';
        } else {
            echo '<font color="#c00000">' . $log['res_id'] . '</font>:<a href="i_message.php?thr=' . $thread_id . '&amp;dis=3&amp;num=' . $log['res_id'] . '">' . sjisconvert(htmlspecialchars($log['title'], ENT_QUOTES | ENT_HTML5)) . '</a><br>';
        }

        $ptime = date('m/d H:i', xoops_getUserTimestamp($log['post_time']));

        if ($log['uid'] > 0) {
            echo '<font color="#008000">' . sjisconvert(htmlspecialchars($log['uname'], ENT_QUOTES | ENT_HTML5)) . '</font>&nbsp;<font color="#696969">' . $ptime . '</font><br>';
        } else {
            echo '<font color="#008000">' . sjisconvert(htmlspecialchars($log['name'], ENT_QUOTES | ENT_HTML5)) . '</font>&nbsp;<font color="#696969">' . $ptime . '</font><br>';
        }

        echo '<hr>';
    } elseif (2 == $display || 3 == $display) {
        if (($now_time - $log['post_time']) <= 60 * 60 * 24) {
            echo '<font color="#c00000">' . $log['res_id'] . '</font>:<font color="#FF0000">New</font>&nbsp;<font color="#000080">' . sjisconvert(htmlspecialchars($log['title'], ENT_QUOTES | ENT_HTML5)) . '</font><br>';
        } else {
            echo '<font color="#c00000">' . $log['res_id'] . '</font>:<font color="#000080">' . sjisconvert(htmlspecialchars($log['title'], ENT_QUOTES | ENT_HTML5)) . '</font><br>';
        }

        $ptime = date('m/d H:i', xoops_getUserTimestamp($log['post_time']));

        if ($log['uid'] > 0) {
            echo '<font color="#008000">' . sjisconvert(htmlspecialchars($log['uname'], ENT_QUOTES | ENT_HTML5)) . '</font>&nbsp;<font color="#696969">' . $ptime . '</font><br>';
        } else {
            echo '<font color="#008000">' . sjisconvert(htmlspecialchars($log['name'], ENT_QUOTES | ENT_HTML5)) . '</font>&nbsp;<font color="#696969">' . $ptime . '</font><br>';
        }

        echo gt_link_i(sjisconvert($myts->displayTarea($log['message'], 0, 1, 1, 1, 1)), $topic, $log['thread_id'], BLUESBB_URL) . '<br><hr>';
    } else {
        redirect_header(BLUESBB_URL . '/i/i_threadlist.php?top=' . $topic, 2, '表示形式が不正です。');

        exit();
    }
}
if (!isset($log['res_id'])) {
    $log['res_id'] = 0;
}
$back_st = $log['res_id'] - 19;
$back_to = $log['res_id'] - 10;
if ($back_st < 1) {
    $back_st = 1;
}
if ($back_to < 10) {
    $back_to = 10;
}
$next_st = $log['res_id'] + 1;
$next_to = $log['res_id'] + 10;
if (1 == $display || 3 == $display) {
    echo '<center><a href="' . BLUESBB_URL . '/i/i_message.php?thr=' . $thread_id . '&amp;dis=1&amp;num=' . $back_st . '-' . $back_to . '">前</a>';

    echo '<a href="' . BLUESBB_URL . '/i/i_message.php?thr=' . $thread_id . '&amp;dis=1&amp;num=' . $next_st . '-' . $next_to . '">次</a>';

    echo '<a href="' . BLUESBB_URL . '/i/i_message.php?thr=' . $thread_id . '&amp;dis=1&amp;num=1-10">1-</a>';

    echo '<a href="' . BLUESBB_URL . '/i/i_message.php?thr=' . $thread_id . '&amp;dis=1&amp;num=l10">新</a>';
} elseif (2 == $display) {
    echo '<center><a href="' . BLUESBB_URL . '/i/i_message.php?thr=' . $thread_id . '&amp;dis=2&amp;num=' . $back_st . '-' . $back_to . '">前</a>';

    echo '<a href="' . BLUESBB_URL . '/i/i_message.php?thr=' . $thread_id . '&amp;dis=2&amp;num=' . $next_st . '-' . $next_to . '">次</a>';

    echo '<a href="' . BLUESBB_URL . '/i/i_message.php?thr=' . $thread_id . '&amp;dis=2&amp;num=1-10">1-</a>';

    echo '<a href="' . BLUESBB_URL . '/i/i_message.php?thr=' . $thread_id . '&amp;dis=2&amp;num=l10">新</a>';
}
if ('1' == $topicdata['topic_access']) {
    echo '<a href="' . BLUESBB_URL . '/i/i_form.php?top=' . $topic . '&amp;thr=' . $thread_id . '&amp;mod=2&amp;dis=' . $display . '">書</a>';
}
echo '<a href="' . BLUESBB_URL . '/i/i_threadlist.php?top=' . $topic . '">板</a>';
?>
</center>
<hr>
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
