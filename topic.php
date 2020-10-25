<?php

// Author: Sting_Band
// URL: http://www.bluish.jp/

require dirname(__DIR__, 2) . '/mainfile.php';
require __DIR__ . '/header.php';
$topic = isset($_GET['top']) ? (int)$_GET['top'] : 0;
$start = !empty($_GET['sta']) ? (int)$_GET['sta'] : 0;
if (empty($topic)) {
    redirect_header(BLUESBB_URL . '/', 2, _MD_ERRORTOPIC);

    exit();
}
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
    //	$cookie="bluesbb_".$topic."_set";

    //	if(isset($_COOKIE["$cookie"])){

    //		$coo = array();

    //		$coo = explode ( ",", $_COOKIE["$cookie"] );

    //		$style = $coo[0];

    //	}else{

    $style = $topicdata['topic_style'];

    //	}
}

$GLOBALS['xoopsOption']['template_main'] = 'bluesbb_topic.html';
require XOOPS_ROOT_PATH . '/header.php';
$myts = MyTextSanitizer::getInstance();
$now_time = time();
//リストディスプレイの抽出
if ('1' == $topicdata['list_display']) {
    $sql = 'SELECT thread_id, title FROM ' . $xoopsDB->prefix('bluesbb') . ' WHERE topic_id = ' . $topic . ' AND res_id = 0 ORDER BY res_time DESC LIMIT 0, ' . $xoopsModuleConfig['topicperth'];

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

        $xoopsTpl->append('thread_name_list', ['srn' => $srn, 'thread_id' => $myrow['thread_id'], 'title' => htmlspecialchars($myrow['title'], ENT_QUOTES | ENT_HTML5), 'count' => $count, 'post_id' => $max1, 'res_id' => ($max2 + 1)]);

        $srn++;
    }

    $GLOBALS['xoopsDB']->freeRecordSet($result);

    $xoopsTpl->assign('list_display', 'on');
} else {
    $xoopsTpl->assign('list_display', 'off');
}

$choice_array = [];
$choice_array = explode(':', $topicdata['style_choice']);
foreach ($choice_array as $ca) {
    switch ($ca) {
        case '1':
            $xoopsTpl->append('style_choice', "<a href='" . BLUESBB_URL . '/topic.php?top=' . $topic . '&amp;sta=' . $start . "&amp;sty=1' title=''>" . _MD_THREAD . '</a>&nbsp;|');
            break;
        case '2':
            $xoopsTpl->append('style_choice', "<a href='" . BLUESBB_URL . '/topic.php?top=' . $topic . '&amp;sta=' . $start . "&amp;sty=2' title=''>" . _MD_TREE . '</a>&nbsp;|');
            break;
        case '3':
            $xoopsTpl->append('style_choice', "<a href='" . BLUESBB_URL . '/topic.php?top=' . $topic . '&amp;sta=' . $start . "&amp;sty=3' title=''>" . _MD_ORDER . '</a>&nbsp;|');
            break;
    }
}

$per_page = '';
$resid0 = '';
switch ($style) {
    case '1':    //スレッド表示処理スタート
        //１さんの抽出
        $sql = 'SELECT b.*, u.uname, u.user_avatar, u.user_sig, u.posts, u.rank FROM '
               . $xoopsDB->prefix('bluesbb')
               . ' b LEFT JOIN '
               . $xoopsDB->prefix('users')
               . ' u ON u.uid = b.uid WHERE b.topic_id = '
               . $topic
               . ' AND b.res_id = 0 ORDER BY b.res_time DESC LIMIT '
               . $start
               . ', '
               . $topicdata['thread_per_page'];
        if (!$result = $xoopsDB->query($sql)) {
            redirect_header(BLUESBB_URL . '/', 3, 'Error!! ' . basename(__FILE__) . ' Line ' . __LINE__);

            exit();
        }
        $where_str = [];
        $top_array = [];
        $thn = 1;
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

            if (1 == $thn) {
                $bthn = $topicdata['thread_per_page'];
            } else {
                $bthn = $thn - 1;
            }

            if ($thn == $topicdata['thread_per_page']) {
                $nthn = 1;
            } else {
                $nthn = $thn + 1;
            }

            $myrow['posts'] = "<a name='" . $thn . "'></a><a href ='#menu'>" . _MD_MENUOFTHREADS . "</a><a href='#" . $bthn . "'>" . _MD_PREVIOUSTOPIC . "</a><a href='#" . $nthn . "'>" . _MD_NEXTTOPIC . '</a>';

            $myrow['name'] = htmlspecialchars($myrow['name'], ENT_QUOTES | ENT_HTML5);

            if (($now_time - $myrow['post_time']) <= 60 * 60 * $xoopsModuleConfig['newtime']) {
                $myrow['title'] = htmlspecialchars($myrow['title'], ENT_QUOTES | ENT_HTML5) . "&nbsp;<font color='#FF0000'>New</font>";
            } else {
                $myrow['title'] = htmlspecialchars($myrow['title'], ENT_QUOTES | ENT_HTML5);
            }

            $myrow['url'] = str_ireplace("javascript", '', preg_replace('/[\x00-\x20\x22\x27]/', '', $myrow['url']));

            $myrow['url'] = htmlspecialchars($myrow['url'], ENT_QUOTES | ENT_HTML5);

            $myrow['mail'] = htmlspecialchars($myrow['mail'], ENT_QUOTES | ENT_HTML5);

            $myrow['message'] = gt_link($myts->displayTarea($myrow['message'], 0, 1, 1, 1, 1), $topic, $myrow['thread_id'], BLUESBB_URL, $style);

            if (is_object($xoopsUser) && $xoopsUser->isAdmin($xoopsModule->mid())) {
                $myrow['message'] .= "<br><br><font color='#4169E1'>" . htmlspecialchars($myrow['poster_ip'], ENT_QUOTES | ENT_HTML5) . '&nbsp;' . htmlspecialchars($myrow['poster_host'], ENT_QUOTES | ENT_HTML5) . '<br>' . htmlspecialchars($myrow['poster_agent'], ENT_QUOTES | ENT_HTML5) . '</font>';
            }

            $myrow['post_time'] = formatTimestamp($myrow['post_time']);

            $where_str[] = $myrow['thread_id'];

            $top_array[] = $myrow;    //１さんを配列に格納

            $thn++;
        }
        $GLOBALS['xoopsDB']->freeRecordSet($result);
        //レスの抽出
        $res_array = [];
        foreach ($where_str as $ws) {
            $sql2 = 'SELECT b.*, u.uname, u.user_avatar, u.user_sig, u.posts, u.rank FROM '
                    . $xoopsDB->prefix('bluesbb')
                    . ' b LEFT JOIN '
                    . $xoopsDB->prefix('users')
                    . ' u ON u.uid = b.uid WHERE b.thread_id = '
                    . $ws
                    . ' AND b.res_id > 0 ORDER BY b.res_time DESC LIMIT 0, '
                    . $topicdata['res_per_thread'];

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

                if (($now_time - $myrow2['post_time']) <= 60 * 60 * $xoopsModuleConfig['newtime']) {
                    $myrow2['title'] = htmlspecialchars($myrow2['title'], ENT_QUOTES | ENT_HTML5) . "&nbsp;<font color='#FF0000'>New</font>";
                } else {
                    $myrow2['title'] = htmlspecialchars($myrow2['title'], ENT_QUOTES | ENT_HTML5);
                }

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
            $sre = $top['thread_id'];

            $data_array[(string)$sre][] = $top;

            foreach ($res_array as $res) {
                if ($sre == $res['thread_id']) {
                    $data_array[(string)$sre][] = $res;
                }
            }
        }
        $per_page = $topicdata['thread_per_page'];
        $resid0 = ' AND res_id = 0';
        break;
    case '2':    //ツリー表示処理スタート
        $sql = 'SELECT thread_id FROM ' . $xoopsDB->prefix('bluesbb') . ' WHERE topic_id = ' . $topic . ' AND res_id = 0 ORDER BY res_time DESC LIMIT ' . $start . ', ' . $topicdata['tree_per_page'];
        if (!$result = $xoopsDB->query($sql)) {
            redirect_header(BLUESBB_URL . '/', 3, 'Error!! ' . basename(__FILE__) . ' Line ' . __LINE__);

            exit();
        }
        $data_array = [];
        while (false !== ($myrow = $xoopsDB->fetchArray($result))) {
            $sql1 = 'SELECT * FROM ' . $xoopsDB->prefix('bluesbb') . ' WHERE thread_id = ' . $myrow['thread_id'];

            if (!$result1 = $xoopsDB->query($sql1)) {
                redirect_header(BLUESBB_URL . '/', 3, 'Error!! ' . basename(__FILE__) . ' Line ' . __LINE__);

                exit();
            }

            $tree_array = [];

            while (false !== ($myrow1 = $xoopsDB->fetchArray($result1))) {
                $myrow1['name'] = htmlspecialchars(xoops_substr($myrow1['name'], 0, 20), ENT_QUOTES | ENT_HTML5);

                $myrow1['title'] = htmlspecialchars(xoops_substr($myrow1['title'], 0, 40), ENT_QUOTES | ENT_HTML5);

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

                ++$myrow1['res_id'];

                $i = 1;

                while ($hig_id > 0) {
                    $num[] = $res_id;

                    $sql3 = 'SELECT COUNT(*) AS count, MAX(res_id) AS max FROM ' . $xoopsDB->prefix('bluesbb') . ' WHERE thread_id = ' . $myrow1['thread_id'] . ' AND hig_id = ' . $hig_id;

                    $result3 = $xoopsDB->query($sql3);

                    [$count, $max] = $xoopsDB->fetchRow($result3);

                    if ($count > 1 && 1 == $i && $res_id != $max) {
                        $val[] = "<a href='" . BLUESBB_URL . '/thread.php?thr=' . $myrow1['thread_id'] . '&amp;sty=' . $style . '&amp;num=t' . $myrow1['res_id'] . "' title='" . _MD_TREETORL . "'><img src='" . BLUESBB_URL . "/images/t.gif' alt='' height='20' width='20' align='top' border='0'></a>";
                    } elseif (($count <= 1 && 1 == $i) || ($count > 1 && 1 == $i && $res_id == $max)) {
                        $val[] = "<a href='" . BLUESBB_URL . '/thread.php?thr=' . $myrow1['thread_id'] . '&amp;sty=' . $style . '&amp;num=t' . $myrow1['res_id'] . "' title='" . _MD_TREETORL . "'><img src='" . BLUESBB_URL . "/images/l.gif' alt='' height='20' width='20' align='top' border='0'></a>";
                    } elseif ($count > 1 && $i > 1 && $res_id != $max) {
                        $val[] = "<img src='" . BLUESBB_URL . "/images/i.gif' alt='' height='20' width='20' align='top' border='0'>";
                    } else {
                        $val[] = "<img src='" . BLUESBB_URL . "/images/empty.gif' alt='' height='20' width='20' align='top' border='0'>";
                    }

                    --$hig_id;

                    $res_id = $hig_id;

                    $sql4 = 'SELECT hig_id FROM ' . $xoopsDB->prefix('bluesbb') . ' WHERE thread_id = ' . $myrow1['thread_id'] . ' AND res_id = ' . $hig_id;

                    $result4 = $xoopsDB->query($sql4);

                    [$hig_id] = $xoopsDB->fetchRow($result4);

                    $i++;
                }

                $num = array_reverse($num);

                foreach ($num as $n) {
                    while (mb_strlen($n) < 3) {
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
            }

            ksort($tree_array, SORT_STRING);

            foreach ($tree_array as $tree) {
                $data_array[] = $tree;
            }
        }
        $per_page = $topicdata['tree_per_page'];
        $resid0 = ' AND res_id = 0';
        $xoopsTpl->assign('treetop', _MD_TREETOP);
        break;
    case '3':    //投稿順表示処理スタート
        $sql = 'SELECT b.*, u.uname, u.user_avatar, u.user_sig, u.posts, u.rank FROM '
               . $xoopsDB->prefix('bluesbb')
               . ' b LEFT JOIN '
               . $xoopsDB->prefix('users')
               . ' u ON u.uid = b.uid WHERE b.topic_id = '
               . $topic
               . ' ORDER BY b.post_time DESC LIMIT '
               . $start
               . ', '
               . $topicdata['order_per_page'];
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

            if (($now_time - $myrow['post_time']) <= 60 * 60 * $xoopsModuleConfig['newtime']) {
                $myrow['title'] = htmlspecialchars($myrow['title'], ENT_QUOTES | ENT_HTML5) . "&nbsp;<font color='#FF0000'>New</font>";
            } else {
                $myrow['title'] = htmlspecialchars($myrow['title'], ENT_QUOTES | ENT_HTML5);
            }

            $myrow['url'] = str_ireplace("javascript", '', preg_replace('/[\x00-\x20\x22\x27]/', '', $myrow['url']));

            $myrow['url'] = htmlspecialchars($myrow['url'], ENT_QUOTES | ENT_HTML5);

            $myrow['mail'] = htmlspecialchars($myrow['mail'], ENT_QUOTES | ENT_HTML5);

            $myrow['message'] = gt_link($myts->displayTarea($myrow['message'], 0, 1, 1, 1, 1), $topic, $myrow['thread_id'], BLUESBB_URL, $style);

            if (is_object($xoopsUser) && $xoopsUser->isAdmin($xoopsModule->mid())) {
                $myrow['message'] .= "<br><br><font color='#4169E1'>" . htmlspecialchars($myrow['poster_ip'], ENT_QUOTES | ENT_HTML5) . '&nbsp;' . htmlspecialchars($myrow['poster_host'], ENT_QUOTES | ENT_HTML5) . '<br>' . htmlspecialchars($myrow['poster_agent'], ENT_QUOTES | ENT_HTML5) . '</font>';
            }

            $myrow['post_time'] = formatTimestamp($myrow['post_time']);

            $data_array[] = $myrow;    //投稿を配列に格納
        }
        $GLOBALS['xoopsDB']->freeRecordSet($result);
        $per_page = $topicdata['order_per_page'];
        break;
}
//テンプレートへの書き出し
foreach ($data_array as $value) {
    $xoopsTpl->append('threads', $value);
}

//ナビゲーションを生成
$sql = 'SELECT COUNT(*) FROM ' . $xoopsDB->prefix('bluesbb') . ' WHERE topic_id = ' . $topic . '' . $resid0;
if (!$result = $xoopsDB->query($sql)) {
    redirect_header(BLUESBB_URL . '/', 3, 'Error!! ' . basename(__FILE__) . ' Line ' . __LINE__);

    exit();
}
[$all_thread] = $xoopsDB->fetchRow($result);
$GLOBALS['xoopsDB']->freeRecordSet($result);
if ($all_thread > $per_page) {
    require_once XOOPS_ROOT_PATH . '/class/pagenav.php';

    $nav = new XoopsPageNav($all_thread, $per_page, $start, 'sta', 'top=' . $topic . '&amp;sty=' . $style);

    $xoopsTpl->assign('topic_pagenav', $nav->renderNav(4));
} else {
    $xoopsTpl->assign('topic_pagenav', '');
}

$xoopsTpl->assign('bluesbb_url', BLUESBB_URL);
$xoopsTpl->assign('xoops_pagetitle', htmlspecialchars($xoopsModule->name(), ENT_QUOTES | ENT_HTML5) . '-&gt;' . htmlspecialchars($topicdata['topic_name'], ENT_QUOTES | ENT_HTML5));
$xoopsTpl->assign('topic_id', $topic);
$xoopsTpl->assign('topic_style', $style);
$xoopsTpl->assign('topic_index_title', sprintf(_MD_BLUESBBINDEX, htmlspecialchars($xoopsConfig['sitename'], ENT_QUOTES | ENT_HTML5)));
$xoopsTpl->assign('topic_name', htmlspecialchars($topicdata['topic_name'], ENT_QUOTES | ENT_HTML5));
$xoopsTpl->assign('topic_info', $myts->displayTarea($topicdata['topic_info'], 0, 1, 1, 1, 1));
$xoopsTpl->assign('postnew', _MD_POSTNEW);
$xoopsTpl->assign('threadlist', _MD_THREADLIST);
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

require XOOPS_ROOT_PATH . '/footer.php';
