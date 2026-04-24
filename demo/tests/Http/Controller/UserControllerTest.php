<?php

declare(strict_types=1);

namespace App\Tests\Http\Controller;

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
    public function create_user(): void
    {
        /** @var UserRepository $repository */
        $repository = self::getContainer()->get(UserRepository::class);

        $this->shouldCreateNewUser(['name' => 'John Doe', 'email' => 'john@test.com']);
        $this->assertCount(1, $repository->findAll());

        $this->shouldCreateNewUser(['name' => 'Jane Doe', 'email' => 'john@test.com']); // use same email
        $this->assertCount(2, $repository->findAll());
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
}