<?php


namespace App\Dto\Input;


use App\Entity\AbstractEntity;

interface InputInterface
{
    public function __construct(array $data);
    public function post(): AbstractEntity;
}