<?php

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