<?php


namespace App\Services;


class StringToSlugService
{
    public static function transformation(string $stingToSlug):? string
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $stingToSlug)));
        $slug = preg_replace('/-+/', '-', $slug);
        return trim($slug, '-');
    }
}