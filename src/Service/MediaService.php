<?php

namespace App\Service;

use App\Entity\Image;
use App\Entity\Trick;
use App\Entity\Video;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Service responsible for handling media (images & videos) for a Trick entity.
 */
class MediaService
{
    public function __construct(
        #[Autowire('%images_directory%')]
        private string $imagesDirectory,
        private EntityManagerInterface $em,
    ) {
    }

    // =========================
    // MAIN IMAGE
    // =========================
    public function handleMainImage(FormInterface $form, Trick $trick): void
    {
        /** @var UploadedFile|null $file */
        $file = $form->get('mainImage')->getData();

        if (!$file instanceof UploadedFile) {
            return;
        }

        $filename = $this->generateFilename((string) $trick->getSlug(), $file->guessExtension());

        $file->move($this->imagesDirectory, $filename);

        $trick->setMainImage($filename);
    }

    // =========================
    // MULTIPLE IMAGES
    // =========================
    public function handleImages(FormInterface $form, Trick $trick): void
    {
        $files = $form->get('images')->getData();

        if (!is_iterable($files)) {
            return;
        }

        foreach ($files as $file) {
            if (!$file instanceof UploadedFile) {
                continue;
            }

            $filename = $this->generateFilename((string) $trick->getSlug(), $file->guessExtension());

            $file->move($this->imagesDirectory, $filename);

            $image = new Image();
            $image->setUrl($filename);
            $image->setAlt($trick->getName() ?? 'Trick image');
            $image->setCreatedAt(new \DateTimeImmutable());
            $image->setIsMain(false);
            $image->setTrick($trick);

            $this->em->persist($image);
        }
    }

    // =========================
    // VIDEOS (Form submission)
    // =========================
    public function handleVideos(FormInterface $form, Trick $trick): void
    {
        /** @var string|null $videosString */
        $videosString = $form->get('videos')->getData();

        if (empty($videosString)) {
            return;
        }

        $urls = preg_split('/[\r\n,]+/', $videosString);

        foreach ($urls as $url) {
            $url = trim($url);
            if (empty($url)) {
                continue;
            }

            $embedUrl = $this->convertToEmbedUrl($url);

            if (null === $embedUrl) {
                continue;
            }

            // Avoid duplicates
            foreach ($trick->getVideos() as $existingVideo) {
                /** @var Video $existingVideo */ // On aide l'analyseur à savoir que c'est une entité Video
                if ($existingVideo->getEmbedUrl() === $embedUrl) {
                    continue 2;
                }
            }

            $video = new Video();
            $video->setEmbedUrl($embedUrl);
            $video->setCreatedAt(new \DateTimeImmutable());
            $video->setIsMain(false);
            $video->setTrick($trick);

            $this->em->persist($video);
        }
    }

    // =========================
    // CONVERSION URL → EMBED (Public for Fixtures)
    // =========================
    public function convertToEmbedUrl(string $url): ?string
    {
        $url = trim($url);

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            $url = 'https://'.$url;
        }

        // YOUTUBE (Better regex for fixtures and share links)
        // Matches: youtube.com/watch?v=ID, youtu.be/ID, youtube.com/embed/ID
        if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match)) {
            return 'https://www.youtube.com/embed/'.$match[1];
        }

        // DAILYMOTION
        if (preg_match('/dailymotion\.com\/video\/([^_?&]+)/i', $url, $match)) {
            return 'https://www.dailymotion.com/embed/video/'.$match[1];
        }

        return null;
    }

    // =========================
    // FILE NAME
    // =========================
    private function generateFilename(string $slug, ?string $extension): string
    {
        return $slug.'_'.uniqid().'.'.($extension ?? 'jpg');
    }
}
