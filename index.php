<?php

// Author: Sting_Band
// URL: http://www.bluish.jp/

require dirname(__DIR__, 2) . '/mainfile.php';
require __DIR__ . '/header.php';
//板数をカウント、１つだけならばその板にリダイレクト。
if ('1' == $xoopsModuleConfig['indexred']) {
    $sql = 'SELECT COUNT(topic_id) FROM ' . $xoopsDB->prefix('bluesbb_topic');

    if (!$result = $xoopsDB->query($sql)) {
        redirect_header(XOOPS_URL . '/index.php', 3, 'Error!! ' . basename(__FILE__) . ' Line ' . __LINE__);

        exit();
    }

    [$topic_count] = $xoopsDB->fetchRow($result);

    if ('1' == $topic_count) {
        $sql = 'SELECT topic_id FROM ' . $xoopsDB->prefix('bluesbb_topic');

        $result = $xoopsDB->query($sql);

        while (false !== ($myrow = $xoopsDB->fetchArray($result))) {
            $topic = $myrow['topic_id'];
        }

        header('Location: ' . BLUESBB_URL . '/topic.php?top=' . $topic);

        exit();
    }
}
$GLOBALS['xoopsOption']['template_main'] = 'bluesbb_index.html';
require XOOPS_ROOT_PATH . '/header.php';
$myts = MyTextSanitizer::getInstance();
$xoopsTpl->assign('bluesbb_url', BLUESBB_URL);
$xoopsTpl->assign('lang_welcomemsg', sprintf(_MD_WELCOME, $xoopsConfig['sitename']));
$xoopsTpl->assign('phone', _MD_PHONE);
$xoopsTpl->assign('copyright', BLUESBB_COPYRIGHT);

//カテゴリを抽出
$where_access = '';
if (is_object($xoopsUser)) {
    $where_access = ' (topic_access = 1 OR topic_access = 2 OR topic_access = 3 OR topic_access = 4 OR topic_access = 5';

    $groups = $memberHandler->getGroupsByUser($xoopsUser->getVar('uid'), true);

    foreach ($groups as $group) {
        $where_access .= ' OR topic_group = ' . $group->getVar('groupid');
    }

    if ($xoopsUser->isAdmin($xoopsModule->mid())) {
        $where_access .= ' OR topic_access = 6';
    }
} else {
    $where_access = ' (topic_access = 1 OR topic_access = 2 OR topic_access = 5';
}
$sql = 'SELECT cat_id FROM ' . $xoopsDB->prefix('bluesbb_topic') . ' WHERE' . $where_access . ') ORDER BY cat_id, topic_id';
if (!$result = $xoopsDB->query($sql)) {
    redirect_header(XOOPS_URL . '/', 3, 'Error!! ' . basename(__FILE__) . ' Line ' . __LINE__);

    exit();
}
$cat_array = [];
while (false !== ($myrow = $xoopsDB->fetchArray($result))) {
    $cat_array[] = $myrow['cat_id'];
}
$cat_array = array_unique($cat_array);
$i = 1;
$cid = '';
foreach ($cat_array as $cat_a) {
    if (1 == $i) {
        $cid .= 'WHERE cat_id=' . $cat_a;
    } else {
        $cid .= ' OR cat_id=' . $cat_a;
    }

    $i++;
}
$sql = 'SELECT * FROM ' . $xoopsDB->prefix('bluesbb_categories') . ' ' . $cid . ' ORDER BY cat_order';
if (!$result = $xoopsDB->query($sql)) {
    redirect_header(XOOPS_URL . '/', 3, 'Error!! ' . basename(__FILE__) . ' Line ' . __LINE__);

    exit();
}
$categories = [];
while (false !== ($cat_row = $xoopsDB->fetchArray($result))) {
    $categories[] = $cat_row;
}

//板を抽出
$sql = 'SELECT * FROM ' . $xoopsDB->prefix('bluesbb_topic') . ' WHERE' . $where_access . ') ORDER BY cat_id, topic_order, topic_id';
if (!$result = $xoopsDB->query($sql)) {
    redirect_header(XOOPS_URL . '/', 3, 'Error!! ' . basename(__FILE__) . ' Line ' . __LINE__);

    exit();
}
$where_top = [];
$topics = [];
while (false !== ($topic_data = $xoopsDB->fetchArray($result))) {
    $where_top[] = $topic_data['topic_id'];

    $topics[] = $topic_data;
}

//スレ一覧を抽出
$now_time = time();
$threads = [];
foreach ($where_top as $wt) {
    $srn = 1;

    $sql = 'SELECT b.topic_id, b.thread_id, b.title, b.res_time, t.topic_style FROM '
           . $xoopsDB->prefix('bluesbb')
           . ' b LEFT JOIN '
           . $xoopsDB->prefix('bluesbb_topic')
           . ' t ON t.topic_id = b.topic_id WHERE b.topic_id = '
           . $wt
           . ' AND b.res_id = 0 ORDER BY b.res_time DESC LIMIT 0, '
           . $xoopsModuleConfig['indexperth'];

    if (!$result = $xoopsDB->query($sql)) {
        redirect_header(XOOPS_URL . '/', 3, 'Error!! ' . basename(__FILE__) . ' Line ' . __LINE__);

        exit();
    }

    while (false !== ($thread_data = $xoopsDB->fetchArray($result))) {
        $count_sql = 'SELECT COUNT(*) AS count, MAX(post_id) AS max1, MAX(res_id) AS max2 FROM ' . $xoopsDB->prefix('bluesbb') . ' WHERE thread_id = ' . $thread_data['thread_id'];

        if (!$count_result = $xoopsDB->query($count_sql)) {
            redirect_header(XOOPS_URL . '/', 3, 'Error!! ' . basename(__FILE__) . ' Line ' . __LINE__);

            exit();
        }

        [$count, $max1, $max2] = $xoopsDB->fetchRow($count_result);    //投稿数をカウント

        $GLOBALS['xoopsDB']->freeRecordSet($count_result);

        if (($now_time - $thread_data['res_time']) <= 60 * 60 * $xoopsModuleConfig['newtime']) {
            $new = '<font color="#FF0000">New</font>';
        } else {
            $new = '';
        }

        switch ($thread_data['topic_style']) {
            case '1':
                $lt = 'l50';
                break;
            case '2':
                $lt = $max2 + 1;
                break;
            case '3':
                $lt = $max1;
                break;
        }

        $thread_data['title'] = "<a href='" . BLUESBB_URL . '/thread.php?thr=' . $thread_data['thread_id'] . '&amp;sty=' . $thread_data['topic_style'] . '&amp;num=' . $lt . "'>" . $srn . ':&nbsp;' . htmlspecialchars($thread_data['title'], ENT_QUOTES | ENT_HTML5)
                                . '(' . $count . ')</a>' . $new . '&nbsp;&nbsp;';

        $threads[] = $thread_data;

        $srn++;
    }
}

//テンプレートへの書き出し
$cat_count = count($categories);
if ($cat_count > 0) {
    for ($i = 0; $i < $cat_count; $i++) {
        $categories[$i]['cat_title'] = htmlspecialchars($categories[$i]['cat_title'], ENT_QUOTES | ENT_HTML5);

        foreach ($topics as $topic_row) {
            if ($topic_row['cat_id'] == $categories[$i]['cat_id']) {
                $categories[$i]['topics']['topic_id'][] = $topic_row['topic_id'];

                $categories[$i]['topics']['topic_name'][] = htmlspecialchars($topic_row['topic_name'], ENT_QUOTES | ENT_HTML5);

                $categories[$i]['topics']['topic_info'][] = $myts->displayTarea($topic_row['topic_info'], 0, 1, 1, 1, 1);

                $categories[$i]['topics']['topic_access'][] = $topic_row['topic_access'];
            }
        }

        $xoopsTpl->append('categories', $categories[$i]);
    }
} else {
    $xoopsTpl->append('categories', []);
}

foreach ($threads as $thread_row) {
    $xoopsTpl->append('threads', $thread_row);
}

require_once XOOPS_ROOT_PATH . '/footer.php';
