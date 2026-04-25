# doctrine-soft-delete

A Symfony Bundle to handle **soft-deleted records** transparently with Doctrine ORM and MySQL.

Instead of permanently deleting rows, soft-delete marks records with a `deleted_at` timestamp and automatically excludes them from all queries via a Doctrine SQL filter.

[![PHP >= 8.1](https://img.shields.io/badge/PHP-%3E%3D8.1-blue)](https://www.php.net/)
[![License: MIT](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

---

## Features

- ✅ Automatic `deleted_at` column filtering via a Doctrine SQL filter
- ✅ MySQL `GENERATED` column (`auto_generated_active_flag`) for safe unique indexes on soft-deletable tables
- ✅ PHP 8 attribute `#[SoftDeleteUniqueIndex]` to declare unique constraints that respect soft deletes
- ✅ Custom MySQL schema comparator that prevents unnecessary migration noise
- ✅ Zero-config autowiring via Symfony's service container

---

## Compatibility 

- Symfony 6.4+
- Doctrine ORM 3.6+
- MySQL 5.7+ (for generated columns)

## Dependencies

| Dependency                          | Version  |
|-------------------------------------|----------|
| PHP                                 | `>= 8.1` |
| `symfony/framework-bundle`          | `^6.4`   |
| `doctrine/doctrine-bundle`          | `^2.18`  |
| `doctrine/orm`                      | `^3.6`   |
| `doctrine/doctrine-migrations-bundle` | `^3.7` |

---

## Installation

```bash
composer require chrisbaltazar/doctrine-soft-delete
```

Please make sure the bundle gets registered in your `config/bundles.php`:
(normally handled by Symfony Flex)

```php
return [
    // ...
    Database\SoftDelete\SoftDeleteBundle::class => ['all' => true],
];
```

## Usage

To auto enable the _soft delete_ function, simply implement the `SoftDeletableInterface` in your entity and add a nullable `deletedAt` property:

```php 
use Database\SoftDelete\Core\Contract\SoftDeletableInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Product implements SoftDeletableInterface
{
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $deletedAt = null;

    public function setDeletedAt(\DateTimeImmutable $deletedAt): void
    {
        $this->deletedAt = $deletedAt;
    }

    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }
}
```

Then just remember to update your entities accordingly when you want to soft-delete a record:

```php
$product->setDeletedAt(new \DateTimeImmutable());
$entityManager->flush();
```

This will automatically exclude soft-deleted records from all queries.

### Temporarily include soft-deleted records

To include them, you can disable the filter at any point during your request flow with:

```php
$entityManager->getFilters()->disable('soft_delete');
... 
$users = $entityManager->getRepository(User::class)->findAll(); // includes soft-deleted records
``` 

### Unique soft-deletable records

For unique items that should ignore soft-deleted records, use the `#[SoftDeleteUniqueIndex]` attribute:
This will create an auto-generated column in your entity table as a boolean flag + a table index, 
that would control the uniqueness behavior of the specified fields, ignoring soft-deleted records (where `deleted_at` is not null).

```php
use Database\SoftDelete\Core\Attribute\SoftDeleteUniqueIndex;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[SoftDeleteUniqueIndex(fields: ['email'])]
class User implements SoftDeletableInterface
{
    // ...
}
```
After that just generate a new migration as normal and execute it to update your database schema.

Then simply handle the unique constraint violation properly as the following example: 

```php
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
... 
// Perform form validation and any other necessary checks before attempting to persist the user entity
...
 
try {
    $entityManager->persist($user);
    $entityManager->flush();
} catch (UniqueConstraintViolationException) {
    $this->addFlash('error', 'A user with this email already exists.');
}

// Continue with the rest of your controller logic, such as rendering a response or redirecting the user
```

## Demo 

A demo Symfony application is available in the `demo/` directory of this repository, showcasing the bundle's features in action. 
To run the demo locally just use the handy commands available in the `Makefile`:

(Make sure you have already a docker environment well configured with `docker-compose` and `Make` installed on your machine)

```bash
make demo-run
```

After that you should be able to access the demo app at `http://localhost:8000` and see how soft-deleted records are handled in the UI.
(the port number can be also customized in the `docker-compose.override.yml` file if needed)

Then just go over: `http://localhost:8000/user` and try it out as any other CRUD 🚀  

## Testing 

To run the tests, simply execute:

```bash
make test
```

This will run all PHPUnit tests defined in the `tests/` directory, ensuring that the bundle's functionality is working as expected, 
ensuring the special order anc conditions needed to properly test the database custom components and the generated column behavior.
