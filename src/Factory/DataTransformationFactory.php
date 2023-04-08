<?php


namespace App\Factory;


use App\DataTransformer\Input\AbstractInputDataTransformer;
use App\DataTransformer\Output\AbstractOutputDataTransformer;
use App\Entity\AbstractEntity;
use App\Exceptions\DataTransformerException;
use App\Exceptions\EntityOutputException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DataTransformationFactory implements DataTransformationFactoryInterface
{
    public function __construct(
        protected ValidatorInterface $validator,
        protected array $outputs,
        protected array $inputs,
    ){}

    /**
     * @param string $entity
     * @param string|null $id
     * @return string|null
     * @throws EntityOutputException
     */
    public function get(string $entity, ?string $id = null):? string
    {
        if(!is_array($this->outputs)){
            throw new NotFoundHttpException();
        }

        $output = $this->outputs[$entity];
        if(!$output instanceof AbstractOutputDataTransformer){
            throw new EntityOutputException();
        }

        return $output->get($id);
    }

    /**
     * @param string $entity
     * @param array $data
     * @return string|null
     * @throws EntityOutputException
     */
    public function post(string $entity, array $data):array|string
    {
        if(!is_array($this->inputs)){
            throw new NotFoundHttpException();
        }

        $input = $this->inputs[$entity];
        if(!$input instanceof AbstractInputDataTransformer){
            throw new EntityOutputException();
        }

        $data = $input->post($data);
        if($data instanceof AbstractEntity){
            return $this->get($entity,$data->getUuid());
        }

        return $data;

    }


    /**
     * @param string $entity
     * @param string $id
     * @param array $body
     * @return string|null
     * @throws EntityOutputException
     */
    public function put(string $entity, string $id, array $body):? string
    {
        if(!is_array($this->inputs)){
            throw new NotFoundHttpException();
        }

        $input = $this->inputs[$entity];
        if(!$input instanceof AbstractInputDataTransformer){
            throw new EntityOutputException();
        }

        $data = $input->put($id,$body);

        if(!$data instanceof AbstractEntity){
            return null;
        }
        return $this->get($entity,$data->getUuid());
    }

    /**
     * @param string $entity
     * @param int|string $id
     * @return AbstractEntity|null
     * @throws EntityOutputException
     */
    public function delete(string $entity, int|string $id):? AbstractEntity
    {
        if(!is_array($this->inputs)){
            throw new NotFoundHttpException();
        }

        $input = $this->inputs[$entity];
        if(!$input instanceof AbstractInputDataTransformer){
            throw new EntityOutputException();
        }

        return $input->delete($id);

    }
}