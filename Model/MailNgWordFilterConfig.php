<?php
/**
 * MailNgWordFilterConfig.php
 *
 * メールNGワードフィルター設定 ダミーモデルクラス
 *
 * @package    MailNgWordFilter
 * @subpackage Model
 * @author     HATTA <https://hattantoco.com>
 * @license    MIT License
 * @link       https://github.com/HATTANTOCO
 */

App::uses('AppModel', 'Model');

/**
 * メールNGワードフィルター設定 ダミーモデル
 */
class MailNgWordFilterConfig extends AppModel {

    // データベーステーブルを使用しない設定
    public $useTable = false;

}
