<?php

namespace App\Services;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class TimeServices {
	public function getServerTime() {
		return new Carbon();
	}

	public function getUserTtu(int $pause) {
        $timeNow = $this->getServerTime();
		return $timeNow->addSeconds($pause);
    }

	public function getUserTtuFoTime(int $day, int $stageHour, int $stageMinute) {
		$carbonTimeNow = $this->getServerTime();
        return $carbonTimeNow->hour($stageHour)->minute($stageMinute)->second(0)->addDays($day);
	}

	public function checkUserRegisterTime(string | null $startHour, string | null $startMinute, string $userRegisterTime) {
		$timeNow = $this->getServerTime();
		if(!isset($startHour)) {
			return false;
			}
		$userRegTime = Carbon::parse($userRegisterTime);
		$userHour = $userRegTime->hour;
		$webTime = $timeNow->hour((int)$startHour)->minute((int)$startMinute)->second(0);
		return $userRegTime >= $webTime;
	}

	public function transformTimeToCarbon($time){
		$hours = (int)$time->hour;
		$minutes = (int)$time->minute;
		$timeNow = $this->getServerTime();
		return $timeNow->hours($hours)->minutes($minutes)->second(0);
	}

	public function transformToSeconds(int $hours, int $minutes, int $seconds){
		return ($hours * 3600) + ($minutes * 60) + $seconds;
	}
}