<?php

namespace App\Service;

use App\Entity\Image;
use App\Entity\Video;
use App\Entity\Trick;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * Service responsible for handling media (images & videos) related to a Trick.
 * 
 * Responsibilities:
 * - Upload and store images
 * - Generate filenames
 * - Convert video URLs to embed format
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
     * Handle the main image upload for a Trick.
     * 
     * @param FormInterface $form
     * @param Trick $trick
     */
    public function handleMainImage(FormInterface $form, Trick $trick): void
    {
        $file = $form->get('mainImage')->getData();

        // If no file is uploaded, do nothing
        if (!$file) {
            return;
        }

        // Generate a unique filename based on slug
        $slug = $trick->getSlug();
        $filename = (string) $slug . '_' . uniqid() . '.' . $file->guessExtension();

        // Move file to configured directory
        $file->move($this->imagesDirectory, $filename);

        // Set filename as main image on Trick entity
        $trick->setMainImage($filename);
    }

    /**
     * Handle multiple image uploads for a Trick.
     * 
     * @param FormInterface $form
     * @param Trick $trick
     */
    public function handleImages(FormInterface $form, Trick $trick): void
    {
        $files = $form->get('images')->getData();

        if (!$files) {
            return;
        }

        foreach ($files as $file) {

            // Skip empty inputs
            if (!$file) {
                continue;
            }

            $slug = $trick->getSlug();
            $filename = $slug . '_' . uniqid() . '.' . $file->guessExtension();

            // Move uploaded file
            $file->move($this->imagesDirectory, $filename);

            // Create Image entity
            $image = new Image();
            $image->setUrl($filename);
            $image->setAlt($trick->getName());
            $image->setCreatedAt(new \DateTimeImmutable());

            // Important: default value to avoid SQL constraint errors
            $image->setIsMain(false);

            // Associate image with Trick
            $image->setTrick($trick);

            $this->em->persist($image);
        }
    }

    /**
     * Handle video URLs and convert them into embed format.
     * Accepts comma-separated URLs.
     * 
     * @param FormInterface $form
     * @param Trick $trick
     */
    public function handleVideos(FormInterface $form, Trick $trick): void
    {
        $videosString = $form->get('videos')->getData();

        if (!$videosString) {
            return;
        }

        // Split input string into individual URLs
        $videos = array_map('trim', explode(',', $videosString));

        foreach ($videos as $url) {

            if (!$url) {
                continue;
            }

            // Convert URL into embeddable format
            $embed = $this->convertToEmbedUrl($url);

            // Skip unsupported URLs
            if (!$embed) {
                continue;
            }

            // Create Video entity
            $video = new Video();
            $video->setEmbedUrl($embed);
            $video->setCreatedAt(new \DateTimeImmutable());

            // Important: default value to avoid SQL constraint errors
            $video->setIsMain(false);

            // Associate video with Trick
            $video->setTrick($trick);

            $this->em->persist($video);
        }
    }

    /**
     * Convert a video URL into an embeddable URL.
     * Supports YouTube and Dailymotion.
     * 
     * @param string $url
     * @return string|null
     */
    private function convertToEmbedUrl(string $url): ?string
    {
        $url = trim($url);

        // Basic URL validation
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        // =========================
        // YOUTUBE
        // =========================

        // youtube.com/watch?v=XXXX
        if (preg_match('/youtube\.com\/watch\?v=([^&]+)/', $url, $m)) {
            return 'https://www.youtube.com/embed/' . $m[1];
        }

        // youtu.be/XXXX
        if (preg_match('/youtu\.be\/([^?&]+)/', $url, $m)) {
            return 'https://www.youtube.com/embed/' . $m[1];
        }

        // Already an embed URL
        if (preg_match('/youtube\.com\/embed\/([^?&]+)/', $url)) {
            return $url;
        }

        // =========================
        // DAILYMOTION
        // =========================

        // dailymotion.com/video/XXXX
        if (preg_match('/dailymotion\.com\/video\/([^_?&]+)/', $url, $m)) {
            return 'https://www.dailymotion.com/embed/video/' . $m[1];
        }

        // geo.dailymotion.com/player.html?video=XXXX
        if (preg_match('/dailymotion\.com\/player\.html\?video=([^&]+)/', $url, $m)) {
            return 'https://www.dailymotion.com/embed/video/' . $m[1];
        }

        // Already an embed URL
        if (preg_match('/dailymotion\.com\/embed\/video\/([^?&]+)/', $url)) {
            return $url;
        }

        // Unsupported provider
        return null;
    }
}