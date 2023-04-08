<?php


namespace App\DataTransformer\Input;

use App\Dto\Input\ArticleInputDto;
use App\Dto\Input\InputInterface;
use App\Entity\Article;

class ArticleInputDataTransformer extends AbstractInputDataTransformer
{

    protected function getClass(): string
    {
        return Article::class;
    }

    protected function getInputDto(array $data): InputInterface
    {
        return new ArticleInputDto($data);
    }
}