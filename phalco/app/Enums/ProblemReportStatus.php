<?php

namespace App\Enums;

enum ProblemReportStatus: int
{
    case NONE = 1;
    case NEED_CONTACT = 2;
    case IN_CONTACT = 3;
    case DONE = 4;

    public function text()
    {
        return match ($this) {
            self::NONE => '報告なし',
            self::NEED_CONTACT => '要対応',
            self::IN_CONTACT => '対応中',
            self::DONE => '対応完了',
        };
    }
}
