<?php

/*
 * (c) Prezent Internet B.V. <info@prezent.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prezent\Doctrine\Translatable;

/**
 * Interface for translatable entities
 */
interface TranslatableInterface
{
    /**
     * Get all translations
     */
    public function getTranslations(): array;

    /**
     * Add a new translation
     */
    public function addTranslation(TranslationInterface $translation): void;

    /**
     * Remove a translation
     */
    public function removeTranslation(TranslationInterface $translation): void;
}
