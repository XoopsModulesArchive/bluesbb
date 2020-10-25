<?php

// Author: Sting_Band
// URL: http://www.bluish.jp/

function gt_link($val, $tid, $sid, $burl, $sty)
{
    $val = preg_replace("/&gt;&gt;([0-9]+)(?![-\d])/", '<a href="' . $burl . '/thread.php?thr=' . $sid . '&amp;sty=' . $sty . '&amp;num=\\1" target="_blank">&gt;&gt;\\1</a>', $val);

    $val = preg_replace("/&gt;&gt;([0-9]+)\-([0-9]+)/", '<a href="' . $burl . '/thread.php?thr=' . $sid . '&amp;sty=' . $sty . '&amp;num=\\1-\\2" target="_blank">&gt;&gt;\\1-\\2</a>', $val);

    return $val;
}

function sjisconvert($str)
{
    $code_check = mb_detect_encoding($str, mb_detect_order(), true);

    if (!eregi('sjis', $code_check) && '' != $code_check) {
        $str = mb_convert_encoding($str, 'SJIS', (string)$code_check);
    }

    return $str;
}

function eucconvert($str)
{
    $code_check = mb_detect_encoding($str, mb_detect_order(), true);

    if (!eregi('euc', $code_check) && '' != $code_check) {
        $str = mb_convert_encoding($str, 'EUC-JP', (string)$code_check);
    }

    return $str;
}

function gt_link_i($val, $tid, $sid, $burl)
{
    $val = preg_replace("/&gt;&gt;([0-9]+)(?![-\d])/", '<a href="' . $burl . '/i/i_message.php?top=' . $tid . '&amp;thr=' . $sid . '&amp;display=3&amp;num=\\1">&gt;&gt;\\1</a>', $val);

    $val = preg_replace("/&gt;&gt;([0-9]+)\-([0-9]+)/", '<a href="' . $burl . '/i/i_message.php?top=' . $tid . '&amp;thr=' . $sid . '&amp;display=3&amp;num=\\1-\\2">&gt;&gt;\\1-\\2</a>', $val);

    return $val;
}

function bbb_p_word($str, $wod)
{
    $wod_arr = preg_preg_split("/[\s,]+/", $wod);

    foreach ($wod_arr as $word) {
        $word = trim($word);

        if (preg_match('/' . $word . '/i', $str) && '' != $word) {
            redirect_header('index.php', 3, _MD_PWORD);

            exit();
        }
    }

    return $str;
}
