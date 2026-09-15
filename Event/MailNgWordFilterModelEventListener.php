<?php
/**
 * MailNgWordFilterModelEventListener.php
 *
 * メールNGワードフィルター モデルイベントリスナークラス
 *
 * @package    MailNgWordFilter
 * @subpackage Event
 * @author     HATTA <https://hattantoco.com>
 * @license    MIT License
 * @link       https://github.com/HATTANTOCO
 */

App::uses('BcModelEventListener', 'Event');

/**
 * メールNGワードフィルター モデルイベントリスナー
 */
class MailNgWordFilterModelEventListener extends BcModelEventListener {

    public $events = array(
        'Mail.MailMessage.beforeValidate'
    );

    /**
     * MailMessageのbeforeValidateイベント処理
     */
    public function mailMailMessageBeforeValidate(CakeEvent $event) {
        $Model = $event->subject();
        
        // Config/setting.php からNGワード設定値を読み込む
        $ngWordsString = Configure::read('MailNgWordFilter.ng_words');
        if (empty($ngWordsString)) {
            return true;
        }

        $ngWords = array_filter(array_map('trim', explode(',', $ngWordsString)));
        if (empty($ngWords)) {
            return true;
        }

        // 送信データ自体がない場合は処理をスキップ
        if (empty($Model->data['MailMessage'])) {
            return true;
        }

        // CakePHPのリクエストパラメータからフォームの実態IDを取得
        $mailFormId = null;
        $request = Router::getRequest();
        
        if (isset($request->params['entityId'])) {
            $mailFormId = $request->params['entityId'];
        } elseif (!empty($request->params['Content']['entity_id'])) {
            $mailFormId = $request->params['Content']['entity_id'];
        }

        // 対象外のフォームである場合は処理を終了
        $targetFormIds = Configure::read('MailNgWordFilter.target_form_ids');
        if (!empty($targetFormIds) && is_array($targetFormIds)) {
            if (!in_array($mailFormId, $targetFormIds)) {
                return true; 
            }
        }

        // モデルが保持しているフィールド情報がない場合は処理をスキップ
        if (empty($Model->mailFields)) {
            return true;
        }

        // モデル内のデータから複数行テキスト（textarea）のフィールドを対象にチェックを実行
        foreach ($Model->mailFields as $field) {
            if (!empty($field['MailField']['type']) && $field['MailField']['type'] === 'textarea' && !empty($field['MailField']['use_field'])) {
                
                $targetKey = $field['MailField']['field_name'];

                if (!empty($Model->data['MailMessage'][$targetKey])) {
                    $value = $Model->data['MailMessage'][$targetKey];

                    if (is_string($value) && $value !== '') {
                        
                        // 禁止文字列が含まれているか確認
                        foreach ($ngWords as $ngWord) {
                            if (mb_strpos($value, $ngWord) !== false) {
                                $Model->invalidate($targetKey, '禁止された文字列が入力されています。');
                                break; 
                            }
                        }
                    }
                }
            }
        }

        return true;
    }
}
