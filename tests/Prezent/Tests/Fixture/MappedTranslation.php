<?php

namespace Prezent\Tests\Fixture;

use Doctrine\ORM\Mapping as ORM;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Prezent\Doctrine\Translatable\TranslatableInterface;
use Prezent\Doctrine\Translatable\TranslationInterface;

/**
 * @ORM\Entity
 */
class MappedTranslation implements TranslationInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="id", type="integer")
     */
    private $id;

    /**
     * @Prezent\Translatable(targetEntity="Prezent\Tests\Fixture\Mapped")
     */
    private $translatable;

    /**
     * @Prezent\Locale
     */
    private $locale;

    /**
     * @ORM\Column(name="name", type="string")
     */
    private $name;

    public function getId()
    {
        return $this->id;
    }

    public function getTranslatable(): TranslatableInterface
    {
        return $this->translatable;
    }
    
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

    public function getLocale(): string
    {
        return $this->locale;
    }
    
    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }

    public function getName()
    {
        return $this->name;
    }
    
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
}
