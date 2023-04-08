<?php


namespace App\Tests\Api\Validation;

use App\Tests\Api\AbstractTest;
use App\Tests\Api\PublicRequest\ArticleTest;

/**
 * Class ArticleValidationTest
 * @package App\Tests\Api\Validation
 */
class ArticleValidationTest extends AbstractTest
{

    public function testTitleError():void
    {
        $response = $this->makeRequest(self::METHOD_POST,ArticleTest::API_ARTICLE,[
            'title' => 'TITLE TEST',
        ]);

        $body = $response->toArray(false);
        $this->assertEquals(400,$response->getStatusCode());
        $this->assertArrayHasKey('body',$body);
        $this->assertEquals('This value should not be null.',$body['body']);

    }

    public function testBodyError():void
    {
        $response = $this->makeRequest(self::METHOD_POST,ArticleTest::API_ARTICLE,[
            'body' => 'TITLE TEST',
        ]);

        $body = $response->toArray(false);
        $this->assertEquals(400,$response->getStatusCode());
        $this->assertArrayHasKey('title',$body);
        $this->assertEquals('This value should not be null.',$body['title']);

    }

}