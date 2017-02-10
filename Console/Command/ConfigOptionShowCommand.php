<?php
namespace Swissup\Command\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Magento\Framework\ObjectManagerInterface;

/**
 * Command for displaying config option value
 */
class ConfigOptionShowCommand extends Command
{
    /**
     * Config data array
     *
     * @var array
     */
    protected $configData;

    /**
     * Backend config data instance
     *
     * @var \Magento\Config\Model\Config
     */
    protected $configDataObject;

    /**
     * Backend Config model factory
     *
     * @var \Magento\Config\Model\Config\Factory
     */
    protected $configFactory;

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
        $this->configFactory = $configFactory;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('config:option:show')
            ->setDescription('Displays config option(s) value')
            ->setDefinition($this->getOptionsList());
        parent::configure();
    }

    /**
     * Get list of options for the command
     *
     * @return InputOption[]
     */
    public function getOptionsList()
    {
        return [
            new InputOption(
                'option',
                null,
                InputOption::VALUE_REQUIRED,
                'Option (example: admin/captcha/enable)',
                'admin/captcha/enable'
            ),
            new InputOption(
                'website',
                null,
                InputOption::VALUE_OPTIONAL,
                'Website (example: 0)',
                0
            ),
            new InputOption(
                'store',
                null,
                InputOption::VALUE_OPTIONAL,
                'Store code (example: 0)',
                0
            )
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $inputOptions = $input->getOptions();
        $option = isset($inputOptions['option']) ? $inputOptions['option'] : 'admin/captcha/enable';
        $option = trim($option, '/');

        $website = isset($inputOptions['website']) ? $inputOptions['website'] : '';
        $store = isset($inputOptions['store']) ? $inputOptions['store'] : '';

        list($section) = explode('/', $option);

        /** @var \Magento\Framework\App\State $appState */
        $appState = $this->objectManager
            ->get('Magento\Framework\App\State');
        $appState->setAreaCode('adminhtml');

        $this->configDataObject = $this->configFactory->create(
            [
                'data' => [
                    'section' => $section,
                    'website' => $website,//$this->getWebsiteCode(),
                    'store' => $store,//$this->getStoreCode(),
                ],
            ]
        );
        $this->configData = $this->configDataObject->load();

        foreach ($this->configData as $key => $value) {
            if (0 === strpos($key, $option)) {
                $output->writeln($key . ' : ' . $value);
            }
        }
    }
}
