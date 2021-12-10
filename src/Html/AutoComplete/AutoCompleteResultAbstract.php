<?php

namespace Triangulum\Yii\Unit\Html\AutoComplete;

use Yii;
use yii\helpers\HtmlPurifier;

abstract class AutoCompleteResultAbstract
{
    /**
     * @var mixed
     */
    public $pk = null;

    public string $term  = '';
    public bool   $valid = false;
    public int    $limit = 30;

    public function __construct()
    {
        $this->term = $this->stringPurify($this->term);
    }

    public static function build(array $config): self
    {
        if (empty($config['class'])) {
            $config['class'] = static::class;
        }

        return Yii::createObject($config);
    }

    abstract public function search(): array;

    abstract public function label($data): string;

    abstract public function loadSelected(): ?string;

    abstract protected function decorate(array $list): array;

    /**
     * @param mixed $id
     * @param mixed $text
     * @return array
     */
    protected static function content($id, $text): array
    {
        return ['id' => $id, 'text' => $text];
    }

    protected function contentEmpty(): array
    {
        return $this->content('', '');
    }

    protected function result(array $content, string $index = 'results'): array
    {
        return [$index => $content];
    }

    protected function resultEmpty(): array
    {
        return $this->result($this->contentEmpty());
    }

    protected function stringPurify(string $value): string
    {
        return trim(HtmlPurifier::process($value));
    }

    protected function isValid(): bool
    {
        return $this->valid;
    }
}
