<?php


namespace App\Entity\Interfaces;


interface SlugInterface
{
    public function getFieldToSlug(): string;
    public function getFieldName(): string;
    public function setSlug(string $slug):? self;
    public function getSlug():? string;
}