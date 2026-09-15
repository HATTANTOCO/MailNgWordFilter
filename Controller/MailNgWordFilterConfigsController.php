<?php
/**
 * MailNgWordFilterConfigsController.php
 *
 * メールNGワードフィルター設定 コントローラー
 *
 * @package    MailNgWordFilter
 * @subpackage Controller
 * @author     HATTA <https://hattantoco.com>
 * @license    MIT License
 * @link       https://github.com/HATTANTOCO
 */

App::uses('AppController', 'Controller');
App::uses('File', 'Utility');

/**
 * メールNGワードフィルター設定 コントローラー
 */
class MailNgWordFilterConfigsController extends AppController {

    public $name = 'MailNgWordFilterConfigs';
    public $components = array('BcAuth', 'Cookie', 'BcAuthConfigure');
    
    // データベーステーブルは直接使用せずコアのContentモデルを経由
    public $uses = array('Content');

    /**
     * [管理画面] 設定フォームおよび保存処理
     */
    public function admin_form() {
        
        // コンテンツ管理からメールフォームのデータのみを取得
        $contents = $this->Content->find('all', [
            'conditions' => [
                'Content.type' => 'MailContent',
                'Content.deleted' => false
            ],
            'fields' => ['Content.entity_id', 'Content.title'],
            'recursive' => -1
        ]);

        // ビューのチェックボックス用に [実態ID => フォームタイトル] の配列に整形
        $mailForms = [];
        if (!empty($contents)) {
            foreach ($contents as $c) {
                $mailForms[$c['Content']['entity_id']] = $c['Content']['title'];
            }
        }
        $this->set('mailForms', $mailForms);

        if ($this->request->is(array('post', 'put'))) {
            
            $ngWords = isset($this->request->data['MailNgWordFilterConfig']['ng_words']) 
                ? $this->request->data['MailNgWordFilterConfig']['ng_words'] 
                : '';

            $targetFormIds = isset($this->request->data['MailNgWordFilterConfig']['target_form_ids'])
                ? $this->request->data['MailNgWordFilterConfig']['target_form_ids']
                : array();

            if (is_array($targetFormIds)) {
                $targetFormIds = array_filter(array_map('intval', $targetFormIds));
            } else {
                $targetFormIds = array();
            }

            $targetFormIdsString = implode(', ', $targetFormIds);

            // 対象の設定ファイルパスを取得
            $settingFilePath = App::pluginPath('MailNgWordFilter') . 'Config' . DS . 'setting.php';
            
            $fileContent = "<?php\n"
                         . "/**\n"
                         . " * MailNgWordFilter用 設定ファイル（管理画面から自動更新）\n"
                         . " */\n"
                         . "\$config['MailNgWordFilter'] = [\n"
                         . "    'ng_words' => '" . addslashes($ngWords) . "',\n"
                         . "    'target_form_ids' => [" . $targetFormIdsString . "]\n"
                         . "];\n";

            // Fileユーティリティを使って書き込み
            $FolderFile = new File($settingFilePath, true, 0666);
            if ($FolderFile->write($fileContent)) {
                $this->BcMessage->setInfo(__d('baser_core', 'メールNGワードフィルターの設定を保存しました。'));
                
                Configure::write('MailNgWordFilter.ng_words', $ngWords);
                Configure::write('MailNgWordFilter.target_form_ids', $targetFormIds);

                $this->redirect(array('action' => 'form'));
            } else {
                $this->BcMessage->setError(__d('baser_core', '設定ファイルの書き込みに失敗しました。'));
            }

        } else {
            // GETアクセスの場合は、Configから現在の設定値をマッピング
            $this->request->data['MailNgWordFilterConfig']['ng_words'] = Configure::read('MailNgWordFilter.ng_words');
            
            $currentFormIds = Configure::read('MailNgWordFilter.target_form_ids');
            $this->request->data['MailNgWordFilterConfig']['target_form_ids'] = is_array($currentFormIds) ? $currentFormIds : array();
        }
    }
}
