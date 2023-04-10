<?php

namespace App\DataTransformer\Output;

use App\Dto\Output\ArticleDto;
use App\Dto\Output\OutputInterface;
use App\Entity\Article;

class ArticleDataTransformer extends AbstractDataTransformer
{
    protected function getOutputDto(): OutputInterface
    {
        return new ArticleDto(true);
    }

    protected function getClass(): string
    {
        return Article::class;
    }
}
