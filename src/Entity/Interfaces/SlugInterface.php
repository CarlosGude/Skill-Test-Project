<?php

namespace App\Entity\Interfaces;

interface SlugInterface
{
    public function getFieldToSlug(): ?string;

    public function getSlugFieldName(): string;

    public function setSlug(string $slug): ?self;

    public function getSlug(): ?string;
}
