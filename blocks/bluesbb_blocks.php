<?php

// Author: Sting_Band
// URL: http://www.bluish.jp/

//最近の投稿(投稿単位)
function b_bluesbb_new_all($options)
{
    $db = XoopsDatabaseFactory::getDatabaseConnection();

    global $xoopsUser, $memberHandler;

    $myts = MyTextSanitizer::getInstance();

    $block = [];

    $query = 'SELECT b.post_id, b.topic_id, b.thread_id, b.res_id, b.name, b.title, b.post_time, b.uid, t.topic_name, t.topic_style FROM ' . $db->prefix('bluesbb') . ' b LEFT JOIN ' . $db->prefix('bluesbb_topic') . ' t ON t.topic_id = b.topic_id WHERE';

    if (is_object($xoopsUser)) {
        $query .= ' (t.topic_access = 1 OR t.topic_access = 2 OR t.topic_access = 3 OR t.topic_access = 4 OR t.topic_access = 5';

        $groups = $memberHandler->getGroupsByUser($xoopsUser->getVar('uid'), true);

        foreach ($groups as $group) {
            $query .= ' OR t.topic_group = ' . $group->getVar('groupid');
        }

        if ($xoopsUser->isAdmin()) {
            $query .= ' OR t.topic_access = 6';
        }
    } else {
        $query .= ' (t.topic_access = 1 OR t.topic_access = 2 OR t.topic_access = 5';
    }

    if ('0' == $options[2]) {
        $catop = '';
    } elseif ('0' != $options[2] && '0' != $options[3]) {
        $catop = ' AND (t.cat_id = ' . $options[2];
    } else {
        $catop = ' AND t.cat_id = ' . $options[2];
    }

    if ('0' == $options[3]) {
        $topop = '';
    } elseif ('0' != $options[2] && '0' != $options[3]) {
        $topop = ' OR t.topic_id = ' . $options[3] . ')';
    } else {
        $topop = ' AND t.topic_id = ' . $options[3];
    }

    $query .= ')' . $catop . '' . $topop . ' ORDER BY b.post_time DESC';

    if (!$result = $db->query($query, $options[0], 0)) {
        return false;
    }

    $block['lang_topic'] = _MB_BLUESBB_TOPIC;

    $block['lang_thread'] = _MB_BLUESBB_THREAD;

    $block['lang_poster'] = _MB_BLUESBB_POSTER;

    $block['lang_gobb'] = _MB_BLUESBB_GOBB;

    $now_time = time();

    while (false !== ($arr = $db->fetchArray($result))) {
        $thread['post_id'] = $arr['post_id'];

        $thread['topic_id'] = $arr['topic_id'];

        $thread['topic_name'] = htmlspecialchars($arr['topic_name'], ENT_QUOTES | ENT_HTML5);

        $thread['topic_style'] = $arr['topic_style'];

        $thread['thread_id'] = $arr['thread_id'];

        $thread['res_id'] = ++$arr['res_id'];

        $thread['title'] = htmlspecialchars($arr['title'], ENT_QUOTES | ENT_HTML5);

        if (($now_time - $arr['post_time']) <= $options[1] * 60 * 60) {
            $thread['new_mark'] = '&nbsp;<font color="#FF0000">New</font>';
        } else {
            $thread['new_mark'] = '';
        }

        if ($arr['uid'] > 0) {
            $thread['poster'] = XoopsUser::getUnameFromId($arr['uid']) . '&nbsp;' . formatTimestamp($arr['post_time'], 'm');
        } else {
            $thread['poster'] = htmlspecialchars($arr['name'], ENT_QUOTES | ENT_HTML5) . '&nbsp;' . formatTimestamp($arr['post_time'], 'm');
        }

        $block['threads'][] = &$thread;

        unset($thread);
    }

    return $block;
}

//最近の投稿(投稿単位＆シンプル)
function b_bluesbb_new_all2($options)
{
    $db = XoopsDatabaseFactory::getDatabaseConnection();

    global $xoopsUser, $memberHandler;

    $myts = MyTextSanitizer::getInstance();

    $block = [];

    $query = 'SELECT b.post_id, b.topic_id, b.thread_id, b.res_id, b.name, b.title, b.post_time, b.uid, t.topic_style FROM ' . $db->prefix('bluesbb') . ' b LEFT JOIN ' . $db->prefix('bluesbb_topic') . ' t ON t.topic_id = b.topic_id WHERE';

    if (is_object($xoopsUser)) {
        $query .= ' (t.topic_access = 1 OR t.topic_access = 2 OR t.topic_access = 3 OR t.topic_access = 4 OR t.topic_access = 5';

        $groups = $memberHandler->getGroupsByUser($xoopsUser->getVar('uid'), true);

        foreach ($groups as $group) {
            $query .= ' OR t.topic_group = ' . $group->getVar('groupid');
        }

        if ($xoopsUser->isAdmin()) {
            $query .= ' OR t.topic_access = 6';
        }
    } else {
        $query .= ' (t.topic_access = 1 OR t.topic_access = 2 OR t.topic_access = 5';
    }

    if ('0' == $options[2]) {
        $catop = '';
    } elseif ('0' != $options[2] && '0' != $options[3]) {
        $catop = ' AND (t.cat_id = ' . $options[2];
    } else {
        $catop = ' AND t.cat_id = ' . $options[2];
    }

    if ('0' == $options[3]) {
        $topop = '';
    } elseif ('0' != $options[2] && '0' != $options[3]) {
        $topop = ' OR t.topic_id = ' . $options[3] . ')';
    } else {
        $topop = ' AND t.topic_id = ' . $options[3];
    }

    $query .= ')' . $catop . '' . $topop . ' ORDER BY b.post_time DESC';

    if (!$result = $db->query($query, $options[0], 0)) {
        return false;
    }

    $now_time = time();

    while (false !== ($arr = $db->fetchArray($result))) {
        $thread['post_id'] = $arr['post_id'];

        $thread['topic_id'] = $arr['topic_id'];

        $thread['topic_style'] = $arr['topic_style'];

        $thread['thread_id'] = $arr['thread_id'];

        $thread['res_id'] = ++$arr['res_id'];

        $thread['title'] = htmlspecialchars($arr['title'], ENT_QUOTES | ENT_HTML5);

        if (($now_time - $arr['post_time']) <= $options[1] * 60 * 60) {
            $thread['new_mark'] = '&nbsp;<font color="#FF0000">New</font>';
        } else {
            $thread['new_mark'] = '';
        }

        if ($arr['uid'] > 0) {
            $thread['poster'] = XoopsUser::getUnameFromId($arr['uid']) . '&nbsp;' . formatTimestamp($arr['post_time'], 's');
        } else {
            $thread['poster'] = htmlspecialchars($arr['name'], ENT_QUOTES | ENT_HTML5) . '&nbsp;' . formatTimestamp($arr['post_time'], 's');
        }

        $block['threads'][] = &$thread;

        unset($thread);
    }

    return $block;
}

//カテゴリ別トピックメニュー
function b_bluesbb_menu($options)
{
    $db = XoopsDatabaseFactory::getDatabaseConnection();

    global $xoopsUser, $memberHandler;

    $myts = MyTextSanitizer::getInstance();

    $where_access = '';

    if (is_object($xoopsUser)) {
        $where_access = ' (topic_access = 1 OR topic_access = 2 OR topic_access = 3 OR topic_access = 4 OR topic_access = 5';

        $groups = $memberHandler->getGroupsByUser($xoopsUser->getVar('uid'), true);

        foreach ($groups as $group) {
            $where_access .= ' OR topic_group = ' . $group->getVar('groupid');
        }

        if ($xoopsUser->isAdmin()) {
            $where_access .= ' OR topic_access = 6';
        }
    } else {
        $where_access = ' (topic_access = 1 OR topic_access = 2 OR topic_access = 5';
    }

    $sql = 'SELECT cat_id FROM ' . $db->prefix('bluesbb_topic') . ' WHERE' . $where_access . ') ORDER BY cat_id, topic_order, topic_id';

    if (!$result = $db->query($sql)) {
        return false;
    }

    $cat_array = [];

    while (false !== ($myrow = $db->fetchArray($result))) {
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

    $block = [];

    $query1 = 'SELECT * FROM ' . $db->prefix('bluesbb_categories') . ' ' . $cid . ' ORDER BY cat_order';

    if (!$result1 = $db->query($query1)) {
        return false;
    }

    while (false !== ($cat_row = $db->fetchArray($result1))) {
        $category['cat_id'] = $cat_row['cat_id'];

        $category['cat_title'] = htmlspecialchars($cat_row['cat_title'], ENT_QUOTES | ENT_HTML5);

        $block['categories'][] = &$category;

        unset($category);
    }

    $query = 'SELECT topic_id, topic_name, cat_id FROM ' . $db->prefix('bluesbb_topic') . ' WHERE' . $where_access . ') ORDER BY cat_id, topic_order, topic_id';

    if (!$result = $db->query($query)) {
        return false;
    }

    while (false !== ($topic_row = $db->fetchArray($result))) {
        $topic['cat_id'] = $topic_row['cat_id'];

        $topic['topic_id'] = $topic_row['topic_id'];

        $topic['topic_name'] = htmlspecialchars($topic_row['topic_name'], ENT_QUOTES | ENT_HTML5);

        $block['topics'][] = &$topic;

        unset($topic);
    }

    return $block;
}

//最近の投稿(スレッド単位)
function b_bluesbb_new_thread($options)
{
    $db = XoopsDatabaseFactory::getDatabaseConnection();

    global $xoopsUser, $memberHandler;

    $myts = MyTextSanitizer::getInstance();

    $block = [];

    $query = 'SELECT b.topic_id, b.thread_id, b.title, t.topic_name, t.topic_style FROM ' . $db->prefix('bluesbb') . ' b LEFT JOIN ' . $db->prefix('bluesbb_topic') . ' t ON t.topic_id = b.topic_id WHERE';

    if (is_object($xoopsUser)) {
        $query .= ' (t.topic_access = 1 OR t.topic_access = 2 OR t.topic_access = 3 OR t.topic_access = 4 OR t.topic_access = 5';

        $groups = $memberHandler->getGroupsByUser($xoopsUser->getVar('uid'), true);

        foreach ($groups as $group) {
            $query .= ' OR t.topic_group = ' . $group->getVar('groupid');
        }

        if ($xoopsUser->isAdmin()) {
            $query .= ' OR t.topic_access = 6';
        }
    } else {
        $query .= ' (t.topic_access = 1 OR t.topic_access = 2 OR t.topic_access = 5';
    }

    if ('0' == $options[2]) {
        $catop = '';
    } elseif ('0' != $options[2] && '0' != $options[3]) {
        $catop = ' AND (t.cat_id = ' . $options[2];
    } else {
        $catop = ' AND t.cat_id = ' . $options[2];
    }

    if ('0' == $options[3]) {
        $topop = '';
    } elseif ('0' != $options[2] && '0' != $options[3]) {
        $topop = ' OR t.topic_id = ' . $options[3] . ')';
    } else {
        $topop = ' AND t.topic_id = ' . $options[3];
    }

    $query .= ')' . $catop . '' . $topop . ' AND b.res_id = 0 ORDER BY b.res_time DESC';

    if (!$result = $db->query($query, $options[0], 0)) {
        return false;
    }

    $block['lang_topic'] = _MB_BLUESBB_TOPIC;

    $block['lang_thread'] = _MB_BLUESBB_THREAD;

    $block['lang_replies'] = _MB_BLUESBB_REPLIES;

    $block['lang_poster'] = _MB_BLUESBB_POSTER;

    $block['lang_gobb'] = _MB_BLUESBB_GOBB;

    $now_time = time();

    while (false !== ($arr = $db->fetchArray($result))) {
        $sql = 'SELECT post_id, res_id, name, post_time, uid FROM ' . $db->prefix('bluesbb') . ' WHERE thread_id = ' . $arr['thread_id'] . ' ORDER BY post_time DESC LIMIT 0, 1';

        if (!$result2 = $db->query($sql)) {
            return false;
        }

        while (false !== ($arr2 = $db->fetchArray($result2))) {
            $thread['post_id'] = $arr2['post_id'];

            $thread['res_id'] = ++$arr2['res_id'];

            if (($now_time - $arr2['post_time']) <= $options[1] * 60 * 60) {
                $thread['new_mark'] = '&nbsp;<font color="#FF0000">New</font>';
            } else {
                $thread['new_mark'] = '';
            }

            if ($arr2['uid'] > 0) {
                $thread['poster'] = XoopsUser::getUnameFromId($arr2['uid']) . '&nbsp;' . formatTimestamp($arr2['post_time'], 'm');
            } else {
                $thread['poster'] = htmlspecialchars($arr2['name'], ENT_QUOTES | ENT_HTML5) . '&nbsp;' . formatTimestamp($arr2['post_time'], 'm');
            }
        }

        $count_sql = 'SELECT COUNT(*) FROM ' . $db->prefix('bluesbb') . ' WHERE thread_id = ' . $arr['thread_id'];

        if (!$count_result = $db->query($count_sql)) {
            return false;
        }

        [$count] = $db->fetchRow($count_result);

        $thread['replies'] = $count - 1;

        $thread['topic_id'] = $arr['topic_id'];

        $thread['topic_name'] = htmlspecialchars($arr['topic_name'], ENT_QUOTES | ENT_HTML5);

        $thread['thread_id'] = $arr['thread_id'];

        $thread['topic_style'] = $arr['topic_style'];

        $thread['title'] = htmlspecialchars($arr['title'], ENT_QUOTES | ENT_HTML5);

        $block['threads'][] = &$thread;

        unset($thread);
    }

    return $block;
}

//最近の投稿(スレッド単位＆シンプル)
function b_bluesbb_new_thread2($options)
{
    $db = XoopsDatabaseFactory::getDatabaseConnection();

    global $xoopsUser, $memberHandler;

    $myts = MyTextSanitizer::getInstance();

    $block = [];

    $query = 'SELECT b.topic_id, b.thread_id, b.title, t.topic_style FROM ' . $db->prefix('bluesbb') . ' b LEFT JOIN ' . $db->prefix('bluesbb_topic') . ' t ON t.topic_id = b.topic_id WHERE';

    if (is_object($xoopsUser)) {
        $query .= ' (t.topic_access = 1 OR t.topic_access = 2 OR t.topic_access = 3 OR t.topic_access = 4 OR t.topic_access = 5';

        $groups = $memberHandler->getGroupsByUser($xoopsUser->getVar('uid'), true);

        foreach ($groups as $group) {
            $query .= ' OR t.topic_group = ' . $group->getVar('groupid');
        }

        if ($xoopsUser->isAdmin()) {
            $query .= ' OR t.topic_access = 6';
        }
    } else {
        $query .= ' (t.topic_access = 1 OR t.topic_access = 2 OR t.topic_access = 5';
    }

    if ('0' == $options[2]) {
        $catop = '';
    } elseif ('0' != $options[2] && '0' != $options[3]) {
        $catop = ' AND (t.cat_id = ' . $options[2];
    } else {
        $catop = ' AND t.cat_id = ' . $options[2];
    }

    if ('0' == $options[3]) {
        $topop = '';
    } elseif ('0' != $options[2] && '0' != $options[3]) {
        $topop = ' OR t.topic_id = ' . $options[3] . ')';
    } else {
        $topop = ' AND t.topic_id = ' . $options[3];
    }

    $query .= ')' . $catop . '' . $topop . ' AND b.res_id = 0 ORDER BY b.res_time DESC';

    if (!$result = $db->query($query, $options[0], 0)) {
        return false;
    }

    $now_time = time();

    while (false !== ($arr = $db->fetchArray($result))) {
        $sql = 'SELECT post_id, res_id, name, post_time, uid FROM ' . $db->prefix('bluesbb') . ' WHERE thread_id = ' . $arr['thread_id'] . ' ORDER BY post_time DESC LIMIT 0, 1';

        if (!$result2 = $db->query($sql)) {
            return false;
        }

        while (false !== ($arr2 = $db->fetchArray($result2))) {
            $thread['post_id'] = $arr2['post_id'];

            $thread['res_id'] = ++$arr2['res_id'];

            if (($now_time - $arr2['post_time']) <= $options[1] * 60 * 60) {
                $thread['new_mark'] = '&nbsp;<font color="#FF0000">New</font>';
            } else {
                $thread['new_mark'] = '';
            }

            if ($arr2['uid'] > 0) {
                $thread['poster'] = XoopsUser::getUnameFromId($arr2['uid']) . '&nbsp;' . formatTimestamp($arr2['post_time'], 'm');
            } else {
                $thread['poster'] = htmlspecialchars($arr2['name'], ENT_QUOTES | ENT_HTML5) . '&nbsp;' . formatTimestamp($arr2['post_time'], 'm');
            }
        }

        $count_sql = 'SELECT COUNT(post_id) FROM ' . $db->prefix('bluesbb') . ' WHERE thread_id = ' . $arr['thread_id'];

        if (!$count_result = $db->query($count_sql)) {
            return false;
        }

        [$count] = $db->fetchRow($count_result);

        $thread['replies'] = $count - 1;

        $thread['topic_id'] = $arr['topic_id'];

        $thread['thread_id'] = $arr['thread_id'];

        $thread['topic_style'] = $arr['topic_style'];

        $thread['title'] = htmlspecialchars($arr['title'], ENT_QUOTES | ENT_HTML5);

        $block['threads'][] = &$thread;

        unset($thread);
    }

    return $block;
}

function b_bluesbb_new_all_edit($options)
{
    $db = XoopsDatabaseFactory::getDatabaseConnection();

    $myts = MyTextSanitizer::getInstance();

    $inputtag = "<input type='text' name='options[0]' value='" . $options[0] . "'>";

    $form = sprintf(_MB_BLUESBB_DISPLAY, $inputtag);

    $form .= '<br>' . _MB_BLUESBB_DISPLAYN . "&nbsp;<input type='text' name='options[1]' value='" . $options[1] . "'>";

    $query1 = 'SELECT * FROM ' . $db->prefix('bluesbb_categories') . ' ORDER BY cat_order';

    if (!$result1 = $db->query($query1)) {
        return false;
    }

    $form .= '<br>' . _MB_BLUESBB_SELCAT . "&nbsp;<select size='1' name='options[2]' id='options[2]'>";

    if ('0' == $options[2]) {
        $form .= "<option value='0' selected='selected'>" . _MB_BLUESBB_ALLSELECT . '</option>';
    } else {
        $form .= "<option value='0'>" . _MB_BLUESBB_ALLSELECT . '</option>';
    }

    while (false !== ($cat_row = $db->fetchArray($result1))) {
        if ($options[2] == $cat_row['cat_id']) {
            $selected = " selected='selected'";
        } else {
            $selected = '';
        }

        $form .= "<option value='" . $cat_row['cat_id'] . "'" . $selected . '>' . htmlspecialchars($cat_row['cat_title'], ENT_QUOTES | ENT_HTML5) . '</option>';
    }

    $form .= '</select>';

    $query2 = 'SELECT topic_id, topic_name FROM ' . $db->prefix('bluesbb_topic') . ' ORDER BY topic_id';

    if (!$result2 = $db->query($query2)) {
        return false;
    }

    $form .= '<br>' . _MB_BLUESBB_SELTOP . "&nbsp;<select size='1' name='options[3]' id='options[3]'>";

    if ('0' == $options[3]) {
        $form .= "<option value='0' selected='selected'>" . _MB_BLUESBB_ALLSELECT . '</option>';
    } else {
        $form .= "<option value='0'>" . _MB_BLUESBB_ALLSELECT . '</option>';
    }

    while (false !== ($top_row = $db->fetchArray($result2))) {
        if ($options[3] == $top_row['topic_id']) {
            $selected = " selected='selected'";
        } else {
            $selected = '';
        }

        $form .= "<option value='" . $top_row['topic_id'] . "'" . $selected . '>' . htmlspecialchars($top_row['topic_name'], ENT_QUOTES | ENT_HTML5) . '</option>';
    }

    $form .= '</select>';

    return $form;
}
