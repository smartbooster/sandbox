<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Smart\AuthenticationBundle\DataFixtures\AbstractFixtures;

/**
 * Nicolas Bastien <nicolas.bastien@smartbooster.io>
 */
class FakeFixtures extends AbstractFixtures implements FixtureGroupInterface
{
    /**
     * @return void
     */
    public function load(ObjectManager $manager)
    {
        $this->fixturesDir = $this->getFixturesDir() . 'fake';

        $this->getLoader()->load($this->getFiles());
    }

    /**
     * @return array<string>
     */
    protected function getFiles()
    {
        $pattern = $this->fixturesDir . '/%s.yml';

        return array_reverse([
            sprintf($pattern, 'administrator'),
        ]);
    }

    /**
     * @return array<string>
     */
    public static function getGroups(): array
    {
        return ['fake'];
    }
}
