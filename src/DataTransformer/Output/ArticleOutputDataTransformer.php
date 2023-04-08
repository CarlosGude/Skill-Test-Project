<?php


namespace App\DataTransformer\Output;

use App\Dto\Output\ArticleOutputDto;
use App\Dto\Output\OutputInterface;
use App\Entity\Article;

class ArticleOutputDataTransformer extends AbstractOutputDataTransformer
{

    protected function getOutputDto(): OutputInterface
    {
        return new ArticleOutputDto(true);
    }

    protected function getClass(): string
    {
        return Article::class;
    }
}