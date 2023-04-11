<?php

namespace App\Makers\DataTransformer;

use App\Exceptions\TypeOrTemplateNotInicializedException;

class SkeletonGenerator
{
    private string $entityName;
    /**
     * @var array|array[]
     */
    private array $dataToBeGenerated = [
        ['type' => 'input', 'template' => 'dataTransformer'],
        ['type' => 'output', 'template' => 'dataTransformer'],
        ['type' => 'input', 'template' => 'dto'],
        ['type' => 'output', 'template' => 'dto'],
    ];
    private ?string $template = null;
    private ?string $type = null;
    private ?string $fileContents;

    public function __construct(
        protected string $projectDir,
        protected string $outputDataTransformerTemplate,
        protected string $inputDataTransformerTemplate,
        protected string $outputDtoTemplate,
        protected string $inputDtoTemplate,
    ) {
    }

    public function initialize(string $type, string $template, ?string $entity): self
    {
        $this->type = $type;
        $this->template = $template;
        $this->entityName = $entity;

        return $this;
    }

    public function generateFileContent(): self
    {
        if (!$this->template || !$this->type) {
            throw new TypeOrTemplateNotInicializedException();
        }

        $this->fileContents = $this->replaceContent(
            ['{{entity}}', '{{entity_lowercase}}'],
            [ucfirst($this->entityName), $this->entityName],
            $this->getTemplateContents($this->type, $this->template),
            $this->type,
            $this->template
        );

        return $this;
    }

    /**
     * @throws \Exception
     */
    public function __invoke(string $entity): void
    {
        foreach ($this->dataToBeGenerated as $make) {
            $template = $this->initialize($make['type'], $make['template'],$entity)->generateFileContent();
            $template->putContents();
        }
    }

    protected function putContents(): void
    {
        if (!$this->template || !$this->type) {
            throw new TypeOrTemplateNotInicializedException();
        }

        $templatePath = ucfirst($this->template).'Path';
        if ($content = $this->getContet() && !file_exists($path = $this->$templatePath($this->type, ucfirst($this->entityName)))) {
            file_put_contents($path, $content);
        }
    }

    protected function replaceContent(array $search, array $replace, string $subject, string $type, string $template): string
    {
        return str_replace($search, $replace, $this->getTemplateContents($type, $template));
    }

    protected function getTemplateContents(string $type, string $template): string
    {
        $template = $type.ucfirst($template).'Template';
        $template = str_replace('/', DIRECTORY_SEPARATOR, $this->$template);

        return file_get_contents($this->projectDir.DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.$template);
    }

    protected function dataTransformerPath(string $type, string $entity): string
    {
        return $this->projectDir.
            DIRECTORY_SEPARATOR.'src'.
            DIRECTORY_SEPARATOR.'DataTransformer'.
            DIRECTORY_SEPARATOR.ucfirst($type).
            DIRECTORY_SEPARATOR.$entity.'DataTransformer.php';
    }

    protected function dtoPath(string $type, string $entity): string
    {
        return $this->projectDir.
            DIRECTORY_SEPARATOR.'src'.
            DIRECTORY_SEPARATOR.'Dto'.
            DIRECTORY_SEPARATOR.ucfirst($type).
            DIRECTORY_SEPARATOR.$entity.'Dto.php';
    }

    public function getContet(): ?string
    {
        return $this->fileContents;
    }
}
