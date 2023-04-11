<?php

namespace App\Tests\Unitary;

use App\Makers\DataTransformer\SkeletonGenerator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @property $kernelPath
 */
class SkeletonDataTransformerTest extends KernelTestCase
{
    private SkeletonGenerator $maker;
    private string $kernelPath;

    protected const TYPE_OUTPUT = 'output';
    protected const TYPE_INPUT = 'input';
    protected const TEMPLATE_DATA_TRANSFORMER = 'dataTransformer';
    protected const FILE_DATA_TRANSFORMER = 'TestDataTransformer.txt';
    protected const TEMPLATE_DTO = 'dto';
    protected const FILE_DTO = 'Dto.txt';
    protected const ENTITY_NAME = 'test';

    protected function setUp(): void
    {
        $container = static::getContainer();
        $this->kernelPath = $container->get('kernel')->getProjectDir();

        $outputDataTransformerTemplate = $_ENV['OUTPUT_DATA_TRANSFORMER_TEMPLATE'];
        $inputDataTransformerTemplate = $_ENV['INPUT_DATA_TRANSFORMER_TEMPLATE'];

        $outputDtoTemplate = $_ENV['OUTPUT_DTO_TEMPLATE'];
        $inputDtoTemplate = $_ENV['INPUT_DTO_TEMPLATE'];

        $this->assertNotNull($this->kernelPath);
        $this->assertNotNull($outputDataTransformerTemplate);
        $this->assertNotNull($inputDataTransformerTemplate);
        $this->assertNotNull($outputDtoTemplate);
        $this->assertNotNull($inputDtoTemplate);

        $this->maker = new SkeletonGenerator(
            $this->kernelPath,
            $outputDataTransformerTemplate,
            $inputDataTransformerTemplate,
            $outputDtoTemplate,
            $inputDtoTemplate
        );
    }

    protected function getExpectedClassDirectory(string $base, string $type): string
    {
        return $base.
            DIRECTORY_SEPARATOR.'tests'.
            DIRECTORY_SEPARATOR.'Unitary'.
            DIRECTORY_SEPARATOR.'ExpectedClass'.
            DIRECTORY_SEPARATOR.ucfirst($type).
            DIRECTORY_SEPARATOR;
    }

    public function testOutputDataTransformer(): void
    {
        $file = $this->maker
            ->initialize(self::TYPE_OUTPUT, self::TEMPLATE_DATA_TRANSFORMER, self::ENTITY_NAME)
            ->generateFileContent()
        ;

        $testMockFilePath = $this->getExpectedClassDirectory($this->kernelPath, self::TYPE_OUTPUT);

        $this->assertNotNull($file);
        $this->assertFileExists($mock = $testMockFilePath.self::FILE_DATA_TRANSFORMER);
        $this->assertEquals($file->getContet(), file_get_contents($mock));
    }

    public function testInputDataTransformer(): void
    {
        $file = $this->maker
            ->initialize(self::TYPE_INPUT, self::TEMPLATE_DATA_TRANSFORMER, self::ENTITY_NAME)
            ->generateFileContent()
        ;
        $testMockFilePath = $this->getExpectedClassDirectory($this->kernelPath, self::TYPE_INPUT);
        $this->assertNotNull($file);
        $this->assertFileExists($mock = $testMockFilePath.self::FILE_DATA_TRANSFORMER);
        $this->assertEquals($file->getContet(), file_get_contents($mock));
    }

    public function testOutputDto(): void
    {
        $file = $this->maker
            ->initialize(self::TYPE_OUTPUT, self::TEMPLATE_DTO, self::ENTITY_NAME)
            ->generateFileContent()
        ;

        $testMockFilePath = $this->getExpectedClassDirectory($this->kernelPath, self::TYPE_OUTPUT);

        $this->assertNotNull($file);
        $this->assertFileExists($mock = $testMockFilePath.self::FILE_DTO);
        $this->assertEquals($file->getContet(), file_get_contents($mock));
    }

    public function testInputDto(): void
    {
        $file = $this->maker
            ->initialize(self::TYPE_INPUT, self::TEMPLATE_DTO, self::ENTITY_NAME)
            ->generateFileContent()
        ;

        $testMockFilePath = $this->getExpectedClassDirectory($this->kernelPath, self::TYPE_INPUT);

        $this->assertNotNull($file);
        $this->assertFileExists($mock = $testMockFilePath.self::FILE_DTO);
        $this->assertEquals($file->getContet(), file_get_contents($mock));
    }
}
