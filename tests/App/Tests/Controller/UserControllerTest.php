<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Enum\UserRole;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Field\ChoiceFormField;
use Symfony\Component\DomCrawler\Form;

final class UserControllerTest extends WebTestCase
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
        $this->client->request('GET', '/user');
        $this->assertResponseIsSuccessful();
    }

    public function testNewGet(): void
    {
        $this->client->request('GET', '/user/new');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');
    }

    public function testNewPostCreatesUser(): void
    {
        $crawler = $this->client->request('GET', '/user/new');
        $this->assertResponseIsSuccessful();

        $form = $crawler->filter('form')->form();

        $this->setTextIfExists($form, 'user[email]', 'user_' . uniqid('', true) . '@example.com');
        $this->setTextIfExists($form, 'user[name]', 'Test User ' . uniqid('', true));

        $this->setChoiceFirstNonEmpty($form, 'user[role]');

        $this->client->submit($form);

        $this->assertResponseStatusCodeSame(303);
        $this->client->followRedirect();
        $this->assertResponseIsSuccessful();
    }

    public function testShow(): void
    {
        $user = $this->createUser('Show User');

        $this->client->request('GET', '/user/' . $user->getId());
        $this->assertResponseIsSuccessful();
    }

    public function testEditGetAndPost(): void
    {
        $user = $this->createUser('Edit User');

        $crawler = $this->client->request('GET', '/user/' . $user->getId() . '/edit');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');

        $form = $crawler->filter('form')->form();

        $this->setTextIfExists($form, 'user[email]', 'user_' . uniqid('', true) . '@example.com');
        $this->setTextIfExists($form, 'user[name]', 'Updated User ' . uniqid('', true));

        $this->setChoiceFirstNonEmpty($form, 'user[role]');

        $this->client->submit($form);

        $this->assertResponseStatusCodeSame(303);
        $this->client->followRedirect();
        $this->assertResponseIsSuccessful();
    }

    public function testDelete(): void
    {
        $user = $this->createUser('Delete User');
        $id = $user->getId();

        $crawler = $this->client->request('GET', '/user/' . $id);
        $this->assertResponseIsSuccessful();

        $formNode = $crawler->filter(sprintf('form[action="/user/%d"]', $id))->first();
        $this->assertGreaterThan(0, $formNode->count());

        $form = $formNode->form();
        $this->client->submit($form);

        $this->assertResponseStatusCodeSame(303);
        $this->client->followRedirect();
        $this->assertResponseIsSuccessful();

        $this->assertNull($this->em->getRepository(User::class)->find($id));
    }

    private function createUser(string $label): User
    {
        $user = (new User())
            ->setEmail('user_' . uniqid('', true) . '@example.com')
            ->setName($label . ' ' . uniqid('', true))
            ->setRole(UserRole::cases()[0]);

        $this->em->persist($user);
        $this->em->flush();

        return $user;
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
}
