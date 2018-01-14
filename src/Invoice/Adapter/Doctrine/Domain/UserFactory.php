<?php

declare(strict_types=1);

namespace Invoice\Adapter\Doctrine\Domain;

use Invoice\Domain\Email;
use Invoice\Domain\User as BaseUser;
use Invoice\Domain\UserFactory as UserFactoryInterface;

class UserFactory implements UserFactoryInterface
{
    public function create(string $email, string $password): BaseUser
    {
        return new User(
            new Email($email),
            $password
        );
    }
}