<?php

namespace Triangulum\Yii\Unit\Front\Items;

use Triangulum\Yii\Unit\Html\Button\Button;
use Triangulum\Yii\Unit\Html\Growl;
use Triangulum\Yii\Unit\Html\Panel\PanelBase;
use Yii;
use yii\base\Widget;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

class FrontSimple extends FrontItem
{
    public string     $actionDelete       = '';
    public string     $reloadGridId       = '';
    public string     $dataMapper         = "['id']";
    public string     $mainContainerClass = 'core-popup-window';
    public string     $legend             = '';
    public ?bool      $pjaxLinkSelector   = false;
    public ?PanelBase $panel              = null;
    public ?int       $pk                 = null;

    protected bool $hasError = true;

    public function __construct()
    {
        if (empty($this->panel)) {
            $this->panel = Yii::$app->panelBase;
        }
    }

    public static function builder(array $params): self
    {
        if (!isset($params['class'])) {
            $params['class'] = static::class;
        }

        return Yii::createObject($params);
    }

    public function exportViewSuccessData(): array
    {
        return [
            'title'     => $this->title,
            'msg'       => 'The operation was successful',
            'popup'     => $this,
            'hideModal' => 1,
        ];
    }

    public function setHasError(bool $state): self
    {
        $this->hasError = $state;

        return $this;
    }

    public function setPk(int $pk): self
    {
        $this->pk = $pk;

        return $this;
    }

    protected function getPkMap(): array
    {
        return empty($this->pk) ? [] : ['id' => $this->pk];
    }

    public function pjaxBegin(bool $hasError = null): void
    {
        if ($hasError === null) {
            $hasError = $this->hasError;
        }

        Pjax::begin($this->pjaxConfig($hasError));
    }

    protected function pjaxConfig(bool $hasErrors): array
    {
        return [
            'id'            => $this->pjaxId(),
            'formSelector'  => $hasErrors ? false : null,
            'linkSelector'  => $this->pjaxLinkSelector,
            'clientOptions' => ['skipOuterContainers' => true],
        ];
    }

    protected function pjaxId(): string
    {
        if (empty($this->pjaxId)) {
            $this->pjaxId = $this->containerClass() . '_pjax' . time();
        }

        return $this->pjaxId;
    }

    public function pjaxEnd(): void
    {
        Pjax::end();
    }

    public function registerPopup(View $view, int $dataMapTableRecord = 0): void
    {
        if ($this->isAllowed()) {
            $this->registerPopupAsset($view);
            $this->containerCreate();
            $view->registerJs($this->popupObserverInit($dataMapTableRecord));
        }
    }

    protected function registerPopupAsset(View $view): void
    {
        FrontPopupAsset::register($view);
    }

    protected function containerClass(): string
    {
        return $this->tag . '_popup';
    }

    protected function containerCreate(): void
    {
        $this->popupEmbed(
            $this->containerClass(),
            $this->contentId()
        );
    }

    protected function popupEmbed(string $modalClass, string $contentId): void
    {
        Modal::begin([
            'options' => ['class' => $this->mainContainerClass . ' fade ' . $modalClass],
            'size'    => Modal::SIZE_LARGE,
            'header'  => '<span class="modal-title panel-heading"></span>',
        ]);
        echo '<div id="' . $contentId . '" class="popover-content"></div>';
        Modal::end();
    }

    protected function contentId(): string
    {
        return $this->containerClass() . '_content_';
    }

    protected function popupObserverInit(int $dataMapTableRecord = 0): string
    {
        $modalClass = $this->containerClass();
        $contentId = $this->contentId();
        $clickClass = $this->clickClass();
        $url = $this->defineUrl();
        $dataMapper = $this->dataMapper;

        return <<<JS
        
PopUp().init({
    clickClass: "$clickClass",
    modalClass: "$modalClass",
    contentId: "$contentId",
    baseUrl: "$url",
    dataMapperModeTr: $dataMapTableRecord,
    dataMapper: $dataMapper,
    doubleClick: false
})

JS;
    }

    protected function defineUrl(string $pkAlias = 'id'): string
    {
        $route = $this->route();
        if (!empty($this->pk)) {
            $route = Yii::$app->getUrlManager()->createUrl([
                $route,
                $pkAlias => $this->pk,
            ]);
        }

        return $route;
    }

    public function clickClass(): string
    {
        return $this->tag . '_clk';
    }

    public function registerPopupDoubleClick(View $view, int $dataMapTableRecord = 0): void
    {
        /* Edit popup */
        if ($this->isAllowed()) {
            $this->registerPopupAsset($view);
            $this->containerCreate();
            $view->registerJs($this->popupObserverInitDoubleClick($dataMapTableRecord));
        }
    }

    protected function popupObserverInitDoubleClick(int $dataMapTableRecord = 0): string
    {
        $modalClass = $this->containerClass();
        $contentId = $this->contentId();
        $clickClass = $this->clickClass();
        $url = $this->defineUrl();
        $dataMapper = $this->dataMapper;

        return <<<JS
        
PopUp().init({
    clickClass: "$clickClass",
    modalClass: "$modalClass",
    contentId: "$contentId",
    baseUrl: "$url",
    doubleClick: true,
    dataMapperModeTr: $dataMapTableRecord,
    dataMapper: $dataMapper
})

JS;
    }

    public function clickClassPointer(): string
    {
        return $this->clickClass() . ' pointer';
    }

    public function htmlButton(string $title = 'Create', string $btnClass = Button::CSS_BTN_SCCS): string
    {
        return $this->isAllowed() ?
            Html::tag(
                'span',
                $title,
                [
                    'class' => [
                        $btnClass,
                        $this->clickClassPointer(),
                    ],
                ]
            ) : '';
    }

    public function reloadGrid(View $view, string $growlTitle, string $growlMsg = 'The operation was successful', bool $success = true): void
    {
        if ($success) {
            Growl::growlOk($growlTitle, $growlMsg);
        } else {
            Growl::growlError($growlTitle, $growlMsg);
        }

        if (!empty($this->reloadGridId)) {
            $js = <<<JS

CORE.refreshGrid('#{$this->reloadGridId}');

JS;

            $view->registerJs($js, View::POS_LOAD);
        }
    }

    public function hideAndReloadGrid(View $view, string $growlTitle, int $hide = 0, string $growlMsg = 'The operation was successful', bool $success = true): void
    {
        if (!$hide) {
            return;
        }

        $view->registerJs($this->popupHideEditForm(), View::POS_LOAD);
        $this->reloadGrid($view, $growlTitle, $growlMsg, $success);
    }

    public function hideOnSuccess(int $success, View $view, string $growlTitle, string $growlMsg = 'The operation was successful'): void
    {
        if (!$success) {
            return;
        }

        Growl::growlOk($growlTitle, $growlMsg);

        $view->registerJs($this->popupHideEditForm(), View::POS_LOAD);
    }

    public function hideOnError(View $view, string $growlTitle, string $growlMsg = 'Error'): void
    {
        Growl::growlError($growlTitle, $growlMsg);

        $view->registerJs($this->popupHideEditForm(), View::POS_LOAD);
    }

    public function popupHideEditForm(): string
    {
        $class = $this->mainContainerClass;

        return <<<JS
 
$(".$class").modal('hide');

JS;
    }

    public function formGetBegin()
    {
        $config = $this->formConfig($this->defineUrl());
        $config['method'] = 'GET';

        return ActiveForm::begin($config);
    }

    public function formPostBegin(string $alias = 'id')
    {
        $config = $this->formConfig($this->defineUrl());
        $config['method'] = 'POST';

        return ActiveForm::begin($config);
    }

    /**
     * @param string|null $action
     * @return Widget|ActiveForm
     */
    public function formBegin(string $action = null)
    {
        return ActiveForm::begin($this->formConfig($action));
    }

    public function formBeginMultiPart(string $action = null)
    {
        $config = $this->formConfig($action);
        $config['options']['enctype'] = 'multipart/form-data';

        return ActiveForm::begin($config);
    }

    protected function formConfig(string $action = null): array
    {
        $conf = [
            'id'      => $this->formId(),
            'options' => [
                'data-pjax' => 1,
                'class'     => $this->formClass(),
            ],
        ];

        if ($action) {
            $conf['action'] = $action;
        }

        return $conf;
    }

    public function formId(): string
    {
        return $this->containerClass() . '_form' . time();
    }

    public function formClass(): string
    {
        return $this->containerClass() . '_form';
    }

    public function formEnd(): void
    {
        ActiveForm::end();
    }

    protected function htmlButtonDelete(array $pKey = [], array $data = []): string
    {
        if (!$pKey || !$this->canDelete()) {
            return '';
        }

        $class = 'btn-danger btn-delete margin-10px';
        $action = 'delete';
        $title = "<span class='$class padding-lateral-5 uppercase '>$action !</span>";

        return Html::a(
            $action,
            array_merge([$this->actionDelete], $pKey),
            [
                'class' => "btn $class btn-xs",
                'data'  => array_merge(
                    [
                        'confirm' => 'Confirm' . ' ' . $title . ' ' . 'Are you sure?',
                        'pjax'    => true,
                        'method'  => 'post',
                    ],
                    $data
                ),
            ]
        );
    }

    protected function canDelete(): bool
    {
        return !empty($this->actionDelete);
    }

    public function panelBegin(string $title = '', bool $encode = true, bool $closeButton = true): void
    {
        echo $this->panel->begin(Html::encode(empty($title) ? $this->title : $title), $closeButton);

        if (!empty($this->pk)) {
            echo $this->htmlButtonDelete($this->getPkMap());
        }
    }

    public function panelEnd(): void
    {
        echo $this->panel->end();
    }

    public function hasLegend(): bool
    {
        return !empty($this->getLegend());
    }

    private function getLegend(): string
    {
        return (string)$this->legend;
    }

    public function setLegend(string $legend): void
    {
        $this->legend = $legend;
    }

    public function legendBegin(): string
    {
        if (!$this->hasLegend()) {
            return '';
        }

        return <<< HTML
<fieldset>
    <legend><span class="label label-form">{$this->getLegend()}</span></legend>

HTML;
    }

    public function legendEnd(): string
    {
        if (!$this->hasLegend()) {
            return '';
        }

        return <<< HTML

</fieldset>
HTML;
    }
}
