<?php

namespace App\Tests\Controller;

use App\Entity\Trick;
use App\Repository\TrickRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class TrickControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $trickRepository;
    private string $path = '/trick/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->trickRepository = $this->manager->getRepository(Trick::class);

        foreach ($this->trickRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Trick index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first()->text());
    }

    public function testNew(): void
    {
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'trick[name]' => 'Testing',
            'trick[slug]' => 'Testing',
            'trick[description]' => 'Testing',
            'trick[mainImage]' => 'Testing',
            'trick[createdAt]' => 'Testing',
            'trick[updatedAt]' => 'Testing',
            'trick[author]' => 'Testing',
            'trick[groups]' => 'Testing',
        ]);

        self::assertResponseRedirects('/trick');

        self::assertSame(1, $this->trickRepository->count([]));

        $this->markTestIncomplete('This test was generated');
    }

    public function testShow(): void
    {
        $fixture = new Trick();
        $fixture->setName('My Title');
        $fixture->setSlug('My Title');
        $fixture->setDescription('My Title');
        $fixture->setMainImage('My Title');
        $fixture->setCreatedAt('My Title');
        $fixture->setUpdatedAt('My Title');
        $fixture->setAuthor('My Title');
        $fixture->setGroups('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Trick');

        // Use assertions to check that the properties are properly displayed.
        $this->markTestIncomplete('This test was generated');
    }

    public function testEdit(): void
    {
        $fixture = new Trick();
        $fixture->setName('Value');
        $fixture->setSlug('Value');
        $fixture->setDescription('Value');
        $fixture->setMainImage('Value');
        $fixture->setCreatedAt('Value');
        $fixture->setUpdatedAt('Value');
        $fixture->setAuthor('Value');
        $fixture->setGroups('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'trick[name]' => 'Something New',
            'trick[slug]' => 'Something New',
            'trick[description]' => 'Something New',
            'trick[mainImage]' => 'Something New',
            'trick[createdAt]' => 'Something New',
            'trick[updatedAt]' => 'Something New',
            'trick[author]' => 'Something New',
            'trick[groups]' => 'Something New',
        ]);

        self::assertResponseRedirects('/trick');

        $fixture = $this->trickRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getName());
        self::assertSame('Something New', $fixture[0]->getSlug());
        self::assertSame('Something New', $fixture[0]->getDescription());
        self::assertSame('Something New', $fixture[0]->getMainImage());
        self::assertSame('Something New', $fixture[0]->getCreatedAt());
        self::assertSame('Something New', $fixture[0]->getUpdatedAt());
        self::assertSame('Something New', $fixture[0]->getAuthor());
        self::assertSame('Something New', $fixture[0]->getGroups());

        $this->markTestIncomplete('This test was generated');
    }

    public function testRemove(): void
    {
        $fixture = new Trick();
        $fixture->setName('Value');
        $fixture->setSlug('Value');
        $fixture->setDescription('Value');
        $fixture->setMainImage('Value');
        $fixture->setCreatedAt('Value');
        $fixture->setUpdatedAt('Value');
        $fixture->setAuthor('Value');
        $fixture->setGroups('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/trick');
        self::assertSame(0, $this->trickRepository->count([]));

        $this->markTestIncomplete('This test was generated');
    }
}
