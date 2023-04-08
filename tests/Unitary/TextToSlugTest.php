<?php

namespace App\Tests\Unitary;

use App\Services\StringToSlugService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TextToSlugTest extends KernelTestCase
{
    public function testSlugToTextTransformation(): void
    {
        $text = 'Hola Mundo!';
        $expected = 'hola-mundo';
        $slug = StringToSlugService::transformation($text);

        $this->assertNotEquals($text, $slug);
        $this->assertEquals($expected, $slug);
        $this->assertStringNotContainsString(' ', $slug);
        $this->assertStringContainsString('-', $slug);
        $this->assertNotEquals(strtolower($text), $slug);
    }
}
