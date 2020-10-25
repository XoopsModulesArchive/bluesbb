<?php

include 'header.php';
$topic = (int)$_GET['topic'];
$more = isset($_GET['more']) ? (int)$_GET['more'] : 0;
if (empty($topic)) {
    redirect_header('index.php', 2, _MD_ERRORFORUM);

    exit();
}
$GLOBALS['xoopsOption']['template_main'] = 'bluesbb_sreadlist.html';
require XOOPS_ROOT_PATH . '/header.php';
$myts = MyTextSanitizer::getInstance();
$xoopsTpl->assign('topic_id', $topic);
$xoopsTpl->assign('topicback', _MD_TOPICBACK);
$xoopsTpl->assign('copyright', BLUESBB_COPYRIGHT);
//スレッド一覧の抽出
$list_more = 300 * $more;
$sql = 'SELECT sread_id, title FROM ' . $xoopsDB->prefix('bluesbb') . ' WHERE topic_id = ' . $topic . ' AND res_id = 0 ORDER BY res_time DESC LIMIT ' . $list_more . ', 300';
if (!$result = $xoopsDB->query($sql)) {
    redirect_header('index.php', 2, _MD_ERRORCONNECT);

    exit();
}
$srn = 1;
while (false !== ($myrow = $xoopsDB->fetchArray($result))) {
    $count_sql = 'SELECT COUNT(post_id) FROM ' . $xoopsDB->prefix('bluesbb') . ' WHERE sread_id = ' . $myrow['sread_id'];

    if (!$count_result = $xoopsDB->query($count_sql)) {
        redirect_header('index.php', 2, _MD_ERRORCONNECT);

        exit();
    }

    [$count] = $xoopsDB->fetchRow($count_result);    //投稿数をカウント

    $GLOBALS['xoopsDB']->freeRecordSet($count_result);

    $xoopsTpl->append('sread_name_list', ['srn' => $srn, 'topic_id' => $topic, 'sread_id' => $myrow['sread_id'], 'title' => htmlspecialchars($myrow['title'], ENT_QUOTES | ENT_HTML5), 'count' => $count]);

    $srn++;
}
$GLOBALS['xoopsDB']->freeRecordSet($result);
$more = ++$more;
$xoopsTpl->assign('next300', '<a href="sreadlist.php?topic=' . $topic . '&amp;more=' . $more . '">' . _MD_NEXT300 . '</a>');
require XOOPS_ROOT_PATH . '/footer.php';
