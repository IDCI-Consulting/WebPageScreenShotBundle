<?php

namespace IDCI\Bundle\WebPageScreenShotBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Generate a screenshot
 *
 * @author baptiste
 */
class CreateScreenshotCommand extends ContainerAwareCommand {
    
    protected function configure()
    {
        $this
            ->setName('idci:create:screenshot')
            ->setDescription('Create (generate and resize) a screenshot from a website')
            ->setHelp(<<<EOT
                The <info>%command.name%</info> command create a screenshot from a website url.
                Do not forget the http:// as part of the url.
                You may add some options : width, height, mode(base64, file) and format (jpg, png, gif)
EOT
            )
            ->addArgument(
                'url',
                InputArgument::REQUIRED,
                'Which website do you want a screenshot from?'
            )
            ->addArgument(
                'width',
                InputArgument::OPTIONAL,
                'What will be the width of the screenshot?'
            )
            ->addArgument(
                'height',
                InputArgument::OPTIONAL,
                'What will be the height of the screenshot?'
            )
            ->addArgument(
                'mode',
                InputArgument::OPTIONAL,
                'Is this a file or a base64 encoded string?'
            )
        ;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $url = $input->getArgument('url');
        $width = $input->getArgument('width');
        $height = $input->getArgument('height');
        $mode = $input->getArgument('mode');
        $params = array();

        if($width) {
            $params['width'] = $width;
        }
        if($height) {
            $params['height'] = $height;
        }
        
        if($mode) {
            $params['mode'] = $mode;
        }

        $screenshot = $this->getContainer()->get('idci_web_page_screen_shot.manager')->createScreenshot($url, $params);

        $output->writeln(sprintf("%s have been created",$screenshot));
    }
}