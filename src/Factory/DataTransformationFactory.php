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
     * @param int|string|null $id
     * @param string $field
     * @return string|null
     * @throws EntityOutputException
     */
    public function get(string $entity, int|string|null $id, string $field = 'id'):? string
    {
        if(!is_array($this->outputs)){
            throw new NotFoundHttpException();
        }

        $output = $this->outputs[$entity];
        if(!$output instanceof AbstractOutputDataTransformer){
            throw new EntityOutputException();
        }

        return $output->get($id,$field);
    }

    /**
     * @param string $entity
     * @param array $data
     * @return string|array
     * @throws EntityOutputException
     * @throws DataTransformerException
     */
    public function post(string $entity, array $data): string | array
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
            return $this->get($entity,$data->getId());
        }

        return $data;

    }

    /**
     * @param string $entity
     * @param int|string $id
     * @return AbstractEntity|null
     * @throws EntityOutputException
     */
    public function put(string $entity, int|string $id, array $body):? string
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
        return $this->get($entity,$data->getId());
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

        $data = $input->delete($id);
        if(!$data instanceof AbstractEntity){
            return null;
        }

        return $data;

    }
}