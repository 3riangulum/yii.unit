<?php

namespace Triangulum\Yii\Unit\Data\Validator;

use Carbon\Carbon;

trait ValidatorDateTime
{
    public function validatorDateTimeNotLessThenNow($attribute, $params = ['dateTimeFormat' => 'Y-m-d H:i:s']): void
    {
        $future = Carbon::createFromFormat($params['dateTimeFormat'], $this->{$attribute});
        if ($future->lt(Carbon::now())) {
            $this->addError($attribute, 'Can`t be less then now');
        }
    }

    public function validatorDateNotLessThenNow($attribute, $params = null): void
    {
        $future = Carbon::createFromFormat($params['dateTimeFormat'], $this->{$attribute})->endOfDay();
        if ($future->lt(Carbon::now()->endOfDay())) {
            $this->addError($attribute, 'Can`t be less then now');
        }
    }

    public function filterDateTimeStartOfMinute($attribute, $params = ['dateTimeFormat' => 'Y-m-d H:i:s']): void
    {
        $this->{$attribute} = Carbon::createFromFormat($params['dateTimeFormat'], $this->{$attribute})
            ->startOfMinute()
            ->format($params['dateTimeFormat']);
    }
}
