<?php
namespace App\Command;

use App\Entity\GalaImage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportImagesCommand extends Command
{
    protected static $defaultName = 'app:import-images';

    public function __construct(private EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Import images from the uploads directory into the database.')
            ->setName('app:import-images');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $directory = 'public/images/galas';
        $files = scandir($directory);

        $importedCount = 0;
        $skippedCount = 0;

        foreach ($files as $file) {
            if (is_file($directory . '/' . $file)) {
                [$galaname, $cours, $imageName] = explode('_', pathinfo($file, PATHINFO_FILENAME));

                // Check if the image already exists in the database
                $existingImage = $this->entityManager->getRepository(GalaImage::class)
                    ->findOneBy(['filename' => $file]);

                if ($existingImage) {
                    $skippedCount++;
                    continue; // Skip this image if it already exists
                }

                // Create a new GalaImage entity
                $image = new GalaImage();
                $image->setFilename($file);
                $image->setGalaname($galaname);
                $image->setCours($cours);
                $image->setPrice(10.00); // Default price

                $this->entityManager->persist($image);
                $importedCount++;
            }
        }

        $this->entityManager->flush();

        $output->writeln("Images imported successfully: $importedCount");
        $output->writeln("Images skipped (already exist): $skippedCount");

        return Command::SUCCESS;
    }
}