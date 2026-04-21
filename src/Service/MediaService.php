<?php

namespace App\Service;

use App\Entity\Image;
use App\Entity\Video;
use App\Entity\Trick;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * Service responsible for handling media (images & videos) for a Trick entity.
 *
 * Responsibilities:
 * - Upload and store images on filesystem
 * - Generate safe and unique filenames
 * - Convert video URLs into embed URLs
 * - Persist Image and Video entities
 */
class MediaService
{
    public function __construct(
        #[Autowire('%images_directory%')]
        private string $imagesDirectory,

        private EntityManagerInterface $em
    ) {}

    /**
     * Handle main image upload for a Trick.
     */
    public function handleMainImage(FormInterface $form, Trick $trick): void
    {
        $file = $form->get('mainImage')->getData();

        // No file uploaded → nothing to do
        if (!$file) {
            return;
        }

        $filename = $this->generateFilename($trick->getSlug(), $file->guessExtension());

        $file->move($this->imagesDirectory, $filename);

        $trick->setMainImage($filename);
    }

    /**
     * Handle multiple additional images upload for a Trick.
     */
    public function handleImages(FormInterface $form, Trick $trick): void
    {
        $files = $form->get('images')->getData();

        if (!$files) {
            return;
        }

        foreach ($files as $file) {
            if (!$file) {
                continue;
            }

            $filename = $this->generateFilename($trick->getSlug(), $file->guessExtension());

            $file->move($this->imagesDirectory, $filename);

            $image = new Image();
            $image->setUrl($filename);
            $image->setAlt($trick->getName());
            $image->setCreatedAt(new \DateTimeImmutable());
            $image->setIsMain(false);
            $image->setTrick($trick);

            $this->em->persist($image);
        }
    }

    /**
     * Handle video URLs (comma-separated) and convert them into embed format.
     */
    public function handleVideos(FormInterface $form, Trick $trick): void
    {
        $videosString = $form->get('videos')->getData();

        if (!$videosString) {
            return;
        }

        $videos = array_map('trim', explode(',', $videosString));

        foreach ($videos as $url) {
            if (!$url) {
                continue;
            }

            $embedUrl = $this->convertToEmbedUrl($url);

            if (!$embedUrl) {
                continue;
            }

            $video = new Video();
            $video->setEmbedUrl($embedUrl);
            $video->setCreatedAt(new \DateTimeImmutable());
            $video->setIsMain(false);
            $video->setTrick($trick);

            $this->em->persist($video);
        }
    }

    /**
     * Convert a URL into an embeddable video URL.
     * Supports YouTube and Dailymotion.
     */
    private function convertToEmbedUrl(string $url): ?string
    {
        $url = trim($url);

        // Validate URL format
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        // =========================
        // YOUTUBE
        // =========================

        if (preg_match('/youtube\.com\/watch\?v=([^&]+)/', $url, $m)) {
            return 'https://www.youtube.com/embed/' . $m[1];
        }

        if (preg_match('/youtu\.be\/([^?&]+)/', $url, $m)) {
            return 'https://www.youtube.com/embed/' . $m[1];
        }

        if (preg_match('/youtube\.com\/embed\/([^?&]+)/', $url)) {
            return $url;
        }

        // =========================
        // DAILYMOTION
        // =========================

        if (preg_match('/dailymotion\.com\/video\/([^_?&]+)/', $url, $m)) {
            return 'https://www.dailymotion.com/embed/video/' . $m[1];
        }

        if (preg_match('/dailymotion\.com\/player\.html\?video=([^&]+)/', $url, $m)) {
            return 'https://www.dailymotion.com/embed/video/' . $m[1];
        }

        if (preg_match('/dailymotion\.com\/embed\/video\/([^?&]+)/', $url)) {
            return $url;
        }

        return null;
    }

    /**
     * Generate a safe and unique filename for uploads.
     */
    private function generateFilename(string $slug, ?string $extension): string
    {
        return $slug . '_' . uniqid() . '.' . ($extension ?? 'jpg');
    }
}