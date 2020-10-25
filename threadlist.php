<?php

// Author: Sting_Band
// URL: http://www.bluish.jp/

require dirname(__DIR__, 2) . '/mainfile.php';
require __DIR__ . '/header.php';
$topic = isset($_GET['top']) ? (int)$_GET['top'] : 0;
$style = isset($_GET['sty']) ? (int)$_GET['sty'] : 0;
$more = isset($_GET['more']) ? (int)$_GET['more'] : 0;
if (empty($topic) || empty($style)) {
    redirect_header(BLUESBB_URL . '/', 2, _MD_ERRORFORUM);

    exit();
}
$GLOBALS['xoopsOption']['template_main'] = 'bluesbb_threadlist.html';
require XOOPS_ROOT_PATH . '/header.php';
$myts = MyTextSanitizer::getInstance();
$xoopsTpl->assign('bluesbb_url', BLUESBB_URL);
$xoopsTpl->assign('topic_id', $topic);
$xoopsTpl->assign('topicback', _MD_TOPICBACK);
$xoopsTpl->assign('copyright', BLUESBB_COPYRIGHT);
//スレッド一覧の抽出
$list_more = 300 * $more;
$sql = 'SELECT thread_id, title FROM ' . $xoopsDB->prefix('bluesbb') . ' WHERE topic_id = ' . $topic . ' AND res_id = 0 ORDER BY res_time DESC LIMIT ' . $list_more . ', 300';
if (!$result = $xoopsDB->query($sql)) {
    redirect_header(BLUESBB_URL . '/', 3, 'Error!! ' . basename(__FILE__) . ' Line ' . __LINE__);

    exit();
}
$srn = 1;
while (false !== ($myrow = $xoopsDB->fetchArray($result))) {
    $count_sql = 'SELECT COUNT(*) AS count, MAX(post_id) AS max1, MAX(res_id) AS max2 FROM ' . $xoopsDB->prefix('bluesbb') . ' WHERE thread_id = ' . $myrow['thread_id'];

    if (!$count_result = $xoopsDB->query($count_sql)) {
        redirect_header(BLUESBB_URL . '/', 3, 'Error!! ' . basename(__FILE__) . ' Line ' . __LINE__);

        exit();
    }

    [$count, $max1, $max2] = $xoopsDB->fetchRow($count_result);    //投稿数をカウント

    $GLOBALS['xoopsDB']->freeRecordSet($count_result);

    $xoopsTpl->append('thread_name_list', ['srn' => $srn, 'topic_id' => $topic, 'thread_id' => $myrow['thread_id'], 'topic_style' => $style, 'title' => htmlspecialchars($myrow['title'], ENT_QUOTES | ENT_HTML5), 'count' => $count, 'post_id' => $max1, 'res_id' => ($max2 + 1)]);

    $srn++;
}
$GLOBALS['xoopsDB']->freeRecordSet($result);
$more = ++$more;
$xoopsTpl->assign('next300', '<a href="' . BLUESBB_URL . '/threadlist.php?top=' . $topic . '&amp;sty=' . $style . '&amp;more=' . $more . '">' . _MD_NEXT300 . '</a>');
require XOOPS_ROOT_PATH . '/footer.php';
