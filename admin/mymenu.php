<?php

if (!defined('XOOPS_ROOT_PATH')) {
    exit;
}

if (!defined('XOOPS_ORETEKI')) {
    // Skip for ORETEKI XOOPS

    if (!isset($module) || !is_object($module)) {
        $module = $xoopsModule;
    } elseif (!is_object($xoopsModule)) {
        die('$xoopsModule is not set');
    }

    if (file_exists('../language/' . $xoopsConfig['language'] . '/modinfo.php')) {
        require_once '../language/' . $xoopsConfig['language'] . '/modinfo.php';
    } else {
        require_once '../language/english/modinfo.php';
    }

    require __DIR__ . '/menu.php';

    //	array_push( $adminmenu , array( 'title' => _PREFERENCES , 'link' => '../system/admin.php?fct=preferences&op=showmod&mod=' . $module->getvar('mid') ) ) ;

    $menuitem_dirname = $module->getVar('dirname');

    if ($module->getVar('hasconfig')) {
        $adminmenu[] = ['title' => _PREFERENCES, 'link' => 'admin/admin.php?fct=preferences&op=showmod&mod=' . $module->getVar('mid')];
    }

    $menuitem_count = 0;

    $mymenu_uri = empty($mymenu_fake_uri) ? $_SERVER['REQUEST_URI'] : $mymenu_fake_uri;

    $mymenu_link = mb_substr(mb_strstr($mymenu_uri, '/admin/'), 1);

    // hilight

    foreach (array_keys($adminmenu) as $i) {
        if ($mymenu_link == $adminmenu[$i]['link']) {
            $adminmenu[$i]['color'] = '#FFCCCC';

            $adminmenu_hilighted = true;
        } else {
            $adminmenu[$i]['color'] = '#DDDDDD';
        }
    }

    if (empty($adminmenu_hilighted)) {
        foreach (array_keys($adminmenu) as $i) {
            if (mb_stristr($mymenu_uri, $adminmenu[$i]['link'])) {
                $adminmenu[$i]['color'] = '#FFCCCC';
            }
        }
    }

    /*	// display
        foreach( $adminmenu as $menuitem ) {
            echo "<a href='".XOOPS_URL."/modules/$menuitem_dirname/{$menuitem['link']}' style='background-color:{$menuitem['color']};font:normal normal bold 9pt/12pt;'>{$menuitem['title']}</a> &nbsp; \n" ;

            if( ++ $menuitem_count >= 4 ) {
                echo "</div>\n<div width='95%' align='center'>\n" ;
                $menuitem_count = 0 ;
            }
        }
        echo "</div>\n" ;
    */

    // display

    echo "<div style='text-align:left;width:98%;'>";

    foreach ($adminmenu as $menuitem) {
        echo "<div style='float:left;height:1.5em;'><nobr><a href='" . XOOPS_URL . "/modules/$menuitem_dirname/{$menuitem['link']}' style='background-color:{$menuitem['color']};font:normal normal bold 9pt/12pt;'>{$menuitem['title']}</a> | </nobr></div>\n";
    }

    echo "</div>\n<hr style='clear:left;display:block;'>\n";
}
