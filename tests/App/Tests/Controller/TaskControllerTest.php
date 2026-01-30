<?php

namespace App\Tests\Controller;

use App\Entity\Project;
use App\Entity\Task;
use App\Entity\Team;
use App\Enum\ProjectStatus;
use App\Enum\TaskPriority;
use App\Enum\TaskStatus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Field\ChoiceFormField;
use Symfony\Component\DomCrawler\Form;

final class TaskControllerTest extends WebTestCase
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
        $this->client->request('GET', '/task');
        $this->assertResponseIsSuccessful();
    }

    public function testNewGet(): void
    {
        $this->client->request('GET', '/task/new');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');
    }

    public function testNewWithProjectPreselected(): void
    {
        $project = $this->createProject('Project for preselect');

        $this->client->request('GET', '/task/new?project=' . $project->getId());
        $this->assertResponseIsSuccessful();
    }

    public function testNewPostCreatesTask(): void
    {
        $project = $this->createProject('Project for task creation');

        $crawler = $this->client->request('GET', '/task/new?project=' . $project->getId());
        $this->assertResponseIsSuccessful();

        $form = $crawler->filter('form')->form();

        $this->setTextIfExists($form, 'task[title]', 'Test Task ' . uniqid('', true));
        $this->setTextIfExists($form, 'task[description]', 'Test description');

        $this->setChoiceFirstNonEmpty($form, 'task[status]');
        $this->setChoiceFirstNonEmpty($form, 'task[priority]');

        $this->setChoiceByValueIfExists($form, 'task[project]', (string) $project->getId());

        $this->client->submit($form);

        $this->assertResponseStatusCodeSame(303);
        $this->client->followRedirect();
        $this->assertResponseIsSuccessful();
    }

    public function testShow(): void
    {
        $task = $this->createTask('Show task');

        $this->client->request('GET', '/task/' . $task->getId());
        $this->assertResponseIsSuccessful();
    }

    public function testEditGetAndPost(): void
    {
        $task = $this->createTask('Edit task');

        $crawler = $this->client->request('GET', '/task/' . $task->getId() . '/edit');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');

        $form = $crawler->filter('form')->form();

        $this->setTextIfExists($form, 'task[title]', 'Updated Task ' . uniqid('', true));

        $this->setChoiceFirstNonEmpty($form, 'task[status]');
        $this->setChoiceFirstNonEmpty($form, 'task[priority]');

        if ($task->getProject()?->getId() !== null) {
            $this->setChoiceByValueIfExists($form, 'task[project]', (string) $task->getProject()->getId());
        }

        $this->client->submit($form);

        $this->assertResponseStatusCodeSame(303);
        $this->client->followRedirect();
        $this->assertResponseIsSuccessful();
    }

    public function testDelete(): void
    {
        $task = $this->createTask('Delete task');
        $id = $task->getId();

        $crawler = $this->client->request('GET', '/task/' . $id);
        $this->assertResponseIsSuccessful();

        $formNode = $crawler->filter(sprintf('form[action="/task/%d"]', $id))->first();
        $this->assertGreaterThan(0, $formNode->count());

        $form = $formNode->form();
        $this->client->submit($form);

        $this->assertResponseStatusCodeSame(303);
        $this->client->followRedirect();
        $this->assertResponseIsSuccessful();

        $this->assertNull($this->em->getRepository(Task::class)->find($id));
    }

    private function createTask(string $label): Task
    {
        $project = $this->createProject('Project for ' . $label);

        $task = (new Task())
            ->setTitle($label . ' ' . uniqid('', true))
            ->setStatus(TaskStatus::cases()[0])
            ->setPriority(TaskPriority::cases()[0])
            ->setProject($project);

        $this->em->persist($task);
        $this->em->flush();

        return $task;
    }

    private function createProject(string $label): Project
    {
        $team = $this->createTeam('Team for ' . $label);

        $project = (new Project())
            ->setTitle($label . ' ' . uniqid('', true))
            ->setStatus(ProjectStatus::cases()[0])
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


    private function setTextIfExists(Form $form, string $field, string $value): void
    {
        if ($form->has($field)) {
            $form[$field] = $value;
        }
    }

    private function setChoiceFirstNonEmpty(Form $form, string $field): void
    {
        if (!$form->has($field)) {
            return;
        }

        $node = $form[$field];

        if (is_array($node)) {
            return;
        }

        if (!$node instanceof ChoiceFormField) {
            return;
        }

        $current = $node->getValue();
        if (is_string($current) && $current !== '') {
            return;
        }

        foreach ($node->availableOptionValues() as $choice) {
            if (is_string($choice) && $choice !== '') {
                $form[$field] = $choice;
                return;
            }
        }
    }

    private function setChoiceByValueIfExists(Form $form, string $field, string $value): void
    {
        if (!$form->has($field)) {
            return;
        }

        $node = $form[$field];

        if (is_array($node)) {
            return;
        }

        if (!$node instanceof ChoiceFormField) {
            return;
        }

        foreach ($node->availableOptionValues() as $choice) {
            if ((string) $choice === $value) {
                $form[$field] = $value;
                return;
            }
        }
    }
}
