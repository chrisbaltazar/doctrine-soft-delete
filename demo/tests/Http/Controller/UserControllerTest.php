<?php

declare(strict_types=1);

namespace App\Tests\Http\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = self::createClient();
    }

    #[Test]
    #[Group('sanity')]
    public function canary_is_alive()
    {
        $this->assertTrue(true);
    }

    #[Test]
    #[Group('SoftDeleteSmoke')]
    public function soft_delete_non_unique(): void
    {
        /** @var UserRepository $repository */
        $repository = self::getContainer()->get(UserRepository::class);

        $this->shouldCreateNewUser(['name' => 'John Doe', 'email' => 'foo@test.com']);
        $this->assertCount(1, $repository->findAll());

        $this->shouldCreateNewUser(['name' => 'Jane Doe', 'email' => 'foo@test.com']); // use same email
        $this->assertCount(2, $repository->findAll());

        $user = $repository->findOneBy(['email' => 'foo@test.com']);
        $this->shouldDeleteUser($user);
        $this->assertCount(1, $repository->findAll());
    }

    #[Test]
    #[Group('SoftDeleteUnique')]
    public function soft_delete_unique_records(): void
    {
        /** @var UserRepository $repository */
        $repository = self::getContainer()->get(UserRepository::class);

        $this->shouldCreateNewUser(['name' => 'John Doe', 'email' => 'foo@test.com']);
        $this->assertCount(1, $repository->findAll());

        $this->shouldCreateNewUser(['name' => 'Jane Doe', 'email' => 'foo@test.com']); // use same email
        $this->assertCount(1, $repository->findAll()); // No record created

        $user = $repository->findOneBy(['email' => 'foo@test.com']);
        $this->shouldDeleteUser($user);
        $this->assertCount(0, $repository->findAll());
    }

    #[Test]
    #[Group('SoftDeleteNoDiff')]
    public function soft_delete_no_schema_diff(): void
    {
        $migrationFiles = glob(TEST_ROOT_DIR . '/../migrations/*.php');
        $this->assertCount(2, $migrationFiles);
    }

    private function shouldCreateNewUser(array $data): void
    {
        $this->client->request('GET', '/user/new');
        self::assertResponseIsSuccessful();

        $this->client->submitForm('Save', [
            'user' => [
                ...$data,
            ],
        ]);
        self::assertResponseRedirects('/user');
    }

    private function shouldDeleteUser(User $user): void
    {
        $this->client->request('GET', "/user/{$user->getId()}/edit");
        self::assertResponseIsSuccessful();

        $this->client->submitForm('Delete');
        self::assertResponseRedirects('/user');
    }
}