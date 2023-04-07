<?php


namespace App\Tests\Api;



use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Messenger\Bridge\Doctrine\Transport\DoctrineTransport;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

abstract class AbstractTest extends KernelTestCase
{

    protected const METHOD_POST ='POST';
    protected const METHOD_GET ='GET';
    protected const METHOD_PUT ='PUT';
    protected const METHOD_DELETE ='DELETE';

    public const API_LOGIN = '/api/login_check';

    protected array $logins = [
        'admin' => ['email' => 'admin@email.test', 'password' => 'password1admin'],
        'anotherUser' => ['email' => 'test@email.test', 'password' => 'password1'],
        'noActiveUser' => ['email' => 'noActiveUser@email.test', 'password' => 'noActiveUser1'],
        'deletedUser' => ['email' => 'deletedUser@email.test', 'password' => 'deletedUser1'],
        'failLogin' => ['email' => 'failLogin@email.test', 'password' => 'failLogin'],
        'test' => ['email' => 'test_created@email.com', 'password' => 'TEST_PASSWORD_1','name' => 'TEST'],
    ];

    protected ?HttpClientInterface $httpClient;
    protected ?EntityManagerInterface $manager;
    protected ?DoctrineTransport $transport;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        self::bootKernel();

        $container = static::getContainer();

        $this->httpClient = $container->get(HttpClientInterface::class);
        $this->manager = $container->get(EntityManagerInterface::class);
        $this->transport = $container->get('messenger.transport.events');
    }

    protected function setUp(): void
    {
        echo(exec('php bin/console cache:clear ') . PHP_EOL);
        echo(exec('php bin/console doctrine:database:drop --force ') . PHP_EOL);
        echo(exec('php bin/console doctrine:database:create ') . PHP_EOL);
        echo(exec('php bin/console doctrine:migrations:migrate -n ') . PHP_EOL);
        echo(exec('php bin/console doctrine:fixtures:load -n ') . PHP_EOL);
    }

    /**
     * @param string $method
     * @param $uri
     * @return ResponseInterface
     * @throws TransportExceptionInterface
     * @throws Exception
     */
    protected function makeRequest(string $method, string $uri, array $data = [], ?string $user = 'admin'): ResponseInterface
    {
        if(in_array($method,[self::METHOD_POST,self::METHOD_PUT]) && empty($data)){
                throw new Exception('The methods post and put requires a body');
        }

        return $this->httpClient->request(
            $method,
            $_ENV['TEST_URL'].$uri,
            ['headers' =>['Authorization' => is_null($user) ? null : 'bearer '.$this->getToken($user)],'json' => $data]
        );
    }

    protected function getToken(string $user = 'admin',int $expectCode = 200):? string
    {
        $response = $this->httpClient->request(
            self::METHOD_POST,
            $_ENV['TEST_URL'].self::API_LOGIN,
            ['json' => $this->logins[$user]]
        );

        if ($response->getStatusCode() !== 200){
            $this->assertEquals($expectCode,$response->getStatusCode());
            return null;
        }
        $body = $response->toArray();
        $this->assertEquals(200,$response->getStatusCode());

        return $body['token'];
    }

    protected function getRepository(string $class): EntityRepository
    {
        return $this->manager->getRepository($class);
    }

}