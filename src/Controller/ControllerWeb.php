<?php

namespace Triangulum\Yii\Unit\Controller;

use Throwable;
use Triangulum\Yii\Unit\Admittance\RouteBase;
use Triangulum\Yii\Unit\Front\Front;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class ControllerWeb extends Controller
{
    protected string $uri                   = '';
    protected array  $csrfValidationExclude = [];
    protected array  $jsonResponse          = [];
    protected array  $accessRules           = [];
    protected array  $verbActions           = [];
    protected ?Front $front                 = null;
    protected string $frontClass            = '';

    public function init()
    {
        parent::init();
        if (!empty($this->frontClass)) {
            /** @var Front $frontClass */
            $frontClass = $this->frontClass;
            $this->front = $frontClass::build();
        }
    }

    /**
     * @throws BadRequestHttpException
     */
    public function beforeAction($action): bool
    {
        if (isset($this->jsonResponse[$action->id])) {
            Yii::$app->response->format = Response::FORMAT_JSON;
        }

        if (isset($this->csrfValidationExclude[$action->id])) {
            Yii::$app->controller->enableCsrfValidation = false;
        }

        if (!$this->accessRules) {
            $this->accessRules = [$this->accessRuleDefault()];
        }

        if (!$this->verbActions) {
            $this->verbActions = $this->verbActionsCrudDefault();
        }

        $this->uri = $action->controller->id . '-' . $action->id;

        return parent::beforeAction($action);
    }

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => $this->accessRules,
            ],
            'verbs'  => [
                'class'   => VerbFilter::class,
                'actions' => $this->verbActions,
            ],
        ];
    }

    protected function accessRuleDefault(array $actionList = []): array
    {
        if (empty($ipWhite = Yii::$app->params['App.IpWhiteList'] ?? [])) {
            $default = [
                'allow' => true,
                'roles' => ['@'],
            ];
        } else {
            $default = [
                'allow' => true,
                'ips'   => $ipWhite,
            ];
        }

        if ($actionList) {
            $default['actions'] = $actionList;
        }

        return $default;
    }

    protected function verbActionsCrudDefault(): array
    {
        return [
            RouteBase::ACTION_DELETE => ['POST'],
        ];
    }

    protected function getUri(): string
    {
        return $this->uri;
    }

    protected function renderThrowable(Throwable $t): string
    {
        Yii::error([$t->getMessage(), $t->getTraceAsString()]);
        $viewPath = Yii::$app->params['App.UI.sys.template_throwable'];
        $data = [
            'error' => YII_DEBUG ? $t->getMessage() : 'Internal error',
            'trace' => YII_DEBUG ? $t->getTraceAsString() : '',
        ];

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax($viewPath, $data);
        }

        return $this->render($viewPath, $data);
    }

    protected function renderViewAction(array $data): string
    {
        return $this->renderAjax(
            Yii::$app->params['App.UI.sys.template_view'],
            $data
        );
    }

    protected function renderNotifyAction(array $data): string
    {
        return $this->renderAjax(
            Yii::$app->params['App.UI.sys.template_notify'],
            $data
        );
    }

    protected function renderDeleteAction(array $data): string
    {
        return $this->renderAjax(
            Yii::$app->params['App.UI.sys.template_delete'],
            $data
        );
    }

//    protected function renderFormTabs(Tabs $formTabs): string
//    {
//        return $this->renderAjax(
//            Yii::$app->params['App.UI.sys.template_form_tabs'],
//            [
//                'tabs' => $formTabs,
//            ]
//        );
//    }

    /**
     * @param string $msg
     * @throws NotFoundHttpException
     */
    protected function notFoundHttpException(string $msg = 'The requested resource does not exist.'): void
    {
        throw new NotFoundHttpException($msg);
    }

    protected function isPost(): bool
    {
        return Yii::$app->request->isPost;
    }

    protected function getPost(): array
    {
        return Yii::$app->request->post();
    }
}
