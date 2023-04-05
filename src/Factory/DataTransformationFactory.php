<?php


namespace App\Factory;


use App\DataTransformer\AbstractDataTransformer;
use Exception;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DataTransformationFactory implements DataTransformationFactoryInterface
{
    public function __construct(protected array $outputs){}

    public function transformation(string $entity, ?string $field, string|int|null $id):? string
    {
        if(!is_array($this->outputs)){
            throw new NotFoundHttpException();
        }

        $output = $this->outputs[$entity];
        if(!$output instanceof AbstractDataTransformer){
            throw new Exception();
        }

        return $output->transformation('GET',$field,$id);
    }
}