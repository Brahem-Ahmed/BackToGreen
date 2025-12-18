<?php

namespace App\Command;

use App\Service\TranslationService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:test-translation',
    description: 'Test the translation service',
)]
class TestTranslationCommand extends Command
{
    public function __construct(
        private TranslationService $translationService
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Testing Translation Service');

        $testText = 'Événement écologique de nettoyage';
        
        $io->section('Testing French to English');
        $io->writeln("Original (FR): $testText");
        
        $translated = $this->translationService->translate($testText, 'en', 'fr');
        $io->writeln("Translated (EN): $translated");
        
        $io->section('Testing French to Arabic');
        $translatedAr = $this->translationService->translate($testText, 'ar', 'fr');
        $io->writeln("Translated (AR): $translatedAr");

        if ($translated !== $testText) {
            $io->success('Translation service is working!');
            return Command::SUCCESS;
        } else {
            $io->error('Translation service is NOT working!');
            return Command::FAILURE;
        }
    }
}
