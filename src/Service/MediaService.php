<?php

namespace App\Service;

use App\Entity\Image;
use App\Entity\Video;
use App\Entity\Trick;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class MediaService
{
    public function __construct(
        #[Autowire('%images_directory%')]
        private string $imagesDirectory,

        private EntityManagerInterface $em
    ) {}

    public function handleMainImage(FormInterface $form, Trick $trick): void
    {
        $file = $form->get('mainImage')->getData();

        if (!$file) {
            return;
        }
        $slug = $trick->getSlug();
        $filename = (string)$slug.'_'.uniqid() . '.' . $file->guessExtension();

        $file->move($this->imagesDirectory, $filename);

        $trick->setMainImage($filename);
    }

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

            $slug = $trick->getSlug();
            $filename = (string)$slug.'_'.uniqid() . '.' . $file->guessExtension();

            $file->move($this->imagesDirectory, $filename);

            $image = new Image();
            $image->setUrl($filename);
            $image->setAlt($trick->getName());
            $image->setCreatedAt(new \DateTimeImmutable());

            // IMPORTANT FIX SQL ERROR
            $image->setIsMain(false);

            $image->setTrick($trick);

            $this->em->persist($image);
        }
    }

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

            $embed = $this->convertToEmbedUrl($url);

            if (!$embed) {
                continue;
            }

            $video = new Video();
            $video->setEmbedUrl($embed);
            $video->setCreatedAt(new \DateTimeImmutable());

            // IMPORTANT FIX SQL ERROR
            $video->setIsMain(false);

            $video->setTrick($trick);

            $this->em->persist($video);
        }
    }

    private function convertToEmbedUrl(string $url): ?string
    {
        $url = trim($url);

        // youtube.com/watch?v=XXXX
        if (preg_match('/youtube\.com\/watch\?v=([^&]+)/', $url, $m)) {
            return 'https://www.youtube.com/embed/' . $m[1];
        }

        // youtu.be/XXXX
        if (preg_match('/youtu\.be\/([^?&]+)/', $url, $m)) {
            return 'https://www.youtube.com/embed/' . $m[1];
        }

        // embed already OK
        if (preg_match('/youtube\.com\/embed\/([^?&]+)/', $url)) {
            return $url;
        }

        return null;
    }
}