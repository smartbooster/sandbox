<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Nelmio\Alice\Fixtures;
use Nelmio\Alice\ProcessorInterface;
use Smart\AuthenticationBundle\DataFixtures\Processor\UserProcessor;
use Symfony\Bridge\Doctrine\Tests\Fixtures\ContainerAwareFixture;

/**
 * @author Nicolas Bastien <nicolas.bastien@smartbooster.io>
 */
class Loader extends ContainerAwareFixture implements FixtureInterface
{
    /**
     * @var string
     */
    private $fixturesDir;

    /**
     * @param ObjectManager $manager
     *
     * @return void
     */
    public function load(ObjectManager $manager)
    {
        $this->fixturesDir = $this->container->getParameter('kernel.root_dir') . '/fixtures';

        Fixtures::load($this->getFiles(), $manager, [], $this->getProcessors());
    }

    /**
     * @return array<string>
     */
    protected function getFiles()
    {
        $pattern = $this->fixturesDir . '/%s.yml';

        return [
            sprintf($pattern, 'administrator')
        ];
    }

    /**
     * @return array<ProcessorInterface>
     */
    protected function getProcessors()
    {
        return [
            new UserProcessor($this->container->get('security.password_encoder')),
        ];
    }
}
