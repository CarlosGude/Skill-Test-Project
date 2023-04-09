<?php

namespace App\Factory;

use App\DataTransformer\Input\AbstractInputDataTransformer;
use App\DataTransformer\Output\AbstractOutputDataTransformer;
use App\Entity\AbstractEntity;
use App\Exceptions\EntityOutputException;
use App\Logger\DataTransformationFactoryLogger;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DataTransformationFactory implements DataTransformationFactoryInterface
{
    public function __construct(
        protected LoggerInterface $logger,
        protected ValidatorInterface $validator,
        protected array $outputs,
        protected array $inputs,
    ) {
    }

    /**
     * @throws EntityOutputException
     */
    public function get(string $entity, ?string $id = null): ?string
    {
        if (!is_array($this->outputs)) {
            $this->logger->error(DataTransformationFactoryLogger::ERROR_ENTITY_NOT_FOUND, [
                'class' => $entity,
                'id' => $id,
            ]);
            throw new NotFoundHttpException();
        }

        $output = $this->outputs[$entity];
        if (!$output instanceof AbstractOutputDataTransformer) {
            $this->logger->error(DataTransformationFactoryLogger::ERROR_OUTPUT_DATA_TRANSFORMER, [
                'outputClass' => $output::class,
            ]);
            throw new EntityOutputException();
        }

        return $output->get($id);
    }

    /**
     * @return string|null
     *
     * @throws EntityOutputException
     */
    public function post(string $entity, array $data): array|string
    {
        if (!is_array($this->inputs)) {
            $this->logger->error(DataTransformationFactoryLogger::ERROR_ENTITY_NOT_FOUND, [
                'class' => $entity,
            ]);
            throw new NotFoundHttpException();
        }

        $input = $this->inputs[$entity];
        if (!$input instanceof AbstractInputDataTransformer) {
            $this->logger->error(DataTransformationFactoryLogger::ERROR_OUTPUT_DATA_TRANSFORMER, [
                'inputClass' => $input::class,
            ]);
            throw new EntityOutputException();
        }

        $data = $input->post($data);

        return ($data instanceof AbstractEntity) ? $this->get($entity, $data->getUuid()) : $data;
    }

    /**
     * @throws EntityOutputException
     */
    public function put(string $entity, string $id, array $body): string|array
    {
        if (!is_array($this->inputs)) {
            $this->logger->error(DataTransformationFactoryLogger::ERROR_ENTITY_NOT_FOUND, [
                'class' => $entity,
                'id' => $id,
            ]);
            throw new NotFoundHttpException();
        }

        $input = $this->inputs[$entity];
        if (!$input instanceof AbstractInputDataTransformer) {
            $this->logger->error(DataTransformationFactoryLogger::ERROR_INPUT_DATA_TRANSFORMER, [
                'inputClass' => $input::class,
                'id' => $id,
            ]);
            throw new EntityOutputException();
        }

        $data = $input->put($id, $body);

        return ($data instanceof AbstractEntity) ? $this->get($entity, $data->getUuid()) : $data;
    }

    /**
     * @throws EntityOutputException
     */
    public function delete(string $entity, int|string $id): ?AbstractEntity
    {
        if (!is_array($this->inputs)) {
            $this->logger->error(DataTransformationFactoryLogger::ERROR_ENTITY_NOT_FOUND, [
                'class' => $entity,
                'id' => $id,
            ]);
            throw new NotFoundHttpException();
        }

        $input = $this->inputs[$entity];
        if (!$input instanceof AbstractInputDataTransformer) {
            $this->logger->error(DataTransformationFactoryLogger::ERROR_INPUT_DATA_TRANSFORMER, [
                'inputClass' => $input::class,
                'id' => $id,
            ]);
            throw new EntityOutputException();
        }

        return $input->delete($id);
    }
}
