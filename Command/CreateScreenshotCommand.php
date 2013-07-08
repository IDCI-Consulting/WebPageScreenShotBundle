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
            ->addArgument(
                'format',
                InputArgument::OPTIONAL,
                'What is the format of the image (png, jpg, gif)?'
            )
        ;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf("\n%s\n%s\n%s",
                                 "<bg=blue>                                        ",
                                 " Welcome to the idci screenshot creator ",
                                 "                                        </bg=blue>\n"
                        )
        );

        $url = $input->getArgument('url');
        $width = $input->getArgument('width');
        $height = $input->getArgument('height');
        $mode = $input->getArgument('mode');
        $format = $input->getArgument('format');
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
        if($format) {
            $params['format'] = $format;
        }
        
        $paramsNumber = count($params);
        if ($paramsNumber != 4 && $paramsNumber != 0) {
            $output->writeln("<error>You must indicate at least 4 parameters (width, height, mode and format). If none indicated, default ones can be set.</error>");
        } elseif ($paramsNumber == 4) {
            $screenshot = $this->getContainer()->get('idci_web_page_screen_shot.manager')->createScreenshot($url, $params);
            $output->writeln(sprintf("<info>%s have been created</info>",$screenshot));
        } else {
            $output->writeln(array(
                'This command generate a website screenshot.',
                '',
                'In addition to the website url, you must indicate several options :',
                '<comment>width</comment>, <comment>height</comment>, <comment>mode</comment>, and <comment>format</comment>.',
                ''
            ));

            $dialog = $this->getHelperSet()->get('dialog');
            $this->askParams($dialog, $params, $output);
            $screenshot = $this->getContainer()->get('idci_web_page_screen_shot.manager')->createScreenshot($url, $params);
            $output->writeln(sprintf("<info>%s have been created</info>",$screenshot));
        }
    }

    public function askParams($dialog, $params, $output) {
        $params['width'] = $dialog->ask(
            $output,
            '<info>Image width</info> [<comment>160</comment>] : ',
            '160'
        );
        $params['height'] = $dialog->ask(
            $output,
            '<info>Image height</info> [<comment>144</comment>] : ',
            '144'
        );
        $params['mode'] = $dialog->ask(
            $output,
            '<info>Image mode (base64, file)</info> [<comment>file</comment>] : ',
            'file'
        );
        $params['format'] = $dialog->ask(
            $output,
            '<info>Image format (png, jg, gif)</info> [<comment>png</comment>] : ',
            'png'
        );
    }
}