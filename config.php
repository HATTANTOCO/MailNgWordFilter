<?php
/**
 * config.php
 *
 * メールNGワードフィルター プラグイン定義ファイル
 *
 * @package    MailNgWordFilter
 * @author     HATTA <https://hattantoco.com>
 * @license    MIT License
 * @link       https://github.com/HATTANTOCO
 */

$title = 'メール禁止ワードフィルター';
$description = 'メールフォームのテキストエリアの送信内容に禁止ワードが含まれている場合、送信をブロックしてバリデーションエラーにします。';
$author = 'HATTA';
$url = 'https://hattantoco.com';

// 管理画面の設定ボタンの遷移先を指定
$adminLink = [
    'admin' => true,
    'plugin' => 'mail_ng_word_filter',
    'controller' => 'mail_ng_word_filter_configs',
    'action' => 'form'
];

// モデルイベントリスナーの登録
$pluginsEvents = array('MailNgWordFilter.MailNgWordFilterModelEventListener');
