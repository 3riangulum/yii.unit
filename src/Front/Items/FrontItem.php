<?php

namespace Triangulum\Yii\Unit\Front\Items;

class FrontItem
{
    public string $tag         = '';
    public string $route       = '';
    public bool   $allowAction = false;
    public string $title       = '';

    protected string $pjaxId = '';

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function extractAction(): string
    {
        $list = explode('/', $this->route());

        return array_pop($list);
    }

    protected function route(): string
    {
        return $this->route;
    }

    public function isAllowed(): bool
    {
        return $this->allowAction;
    }
}
