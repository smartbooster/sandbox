<?php

namespace AppBundle\Command;

use AppBundle\Entity\User\Administrator;
use Smart\EtlBundle\Extractor\YamlEntityExtractor;
use Smart\EtlBundle\Loader\DoctrineInsertUpdateLoader;
use Smart\EtlBundle\Transformer\CallbackTransformer;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Nicolas Bastien <nicolas.bastien@smartbooster.io>
 */
class LoadDataCommand extends ContainerAwareCommand
{
    /**
     * @var YamlEntityExtractor
     */
    protected $extractor;

    /**
     * @var DoctrineInsertUpdateLoader
     */
    protected $loader;

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('app:data:load')
            ->setDescription('Example for loading data with etl-bundle.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $this->extractor = new YamlEntityExtractor();
        $this->extractor->setFolderToExtract(
            $this->getContainer()->get('kernel')->getRootDir() . '/../etl'
        );
        $this->extractor->addEntityToProcess(
            'administrator',
            Administrator::class,
            function ($e) {
                return $e->getImportId();
            }
        );

        $em = $this->getContainer()->get('doctrine')->getManager('default');
        $this->loader = new DoctrineInsertUpdateLoader($em);

        $this->loader->addEntityToProcess(
            Administrator::class,
            function ($e) {
                return $e->getImportId();
            },
            'id',
            ['email', 'firstname', 'lastname']
        );

        $this->output->writeln('<info>Extract data</info>');
        $entities = $this->extractor->extract();
        $this->output->writeln(sprintf('<info>%d data extracted.</info>', count($entities)));

        $this->loader->load($entities);
        $this->output->writeln('<info>Loading done</info>');

        return 0;
    }
}
