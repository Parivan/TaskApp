<?php

namespace App\Tests\Controller;

use App\Entity\Project;
use App\Entity\Team;
use App\Enum\ProjectStatus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ProjectControllerTest extends WebTestCase
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
        $this->client->request('GET', '/project');
        $this->assertResponseIsSuccessful();
    }

    public function testNewGet(): void
    {
        $this->client->request('GET', '/project/new');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');
    }

    public function testNewPostCreatesProjectAndRedirects(): void
    {
        $team = $this->createTeam('Team for new form');

        $crawler = $this->client->request('GET', '/project/new');
        $this->assertResponseIsSuccessful();

        $form = $crawler->filter('form')->form();
        $values = $form->getValues();

        $this->setIfExists($values, 'project[title]', 'Test Project ' . uniqid('', true));

        $statusFieldName = 'project[status]';
        if (array_key_exists($statusFieldName, $values)) {
            if ($values[$statusFieldName] === '' || $values[$statusFieldName] === null) {
                $values[$statusFieldName] = $this->pickAnyProjectStatusFormValue();
            }
        }

        $teamFieldName = 'project[team]';
        if (array_key_exists($teamFieldName, $values)) {
            $values[$teamFieldName] = (string) $team->getId();
        }

        $this->client->submit($form, $values);

        $this->assertResponseStatusCodeSame(303);
        $this->client->followRedirect();
        $this->assertResponseIsSuccessful();
    }

    public function testShow(): void
    {
        $project = $this->createProject('Show Project');

        $this->client->request('GET', '/project/' . $project->getId());
        $this->assertResponseIsSuccessful();
    }

    public function testEditGetAndPost(): void
    {
        $project = $this->createProject('Edit Project');

        $crawler = $this->client->request('GET', '/project/' . $project->getId() . '/edit');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');

        $form = $crawler->filter('form')->form();
        $values = $form->getValues();

        $this->setIfExists($values, 'project[title]', 'Updated Project ' . uniqid('', true));

        $statusFieldName = 'project[status]';

        if (
            array_key_exists($statusFieldName, $values)
            && (
                $values[$statusFieldName] === ''
                || $values[$statusFieldName] === null
            )
        ) {
            $values[$statusFieldName] = $this->pickAnyProjectStatusFormValue();
        }


        $teamFieldName = 'project[team]';
        if (
            array_key_exists($teamFieldName, $values)
            && (
                $values[$teamFieldName] === ''
                || $values[$teamFieldName] === null
            )
        ) {
            $values[$teamFieldName] = (string) $project->getTeam()?->getId();
        }

        $this->client->submit($form, $values);

        $this->assertResponseStatusCodeSame(303);
        $this->client->followRedirect();
        $this->assertResponseIsSuccessful();
    }

    public function testDelete(): void
    {
        $project = $this->createProject('Delete Project');
        $id = $project->getId();

        $crawler = $this->client->request('GET', '/project');
        $this->assertResponseIsSuccessful();

        $deleteFormNode = $crawler->filter(sprintf('form[action="/project/%d"]', $id))->first();

        if ($deleteFormNode->count() === 0) {
            $crawler = $this->client->request('GET', '/project/' . $id);
            $this->assertResponseIsSuccessful();
            $deleteFormNode = $crawler->filter(sprintf('form[action="/project/%d"]', $id))->first();
        }

        $this->assertGreaterThan(
            0,
            $deleteFormNode->count(),
            'Delete Form not found.'
        );

        $form = $deleteFormNode->form();
        $this->client->submit($form);

        $this->assertResponseStatusCodeSame(303);
        $this->client->followRedirect();
        $this->assertResponseIsSuccessful();

        $this->assertNull($this->em->getRepository(Project::class)->find($id));
    }

    private function createProject(string $title): Project
    {
        $team = $this->createTeam('Team for ' . $title);

        $project = (new Project())
            ->setTitle($title . ' ' . uniqid('', true))
            ->setStatus($this->pickAnyProjectStatus())
            ->setTeam($team);

        $this->em->persist($project);
        $this->em->flush();

        return $project;
    }

    private function createTeam(string $label): Team
    {
        $team = new Team();
        $team->setName($label . ' ' . uniqid('', true));

        $this->em->persist($team);
        $this->em->flush();

        return $team;
    }


    private function pickAnyProjectStatus(): ProjectStatus
    {
        $cases = ProjectStatus::cases();
        return $cases[0];
    }

    private function pickAnyProjectStatusFormValue(): string
    {
        $case = $this->pickAnyProjectStatus();

        if (property_exists($case, 'value')) {
            return $case->value;
        }

        return $case->name;
    }

    /**
     *
     * @param array<string, mixed> $values
     */
    private function setIfExists(array &$values, string $key, mixed $value): void
    {
        if (array_key_exists($key, $values)) {
            $values[$key] = $value;
        }
    }
}
