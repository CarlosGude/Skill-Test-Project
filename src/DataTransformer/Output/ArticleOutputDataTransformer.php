<?php


namespace App\DataTransformer\Output;

use App\Dto\Output\ArticleOutputDto;
use App\Entity\Article;

class ArticleOutputDataTransformer extends AbstractOutputDataTransformer
{

    protected function getOutputDto()
    {
        return new ArticleOutputDto(true);
    }

    protected function getClass()
    {
        return Article::class;
    }
}