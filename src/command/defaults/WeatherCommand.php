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

namespace pocketmine\command\defaults;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\overload\BranchingOverloadBuilder;
use pocketmine\command\overload\IntRangeParameter;
use pocketmine\lang\KnownTranslationFactory;
use pocketmine\permission\DefaultPermissionNames;
use pocketmine\player\Player;
use pocketmine\utils\AssumptionFailedError;
use pocketmine\world\WeatherType;
use pocketmine\world\World;
use function max;
use function mt_rand;

final class WeatherCommand{
	private function __construct(){
		//NOOP
	}

	public static function create(string $namespace, string $name) : Command{
		$durationParameter = new IntRangeParameter("duration", "duration", 0, 1000000);
		return new Command(
			$namespace,
			$name,
			BranchingOverloadBuilder::make()
				->executor([], DefaultPermissionNames::COMMAND_WEATHER, self::getWeather(...))
				->executor(["clear", $durationParameter], DefaultPermissionNames::COMMAND_WEATHER, self::clearWeather(...))
				->executor(["rain", $durationParameter], DefaultPermissionNames::COMMAND_WEATHER, self::rainWeather(...))
				->executor(["thunder", $durationParameter], DefaultPermissionNames::COMMAND_WEATHER, self::thunderWeather(...))
				->build(),
			KnownTranslationFactory::pocketmine_command_weather_description(),
		);
	}

	private static function getWorld(CommandSender $sender) : World{
		if($sender instanceof Player){
			$world = $sender->getWorld();
		}else{
			$world = $sender->getServer()->getWorldManager()->getDefaultWorld();
		}

		return $world ?? throw new AssumptionFailedError("Failed to retrieve world instance. Default world is not loaded.");
	}

	private static function getWeather(CommandSender $sender) : void{
		$world = self::getWorld($sender);

		$current = $world->getWeather();
		$stateName = match($current){
			WeatherType::CLEAR => KnownTranslationFactory::commands_weather_query_clear(),
			WeatherType::RAIN => KnownTranslationFactory::commands_weather_query_rain(),
			WeatherType::THUNDER => KnownTranslationFactory::commands_weather_query_thunder(),
		};
		$sender->sendMessage(KnownTranslationFactory::commands_weather_query($stateName));
	}

	private static function clearWeather(CommandSender $sender, ?int $duration = null) : void{
		self::getWorld($sender)->setWeather(WeatherType::CLEAR, max(100, $duration ?? mt_rand(6000, 18000)));
		$sender->sendMessage(KnownTranslationFactory::commands_weather_clear());
	}

	private static function rainWeather(CommandSender $sender, ?int $duration = null) : void{
		self::getWorld($sender)->setWeather(WeatherType::RAIN, max(100, $duration ?? mt_rand(6000, 18000)));
		$sender->sendMessage(KnownTranslationFactory::commands_weather_rain());
	}

	private static function thunderWeather(CommandSender $sender, ?int $duration = null) : void{
		self::getWorld($sender)->setWeather(WeatherType::THUNDER, max(100, $duration ?? mt_rand(6000, 18000)));
		$sender->sendMessage(KnownTranslationFactory::commands_weather_thunder());
	}
}
