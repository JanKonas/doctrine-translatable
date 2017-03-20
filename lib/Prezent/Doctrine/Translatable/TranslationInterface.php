<?php

/*
 * (c) Prezent Internet B.V. <info@prezent.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prezent\Doctrine\Translatable;

/**
 * Interface for translation entities
 */
interface TranslationInterface
{
    /**
     * Get the translatable object
     */
    public function getTranslatable(): TranslatableInterface;

    /**
     * Set the translatable object
     */
    public function setTranslatable(TranslatableInterface $translatable = null): void;

    /**
     * Get the locale
     */
    public function getLocale(): string;

    /**
     * Set the locale
     */
    public function setLocale(string $locale): void;
}
