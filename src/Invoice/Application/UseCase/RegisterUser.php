<?php

declare(strict_types=1);

namespace Invoice\Application\UseCase;

use Invoice\Application\TransactionManager;
use Invoice\Domain\UserFactory;
use Invoice\Domain\Users;
use Throwable;

class RegisterUser
{
    private $transactionManager;
    private $users;
    private $userFactory;

    public function __construct(TransactionManager $transactionManager, Users $users, UserFactory $userFactory)
    {
        $this->transactionManager = $transactionManager;
        $this->users = $users;
        $this->userFactory = $userFactory;
    }

    public function execute(RegisterUser\Command $command): void
    {
        $this->transactionManager->begin();

        try {
            $this->users->add(
                $this->userFactory->create($command->email(), $command->password())
            );

            $this->transactionManager->commit();
        } catch (Throwable $exception) {
            $this->transactionManager->rollback();

            throw $exception;
        }
    }
}