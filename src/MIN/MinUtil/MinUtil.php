<?php

declare(strict_types=1);

namespace MIN\MinUtil;

use pocketmine\item\Item;
use pocketmine\nbt\BigEndianNbtSerializer;
use pocketmine\nbt\TreeRoot;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\world\Position;
use function base64_decode;
use function base64_encode;
use function explode;
use function time;

final class MinUtil
{
	public const FormPrefix = '§r§l§a▶ §r§f';

	public const ChatPrefix = '§r§l§a• §r';

	private static array $cool = [];

	public static function addCoolTime(Player $player, string $name): void
	{
		self::$cool[$player->getName()][$name] = time();
	}

	public static function positionHash(Position $pos): string
	{
		return $pos->getWorld()->getFolderName() . ':' . $pos->getX() . ':' . $pos->getY() . ':' . $pos->getZ();
	}

	public static function getPosition(string $position): Position
	{
		$position = explode(':', $position);
		return new Position(
			(float)$position[1],
			(float)$position[2],
			(float)$position[3],
			Server::getInstance()->getWorldManager()->getWorldByName($position[0])
		);
	}

	public static function isCoolTime(Player $player, string $name, int $time): bool
	{
		if (isset(self::$cool[$player->getName()][$name])) {
			return !(time() - self::$cool[$player->getName()][$name] > $time);
		}
		return false;
	}

	public static function makeButton(string $title, string $subtitle): array
	{
		return ['text' => "§l$title\n§r§8▶ $subtitle §r§8◀"];
	}

	public static function makeInput(string $text, string $placeholder = '', string $default = ''): array
	{
		return ['type' => 'input', 'text' => MinUtil::FormPrefix . $text, 'placeholder' => $placeholder, 'default' => $default];
	}

	public static function makeDrowdown(string $text, array $options, int $default = 0): array
	{
		return ['type' => 'dropdown', 'text' => MinUtil::FormPrefix . $text, 'options' => $options, 'default' => $default];
	}

	public static function makeToggle(string $text, bool $default = false): array
	{
		return ['type' => 'toggle', 'text' => MinUtil::FormPrefix . $text, 'default' => $default];
	}

	public static function ItemDataSerialize(Item $data): string
	{
		return base64_encode((new BigEndianNbtSerializer())->write(new TreeRoot($data->nbtSerialize())));
	}

	public static function ItemDataDeserialize(string $data): Item
	{
		return Item::nbtDeserialize((new BigEndianNbtSerializer())->read(base64_decode($data))->mustGetCompoundTag());
	}
}