<?php

// Author: Sting_Band
// URL: http://www.bluish.jp/

require dirname(__DIR__, 2) . '/mainfile.php';
require __DIR__ . '/header.php';
$thread = isset($_GET['thr']) ? (int)$_GET['thr'] : 0;
if (empty($thread)) {
    redirect_header(BLUESBB_URL . '/', 2, _MD_ERRORTHREAD);

    exit();
}
$sql = 'SELECT topic_id FROM ' . $xoopsDB->prefix('bluesbb') . ' WHERE thread_id = ' . $thread . ' AND res_id = 0';
if (!$result = $xoopsDB->query($sql)) {
    redirect_header(BLUESBB_URL . '/', 3, 'Error!! ' . basename(__FILE__) . ' Line ' . __LINE__);

    exit();
}
if (!$topic_id = $xoopsDB->fetchArray($result)) {
    redirect_header(BLUESBB_URL . '/', 2, _MD_ERROREXIST);

    exit();
}
$topic = '';
$topic = $topic_id['topic_id'];
$sql = 'SELECT * FROM ' . $xoopsDB->prefix('bluesbb_topic') . ' WHERE topic_id = ' . $topic;
if (!$result = $xoopsDB->query($sql)) {
    redirect_header(BLUESBB_URL . '/', 3, 'Error!! ' . basename(__FILE__) . ' Line ' . __LINE__);

    exit();
}
if (!$topicdata = $xoopsDB->fetchArray($result)) {
    redirect_header(BLUESBB_URL . '/', 2, _MD_ERROREXIST);

    exit();
}
$accesserror = 0;
if ('3' == $topicdata['topic_access'] || '4' == $topicdata['topic_access']) {
    if (!is_object($xoopsUser)) {
        $accesserror = 1;
    }
}
if ('6' == $topicdata['topic_access']) {
    $accesserror = 1;

    if (is_object($xoopsUser)) {
        $groups = $memberHandler->getGroupsByUser($xoopsUser->getVar('uid'), true);

        foreach ($groups as $group) {
            if (preg_match("/\b" . $topicdata['topic_group'] . "\b/", $group->getVar('groupid'))) {
                $accesserror = 0;
            }
        }

        if ($xoopsUser->isAdmin($xoopsModule->mid())) {
            $accesserror = 0;
        }
    }
}
if (1 == $accesserror) {
    redirect_header(BLUESBB_URL . '/', 3, _MD_TOPICACCESSERROR);

    exit();
}
$style = isset($_GET['sty']) ? (int)$_GET['sty'] : 0;
if ('1' != $style && '2' != $style && '3' != $style) {
    $style = $topicdata['topic_style'];
}

$GLOBALS['xoopsOption']['template_main'] = 'bluesbb_thread.html';
require XOOPS_ROOT_PATH . '/header.php';
$myts = MyTextSanitizer::getInstance();
$now_time = time();
switch ($style) {
    case '1':    //スレッド表示処理スタート
        $number = empty($_GET['num']) ? 'l50' : $_GET['num'];
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
        //ナビゲーション生成
        $sql = 'SELECT COUNT(*) FROM ' . $xoopsDB->prefix('bluesbb') . ' WHERE thread_id = ' . $thread;
        if (!$result = $xoopsDB->query($sql)) {
            redirect_header(BLUESBB_URL . '/', 3, 'Error!! ' . basename(__FILE__) . ' Line ' . __LINE__);

            exit();
        }
        [$post_count] = $xoopsDB->fetchRow($result);
        for ($iCnt = 1; $iCnt <= $post_count; $iCnt += 100) {
            $iTo = $iCnt + 99;

            $pnavi = "<a href='" . BLUESBB_URL . '/thread.php?thr=' . $thread . '&amp;sty=' . $style . '&amp;num=' . $iCnt . '-' . $iTo . "'>" . $iCnt . '-</a>&nbsp;';

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
            $u = '';

            if (2 == $mae) {
                $xoopsTpl->assign('last100', "<a href='" . BLUESBB_URL . '/thread.php?thr=' . $thread . '&amp;sty=' . $style . "&amp;num=1'>" . _MD_BACK100 . '</a>&nbsp;');
            } else {
                if ($mae > 101) {
                    $u = $mae - 99;
                }

                $xoopsTpl->assign('last100', "<a href='" . BLUESBB_URL . '/thread.php?thr=' . $thread . '&amp;sty=' . $style . '&amp;num=' . $u . '-' . $mae . "'>" . _MD_BACK100 . '</a>&nbsp;');
            }
        }
        $xoopsTpl->assign('next100', "<a href='" . BLUESBB_URL . '/thread.php?thr=' . $thread . '&amp;sty=' . $style . '&amp;num=' . $s . '-' . $t . "'>" . _MD_NEXT100 . '</a>&nbsp;');
        //１さんの抽出
        $sql = 'SELECT b.*, u.uname, u.user_avatar, u.user_sig, u.posts, u.rank FROM ' . $xoopsDB->prefix('bluesbb') . ' b LEFT JOIN ' . $xoopsDB->prefix('users') . ' u ON u.uid = b.uid WHERE b.thread_id = ' . $thread . ' AND b.res_id = 0';
        if (!$result = $xoopsDB->query($sql)) {
            redirect_header(BLUESBB_URL . '/', 3, 'Error!! ' . basename(__FILE__) . ' Line ' . __LINE__);

            exit();
        }
        $top_array = [];
        while (false !== ($myrow = $xoopsDB->fetchArray($result))) {
            if ($myrow['uid'] > 0) {
                $myrow['uname'] = "<a href='" . XOOPS_URL . '/userinfo.php?uid=' . $myrow['uid'] . "'>" . $myrow['uname'] . '</a>';

                $rank = xoops_getrank($myrow['rank'], $myrow['posts']);

                if (empty($rank['image'])) {
                    $rank['image'] = 'blank.gif';
                }

                $myrow['rank'] = $rank['title'] . "<br><img src='" . XOOPS_URL . '/uploads/' . $rank['image'] . "' alt=''>";

                $myrow['user_avatar'] = "<img class='comUserImg' src='" . XOOPS_URL . '/uploads/' . $myrow['user_avatar'] . "' alt=''>";

                if (1 == $topicdata['allow_sig'] && 1 == $myrow['attachsig']) {
                    $myrow['user_sig'] = "<br><br><hr size='1'>" . $myts->displayTarea($myrow['user_sig'], 0, 1, 1, 1, 1);
                } else {
                    $myrow['user_sig'] = '';
                }
            } else {
                $myrow['uname'] = "<a href='" . XOOPS_URL . "/register.php'>" . $xoopsConfig['anonymous'] . '</a>';
            }

            ++$myrow['res_id'];

            $myrow['name'] = htmlspecialchars($myrow['name'], ENT_QUOTES | ENT_HTML5);

            $myrow['title'] = htmlspecialchars($myrow['title'], ENT_QUOTES | ENT_HTML5);

            $myrow['url'] = str_ireplace("javascript", '', preg_replace('/[\x00-\x20\x22\x27]/', '', $myrow['url']));

            $myrow['url'] = htmlspecialchars($myrow['url'], ENT_QUOTES | ENT_HTML5);

            $myrow['mail'] = htmlspecialchars($myrow['mail'], ENT_QUOTES | ENT_HTML5);

            $myrow['message'] = gt_link($myts->displayTarea($myrow['message'], 0, 1, 1, 1, 1), $topic, $myrow['thread_id'], BLUESBB_URL, $style);

            if (is_object($xoopsUser) && $xoopsUser->isAdmin($xoopsModule->mid())) {
                $myrow['message'] .= "<br><br><font color='#4169E1'>" . htmlspecialchars($myrow['poster_ip'], ENT_QUOTES | ENT_HTML5) . '&nbsp;' . htmlspecialchars($myrow['poster_host'], ENT_QUOTES | ENT_HTML5) . '<br>' . htmlspecialchars($myrow['poster_agent'], ENT_QUOTES | ENT_HTML5) . '</font>';
            }

            if (($now_time - $myrow['post_time']) <= 60 * 60 * $xoopsModuleConfig['newtime']) {
                $myrow['post_time'] = formatTimestamp($myrow['post_time'], 'm') . "&nbsp;<font color='#FF0000'>New</font>";
            } else {
                $myrow['post_time'] = formatTimestamp($myrow['post_time'], 'm');
            }

            $xoopsTpl->assign('xoops_pagetitle', htmlspecialchars($xoopsModule->name(), ENT_QUOTES | ENT_HTML5) . '-&gt;' . htmlspecialchars($topicdata['topic_name'], ENT_QUOTES | ENT_HTML5) . '-&gt;' . $myrow['title']);

            $top_array[] = $myrow;    //１さんを配列に格納
        }
        $GLOBALS['xoopsDB']->freeRecordSet($result);
        //レスの抽出
        $res_array = [];
        $sql2 = 'SELECT b.*, u.uname, u.user_avatar, u.user_sig, u.posts, u.rank FROM ' . $xoopsDB->prefix('bluesbb') . ' b LEFT JOIN ' . $xoopsDB->prefix('users') . ' u ON u.uid = b.uid WHERE b.thread_id = ' . $thread . ' AND ' . $where;
        if (!$result2 = $xoopsDB->query($sql2)) {
            redirect_header(BLUESBB_URL . '/', 3, 'Error!! ' . basename(__FILE__) . ' Line ' . __LINE__);

            exit();
        }
        while (false !== ($myrow2 = $xoopsDB->fetchArray($result2))) {
            if ($myrow2['uid'] > 0) {
                $myrow2['uname'] = "<a href='" . XOOPS_URL . '/userinfo.php?uid=' . $myrow2['uid'] . "'>" . $myrow2['uname'] . '</a>';

                $rank = xoops_getrank($myrow2['rank'], $myrow2['posts']);

                if (empty($rank['image'])) {
                    $rank['image'] = 'blank.gif';
                }

                $myrow2['rank'] = $rank['title'] . "<br><img src='" . XOOPS_URL . '/uploads/' . $rank['image'] . "' alt=''>";

                $myrow2['user_avatar'] = "<img class='comUserImg' src='" . XOOPS_URL . '/uploads/' . $myrow2['user_avatar'] . "' alt=''>";

                if (1 == $topicdata['allow_sig'] && 1 == $myrow2['attachsig']) {
                    $myrow2['user_sig'] = "<br><br><hr size='1'>" . $myts->displayTarea($myrow2['user_sig'], 0, 1, 1, 1, 1);
                } else {
                    $myrow2['user_sig'] = '';
                }
            } else {
                $myrow2['uname'] = "<a href='" . XOOPS_URL . "/register.php'>" . $xoopsConfig['anonymous'] . '</a>';
            }

            ++$myrow2['res_id'];

            $myrow2['name'] = htmlspecialchars($myrow2['name'], ENT_QUOTES | ENT_HTML5);

            $myrow2['title'] = htmlspecialchars($myrow2['title'], ENT_QUOTES | ENT_HTML5);

            $myrow2['url'] = str_ireplace("javascript", '', preg_replace('/[\x00-\x20\x22\x27]/', '', $myrow2['url']));

            $myrow2['url'] = htmlspecialchars($myrow2['url'], ENT_QUOTES | ENT_HTML5);

            $myrow2['mail'] = htmlspecialchars($myrow2['mail'], ENT_QUOTES | ENT_HTML5);

            $myrow2['message'] = gt_link($myts->displayTarea($myrow2['message'], 0, 1, 1, 1, 1), $topic, $myrow2['thread_id'], BLUESBB_URL, $style);

            if (is_object($xoopsUser) && $xoopsUser->isAdmin($xoopsModule->mid())) {
                $myrow2['message'] .= "<br><br><font color='#4169E1'>" . htmlspecialchars($myrow2['poster_ip'], ENT_QUOTES | ENT_HTML5)
                                      . '&nbsp;' . htmlspecialchars($myrow2['poster_host'], ENT_QUOTES | ENT_HTML5)
                                      . '<br>' . htmlspecialchars($myrow2['poster_agent'], ENT_QUOTES | ENT_HTML5)
                                      . '</font>';
            }

            if (($now_time - $myrow2['post_time']) <= 60 * 60 * $xoopsModuleConfig['newtime']) {
                $myrow2['post_time'] = formatTimestamp($myrow2['post_time'], 'm') . "&nbsp;<font color='#FF0000'>New</font>";
            } else {
                $myrow2['post_time'] = formatTimestamp($myrow2['post_time'], 'm');
            }

            $res_array[] = $myrow2;    //レスを配列に格納
        }
        $GLOBALS['xoopsDB']->freeRecordSet($result2);
        //レス配列をひっくり返す
        $res_array = array_reverse($res_array);
        //1さんとレスを結合
        $data_array = [];
        foreach ($top_array as $top) {
            $sre = $top['thread_id'];

            $data_array[(string)$sre][] = $top;

            foreach ($res_array as $res) {
                if ($sre == $res['thread_id']) {
                    $data_array[(string)$sre][] = $res;
                }
            }
        }
        break;
    case '2':    //ツリー表示処理スタート
        $number = $_GET['num'] ?? 1;
        if ('t' == mb_substr($number, 0, 1)) {
            $st = (int)mb_substr($number, 1);

            $tree_single = 'off';
        } else {
            $st = (int)$number;

            $tree_single = 'on';
        }
        $data_array = [];
        $flat_array = [];
        $tree_array = [];
        $sql1 = 'SELECT b.*, u.uname, u.user_avatar, u.user_sig, u.posts, u.rank FROM ' . $xoopsDB->prefix('bluesbb') . ' b LEFT JOIN ' . $xoopsDB->prefix('users') . ' u ON u.uid = b.uid WHERE thread_id = ' . $thread . ' ORDER BY res_id';
        if (!$result1 = $xoopsDB->query($sql1)) {
            redirect_header(BLUESBB_URL . '/', 3, 'Error!! ' . basename(__FILE__) . ' Line ' . __LINE__);

            exit();
        }
        while (false !== ($myrow1 = $xoopsDB->fetchArray($result1))) {
            if ($myrow1['uid'] > 0) {
                $myrow1['uname'] = "<a href='" . XOOPS_URL . '/userinfo.php?uid=' . $myrow1['uid'] . "'>" . $myrow1['uname'] . '</a>';

                $rank = xoops_getrank($myrow1['rank'], $myrow1['posts']);

                if (empty($rank['image'])) {
                    $rank['image'] = 'blank.gif';
                }

                $myrow1['rank'] = $rank['title'] . "<br><img src='" . XOOPS_URL . '/uploads/' . $rank['image'] . "' alt=''>";

                $myrow1['user_avatar'] = "<img class='comUserImg' src='" . XOOPS_URL . '/uploads/' . $myrow1['user_avatar'] . "' alt=''>";

                if (1 == $topicdata['allow_sig'] && 1 == $myrow1['attachsig']) {
                    $myrow1['user_sig'] = "<br><br><hr size='1'>" . $myts->displayTarea($myrow1['user_sig'], 0, 1, 1, 1, 1);
                } else {
                    $myrow1['user_sig'] = '';
                }
            } else {
                $myrow1['uname'] = "<a href='" . XOOPS_URL . "/register.php'>" . $xoopsConfig['anonymous'] . '</a>';
            }

            $myrow1['name2'] = htmlspecialchars(xoops_substr($myrow1['name'], 0, 20), ENT_QUOTES | ENT_HTML5);

            $myrow1['title2'] = htmlspecialchars(xoops_substr($myrow1['title'], 0, 40), ENT_QUOTES | ENT_HTML5);

            $myrow1['name'] = htmlspecialchars($myrow1['name'], ENT_QUOTES | ENT_HTML5);

            $myrow1['title'] = htmlspecialchars($myrow1['title'], ENT_QUOTES | ENT_HTML5);

            $myrow1['url'] = str_ireplace("javascript", '', preg_replace('/[\x00-\x20\x22\x27]/', '', $myrow1['url']));

            $myrow1['url'] = htmlspecialchars($myrow1['url'], ENT_QUOTES | ENT_HTML5);

            $myrow1['mail'] = htmlspecialchars($myrow1['mail'], ENT_QUOTES | ENT_HTML5);

            $myrow1['message'] = gt_link($myts->displayTarea($myrow1['message'], 0, 1, 1, 1, 1), $topic, $myrow1['thread_id'], BLUESBB_URL, $style);

            if (is_object($xoopsUser) && $xoopsUser->isAdmin($xoopsModule->mid())) {
                $myrow1['message'] .= "<br><br><font color='#4169E1'>" . htmlspecialchars($myrow1['poster_ip'], ENT_QUOTES | ENT_HTML5)
                                      . '&nbsp;' . htmlspecialchars($myrow1['poster_host'], ENT_QUOTES | ENT_HTML5)
                                      . '<br>' . htmlspecialchars($myrow1['poster_agent'], ENT_QUOTES | ENT_HTML5)
                                      . '</font>';
            }

            if (($now_time - $myrow1['post_time']) <= 60 * 60 * $xoopsModuleConfig['newtime']) {
                $myrow1['post_time'] = formatTimestamp($myrow1['post_time'], 'm') . "&nbsp;<font color='#FF0000'>New</font>";
            } else {
                $myrow1['post_time'] = formatTimestamp($myrow1['post_time'], 'm');
            }

            $num = [];

            $key = '';

            $val = [];

            $post_title = '';

            $hig_id = $myrow1['hig_id'];

            $res_id = $myrow1['res_id'];

            $myrow1['tree_display'] = 'off';

            ++$myrow1['res_id'];

            if ($myrow1['res_id'] == $st) {
                $myrow1['tree_display'] = 'on';
            }

            $i = 1;

            while ($hig_id > 0) {
                if ($myrow1['res_id'] == $st || ($hig_id == $st && 'off' == $tree_single)) {
                    $myrow1['tree_display'] = 'on';
                }

                $num[] = $res_id;

                $sql3 = 'SELECT COUNT(*) AS count, MAX(res_id) AS max FROM ' . $xoopsDB->prefix('bluesbb') . ' WHERE thread_id = ' . $thread . ' AND hig_id = ' . $hig_id;

                $result3 = $xoopsDB->query($sql3);

                [$count, $max] = $xoopsDB->fetchRow($result3);

                if ($count > 1 && 1 == $i && $res_id != $max) {
                    $val[] = "<a href='" . BLUESBB_URL . '/thread.php?thr=' . $thread . '&amp;sty=' . $style . '&amp;num=t' . $myrow1['res_id'] . "' title='" . _MD_TREETORL . "'><img src='" . BLUESBB_URL . "/images/t.gif' alt='' height='20' width='20' align='top' border='0'></a>";
                } elseif (($count <= 1 && 1 == $i) || ($count > 1 && 1 == $i && $res_id == $max)) {
                    $val[] = "<a href='" . BLUESBB_URL . '/thread.php?thr=' . $thread . '&amp;sty=' . $style . '&amp;num=t' . $myrow1['res_id'] . "' title='" . _MD_TREETORL . "'><img src='" . BLUESBB_URL . "/images/l.gif' alt='' height='20' width='20' align='top' border='0'></a>";
                } elseif ($count > 1 && $i > 1 && $res_id != $max) {
                    $val[] = "<img src='" . BLUESBB_URL . "/images/i.gif' alt='' height='20' width='20' align='top' border='0'>";
                } else {
                    $val[] = "<img src='" . BLUESBB_URL . "/images/empty.gif' alt='' height='20' width='20' align='top' border='0'>";
                }

                --$hig_id;

                $res_id = $hig_id;

                $sql4 = 'SELECT hig_id FROM ' . $xoopsDB->prefix('bluesbb') . ' WHERE thread_id = ' . $thread . ' AND res_id = ' . $hig_id;

                $result4 = $xoopsDB->query($sql4);

                [$hig_id] = $xoopsDB->fetchRow($result4);

                $i++;
            }

            $num = array_reverse($num);

            foreach ($num as $n) {
                while (mb_strlen($n) < 8) {
                    $n = '0' . $n;
                }

                $key .= $n;
            }

            if ('1' != $myrow1['res_id']) {
                $val[] = "<img src='" . BLUESBB_URL . "/images/empty.gif' alt='' height='20' width='20' align='top' border='0'>";
            }

            $val = array_reverse($val);

            foreach ($val as $v) {
                $post_title .= $v;
            }

            $myrow1['oyapost'] = $post_title;

            $tree_array[(string)$key] = $myrow1;

            $flat_array[] = $myrow1;
        }
        ksort($tree_array, SORT_STRING);
        foreach ($tree_array as $tree) {
            $data_array[] = $tree;
        }
        foreach ($flat_array as $flat) {
            $xoopsTpl->append('flats', $flat);
        }
        $xoopsTpl->assign('st', $st);
        $xoopsTpl->assign('tree_single', $tree_single);
        $xoopsTpl->assign('treetop', _MD_TREETOP);
        $xoopsTpl->assign('nowhere', _MD_NOWHERE);
        break;
    case '3':    //投稿順表示処理スタート
        $number = isset($_GET['num']) ? (int)$_GET['num'] : 0;
        if (empty($number)) {
            redirect_header(BLUESBB_URL . '/', 2, _MD_ERRORTOPIC);

            exit();
        }
        $sql = 'SELECT b.*, u.uname, u.user_avatar, u.user_sig, u.posts, u.rank FROM ' . $xoopsDB->prefix('bluesbb') . ' b LEFT JOIN ' . $xoopsDB->prefix('users') . ' u ON u.uid = b.uid WHERE b.post_id = ' . $number;
        if (!$result = $xoopsDB->query($sql)) {
            redirect_header(BLUESBB_URL . '/', 3, 'Error!! ' . basename(__FILE__) . ' Line ' . __LINE__);

            exit();
        }
        $data_array = [];
        while (false !== ($myrow = $xoopsDB->fetchArray($result))) {
            if ($myrow['uid'] > 0) {
                $myrow['uname'] = "<a href='" . XOOPS_URL . '/userinfo.php?uid=' . $myrow['uid'] . "'>" . $myrow['uname'] . '</a>';

                $rank = xoops_getrank($myrow['rank'], $myrow['posts']);

                if (empty($rank['image'])) {
                    $rank['image'] = 'blank.gif';
                }

                $myrow['rank'] = $rank['title'] . "<br><img src='" . XOOPS_URL . '/uploads/' . $rank['image'] . "' alt=''>";

                $myrow['user_avatar'] = "<img class='comUserImg' src='" . XOOPS_URL . '/uploads/' . $myrow['user_avatar'] . "' alt=''>";

                if (1 == $topicdata['allow_sig'] && 1 == $myrow['attachsig']) {
                    $myrow['user_sig'] = "<br><br><hr size='1'>" . $myts->displayTarea($myrow['user_sig'], 0, 1, 1, 1, 1);
                } else {
                    $myrow['user_sig'] = '';
                }
            } else {
                $myrow['uname'] = "<a href='" . XOOPS_URL . "/register.php'>" . $xoopsConfig['anonymous'] . '</a>';
            }

            ++$myrow['res_id'];

            $myrow['name'] = htmlspecialchars($myrow['name'], ENT_QUOTES | ENT_HTML5);

            $myrow['title'] = htmlspecialchars($myrow['title'], ENT_QUOTES | ENT_HTML5);

            $myrow['url'] = str_ireplace("javascript", '', preg_replace('/[\x00-\x20\x22\x27]/', '', $myrow['url']));

            $myrow['url'] = htmlspecialchars($myrow['url'], ENT_QUOTES | ENT_HTML5);

            $myrow['mail'] = htmlspecialchars($myrow['mail'], ENT_QUOTES | ENT_HTML5);

            $myrow['message'] = gt_link($myts->displayTarea($myrow['message'], 0, 1, 1, 1, 1), $topic, $myrow['thread_id'], BLUESBB_URL, $style);

            if (is_object($xoopsUser) && $xoopsUser->isAdmin($xoopsModule->mid())) {
                $myrow['message'] .= "<br><br><font color='#4169E1'>" . htmlspecialchars($myrow['poster_ip'], ENT_QUOTES | ENT_HTML5) . '&nbsp;' . htmlspecialchars($myrow['poster_host'], ENT_QUOTES | ENT_HTML5) . '<br>' . htmlspecialchars($myrow['poster_agent'], ENT_QUOTES | ENT_HTML5) . '</font>';
            }

            if (($now_time - $myrow['post_time']) <= 60 * 60 * $xoopsModuleConfig['newtime']) {
                $myrow['post_time'] = formatTimestamp($myrow['post_time'], 'm') . "&nbsp;<font color='#FF0000'>New</font>";
            } else {
                $myrow['post_time'] = formatTimestamp($myrow['post_time'], 'm');
            }

            $xoopsTpl->assign('xoops_pagetitle', htmlspecialchars($xoopsModule->name(), ENT_QUOTES | ENT_HTML5) . '-&gt;' . htmlspecialchars($topicdata['topic_name'], ENT_QUOTES | ENT_HTML5) . '-&gt;' . $myrow['title']);

            $data_array[] = $myrow;
        }
        $GLOBALS['xoopsDB']->freeRecordSet($result);
        break;
}
//テンプレートへの書き出し
foreach ($data_array as $value) {
    $xoopsTpl->append('threads', $value);
}

$xoopsTpl->assign('bluesbb_url', BLUESBB_URL);
$xoopsTpl->assign('topic_id', $topic);
$xoopsTpl->assign('topic_style', $style);
$xoopsTpl->assign('thread_id', $thread);
$xoopsTpl->assign('topicback', _MD_TOPICBACK);
$xoopsTpl->assign('allthreadview', _MD_ALLTHREADVIEW);
$xoopsTpl->assign('new50', _MD_NEW50);
$xoopsTpl->assign('site', _MD_SITE);
$xoopsTpl->assign('mail', _MD_MAIL);
$xoopsTpl->assign('replies', _MD_REPLIES);
$xoopsTpl->assign('edit', _MD_EDIT);
$xoopsTpl->assign('copyright', BLUESBB_COPYRIGHT);

require XOOPS_ROOT_PATH . '/footer.php';
