<?php

/*
 *   Copyright (C) 2022  Cosmic5173
 *
 *   This program is free software: you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation, either version 3 of the License, or
 *   (at your option) any later version.
 *
 *   This program is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with this program.  If not, see <https://www.gnu.org/licenses/>.
 *
 *
 *   ----------------------------------------------------------------------
 *
 *
 *   Discord: Cosmic#8011
 *   Email: contact@cosmic5173.com
 *   Website: https://www.cosmic5173.com
 *   GitHub: https://github.cosmic5173.com
 *   Community Server: https://discord.cosmic5173.com
 *
 *   Thank you for using my work, I really do appreciate it!
 */

namespace Cosmic5173\MessageFilter;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;

final class Filter extends PluginBase {

    private static Filter|null $instance = null;

    /** @var bool */
    private bool $registered = false;

    private array $bannedWords;
    private array $bannedPhrases;
    private array $websiteComponents;
    private array $specialCharacters;

    public static function getInstance(): self {
        return self::$instance;
    }

    protected function onLoad(): void {
        self::$instance = $this;
    }

    public function register(array $bannedWords, array $bannedPhrases, array $websiteComponents, array $specialCharacters) {
        if(!$this->isRegistered()) {
            $this->bannedWords = $bannedWords;
            $this->bannedPhrases = $bannedPhrases;
            $this->websiteComponents = array_merge($this->getDefaultWebsiteComponents(), $websiteComponents);
            $this->specialCharacters = array_merge($this->getDefaultSpecialCharacters(), $specialCharacters);
        } else {
            throw new \RuntimeException("Library is already registered.");
        }
    }

    /**
     * Check to see of Loader::register() has been called.
     * @return bool
     */
    public function isRegistered(): bool {
        return $this->registered;
    }

    public function includesBannedWord(string $message): bool {
        $message = $this->cleanMessage($message);
        foreach ($this->bannedWords as $word) {
            if (str_contains($message, $word)) return true;
        }

        return false;
    }

    public function includesBannedPhrase(string $message): bool {
        $message = $this->cleanMessage($message);
        foreach ($this->bannedPhrases as $phrase) {
            if(str_contains($message, trim(str_replace(" ", "", $phrase)))) return true;
        }

        return false;
    }

    public function includesLink(string $message): bool {
        $message = $this->cleanMessage($message, false);
        foreach ($this->websiteComponents as $component) {
            if (str_contains($message, $component)) return true;
        }

        return false;
    }

    public function isMessageClean(string $message): bool {
        return !$this->includesBannedWord($message) && !$this->includesBannedPhrase($message) && !$this->includesLink($message);
    }

    private function cleanMessage(string $message, bool $removeSpecialCharacters = true): bool {
        $message = trim(str_replace(" ", "", TextFormat::clean($message)));
        if ($removeSpecialCharacters)
            foreach ($this->specialCharacters as $character) {
                $message = str_replace($character, "", $message);
            }

        return $message;
    }

    /**
     * @return array
     */
    public function getBannedWords(): array {
        return $this->bannedWords;
    }

    /**
     * @param array $bannedWords
     */
    public function setBannedWords(array $bannedWords): void {
        $this->bannedWords = $bannedWords;
    }

    /**
     * @return array
     */
    public function getBannedPhrases(): array {
        return $this->bannedPhrases;
    }

    /**
     * @param array $bannedPhrases
     */
    public function setBannedPhrases(array $bannedPhrases): void {
        $this->bannedPhrases = $bannedPhrases;
    }

    /**
     * @return array
     */
    public function getWebsiteComponents(): array {
        return $this->websiteComponents;
    }

    /**
     * @param array $websiteComponents
     */
    public function setWebsiteComponents(array $websiteComponents): void {
        $this->websiteComponents = $websiteComponents;
    }

    /**
     * @return array
     */
    public function getSpecialCharacters(): array {
        return $this->specialCharacters;
    }

    /**
     * @param array $specialCharacters
     */
    public function setSpecialCharacters(array $specialCharacters): void {
        $this->specialCharacters = $specialCharacters;
    }

    public function getDefaultWebsiteComponents(): array {
        return [];
    }

    public function getDefaultSpecialCharacters(): array {
        return [];
    }
}