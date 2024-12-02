<?php

namespace LuqmanDv\Profile;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use jojoe77777\FormAPI\SimpleForm;

class Main extends PluginBase implements Listener {

    private string $dataFolder;

    public function onEnable(): void {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->dataFolder = $this->getDataFolder() . "players/";
        @mkdir($this->dataFolder);
    }

    public function onJoin(PlayerJoinEvent $event): void {
        $player = $event->getPlayer();
        $playerName = $player->getName();
        $filePath = $this->dataFolder . $playerName . ".json";

        if (!file_exists($filePath)) {
            $defaultData = [
                "name" => $playerName,
                "coin" => 0,
                "money" => 0
            ];
            file_put_contents($filePath, json_encode($defaultData, JSON_PRETTY_PRINT));
        }
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
        if ($command->getName() === "profile") {
            if ($sender instanceof Player) {
                $this->openProfile($sender);
            } else {
                $sender->sendMessage("This command can only be used in-game.");
            }
            return true;
        }
        return false;
    }

    private function openProfile(Player $player): void {
        $filePath = $this->dataFolder . $player->getName() . ".json";

        if (file_exists($filePath)) {
            $data = json_decode(file_get_contents($filePath), true);

            $form = new SimpleForm(function (Player $player, ?int $data) {
            });

            $form->setTitle($data["name"] . "'s Profile");
            $form->setContent("Coin: " . $data["coin"] . "\nMoney: " . $data["money"]);
            $form->addButton("Close");

            $player->sendForm($form);
        } else {
            $player->sendMessage("Profile data not found.");
        }
    }
}
