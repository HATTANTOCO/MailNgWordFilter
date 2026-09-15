<?php
/**
 * form.php
 *
 * メールNGワードフィルター設定画面 ビューテンプレート
 *
 * @package    MailNgWordFilter
 * @subpackage View
 * @author     HATTA <https://hattantoco.com>
 * @license    MIT License
 * @link       https://github.com/HATTANTOCO
 */

$this->BcBaser->setTitle(__d('baser_core', 'メールNGワードフィルター設定'));

// Config/setting.php から現在の設定値を読み込んで初期値にする
$currentNgWords = Configure::read('MailNgWordFilter.ng_words');
$currentFormIds = Configure::read('MailNgWordFilter.target_form_ids');
if (!is_array($currentFormIds)) {
    $currentFormIds = [];
}
?>

<!-- フォーム開始 -->
<?php echo $this->BcForm->create('MailNgWordFilterConfig', ['url' => ['action' => 'form']]) ?>

<div class="section bca-section">
    <table class="form-table bca-form-table">
        <!-- NGワード設定 -->
        <tr>
            <th class="col-head bca-form-table__label">
                <?php echo $this->BcForm->label('MailNgWordFilterConfig.ng_words', 'NGワード（禁止文字列）') ?>
            </th>
            <td class="col-input bca-form-table__input">
                <?php echo $this->BcForm->input('MailNgWordFilterConfig.ng_words', [
                    'type' => 'text',
                    'value' => $currentNgWords,
                    'class' => 'bca-textbox__input',
                    'size' => 50,
                    'placeholder' => '例：株式会社, 研究所, http://, https://'
                ]) ?>
                
                <i class="bca-icon--question-circle btn help bca-help" id="helpNgWords"></i>
                <div class="helptext" id="helptextNgWords">
                    <ul>
                        <li>メールフォームの「テキストエリア（複数行入力欄）」のみを対象に、ここで指定した文字列が含まれているかチェックします。</li>
                        <li>複数のワードを指定する場合は、**半角カンマ（,）**で区切って入力してください。</li>
                        <li>前後のスペースは自動で取り除かれます。</li>
                        <li>お名前や件名など、テキストエリア以外の項目に入力された場合はチェックをスキップ（スルー）します。</li>
                    </ul>
                </div>
                <?php echo $this->BcForm->error('MailNgWordFilterConfig.ng_words') ?>
            </td>
        </tr>

        <!-- 対象フォームの選択設定 -->
        <tr>
            <th class="col-head bca-form-table__label">
                <?php echo $this->BcForm->label('MailNgWordFilterConfig.target_form_ids', '対象メールフォーム') ?>
            </th>
            <td class="col-input bca-form-table__input">
                <?php if (!empty($mailForms)): ?>
                    <div class="bca-checkbox-group" style="display: inline-block; vertical-align: middle; margin-right: 5px;">
                        <?php echo $this->BcForm->input('MailNgWordFilterConfig.target_form_ids', [
                            'type' => 'select',
                            'multiple' => 'checkbox',
                            'options' => $mailForms,
                            'value' => $currentFormIds,
                            'class' => 'bca-checkbox__input'
                        ]) ?>
                    </div>
                <?php else: ?>
                    <p style="color: #999; margin: 0; display: inline-block;">有効なメールフォームが見つかりません。先にメールフォームプラグインでフォームを作成してください。</p>
                <?php endif; ?>
                
                <!-- ヘルプボタンとテキスト -->
                <i class="bca-icon--question-circle btn help bca-help" id="helpTargetFormIds" style="vertical-align: middle;"></i>
                <div class="helptext" id="helptextTargetFormIds">
                    <ul>
                        <li>NGワードフィルターを適用したいメールフォームにチェックを入れてください（複数選択可）。</li>
                        <li>チェックを入れていないフォームでは、NGワードが含まれていても通常通り送信されます。</li>
                    </ul>
                </div>
                <?php echo $this->BcForm->error('MailNgWordFilterConfig.target_form_ids') ?>
            </td>
        </tr>
    </table>
</div>

<!-- ボタンエリア -->
<div class="submit bca-actions">
    <div class="bca-actions__main">
        <?php echo $this->BcForm->submit(__d('baser_core', '保存'), [
            'div' => false,
            'class' => 'button bca-btn bca-actions__item',
            'data-bca-btn-type' => 'save',
            'data-bca-btn-size' => 'lg'
        ]) ?>
    </div>
</div>

<?php echo $this->BcForm->end() ?>
