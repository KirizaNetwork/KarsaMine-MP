<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 *
 *
 */

declare(strict_types=1);

namespace pocketmine\event\weather;

use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\event\Event;
use pocketmine\world\WeatherType;
use pocketmine\world\World;

class WeatherChangeEvent extends Event implements Cancellable{
	use CancellableTrait;

	public function __construct(
		private World $world,
		private WeatherType $oldWeather,
		private WeatherType $newWeather
	){}

	public function getWorld() : World{
		return $this->world;
	}

	public function getOldWeather() : WeatherType{
		return $this->oldWeather;
	}

	public function getNewWeather() : WeatherType{
		return $this->newWeather;
	}
}
