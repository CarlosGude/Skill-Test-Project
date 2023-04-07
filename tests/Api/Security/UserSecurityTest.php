<?php


namespace App\Tests\Api\Security;


use App\DataFixtures\UserFixtures;
use App\Entity\User;
use App\Message\EntityEvent;
use App\Tests\Api\AbstractTest;
use App\Tests\Api\Success\UserTest;

/**
 * Class UserSecurityTest
 * @package App\Tests\Api\Security
 */
class UserSecurityTest extends AbstractTest
{
    public const ACTIVATE_USER = '/activate/user/';
    public function testPostUser(): void
    {

        $userData = $this->logins['test'];
        $response = $this->makeRequest(self::METHOD_POST,UserTest::API_USER,[
            'name' => $userData['name'],
            'email' => $userData['email'],
            'password' => $userData['password'],
        ]);

        $body = $response->toArray();
        $this->assertEquals(201,$response->getStatusCode());
        $this->assertEquals($userData['name'],$body['name']);
        $this->assertEquals($userData['email'],$body['email']);
        $this->assertCount(1, $this->transport->get());
        $this->assertInstanceOf(EntityEvent::class, $this->transport->get()[0]->getMessage());

        $this->getToken('test',403);

        /** @var User $user */
        $user = $this->getRepository(User::class)->findOneBy(['email' => $userData['email']]);

        $this->makeRequest(self::METHOD_GET,self::ACTIVATE_USER.$user->getToken());

        $this->getToken('test');
    }

    public function testPutUser(): void
    {
        /** @var User $user */
        $user = $this->getRepository(User::class)->findOneBy(['email' => UserFixtures::getUsers()['admin']['email']]);

        $response = $this->makeRequest(self::METHOD_PUT,UserTest::API_USER.'/'.$user->getId(),['name' => 'TEST CHANGE NAME']);

        $body = $response->toArray();
        $this->assertEquals(200,$response->getStatusCode());
        $this->assertEquals('TEST CHANGE NAME',$body['name']);

    }

    public function testTheUserAuthorizedCanNotEditAnotherUser(): void
    {
        /** @var User $user */
        $user = $this->getRepository(User::class)->findOneBy(['email' => UserFixtures::getUsers()['anotherUser']['email']]);

        $response = $this->makeRequest(self::METHOD_PUT,UserTest::API_USER.'/'.$user->getId(),['name' => 'TEST CHANGE NAME']);
        $this->assertEquals(403,$response->getStatusCode());
    }

    public function testSuccessLogin():void
    {
        $token = $this->getToken();
        $token = json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $token)[1]))));
        $this->assertEquals(UserFixtures::getUsers()['admin']['email'],$token->username);
    }

    public function testUnActiveUserLogin():void
    {
        $this->getToken('noActiveUser',403);
    }

    public function testDeletedUserLogin():void
    {
        $this->getToken('noActiveUser',403);
    }

    public function testFailureLogin():void
    {
        $this->getToken('failLogin',401);
    }

    public function testDeletedUser(): void
    {
        /** @var User $user */
        $user = $this->getRepository(User::class)->findOneBy(['email' => UserFixtures::getUsers()['anotherUser']['email']]);

        $response = $this->makeRequest(self::METHOD_DELETE,UserTest::API_USER.'/'.$user->getId());

        $this->assertEquals(204,$response->getStatusCode());
        $response = $this->makeRequest(self::METHOD_GET,UserTest::API_USER.'/'.$user->getId());

        $this->assertEquals(404,$response->getStatusCode());

    }

    public function testAnRoleAdminCanDeleteHimself(): void
    {
        /** @var User $user */
        $user = $this->getRepository(User::class)->findOneBy(['email' => UserFixtures::getUsers()['admin']['email']]);

        $response = $this->makeRequest(self::METHOD_DELETE,UserTest::API_USER.'/'.$user->getId());

        $this->assertEquals(403,$response->getStatusCode());

    }

    public function testAnRoleNormalCanDeleteRoleAdmin(): void
    {
        /** @var User $user */
        $user = $this->getRepository(User::class)->findOneBy(['email' => UserFixtures::getUsers()['admin']['email']]);

        $response = $this->makeRequest(self::METHOD_DELETE,UserTest::API_USER.'/'.$user->getId(),[],'anotherUser');

        $this->assertEquals(403,$response->getStatusCode());

    }


    public function testUpdatePassword(): void
    {
        /** @var User $user */
        $user = $this->getRepository(User::class)->findOneBy(['email' => UserFixtures::getUsers()['anotherUser']['email']]);

        $response = $this->makeRequest(self::METHOD_PUT,UserTest::API_USER.'/'.$user->getId(),['password' => 'TEST_CHANGE_NAME'],'anotherUser');
        $this->assertEquals(200,$response->getStatusCode());
    }
}