<?php

// Author: Sting_Band
// URL: http://www.bluish.jp/

ini_set('mbstring.language', 'Japanese');
ini_set('mbstring.http_output', 'pass');
require dirname(__DIR__, 3) . '/mainfile.php';
require __DIR__ . '/i_header.php';
//板数をカウント、１つだけならばその板にリダイレクト。
if (BLUESBB_I_INDEXREDIRECT == 'on') {
    $sql = 'SELECT COUNT(topic_id) FROM ' . $xoopsDB->prefix('bluesbb_topic');

    if (!$result = $xoopsDB->query($sql)) {
        redirect_header(XOOPS_ROOT_PATH . '/index.php', 2, _MD_ERRORCONNECT);

        exit();
    }

    [$topic_count] = $xoopsDB->fetchRow($result);

    if (1 == $topic_count) {
        $sql = 'SELECT topic_id FROM ' . $xoopsDB->prefix('bluesbb_topic');

        $result = $xoopsDB->query($sql);

        while (false !== ($myrow = $xoopsDB->fetchArray($result))) {
            $topic = $myrow['topic_id'];
        }

        header('Location: ' . BLUESBB_URL . '/i/i_threadlist.php?top=' . $topic);

        exit();
    }
}
//カテゴリを抽出
$sql = 'SELECT * FROM ' . $xoopsDB->prefix('bluesbb_categories') . ' ORDER BY cat_order';
if (!$result = $xoopsDB->query($sql)) {
    redirect_header(XOOPS_URL . '/', 3, 'Error!! ' . basename(__FILE__) . ' Line ' . __LINE__);

    exit();
}
$categories = [];
while (false !== ($cat_row = $xoopsDB->fetchArray($result))) {
    $categories[] = $cat_row;
}
//板を抽出
$sql = 'SELECT topic_id, topic_name, cat_id FROM ' . $xoopsDB->prefix('bluesbb_topic') . ' WHERE topic_access = 1 OR topic_access = 2 OR topic_access = 5 ORDER BY cat_id, topic_order, topic_id';
if (!$result = $xoopsDB->query($sql)) {
    redirect_header(XOOPS_URL . '/', 3, 'Error!! ' . basename(__FILE__) . ' Line ' . __LINE__);

    exit();
}
$topics = [];
while (false !== ($topic_row = $xoopsDB->fetchArray($result))) {
    $topics[] = $topic_row;
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
?>
<center><font color="#000000"><?php echo sjisconvert(htmlspecialchars($xoopsConfig['sitename'], ENT_QUOTES | ENT_HTML5)); ?></font></center>
<hr>
<?php
foreach ($categories as $category) {
    echo sjisconvert(htmlspecialchars($category['cat_title'], ENT_QUOTES | ENT_HTML5)) . "<br>\r\n";

    echo '<ul>';

    foreach ($topics as $topic) {
        if ($topic['cat_id'] == $category['cat_id']) {
            echo '<li><a href="' . BLUESBB_URL . '/i/i_threadlist.php?top=' . $topic['topic_id'] . '">' . sjisconvert(htmlspecialchars($topic['topic_name'], ENT_QUOTES | ENT_HTML5)) . '</a></li>';
        }
    }

    echo '</ul>';
}
?>
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
