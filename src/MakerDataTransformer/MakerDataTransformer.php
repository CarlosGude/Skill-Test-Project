<?php

namespace App\MakerDataTransformer;

use App\Exceptions\TypeOrTemplateNotInicializedException;

/**
 * TODO: Test pending.
 */
class MakerDataTransformer
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

    public function setTypeAndTemplate(string $type, string $template): self
    {
        $this->type = $type;
        $this->template = $template;

        return $this;
    }

    public function generateFileTemplates(): self
    {
        if(!$this->template || !$this->type){
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
        $this->entityName = $entity;
        foreach ($this->dataToBeGenerated as $make) {
            $template = $this->setTypeAndTemplate($make['type'],$make['template'])->generateFileTemplates();
            $template->putContents();
        }
    }

    protected function putContents(): void
    {
        if(!$this->template || !$this->type){
            throw new TypeOrTemplateNotInicializedException();
        }

        $templatePath = ucfirst($this->template).'Path';
        if ($this->fileContents && !file_exists($path = $this->$templatePath($this->type, ucfirst($this->entityName)))) {
            file_put_contents($path, $this->fileContents);
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
}
