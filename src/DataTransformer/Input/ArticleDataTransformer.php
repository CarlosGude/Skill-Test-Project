<?php

namespace App\DataTransformer\Input;

use App\Dto\Input\ArticleDto;
use App\Dto\Input\InputInterface;
use App\Entity\Article;

class ArticleDataTransformer extends AbstractDataTransformer
{
    protected function getClass(): string
    {
        return Article::class;
    }

    protected function getInputDto(array $data): InputInterface
    {
        return new ArticleDto($data);
    }
}
