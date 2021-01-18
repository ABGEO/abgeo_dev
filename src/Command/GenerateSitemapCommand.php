<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use DOMDocument;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Yaml\Yaml;

use function rtrim;

/**
 * Class GenerateSitemapCommand.
 *
 * @category Command
 * @package  App
 */
class GenerateSitemapCommand extends Command
{
    protected static $defaultName = 'app:generate-sitemap';
    protected static $defaultFile = __DIR__ . '/../../public/sitemap.xml';

    /**
     * @var string|RouterInterface|null
     */
    private $router;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * {@inheritDoc}
     */
    public function __construct(RouterInterface $router, EntityManagerInterface $em)
    {
        parent::__construct();

        $this->router = $router;
        $this->entityManager = $em;
    }

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setDescription('Generate Sitemap based on config/sitemap.yaml settings.')
            ->addOption('base-url', 'u', InputOption::VALUE_REQUIRED, 'Website base URL')
            ->addOption('file', 'f', InputOption::VALUE_OPTIONAL, 'Output file name', self::$defaultFile)
            ->addOption('minify', 'm', InputOption::VALUE_OPTIONAL, 'Minify the output file', true);
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $baseUrl = rtrim($input->getOption('base-url'), '/');
        $file = $input->getOption('file');
        $config = Yaml::parseFile(__DIR__.'/../../config/sitemap.yaml');
        $dom = new DOMDocument();
        $root = null;
        $route = null;
        $url_node = null;
        $url_node = null;
        $query = null;

        $dom->xmlVersion = '1.0';
        $dom->encoding = 'utf-8';
        $dom->formatOutput = $input->getOption('minify');

        $root = $dom->createElement('urlset');
        $root->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $root->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $root->setAttribute(
            'xsi:schemaLocation',
            'http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd'
        );

        foreach ($config['static'] as $item) {
            $route = $this->router->generate($item['route']);
            $url_node = $dom->createElement('url');
            $url_node->appendChild($dom->createElement('loc', $baseUrl.$route));
            $url_node->appendChild($dom->createElement('changefreq', $item['changefreq']));
            $url_node->appendChild($dom->createElement('priority', $item['priority']));
            $root->appendChild($url_node);
        }

        foreach ($config['dynamic'] as $item) {
            $query = $this->entityManager->createQuery("SELECT e.id FROM {$item['class']} e");

            foreach ($query->execute() as $data) {
                $route = $this->router->generate($item['route'], ['id' => $data['id']]);
                $url_node = $dom->createElement('url');
                $url_node->appendChild($dom->createElement('loc', $baseUrl.$route));
                $url_node->appendChild($dom->createElement('changefreq', $item['changefreq']));
                $url_node->appendChild($dom->createElement('priority', $item['priority']));
                $root->appendChild($url_node);
            }
        }

        $dom->appendChild($root);
        $dom->save($file);

        $io->success('Sitemap has been generated successfully.');

        return 0;
    }

}
