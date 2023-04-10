<?php

namespace App\MakerDataTransformer;

class MakerDataTransformer
{
    public function __construct(
        protected string $projectDir,
        protected string $outputDataTransformerTemplate,
        protected string $inputDataTransformerTemplate,
        protected string $outputDtoTemplate,
        protected string $inputDtoTemplate,
    ) {
    }

    public function generateClass(string $entity): void
    {
        $capitalizedEntityName = ucfirst($entity);

        if (!file_exists($path = $this->dataTransformerPath('output', $capitalizedEntityName))) {
            file_put_contents(
                $path,
                $this->replaceContent(
                    ['{{entity}}'],
                    [$capitalizedEntityName],
                    $this->getTemplateContents('output', 'DataTransformer'),
                    'output',
                    'DataTransformer'
                )
            );
        }

        if (!file_exists($path = $this->dtoPath('output', $capitalizedEntityName))) {
            file_put_contents(
                $path,
                $this->replaceContent(
                    ['{{entity}}'],
                    [$capitalizedEntityName],
                    $this->getTemplateContents('output', 'Dto'),
                    'output',
                    'Dto'
                )
            );
        }

        if (!file_exists($path = $this->dataTransformerPath('input', $capitalizedEntityName))) {
            file_put_contents(
                $path,
                $this->replaceContent(
                    ['{{entity}}'],
                    [$capitalizedEntityName],
                    $this->getTemplateContents('input', 'DataTransformer'),
                    'input',
                    'DataTransformer'
                )
            );
        }

        if (!file_exists($path = $this->dtoPath('input', $capitalizedEntityName))) {
            file_put_contents(
                $path,
                $this->replaceContent(
                    ['{{entity}}', '{{entity_lowercase}}'],
                    [$capitalizedEntityName, $entity],
                    $this->getTemplateContents('input', 'Dto'),
                    'input',
                    'Dto'
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
        $template = $type.$template.'Template';
        $template = str_replace('/', DIRECTORY_SEPARATOR, $this->$template);

        return file_get_contents($this->projectDir.DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.$template);
    }

    protected function dataTransformerPath(string $type, string $entity): string
    {
        return $this->projectDir.
            DIRECTORY_SEPARATOR.'src'.
            DIRECTORY_SEPARATOR.'DataTransformer'.
            DIRECTORY_SEPARATOR.ucfirst($type).
            DIRECTORY_SEPARATOR.$entity.ucfirst($type).'DataTransformer.php';
    }

    protected function dtoPath(string $type, string $entity): string
    {
        return $this->projectDir.
            DIRECTORY_SEPARATOR.'src'.
            DIRECTORY_SEPARATOR.'Dto'.
            DIRECTORY_SEPARATOR.ucfirst($type).
            DIRECTORY_SEPARATOR.$entity.ucfirst($type).'Dto.php';
    }
}
