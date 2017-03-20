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
use Prezent\Doctrine\Translatable\TranslatableInterface;

trait TranslationTrait
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="id", type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(name="locale", type="string")
     * @Prezent\Locale
     */
    protected $locale;

    /**
     * Get the ID
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the translatable object
     *
     * @return TranslatableInterface
     */
    public function getTranslatable(): TranslatableInterface
    {
        return $this->translatable;
    }
    
    /**
     * Set the translatable object
     */
    public function setTranslatable(TranslatableInterface $translatable = null): void
    {
        if ($this->translatable == $translatable) {
            return;
        }
    
        $old = $this->translatable;
        $this->translatable = $translatable;
    
        if ($old !== null) {
            $old->removeTranslation($this);
        }
    
        if ($translatable !== null) {
            $translatable->addTranslation($this);
        }
    }

    /**
     * Get the locale
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * Set the locale
     */
    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }
}
