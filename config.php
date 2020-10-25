<?php

// Author: Sting_Band
// URL: http://www.bluish.jp/

//Since it is meaningless even if it displays index.php when the number of topics is one, redirection is carried out to topic.php. When you use this function, please rewrite "off" to "on".
//[携帯電話用設定]トピックが１つだけだった場合、index.phpを表示しても意味が無いので、topic.phpへリダイレクトさせます。この機能を利用する場合は"off"を"on"に書き換えてください。
define('BLUESBB_I_INDEXREDIRECT', 'off');

//Prohibition WORD is set up. It cannot contribute, when the character set up here is contained in the text. WORD is divided with the comma and can also set up shoes.
//[携帯電話用設定]禁止ワードを設定します。ここで設定された文字が本文に含まれている場合は投稿できません。ワードはカンマ「,」で区切っていくつでも設定できます。
define('BLUESBB_I_PWORD', 'hogehogeshop.com,オ-プン特価セ－ル,今なら無料サービス');

//When there is post, it is automatic to an administrator and sets up whether mail is sent or not. It sends by on. It does not send in off.
//[携帯電話用設定]投稿があった時に管理人に自動でメールを送るかどうか設定します。onで送る。offで送らない。
define('BLUESBB_I_SENDMAIL', 'on');

//It is a copyright display. In any cases, it cannot be deletion changed.
//著作権表示です。いかなる場合も削除変更不可です。
define('BLUESBB_COPYRIGHT', "BluesBB&nbsp;<a href='http://www.bluish.jp/' target='_blank'>&copy;Sting_Band</a>");
