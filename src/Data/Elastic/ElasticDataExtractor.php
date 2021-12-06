<?php

namespace Triangulum\Yii\Unit\Data\Elastic;

use Carbon\Carbon;
use DateTimeZone;
use Yii;

final class ElasticDataExtractor
{
    private ?string $timeZone    = null;
    private string  $sourceIndex = '_source';
    private string  $hitIndex    = 'hits';

    public static function build(string $timeZone = null): self
    {
        return Yii::createObject(
            self::class,
            ['timeZone' => empty($timeZone) ? Yii::$app->timeZone : $timeZone]
        );
    }

    public function __construct(string $timeZone)
    {
        $this->timeZone = $timeZone;
    }

    /**
     * @param string $attr
     * @param array  $data
     * @return null|mixed
     */
    public function row(string $attr, array $data)
    {
        return $data[$this->sourceIndex][$attr] ?? null;
    }

    public function dateTime(string $attr, array $data): ?Carbon
    {
        if ($dateTime = $this->row($attr, $data)) {
            return Carbon::createFromFormat('Y-m-d\TH:i:s.u\Z', $dateTime, 'UTC')
                ->setTimezone(new DateTimeZone($this->timeZone));
        }

        return null;
    }

    public function timestamp(string $attr, array $data): ?Carbon
    {
        if ($timestamp = $this->row($attr, $data)) {
            return Carbon::createFromTimestampUTC($timestamp)
                ->setTimezone(new DateTimeZone($this->timeZone));
        }

        return null;
    }

    public function timestampMs(string $attr, array $data): ?Carbon
    {
        if ($timestamp = $this->row($attr, $data)) {
            return Carbon::createFromTimestampMs(
                $timestamp,
                new DateTimeZone($this->timeZone)
            );
        }

        return null;
    }

    public function source(array $hitSource): array
    {
        return $hitSource[$this->hitIndex] ?? [];
    }

    public function hitsHits(array $data): array
    {
        return $data[$this->hitIndex][$this->hitIndex] ?? [];
    }
}
