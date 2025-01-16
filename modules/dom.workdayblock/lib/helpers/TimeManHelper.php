<?php

namespace DomDigital\WorkDayBlock\Helpers;

use CTimeManUser;

final class TimeManHelper
{

    public function getUserStatus(int $userId): string
    {
        $timeManUser = new CTimeManUser($userId);

        return $this->parseStatus(timeManUser: $timeManUser);
    }


    private function parseStatus(CTimeManUser $timeManUser): string
    {
        if ($timeManUser->isDayExpired()) {
            return 'dayExpired';
        }

        $currentInfo = $timeManUser->GetCurrentInfo();

        if ($currentInfo === false || !$timeManUser->isDayOpenedToday()) {
            return 'dayNotStarted';
        }

        return match ($currentInfo['CURRENT_STATUS']) {
            'OPENED' => 'dayOpened',
            'PAUSED' => 'dayPaused',
            'CLOSED' => 'dayClosed',
            default => 'unknown',
        };
    }


}
