<?php

/*
 * (c) Prezent Internet B.V. <info@prezent.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prezent\Doctrine\Translatable\Entity;

use Doctrine\ORM\Mapping as ORM;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Prezent\Doctrine\Translatable\TranslationInterface;

trait TranslatableTrait
{
    /**
     * Get the translations
     */
    public function getTranslations(): array
    {
        return $this->translations->toArray();
    }
    
    /**
     * Add a translation
     */
    public function addTranslation(TranslationInterface $translation): void
    {
        if (!$this->translations->contains($translation)) {
            $this->translations[$translation->getLocale()] = $translation;
            $translation->setTranslatable($this);
        }
    }
    
    /**
     * Remove a translation
     */
    public function removeTranslation(TranslationInterface $translation): void
    {
        if ($this->translations->removeElement($translation)) {
            $translation->setTranslatable(null);
        }
    }
}
