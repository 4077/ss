<?php namespace ss\multisource\ui\division\importersControls\detectorMap;

class ValueSvc
{
    public function getContent($value)
    {
        if ($value == '~e') {
            return ['<div class="label">Пустое</div>', $value];
        }

        if ($value == '~ne') {
            return ['<div class="label">Не пустое</div>', $value];
        }

        return [str_replace([' ', "\n"], ['&#9679;', '&crarr;'], $value), $value];
    }

    public function getClass($value)
    {
        if ($value == '~e') {
            return 'require_empty';
        }

        if ($value == '~ne') {
            return 'require_not_empty';
        }
    }

    public function parseValue($value)
    {
        return $value;
    }
}
