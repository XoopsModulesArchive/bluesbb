<?php

include 'header.php';
$topic = (int)$_GET['topic'];
if (empty($topic)) {
    redirect_header('index.php', 2, _MD_ERRORTOPIC);

    exit();
}
$sql = 'SELECT * FROM ' . $xoopsDB->prefix('bluesbb_topic') . ' WHERE topic_id = ' . $topic;
if (!$result = $xoopsDB->query($sql)) {
    redirect_header('index.php', 2, _MD_ERRORCONNECT);

    exit();
}
if (!$topicdata = $xoopsDB->fetchArray($result)) {
    redirect_header('index.php', 2, _MD_ERROREXIST);

    exit();
}
$accesserror = 0;
if ('3' == $topicdata['topic_access'] || '4' == $topicdata['topic_access']) {
    if (!$xoopsUser) {
        $accesserror = 1;
    }
}
if ('6' == $topicdata['topic_access']) {
    $accesserror = 1;

    if ($xoopsUser) {
        $groups = $memberHandler->getGroupsByUser($xoopsUser->getVar('uid'), true);

        foreach ($groups as $group) {
            if (preg_match('/' . $topicdata['topic_group'] . '/', $group->getVar('groupid'))) {
                $accesserror = 0;
            }
        }

        if ($xoopsUser->isAdmin($xoopsModule->mid())) {
            $accesserror = 0;
        }
    }
}
if (1 == $accesserror) {
    redirect_header('index.php', 3, _MD_TOPICACCESSERROR);

    exit();
}

$GLOBALS['xoopsOption']['template_main'] = 'bluesbb_viewtopic.html';
require XOOPS_ROOT_PATH . '/header.php';

$xoopsTpl->assign('topic_id', $topic);
$xoopsTpl->assign('topic_index_title', sprintf(_MD_BLUESBBINDEX, $xoopsConfig['sitename']));
$myts = MyTextSanitizer::getInstance();
$xoopsTpl->assign('topic_name', htmlspecialchars($topicdata['topic_name'], ENT_QUOTES | ENT_HTML5));
$xoopsTpl->assign('topic_info', $myts->displayTarea($topicdata['topic_info']));
$xoopsTpl->assign('postnew', _MD_POSTNEW);
$xoopsTpl->assign('sreadlist', _MD_SREADLIST);
$xoopsTpl->assign('site', _MD_SITE);
$xoopsTpl->assign('mail', _MD_MAIL);
$xoopsTpl->assign('replies', _MD_REPLIES);
$xoopsTpl->assign('edit', _MD_EDIT);
$xoopsTpl->assign('allview', _MD_ALLVIEW);
$xoopsTpl->assign('new50', _MD_NEW50);
$xoopsTpl->assign('topichead', _MD_TOPICHEAD);
$xoopsTpl->assign('reload', _MD_RELOAD);
$xoopsTpl->assign('phone', _MD_PHONE);
$xoopsTpl->assign('copyright', BLUESBB_COPYRIGHT);
$now_time = time();
//スレッド一覧の抽出
$sql = 'SELECT sread_id, title FROM ' . $xoopsDB->prefix('bluesbb') . ' WHERE topic_id = ' . $topic . ' AND res_id = 0 ORDER BY res_time DESC LIMIT 0, ' . BLUESBB_TOPICSREAD;
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

//１さんの抽出
$sql = 'SELECT b.*, u.uname, u.user_avatar, u.user_sig, u.posts, u.rank FROM ' . $xoopsDB->prefix('bluesbb') . ' b LEFT JOIN ' . $xoopsDB->prefix('users') . ' u ON u.uid = b.uid WHERE b.topic_id = ' . $topic . ' AND b.res_id = 0 ORDER BY b.res_time DESC LIMIT 0, ' . $topicdata['sread_per_page'];
if (!$result = $xoopsDB->query($sql)) {
    redirect_header('index.php', 2, _MD_ERRORCONNECT);

    exit();
}
$where_str = [];
$top_array = [];
while (false !== ($myrow = $xoopsDB->fetchArray($result))) {
    if ($myrow['uid'] > 0) {
        $myrow['uname'] = '<a href="' . XOOPS_URL . '/userinfo.php?uid=' . $myrow['uid'] . '">' . $myrow['uname'] . '</a>';

        $rank = xoops_getrank($myrow['rank'], $myrow['posts']);

        $myrow['rank'] = $rank['title'];

        $myrow['user_avatar'] = '<img class="comUserImg" src="' . XOOPS_URL . '/uploads/' . $myrow['user_avatar'] . '" alt="">';

        if (1 == $topicdata['allow_sig'] && 1 == $myrow['attachsig']) {
            $myrow['user_sig'] = '<br><br><hr size="1">' . $myts->displayTarea($myrow['user_sig'], 0, 1, 1);
        } else {
            $myrow['user_sig'] = '';
        }
    } else {
        $myrow['uname'] = '<a href="' . XOOPS_URL . '/register.php">' . $xoopsConfig['anonymous'] . '</a>';
    }

    ++$myrow['res_id'];

    $myrow['name'] = htmlspecialchars($myrow['name'], ENT_QUOTES | ENT_HTML5);

    if (($now_time - $myrow['post_time']) <= 60 * 60 * 24) {
        $myrow['title'] = htmlspecialchars($myrow['title'], ENT_QUOTES | ENT_HTML5) . '&nbsp;<font color="#FF0000">New</font>';
    } else {
        $myrow['title'] = htmlspecialchars($myrow['title'], ENT_QUOTES | ENT_HTML5);
    }

    $myrow['url'] = htmlspecialchars($myrow['url'], ENT_QUOTES | ENT_HTML5);

    $myrow['mail'] = htmlspecialchars($myrow['mail'], ENT_QUOTES | ENT_HTML5);

    $myrow['message'] = gt_link($myts->displayTarea($myrow['message'], 0, 1, 1), $topic, $myrow['sread_id']);

    if ($xoopsUser) {
        if ($xoopsUser->isAdmin($xoopsModule->mid())) {
            $myrow['message'] .= '<br><br><font color="#4169E1">' . htmlspecialchars($myrow['poster_ip'], ENT_QUOTES | ENT_HTML5) . '&nbsp;' . htmlspecialchars($myrow['poster_host'], ENT_QUOTES | ENT_HTML5) . '<br>' . htmlspecialchars($myrow['poster_agent'], ENT_QUOTES | ENT_HTML5) . '</font>';
        }
    }

    $myrow['post_time'] = formatTimestamp($myrow['post_time']);

    $where_str[] = $myrow['sread_id'];

    $top_array[] = $myrow;    //１さんを配列に格納
}
$GLOBALS['xoopsDB']->freeRecordSet($result);

//レスの抽出
$res_array = [];
foreach ($where_str as $ws) {
    $sql2 = 'SELECT b.*, u.uname, u.user_avatar, u.user_sig, u.posts, u.rank FROM ' . $xoopsDB->prefix('bluesbb') . ' b LEFT JOIN ' . $xoopsDB->prefix('users') . ' u ON u.uid = b.uid WHERE b.sread_id = ' . $ws . ' AND b.res_id > 0 ORDER BY b.res_time DESC LIMIT 0, ' . $topicdata['res_per_sread'];

    if (!$result2 = $xoopsDB->query($sql2)) {
        redirect_header('index.php', 2, _MD_ERRORCONNECT);

        exit();
    }

    while (false !== ($myrow2 = $xoopsDB->fetchArray($result2))) {
        if ($myrow2['uid'] > 0) {
            $myrow2['uname'] = '<a href="' . XOOPS_URL . '/userinfo.php?uid=' . $myrow2['uid'] . '">' . $myrow2['uname'] . '</a>';

            $rank = xoops_getrank($myrow2['rank'], $myrow2['posts']);

            $myrow2['rank'] = $rank['title'];

            $myrow2['user_avatar'] = '<img class="comUserImg" src="' . XOOPS_URL . '/uploads/' . $myrow2['user_avatar'] . '" alt="">';

            if (1 == $topicdata['allow_sig'] && 1 == $myrow2['attachsig']) {
                $myrow2['user_sig'] = '<br><br><hr size="1">' . $myts->displayTarea($myrow2['user_sig'], 0, 1, 1);
            } else {
                $myrow2['user_sig'] = '';
            }
        } else {
            $myrow2['uname'] = '<a href="' . XOOPS_URL . '/register.php">' . $xoopsConfig['anonymous'] . '</a>';
        }

        ++$myrow2['res_id'];

        $myrow2['name'] = htmlspecialchars($myrow2['name'], ENT_QUOTES | ENT_HTML5);

        if (($now_time - $myrow2['post_time']) <= 60 * 60 * 24) {
            $myrow2['title'] = htmlspecialchars($myrow2['title'], ENT_QUOTES | ENT_HTML5) . '&nbsp;<font color="#FF0000">New</font>';
        } else {
            $myrow2['title'] = htmlspecialchars($myrow2['title'], ENT_QUOTES | ENT_HTML5);
        }

        $myrow2['url'] = htmlspecialchars($myrow2['url'], ENT_QUOTES | ENT_HTML5);

        $myrow2['mail'] = htmlspecialchars($myrow2['mail'], ENT_QUOTES | ENT_HTML5);

        $myrow2['message'] = gt_link($myts->displayTarea($myrow2['message'], 0, 1, 1), $topic, $myrow2['sread_id']);

        if ($xoopsUser) {
            if ($xoopsUser->isAdmin($xoopsModule->mid())) {
                $myrow2['message'] .= '<br><br><font color="#4169E1">' . htmlspecialchars($myrow2['poster_ip'], ENT_QUOTES | ENT_HTML5)
                                      . '&nbsp;' . htmlspecialchars($myrow2['poster_host'], ENT_QUOTES | ENT_HTML5)
                                      . '<br>' . htmlspecialchars($myrow2['poster_agent'], ENT_QUOTES | ENT_HTML5)
                                      . '</font>';
            }
        }

        $myrow2['post_time'] = formatTimestamp($myrow2['post_time']);

        $res_array[] = $myrow2;    //レスを配列に格納
    }

    $GLOBALS['xoopsDB']->freeRecordSet($result2);
}
//レス配列をひっくり返す
$res_array = array_reverse($res_array);
//1さんとレスを結合
$data_array = [];
foreach ($top_array as $top) {
    $sre = $top['sread_id'];

    $data_array[(string)$sre][] = $top;

    foreach ($res_array as $res) {
        if ($sre == $res['sread_id']) {
            $data_array[(string)$sre][] = $res;
        }
    }
}
//テンプレートへの書き出し
foreach ($data_array as $value) {
    $xoopsTpl->append('sreads', $value);
}

require XOOPS_ROOT_PATH . '/footer.php';
