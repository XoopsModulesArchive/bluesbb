<?php

include 'header.php';
foreach (['topic', 'sread_id'] as $getint) {
    ${$getint} = isset($_GET[$getint]) ? (int)$_GET[$getint] : 0;
}
if (empty($topic)) {
    redirect_header('index.php', 2, _MD_ERRORTOPIC);

    exit();
} elseif (empty($sread_id)) {
    redirect_header("viewtopic.php?topic=$topic", 2, _MD_ERRORSREAD);

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
    $number = $_GET['number'];
    if (empty($number)) {
        redirect_header('index.php', 2, _MD_ERRORPOST);

        exit();
    }
    $where = '';
    if ('l' == mb_substr($number, 0, 1)) {
        $ls = mb_substr($number, 1);

        $ls = (int)$ls;

        $where = 'b.res_id > 0 ORDER BY b.res_time DESC LIMIT 0, ' . $ls;
    } elseif (preg_match("/\-/", $number)) {
        [$st, $to] = explode('-', $number);

        if (!$st || $st < 2) {
            $st = 2;
        }

        $st2 = (int)$st - 1;

        $to2 = (int)$to - 1;

        $where = '(b.res_id BETWEEN ' . $st2 . ' AND ' . $to2 . ') ORDER BY b.res_time DESC';
    } else {
        $st = (int)$number;

        $to = (int)$number;

        $st2 = $st - 1;

        if (0 == $st2) {
            $st2 = 10000;
        }

        $where = 'b.res_id = ' . $st2;
    }
    $GLOBALS['xoopsOption']['template_main'] = 'bluesbb_viewsread.html';
    require XOOPS_ROOT_PATH . '/header.php';
    $myts = MyTextSanitizer::getInstance();
    $xoopsTpl->assign('topic_id', $topic);
    $xoopsTpl->assign('sread_id', $sread_id);
    $xoopsTpl->assign('topicback', _MD_TOPICBACK);
    $xoopsTpl->assign('allsreadview', _MD_ALLSREADVIEW);
    $xoopsTpl->assign('new50', _MD_NEW50);
    $xoopsTpl->assign('site', _MD_SITE);
    $xoopsTpl->assign('mail', _MD_MAIL);
    $xoopsTpl->assign('replies', _MD_REPLIES);
    $xoopsTpl->assign('edit', _MD_EDIT);
    $xoopsTpl->assign('copyright', BLUESBB_COPYRIGHT);
    //ナビゲーション生成
    $sql = 'SELECT COUNT(*) FROM ' . $xoopsDB->prefix('bluesbb') . ' WHERE sread_id = ' . $sread_id;
    if (!$result = $xoopsDB->query($sql)) {
        redirect_header('index.php', 2, _MD_ERRORCONNECT);

        exit();
    }
    [$post_count] = $xoopsDB->fetchRow($result);
    for ($iCnt = 1; $iCnt <= $post_count; $iCnt += 100) {
        $iTo = $iCnt + 99;

        $pnavi = '<a href="viewsread.php?topic=' . $topic . '&amp;sread_id=' . $sread_id . '&amp;number=' . $iCnt . '-' . $iTo . '">' . $iCnt . '-</a>&nbsp;';

        $xoopsTpl->append('pnavi', $pnavi);
    }
    $s = 1;
    if (isset($st)) {
        if (preg_match("/^\d+$/", $st)) {
            $s = ($st < $post_count + 1) ? $st : $post_count;
        }
    }
    if (isset($ls)) {
        $s = ($s > $post_count) ? $post_count : $post_count - $ls + 2;
    }
    if ($s < 1) {
        $s = 1;
    }
    if ($s > 1) {
        $mae = $s;
    }
    $t = $s + 99;
    if (isset($mae)) {
        if (2 == $mae) {
            $xoopsTpl->assign('last100', '<a href="viewsread.php?topic=' . $topic . '&amp;sread_id=' . $sread_id . '&amp;number=1">前100</a>&nbsp;');
        } else {
            if ($mae > 101) {
                $u = $mae - 99;
            }

            $xoopsTpl->assign('last100', '<a href="viewsread.php?topic=' . $topic . '&amp;sread_id=' . $sread_id . '&amp;number=' . $u . '-' . $mae . '">前100</a>&nbsp;');
        }
    }
    $xoopsTpl->assign('next100', '<a href="viewsread.php?topic=' . $topic . '&amp;sread_id=' . $sread_id . '&amp;number=' . $s . '-' . $t . '">次100</a>&nbsp;');

    //１さんの抽出
    $sql = 'SELECT b.*, u.uname, u.user_avatar, u.user_sig, u.posts, u.rank FROM ' . $xoopsDB->prefix('bluesbb') . ' b LEFT JOIN ' . $xoopsDB->prefix('users') . ' u ON u.uid = b.uid WHERE b.sread_id = ' . $sread_id . ' AND b.res_id = 0';
    if (!$result = $xoopsDB->query($sql)) {
        redirect_header('index.php', 2, _MD_ERRORCONNECT);

        exit();
    }
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

        $myrow['title'] = htmlspecialchars($myrow['title'], ENT_QUOTES | ENT_HTML5);

        $myrow['url'] = htmlspecialchars($myrow['url'], ENT_QUOTES | ENT_HTML5);

        $myrow['mail'] = htmlspecialchars($myrow['mail'], ENT_QUOTES | ENT_HTML5);

        $myrow['message'] = gt_link($myts->displayTarea($myrow['message'], 0, 1, 1), $topic, $myrow['sread_id']);

        if ($xoopsUser) {
            if ($xoopsUser->isAdmin($xoopsModule->mid())) {
                $myrow['message'] .= '<br><br><font color="#4169E1">' . htmlspecialchars($myrow['poster_ip'], ENT_QUOTES | ENT_HTML5) . '&nbsp;' . htmlspecialchars($myrow['poster_host'], ENT_QUOTES | ENT_HTML5) . '<br>' . htmlspecialchars($myrow['poster_agent'], ENT_QUOTES | ENT_HTML5) . '</font>';
            }
        }

        $myrow['post_time'] = formatTimestamp($myrow['post_time']);

        $top_array[] = $myrow;    //１さんを配列に格納
    }
    $GLOBALS['xoopsDB']->freeRecordSet($result);

    //レスの抽出
    $res_array = [];
    $sql2 = 'SELECT b.*, u.uname, u.user_avatar, u.user_sig, u.posts, u.rank FROM ' . $xoopsDB->prefix('bluesbb') . ' b LEFT JOIN ' . $xoopsDB->prefix('users') . ' u ON u.uid = b.uid WHERE b.sread_id = ' . $sread_id . ' AND ' . $where;
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

        $myrow2['title'] = htmlspecialchars($myrow2['title'], ENT_QUOTES | ENT_HTML5);

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
