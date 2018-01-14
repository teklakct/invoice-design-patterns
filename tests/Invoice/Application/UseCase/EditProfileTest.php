<?php

declare(strict_types=1);

namespace Tests\Invoice\Application\UseCase;

use Invoice\Adapter\Legacy\Domain\VatNumberFactory;
use Invoice\Adapter\Pdo\Application\TransactionManager;
use Invoice\Adapter\Pdo\Domain\UserFactory;
use Invoice\Adapter\Pdo\Domain\Users;
use Invoice\Adapter\Pdo\UnitOfWork;
use Invoice\Application\UseCase\RegisterUser;
use Invoice\Application\UseCase\EditProfile;
use Invoice\Domain\Exception\UserNotFound;
use Tests\Invoice\DbTestCase;

/**
 * @integration
 */
class EditProfileTest extends DbTestCase
{
    /**
     * @var EditProfile
     */
    private $editProfile;

    public function setUp()
    {
        parent::setUp();
        $unitOfWork = new UnitOfWork();
        $transactionManager = new TransactionManager($this->pdo, $unitOfWork);
        $users = new Users($this->pdo, $unitOfWork);
        $registerUser = new RegisterUser(
            $transactionManager,
            $users,
            new UserFactory()
        );

        $registerUser->execute(new RegisterUser\Command(
            'leszek.prabucki@gmail.com',
            password_hash('ktoIdziePoPiwo', PASSWORD_BCRYPT)
        ));

        $this->editProfile = new EditProfile(
            $transactionManager,
            $users,
            new VatNumberFactory()
        );
    }

    function testThatEditProfile()
    {
        $this->editProfile->execute(new EditProfile\Command(
            'leszek.prabucki@gmail.com',
            '2134',
            'Leszek Prabucki',
            'adress'
        ));
        $this->editProfile->execute(new EditProfile\Command(
            'leszek.prabucki@gmail.com',
            '2134',
            'Leszek Prabucki',
            'address 2'
        ));

        $users = $this->pdo->query('SELECT * FROM users')->fetchAll();

        self::assertCount(1, $users);
        self::assertEquals('2134', $users[0]['vat']);
        self::assertEquals('Leszek Prabucki', $users[0]['name']);
        self::assertEquals('address 2', $users[0]['address']);
    }

    function testThatCannotEditUserIfNotFoundj()
    {
        $this->expectException(UserNotFound::class);
        $this->editProfile->execute(new EditProfile\Command(
            'leszek.prabucki2123123@gmail.com',
            '2134',
            'Leszek Prabucki',
            'address 2'
        ));
    }
}