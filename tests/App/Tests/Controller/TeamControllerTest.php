<?php

namespace App\Tests\Controller;

use App\Entity\Team;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Form;

final class TeamControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $em;

    protected function setUp(): void
    {
        self::ensureKernelShutdown();
        $this->client = static::createClient();
        $this->em = static::getContainer()->get(EntityManagerInterface::class);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        self::ensureKernelShutdown();
    }

    public function testIndex(): void
    {
        $this->client->request('GET', '/team');
        $this->assertResponseIsSuccessful();
    }

    public function testNewGet(): void
    {
        $this->client->request('GET', '/team/new');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');
    }

    public function testNewPostCreatesTeam(): void
    {
        $crawler = $this->client->request('GET', '/team/new');
        $this->assertResponseIsSuccessful();

        $form = $crawler->filter('form')->form();

        $this->setTextIfExists($form, 'team[name]', 'Test Team ' . uniqid('', true));
        $this->setTextIfExists($form, 'team[description]', 'Test description');

        $this->client->submit($form);

        $this->assertResponseStatusCodeSame(303);
        $this->client->followRedirect();
        $this->assertResponseIsSuccessful();
    }

    public function testShow(): void
    {
        $team = $this->createTeam('Show team');

        $this->client->request('GET', '/team/' . $team->getId());
        $this->assertResponseIsSuccessful();
    }

    public function testEditGetAndPost(): void
    {
        $team = $this->createTeam('Edit team');

        $crawler = $this->client->request('GET', '/team/' . $team->getId() . '/edit');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');

        $form = $crawler->filter('form')->form();

        $this->setTextIfExists($form, 'team[name]', 'Updated Team ' . uniqid('', true));
        $this->setTextIfExists($form, 'team[description]', 'Updated description');

        $this->client->submit($form);

        $this->assertResponseStatusCodeSame(303);
        $this->client->followRedirect();
        $this->assertResponseIsSuccessful();
    }

    public function testDelete(): void
    {
        $team = $this->createTeam('Delete team');
        $id = $team->getId();

        $crawler = $this->client->request('GET', '/team/' . $id);
        $this->assertResponseIsSuccessful();

        $formNode = $crawler->filter(sprintf('form[action="/team/%d"]', $id))->first();
        $this->assertGreaterThan(0, $formNode->count());

        $form = $formNode->form();
        $this->client->submit($form);

        $this->assertResponseStatusCodeSame(303);
        $this->client->followRedirect();
        $this->assertResponseIsSuccessful();

        $this->assertNull($this->em->getRepository(Team::class)->find($id));
    }

    private function createTeam(string $label): Team
    {
        $team = (new Team())
            ->setName($label . ' ' . uniqid('', true))
            ->setDescription('Team description');

        $this->em->persist($team);
        $this->em->flush();

        return $team;
    }

    private function setTextIfExists(Form $form, string $field, string $value): void
    {
        if ($form->has($field)) {
            $form[$field] = $value;
        }
    }
}
