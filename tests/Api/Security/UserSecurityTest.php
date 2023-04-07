<?php


namespace App\Tests\Api\Security;


use App\DataFixtures\UserFixtures;
use App\Entity\User;
use App\Tests\Api\AbstractTest;
use App\Tests\Api\Success\UserTest;

/**
 * Class UserSecurityTest
 * @package App\Tests\Api\Security
 */
class UserSecurityTest extends AbstractTest
{
    public function testPostUser(): void
    {

        $response = $this->makeRequest(self::METHOD_POST,UserTest::API_USER,[
            'name' => 'TEST CHANGE NAME',
            'email' => 'test_created@email.com',
            'password' => 'TEST_PASSWORD_1',
        ]);

        $body = $response->toArray();
        $this->assertEquals(201,$response->getStatusCode());
        $this->assertEquals('TEST CHANGE NAME',$body['name']);

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

    public function testFailureLogin():void
    {
        $this->getToken('failLogin');
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