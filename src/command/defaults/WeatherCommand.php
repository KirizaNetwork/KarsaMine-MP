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

use pocketmine\command\CommandSender;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\lang\KnownTranslationFactory;
use pocketmine\permission\DefaultPermissionNames;
use pocketmine\player\Player;
use pocketmine\utils\AssumptionFailedError;
use pocketmine\world\WeatherType;
use function count;
use function max;
use function strtolower;

class WeatherCommand extends VanillaCommand{

	public function __construct(string $namespace, string $name){
		parent::__construct(
			$namespace,
			$name,
			KnownTranslationFactory::pocketmine_command_weather_description(),
			KnownTranslationFactory::pocketmine_command_weather_usage(),
		);
		$this->setPermission(DefaultPermissionNames::COMMAND_WEATHER);
	}

	public function execute(CommandSender $sender, string $label, array $args) : void{
		if($sender instanceof Player){
			$world = $sender->getWorld();
		}else{
			$world = $sender->getServer()->getWorldManager()->getDefaultWorld();
		}

		if($world === null){
			throw new AssumptionFailedError("Failed to retrieve world instance. Default world is not loaded.");
		}

		if(count($args) < 1){
			$current = $world->getWeather();
			$stateName = match($current){
				WeatherType::CLEAR => KnownTranslationFactory::commands_weather_query_clear(),
				WeatherType::RAIN => KnownTranslationFactory::commands_weather_query_rain(),
				WeatherType::THUNDER => KnownTranslationFactory::commands_weather_query_thunder(),
			};
			$sender->sendMessage(KnownTranslationFactory::commands_weather_query($stateName));
			return;
		}

		$type = match(strtolower($args[0])){
			"clear" => WeatherType::CLEAR,
			"rain" => WeatherType::RAIN,
			"thunder" => WeatherType::THUNDER,
			default => throw new InvalidCommandSyntaxException(),
		};

		$duration = isset($args[1]) ? max(100, (int) $args[1]) : 6000;
		$world->setWeather($type, $duration);
		$sender->sendMessage(match($type){
			WeatherType::CLEAR => KnownTranslationFactory::commands_weather_clear(),
			WeatherType::RAIN => KnownTranslationFactory::commands_weather_rain(),
			WeatherType::THUNDER => KnownTranslationFactory::commands_weather_thunder(),
		});
	}
}
