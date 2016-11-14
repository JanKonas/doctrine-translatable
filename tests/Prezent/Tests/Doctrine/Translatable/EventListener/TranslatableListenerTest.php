<?php

namespace Prezent\Tests\Doctrine\Translatable\Mapping;

use Prezent\Tests\Tool\ORMTestCase;

class TranslatableListenerTest extends ORMTestCase
{
    public function getFixtureClasses()
    {
        $fixtures = array(
            'Prezent\\Tests\\Fixture\\Basic',
            'Prezent\\Tests\\Fixture\\BasicTranslation',
            'Prezent\\Tests\\Fixture\\Mapped',
            'Prezent\\Tests\\Fixture\\MappedTranslation',
            'Prezent\\Tests\\Fixture\\Inherited',
            'Prezent\\Tests\\Fixture\\InheritedTranslation',
        );

        if (version_compare(PHP_VERSION, '5.4.0') >= 0) {
            $fixtures[] = 'Prezent\\Tests\\Fixture\\Mixin';
            $fixtures[] = 'Prezent\\Tests\\Fixture\\MixinTranslation';
        }

        return $fixtures;
    }

    public function getEntities()
    {
        return array(
            array('5.3.0', 'Prezent\\Tests\\Fixture\\Basic',     'Prezent\\Tests\\Fixture\\BasicTranslation'),
            array('5.3.0', 'Prezent\\Tests\\Fixture\\Mapped',    'Prezent\\Tests\\Fixture\\MappedTranslation'),
            array('5.3.0', 'Prezent\\Tests\\Fixture\\Inherited', 'Prezent\\Tests\\Fixture\\InheritedTranslation'),
            array('5.4.0', 'Prezent\\Tests\\Fixture\\Mixin',     'Prezent\\Tests\\Fixture\\MixinTranslation'),
        );
    }

    /**
     * @dataProvider getEntities
     */
    public function testPostLoad($version, $translatableClass, $translationClass)
    {
        if (version_compare(PHP_VERSION, $version) < 0) {
            $this->markTestSkipped('Traits require PHP 5.4');
        }

        // setup
        $em = $this->getEntityManager();
        $listener = $this->getTranslatableListener();
        $listener->setCurrentLocale('de')
                 ->setFallbackLocale('en');

        $en = new $translationClass();
        $en->setLocale('en')
           ->setName('foo');

        $de = new $translationClass();
        $de->setLocale('de')
           ->setName('bar');

        $entity = new $translatableClass();
        $entity->addTranslation($en)
               ->addTranslation($de);

        $em->persist($entity);
        $em->flush();
        $em->clear();
        // end setup

        $entity = $em->find($translatableClass, 1);

        $this->assertNotNull($entity);
        $this->assertEquals('de', $entity->currentLocale);
        $this->assertEquals('de', $entity->getTranslations()->get($entity->currentLocale)->getLocale());
        $this->assertEquals('en', $entity->fallbackLocale);
        $this->assertEquals('en', $entity->getTranslations()->get($entity->fallbackLocale)->getLocale());
    }

    /**
     * @dataProvider getEntities
     */
    public function testLocaleResolvers($version, $translatableClass, $translationClass)
    {
        if (version_compare(PHP_VERSION, $version) < 0) {
            $this->markTestSkipped('Traits require PHP 5.4');
        }

        // setup
        $em = $this->getEntityManager();
        $listener = $this->getTranslatableListener();

        $fallback = new $translationClass();
        $fallback->setLocale($this->getFallbackLocale())
           ->setName('foo');

        $current = new $translationClass();
        $current->setLocale($this->getCurrentLocale())
           ->setName('bar');

        $entity = new $translatableClass();
        $entity->addTranslation($fallback)
               ->addTranslation($current);

        $em->persist($entity);
        $em->flush();
        $em->clear();
        // end setup

        // test 1 - proper current and fallback locales
        $listener->setCurrentLocaleResolver(array($this, 'getCurrentLocale'))
            ->setFallbackLocaleResolver(array($this, 'getFallbackLocale'));
        $entity = $em->find($translatableClass, 1);

        $this->assertNotNull($entity);
        $this->assertEquals($this->getCurrentLocale(), $entity->currentLocale);
        $this->assertEquals($this->getCurrentLocale(), $entity->getTranslations()->get($entity->currentLocale)->getLocale());
        $this->assertEquals($this->getFallbackLocale(), $entity->fallbackLocale);
        $this->assertEquals($this->getFallbackLocale(), $entity->getTranslations()->get($entity->fallbackLocale)->getLocale());

        // test 2 - switched current and fallback locales
        $em->clear();
        $listener->setCurrentLocaleResolver(array($this, 'getFallbackLocale'))
            ->setFallbackLocaleResolver(array($this, 'getCurrentLocale'));
        $entity = $em->find($translatableClass, 1);

        $this->assertNotNull($entity);
        $this->assertEquals($this->getFallbackLocale(), $entity->currentLocale);
        $this->assertEquals($this->getFallbackLocale(), $entity->getTranslations()->get($entity->currentLocale)->getLocale());
        $this->assertEquals($this->getCurrentLocale(), $entity->fallbackLocale);
        $this->assertEquals($this->getCurrentLocale(), $entity->getTranslations()->get($entity->fallbackLocale)->getLocale());
    }

    public function testLocalePriorities()
    {
        $listener = $this->getTranslatableListener();

        $defaultLocale = 'default';
        $listener->setDefaultLocale($defaultLocale);
        $this->assertEquals($defaultLocale, $listener->getCurrentLocale());
        $this->assertEquals($defaultLocale, $listener->getFallbackLocale());

        $listener->setFallbackLocaleResolver(array($this, 'getFallbackLocale'));
        $this->assertEquals($defaultLocale, $listener->getCurrentLocale());
        $this->assertEquals($this->getFallbackLocale(), $listener->getFallbackLocale());

        $listener->setCurrentLocaleResolver(array($this, 'getCurrentLocale'));
        $this->assertEquals($this->getCurrentLocale(), $listener->getCurrentLocale());
        $this->assertEquals($this->getFallbackLocale(), $listener->getFallbackLocale());

        $fallbackLocale = 'fallback-top-prio';
        $listener->setFallbackLocale($fallbackLocale);
        $this->assertEquals($this->getCurrentLocale(), $listener->getCurrentLocale());
        $this->assertEquals($fallbackLocale, $listener->getFallbackLocale());

        $currentLocale = 'current-top-prio';
        $listener->setCurrentLocale($currentLocale);
        $this->assertEquals($currentLocale, $listener->getCurrentLocale());
        $this->assertEquals($fallbackLocale, $listener->getFallbackLocale());
    }

    public function getCurrentLocale()
    {
        return 'current';
    }

    public function getFallbackLocale()
    {
        return 'fallback';
    }
}
