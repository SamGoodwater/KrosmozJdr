<?php

declare(strict_types=1);

namespace App\Console\Concerns;

use App\Console\YesNoFlags;

/**
 * Confirmations `-y` / `--yes` (accepter) et `--no` (refuser) sur les commandes projet.
 *
 * `-n` reste `--no-interaction` (Symfony) : sans `-y`, IDE Helper n’écrase pas les modèles.
 *
 * @example $this->confirmOrFlag('Continuer ?', false)
 */
trait AcceptsYesNoFlags
{
    protected function abortIfConflictingYesNoFlags(): bool
    {
        if (! YesNoFlags::isConflicting($this)) {
            return false;
        }

        $this->error(YesNoFlags::CONFLICT_MESSAGE);

        return true;
    }

    protected function wantsYes(): bool
    {
        return YesNoFlags::wantsYes($this);
    }

    protected function wantsNo(): bool
    {
        return YesNoFlags::wantsNo($this);
    }

    /**
     * true si stdin non interactif ou `--no-interaction` / `-n`.
     */
    public function isNonInteractiveInput(): bool
    {
        return (bool) $this->option('no-interaction') || ! $this->input->isInteractive();
    }

    /**
     * Résout une confirmation yes/no.
     *
     * @param  bool  $default  Valeur si non interactif et ni `-y` ni `--no`
     */
    protected function confirmOrFlag(string $question, bool $default = false): bool
    {
        if ($this->wantsYes()) {
            return true;
        }

        if ($this->wantsNo()) {
            return false;
        }

        if ($this->isNonInteractiveInput()) {
            return $default;
        }

        return $this->confirm($question, $default);
    }

    /**
     * @return array<string, bool>
     */
    protected function yesNoCallOptions(): array
    {
        return YesNoFlags::callOptions($this);
    }

    /**
     * @return array<string, bool>
     */
    public function ideHelperModelsArguments(): array
    {
        return YesNoFlags::ideHelperModelsArguments(
            $this->wantsYes(),
            $this->wantsNo(),
            $this->isNonInteractiveInput()
        );
    }
}
