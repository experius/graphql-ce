<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Setup\Console\Command;

use Magento\Setup\Model\ObjectManagerProvider;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Abstract class for Enable and Disable commands to consolidate common logic
 */
abstract class AbstractModuleCommand extends AbstractSetupCommand
{
    /**
     * Names of input arguments or options
     */
    const INPUT_KEY_MODULES = 'module';
    const INPUT_KEY_CLEAR_STATIC_CONTENT = 'clear-static-content';

    /**
     * Object manager provider
     *
     * @var ObjectManagerProvider
     */
    protected $objectManagerProvider;

    /**
     * Inject dependencies
     *
     * @param ObjectManagerProvider $objectManagerProvider
     */
    public function __construct(ObjectManagerProvider $objectManagerProvider)
    {
        $this->objectManagerProvider = $objectManagerProvider;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->addArgument(
            self::INPUT_KEY_MODULES,
            InputArgument::IS_ARRAY | InputArgument::OPTIONAL,
            'Name of the module'
        );
        $this->addOption(
            self::INPUT_KEY_CLEAR_STATIC_CONTENT,
            'c',
            InputOption::VALUE_NONE,
            'Clear generated static view files. Necessary, if the module(s) have static view files'
        );

        parent::configure();
    }

    /**
     * Cleanup after updated modules status
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function cleanup(InputInterface $input, OutputInterface $output)
    {
        $objectManager = $this->objectManagerProvider->get();
        /** @var \Magento\Framework\App\Cache $cache */
        $cache = $objectManager->get('Magento\Framework\App\Cache');
        $cache->clean();
        $output->writeln('<info>Cache cleared successfully.</info>');
        /** @var \Magento\Framework\App\State\CleanupFiles $cleanupFiles */
        $cleanupFiles = $objectManager->get('Magento\Framework\App\State\CleanupFiles');
        $cleanupFiles->clearCodeGeneratedClasses();
        $output->writeln('<info>Generated classes cleared successfully.</info>');
        if ($input->getOption(self::INPUT_KEY_CLEAR_STATIC_CONTENT)) {
            $cleanupFiles->clearMaterializedViewFiles();
            $output->writeln('<info>Generated static view files cleared successfully.</info>');
        } else {
            $output->writeln(
                '<error>Alert: Generated static view files were not cleared.'
                . ' You can clear them using the --' . self::INPUT_KEY_CLEAR_STATIC_CONTENT . ' option.'
                . ' Failure to clear static view files might cause display issues in the Admin and storefront.</error>'
            );
        }
    }
}
