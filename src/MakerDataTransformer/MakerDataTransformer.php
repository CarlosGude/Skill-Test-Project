<?php

namespace App\MakerDataTransformer;

/**
 * TODO: Test pending.
 */
class MakerDataTransformer
{
    private string $entityName;
    /**
     * @var array|array[]
     */
    private array $dataToBeGenerated;

    public function __construct(
        protected string $projectDir,
        protected string $outputDataTransformerTemplate,
        protected string $inputDataTransformerTemplate,
        protected string $outputDtoTemplate,
        protected string $inputDtoTemplate,
    ) {
        $this->dataToBeGenerated = [
            ['type' => 'input', 'template' => 'dataTransformer'],
            ['type' => 'output', 'template' => 'dataTransformer'],
            ['type' => 'input', 'template' => 'dto'],
            ['type' => 'output', 'template' => 'dto'],
        ];
    }

    public function __invoke(string $entity): void
    {
        $this->entityName = $entity;
        foreach ($this->dataToBeGenerated as $make) {
            $this->generate($make['type'], $make['template']);
        }
    }

    public function generate(string $type, string $template): void
    {
        $templatePath = ucfirst($template).'Path';
        if (!file_exists($path = $this->$templatePath($type, ucfirst($this->entityName)))) {
            file_put_contents(
                $path,
                $this->replaceContent(
                    ['{{entity}}', '{{entity_lowercase}}'],
                    [ucfirst($this->entityName), $this->entityName],
                    $this->getTemplateContents($type, $template),
                    $type,
                    $template
                )
            );
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
