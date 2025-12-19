<?php

namespace App\Command;

use App\Repository\AvisRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

#[AsCommand(
    name: 'app:avis:import-sentiment',
    description: 'Import sentiment labels from a classified JSON file and persist to DB'
)]
class ImportAvisSentimentCommand extends Command
{

    private EntityManagerInterface $em;
    private AvisRepository $avisRepository;
    private Filesystem $fs;

    public function __construct(EntityManagerInterface $em, AvisRepository $avisRepository)
    {
        parent::__construct();
        $this->em = $em;
        $this->avisRepository = $avisRepository;
        $this->fs = new Filesystem();
    }

    protected function configure(): void
    {
        $this
            ->addOption('input', 'i', InputOption::VALUE_OPTIONAL, 'Input file path', 'var/avis_classified.json')
            ->addOption('batch', null, InputOption::VALUE_OPTIONAL, 'Flush batch size', 50)
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Do not persist changes, just show what would be done');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = $input->getOption('input');
        $batch = (int) $input->getOption('batch');
        $dryRun = (bool) $input->getOption('dry-run');

        if (!$this->fs->exists($file)) {
            $output->writeln('<error>Input file not found: ' . $file . '</error>');
            return Command::FAILURE;
        }

        $output->writeln('Reading file: ' . $file);
        $content = file_get_contents($file);
        $items = json_decode($content, true);

        if (!is_array($items)) {
            $output->writeln('<error>Invalid JSON payload.</error>');
            return Command::FAILURE;
        }

        $count = 0;
        $updated = 0;

        foreach ($items as $row) {
            $count++;
            if (!isset($row['id'])) {
                $output->writeln("<comment>Skipping item without id (index: {$count})</comment>");
                continue;
            }
            $id = $row['id'];
            $sentiment = $row['sentiment'] ?? null;

            $avis = $this->avisRepository->find($id);
            if (!$avis) {
                $output->writeln("<comment>Avis with id {$id} not found, skipping.</comment>");
                continue;
            }

            $old = $avis->getSentiment();

            // Normalize incoming sentiment to canonical values
            $normalized = $this->normalizeSentiment($sentiment);

            if ($old === $normalized) {
                continue;
            }

            $output->writeln("Updating Avis {$id}: '{$old}' -> '{$normalized}'");
            if (!$dryRun) {
                $avis->setSentiment($normalized);
                $this->em->persist($avis);
            }

            $updated++;

            if (!$dryRun && ($updated % $batch) === 0) {
                $this->em->flush();
                $this->em->clear();
            }
        }

        if (!$dryRun) {
            $this->em->flush();
        }

        $output->writeln("<info>Processed {$count} items, updated {$updated} records.</info>");

        return Command::SUCCESS;
    }

    private function normalizeSentiment(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $s = trim(mb_strtolower($value));
        if ($s === '') {
            return null;
        }

        // Remove accents for basic matching
        $ascii = iconv('UTF-8', 'ASCII//TRANSLIT', $s) ?: $s;
        $ascii = preg_replace('/[^a-z0-9+\- ]/', '', $ascii);

        // Common positive tokens
        $positive = ['positive', 'pos', 'good', 'great', 'excellent', 'bien', 'bon', '+', '++'];
        // Common negative tokens
        $negative = ['negative', 'neg', 'bad', 'poor', 'mauvais', 'tres mauvais', '-', '--', 'negatif'];
        // Neutral tokens / fallback
        $neutral = ['neutral', 'neutre', 'mixed', 'none', 'n/a', 'na', 'null', 'neut', 'indifferent'];

        foreach ($positive as $tok) {
            if ($tok === '+' || $tok === '++') {
                if (strpos($ascii, '+') !== false) {
                    return 'positive';
                }
                continue;
            }
            if (strpos($ascii, $tok) !== false) {
                return 'positive';
            }
        }

        foreach ($negative as $tok) {
            if ($tok === '-' || $tok === '--') {
                if (strpos($ascii, '-') !== false) {
                    return 'negative';
                }
                continue;
            }
            if (strpos($ascii, $tok) !== false) {
                return 'negative';
            }
        }

        foreach ($neutral as $tok) {
            if (strpos($ascii, $tok) !== false) {
                return 'neutral';
            }
        }

        // If it contains words that look like positive or negative common words
        if (preg_match('/\b(very|extremely)\b/', $ascii)) {
            return 'positive';
        }

        // Default fallback -> neutral
        return 'neutral';
    }
}
