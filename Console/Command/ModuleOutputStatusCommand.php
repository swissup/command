<?php
namespace Swissup\Command\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Magento\Framework\ObjectManagerInterface;

/**
 * Command for displaying status of output modules
 */
class ModuleOutputStatusCommand extends Command
{
    /**
     * Config data array
     *
     * @var array
     */
    protected $_configData;

    /**
     * Backend config data instance
     *
     * @var \Magento\Config\Model\Config
     */
    protected $_configDataObject;

    /**
     * Backend Config model factory
     *
     * @var \Magento\Config\Model\Config\Factory
     */
    protected $_configFactory;

    /**
     * object manager to create various objects
     *
     * @var ObjectManagerInterface
     *
     */
    private $objectManager;

    /**
     * Inject dependencies
     *
     * @param ObjectManagerInterface $objectManager
     * @param \Magento\Config\Model\Config\Factory $configFactory
     */
    public function __construct(ObjectManagerInterface $objectManager, \Magento\Config\Model\Config\Factory $configFactory)
    {
        $this->objectManager = $objectManager;
        $this->_configFactory = $configFactory;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('module:output:status')
            ->setDescription('Displays output status of modules');
        parent::configure();
    }

    protected function getConfigData()
    {
        /** @var \Magento\Framework\App\State $appState */
        $appState = $this->objectManager
            ->get('Magento\Framework\App\State');
        $appState->setAreaCode('frontend'); //adminhtml

        $this->_configDataObject = $this->_configFactory->create(
            [
                'data' => [
                    'section' => 'advanced',//$this->getSectionCode(),
                    'website' => '',//$this->getWebsiteCode(),
                    'store' => '',//$this->getStoreCode(),
                ],
            ]
        );

        $this->_configData = $this->_configDataObject->load();

        return $this->_configData;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $configData = $this->getConfigData();
        // $moduleList = $this->objectManager->create('Magento\Framework\Module\ModuleList');
        $moduleList = $this->objectManager->create('Magento\Framework\Module\FullModuleList');
        $output->writeln('<info>List of output enabled modules:</info>');

        $modules = $moduleList->getNames();
        $enabledModules = $disabledModules = [];
        foreach ($modules as $moduleName) {
            // $output->writeln($moduleName);
            $path = 'advanced/modules_disable_output/' . $moduleName;
            if (isset($configData[$path]) && 1 == $configData[$path]) {
                $disabledModules[] = $moduleName;
            } else {
                $enabledModules[] = $moduleName;
            }
        }

        if (count($enabledModules) === 0) {
            $output->writeln('None');
        } else {
            $output->writeln(join("\n", $enabledModules));
        }
        $output->writeln('');

        $output->writeln("<info>List of output disabled modules:</info>");
        if (count($disabledModules) === 0) {
            $output->writeln('None');
        } else {
            $output->writeln(join("\n", $disabledModules));
        }
    }
}
