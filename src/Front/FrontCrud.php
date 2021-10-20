<?php

namespace Triangulum\Yii\Unit\Front;

use Triangulum\Yii\Unit\Admittance\RouteBase;
use Triangulum\Yii\Unit\Data\Db\Entity;
use Triangulum\Yii\Unit\Front\Items\FrontGrid;
use Triangulum\Yii\Unit\Front\Items\FrontSimple;

/**
 * @method static self build
 */
abstract class FrontCrud extends FrontBase
{
    protected string $title           = '';
    protected string $titleGrid       = '';
    protected string $titleEditor     = '';
    protected string $titleCreator    = '';
    protected string $titleDuplicator = '';
    protected string $titleEraser     = '';

    private ?FrontSimple $creator    = null;
    private ?FrontSimple $editor     = null;
    private ?FrontSimple $viewer     = null;
    private ?FrontSimple $duplicator = null;
    private ?FrontSimple $eraser     = null;

    public function __construct(RouteBase $router)
    {
        parent::__construct($router);

        $this->loadDefaultActionTitles();

        $cfg = FrontConfig::build([
            'router'       => $router,
            'gridClass'    => $this->gridClass,
            'delete'       => RouteBase::ACTION_DELETE,
            'deleteAction' => $router->isAllowed(RouteBase::ACTION_DELETE) ?
                $router->route(RouteBase::ACTION_DELETE) : '',
        ]);

        $this->actionConfig = $cfg
            ->buildGrid(self::ALIAS_GRID, RouteBase::ACTION_INDEX, $this->titleGrid)
            ->buildPopup(self::ALIAS_EDITOR, RouteBase::ACTION_EDIT)
            ->buildPopup(self::ALIAS_CREATOR, RouteBase::ACTION_CREATE, true, false)
            ->buildPopup(self::ALIAS_DUPLICATOR, RouteBase::ACTION_DUPLICATE, true, false)
            ->buildPopup(self::ALIAS_VIEWER, RouteBase::ACTION_VIEW, false, false)
            ->buildPopup(self::ALIAS_ERASER, RouteBase::ACTION_DELETE)
            ->export();
    }

    public function grid(array $searchParams = []): FrontGrid
    {
        $grid = FrontGrid::build($this->actionConfig()[self::ALIAS_GRID]);

        if ($this->editor()->isAllowed()) {
            $grid->clickEventSet($this->editor(), self::ALIAS_EDITOR);
        } elseif ($this->viewer()->isAllowed()) {
            $grid->clickEventSet($this->viewer(), self::ALIAS_VIEWER);
        }

        if ($this->duplicator()->isAllowed()) {
            $grid->clickEventSet($this->duplicator(), self::ALIAS_DUPLICATOR);
            $grid->actionColumnSet([$this->duplicator()]);
        }

        $dataExplorer = $this->loadDataExplorer();
        $grid->dataProviderSet($dataExplorer->search($searchParams));
        if ($this->gridFilterEnable) {
            $grid->searchModelSet($dataExplorer);
        }

        if (!empty($grid->sortableAction) && $this->router->isAllowed($grid->sortableAction)) {
            $grid->sortableAction = $this->router->route($grid->sortableAction);
        }

        return $grid;
    }

    private function loadDefaultActionTitles(): void
    {
        $this->titleGrid = $this->title;
        $this->titleCreator = 'Creation.' . $this->title;
        $this->titleEditor = 'Redaction.' . $this->title;
        $this->titleDuplicator = 'Duplication.' . $this->title;
        $this->titleEraser = 'Deletion.' . $this->title;
    }

    public function viewer(): FrontSimple
    {
        if (null === $this->viewer) {
            $this->viewer = $this
                ->popupLoad(self::ALIAS_VIEWER)
                ->setTitle($this->title);
        }

        return $this->viewer;
    }

    public function editor(Entity $entity = null): FrontSimple
    {
        if (null === $this->editor) {
            $this->editor = $this
                ->popupSetup(self::ALIAS_EDITOR, $entity)
                ->setTitle($this->titleEditor);
        }

        return $this->editor;
    }

    public function creator(Entity $entity = null): FrontSimple
    {
        if (null === $this->creator) {
            $this->creator = $this
                ->popupSetup(self::ALIAS_CREATOR, $entity)
                ->setTitle($this->titleCreator);
        }

        return $this->creator;
    }

    public function eraser(): FrontSimple
    {
        if (null === $this->eraser) {
            $this->eraser = $this
                ->popupSetup(self::ALIAS_ERASER)
                ->setTitle($this->titleEraser);
        }

        return $this->eraser;
    }

    public function duplicator(Entity $entity = null): FrontSimple
    {
        if (null === $this->duplicator) {
            $this->duplicator = $this
                ->popupSetup(self::ALIAS_DUPLICATOR, $entity)
                ->setTitle($this->titleDuplicator);
        }

        return $this->duplicator;
    }

    private function popupSetup(string $alias, Entity $entity = null): FrontSimple
    {
        $element = $this->popupLoad($alias);
        if ($entity && null !== $entity) {
            $element
                ->setPk($entity->pkGet())
                ->setHasError($entity->hasErrors());
        }

        return $element;
    }
}
