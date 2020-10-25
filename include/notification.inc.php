<?php

// Author: Sting_Band
// URL: http://www.bluish.jp/

function bluesbb_notify_iteminfo($category, $item_id)
{
    $moduleHandler = xoops_getHandler('module');

    $module = $moduleHandler->getByDirname('bluesbb');

    if ('global' == $category) {
        $item['name'] = '';

        $item['url'] = '';

        return $item;
    }

    global $xoopsDB;

    if ('topic' == $category) {
        $sql = 'SELECT topic_name FROM ' . $xoopsDB->prefix('bluesbb_topic') . ' WHERE topic_id = ' . $item_id;

        $result = $xoopsDB->query($sql); // TODO: error check

        $result_array = $xoopsDB->fetchArray($result);

        $item['name'] = $result_array['topic_name'];

        $item['url'] = XOOPS_URL . '/modules/' . $module->getVar('dirname') . '/topic.php?top=' . $item_id;

        return $item;
    }

    if ('thread' == $category) {
        $sql = 'SELECT b.post_id,b.title,t.topic_name,t.topic_style FROM ' . $xoopsDB->prefix('bluesbb') . ' b, ' . $xoopsDB->prefix('bluesbb_topic') . ' t WHERE b.topic_id = t.topic_id AND b.thread_id = ' . $item_id . ' AND res_id = 0 LIMIT 1';

        $result = $xoopsDB->query($sql); // TODO: error check

        $result_array = $xoopsDB->fetchArray($result);

        $item['name'] = $result_array['title'];

        switch ($result_array['topic_style']) {
            case '1':
                $lt = 'l50#p' . $result_array['post_id'];
                break;
            case '2':
                $lt = 1;
                break;
            case '3':
                $lt = $result_array['post_id'];
                break;
        }

        $item['url'] = XOOPS_URL . '/modules/' . $module->getVar('dirname') . '/thread.php?thr=' . $item_id . '&amp;sty=' . $result_array['topic_style'] . '&amp;num=' . $lt;

        return $item;
    }

    if ('post' == $category) {
        $sql = 'SELECT b.thread_id,b.title,t.topic_style FROM ' . $xoopsDB->prefix('bluesbb') . ' b, ' . $xoopsDB->prefix('bluesbb_topic') . ' t WHERE b.topic_id = t.topic_id AND b.post_id = ' . $item_id . ' LIMIT 1';

        $result = $xoopsDB->query($sql);

        $result_array = $xoopsDB->fetchArray($result);

        $item['name'] = $result_array['title'];

        switch ($result_array['topic_style']) {
            case '1':
                $lt = 'l50#p' . $item_id;
                break;
            case '2':
                $lt = 1;
                break;
            case '3':
                $lt = $item_id;
                break;
        }

        $item['url'] = XOOPS_URL . '/modules/' . $module->getVar('dirname') . '/thread.php?thr=' . $result_array['thread_id'] . '&amp;sty=' . $result_array['topic_style'] . '&amp;num=' . $lt;

        return $item;
    }
}
