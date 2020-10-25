<?php

define('_MD_GO', '送信');
//index.php
define('_MD_WELCOME', '%s 掲示板へようこそ');
define('_MD_PHONE', 'i-mode');
//topic.php
define('_MD_BLUESBBINDEX', 'トップ');
define('_MD_POSTNEW', '新規投稿');
define('_MD_THREADLIST', 'スレッド一覧はこちら');
define('_MD_SITE', 'SITE');
define('_MD_MAIL', 'MAIL');
define('_MD_REPLIES', '返信');
define('_MD_EDIT', '編集');
define('_MD_ALLVIEW', '全部読む');
define('_MD_NEW50', '最新50');
define('_MD_TOPICHEAD', '板のトップ');
define('_MD_RELOAD', 'リロード');
define('_MD_NEXT300', '次の300スレ');
define('_MD_MENUOFTHREADS', '■');
define('_MD_PREVIOUSTOPIC', '▲');
define('_MD_NEXTTOPIC', '▼');
define('_MD_THREAD', 'スレッド表示');
define('_MD_TREE', 'ツリー表示');
define('_MD_ORDER', '投稿順表示');
define('_MD_TREETOP', 'ツリーに属する記事を全て表示します');
define('_MD_TREETORL', 'この記事にぶら下がってる記事を全て表示します');
//thread.php
define('_MD_TOPICBACK', '掲示板に戻る');
define('_MD_ALLTHREADVIEW', '全部');
define('_MD_NOWHERE', '←いまここ～');
define('_MD_BACK100', '前100');
define('_MD_NEXT100', '次100');
// ERROR messages
define('_MD_ERRORTOPIC', 'エラー: トピックが選択されていません');
define('_MD_ERRORPOST', 'エラー: 投稿が選択されていません');
define('_MD_EMPTYPASS', 'パスワードが空なので編集するの無理です。');
define('_MD_ERRORTHREAD', 'エラー: スレッドが選択されていません');
define('_MD_ERRORCONNECT', 'エラー: データベースにアクセスすることができませんでした。');
define('_MD_ERROREXIST', 'エラー: 選択されたトピックは見つかりませんでした。もう一度やり直してください。');
define('_MD_ERROREXIST2', 'エラー: 選択された投稿は見つかりませんでした。もう一度やり直してください。');
define('_MD_ERROROCCURED', 'エラーが発生しました。');
define('_MD_COULDNOTQUERY', 'フォーラムデータベースに問い合わせすることができませんでした。');
define('_MD_COULDNOTREMOVETXT', '番の記事レコードの削除に失敗しました。');
define('_MD_COULDNOTREMOVETXT2', '番のスレッド削除に失敗しました。');
define('_MD_TOPICACCESSERROR', 'エラー: このトピックへのアクセスは許可されていません。');
define('_MD_TOPICACCESSERROR2', 'エラー: このトピックへの書込みは許可されていません。');
define('_MD_PASSERROR', 'パスワード不一致');
define('_MD_DBINSERTERROR', '<font color="#FF0000">DBへの入力エラー！</font>投稿処理は失敗しました。');
define('_MD_TIMEUPDATEERROR', '<font color="#FF0000">エラー！</font>スレッドの最新返信日時の更新に失敗しました。');
define('_MD_POSTSUPDATEERROR', '<font color="#FF0000">エラー！</font>投稿ランキングの更新に失敗しました。');
//reply.php
define('_MD_RESERROR', 'このスレッドは規定投稿数を超えていますので、書込むことができません。');
define('_MD_BY', '投稿者：');
define('_MD_ON', '投稿日時：');
define('_MD_USERWROTE', '%sさんは書きました：'); // %s is username
//post.php
define('_MD_NOBODY', '名無しさん');
define('_MD_NOTITLE', 'タイトルが記入されていません。');
define('_MD_NOMESSAGE', '本文が記入されていません。');
define('_MD_MESSAGEMAX', 'メッセージ文字数が多すぎます。');
define('_MD_PWORD', '<font color="#FF0000">エラー！</font>禁止ワードが含まれていますので、投稿できません。');
define('_MD_NOMODE', 'modeが指定されていません。');
define('_MD_THANKSSUBMIT', '<font color="#0000FF">記事の投稿は問題なく処理されました。</font>');
define('_MD_NEWPOSTED', 'に新規スレッドがたてられました。');
define('_MD_REPLYPOSTED', 'に返信が投稿されました。');
define('_MD_EDITPOSTED', 'の投稿が編集されました。');
//bbbform.inc
define('_MD_QUOTE', '引用');
define('_MD_POSTNAME', '名前※');
define('_MD_POSTSITE', 'SITE※');
define('_MD_POSTMAIL', 'MAIL※');
define('_MD_SUBJECTC', '題名');
define('_MD_MESSAGEC', 'メッセージ');
define('_MD_POSTPASS', 'パスワード※');
define('_MD_OPTIONS', 'オプション');
define('_MD_COOKIECHECK', '※印の項目をクッキーに保存');
define('_MD_ATTACHSIG', '署名を付ける');
define('_MD_THREADSTOP', 'スレッドストップ（この投稿を最後に、このスレッドへの追加書込みをできなくします）');
define('_MD_POST', '投稿する');
define('_MD_SUBMIT', '確定');
define('_MD_CANCELPOST', '投稿中止');

// pass_check.php
define('_MD_POSTEDIT', '番の記事を操作します。');
define('_MD_POSTEDIT2', '記事の編集');
define('_MD_POSTEDIT3', 'この記事に対する返信記事が存在するため、この記事を削除することはできません。');
define('_MD_POSTEDIT4', '記事の削除<font color="#FF0000">※すぐに削除されますので注意して下さい。</font>');
define('_MD_POSTEDIT5', '投稿者本人と確認しましたのでパスワードは必要ありません。');
define('_MD_POSTEDIT6', '管理人と確認しましたのでパスワードは必要ありません。');
define('_MD_POSTEDIT7', 'スレッド丸ごと削除<font color="#cc0000">※このスレッド配下の記事をスレごと丸っと全て削除します。すぐに削除されますので注意して下さい。送信してしまうと後戻りできません。またこれで削除した結果は投稿数ランキングに反映しません。</font>');
define('_MD_PASS', 'パスワード');
define('_MD_POSTSDELETED', '番の記事レコードを削除しました。');
define('_MD_POSTSDELETED2', '番のスレッドを削除しました。');
