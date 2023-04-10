<?php

namespace App\Factory;

use App\DataTransformer\Input\AbstractDataTransformer as InputDataTransformer;
use App\DataTransformer\Output\AbstractDataTransformer as OutputDataTransformer;
use App\Entity\AbstractEntity;
use App\Exceptions\EntityOutputException;
use App\Logger\DataTransformationFactoryLogger;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DataTransformationFactory implements DataTransformationFactoryInterface
{
    protected const DATA_TRANSFORMER_OUTPUT_PREFIX = 'App\DataTransformer\Output\\';
    protected const DATA_TRANSFORMER_INPUT_PREFIX = 'App\DataTransformer\Input\\';
    protected const DATA_TRANSFORMER_SUFFIX = 'DataTransformer';

    public function __construct(
        protected LoggerInterface $logger,
        protected ValidatorInterface $validator,
        protected ContainerInterface $container,
    ) {
    }

    protected function getOutput(string $entity): OutputDataTransformer
    {
        $class = self::DATA_TRANSFORMER_OUTPUT_PREFIX.ucfirst($entity).self::DATA_TRANSFORMER_SUFFIX;
        if (!class_exists($class)) {
            $this->logger->error(DataTransformationFactoryLogger::ERROR_ENTITY_NOT_FOUND, ['class' => $entity]);
            throw new NotFoundHttpException();
        }

        $output = $this->container->get($class);

        if (!$output instanceof OutputDataTransformer) {
            $this->logger->error(DataTransformationFactoryLogger::ERROR_OUTPUT_DATA_TRANSFORMER, [
                'outputClass' => $class,
            ]);
            throw new EntityOutputException();
        }

        return $output;
    }

    protected function getInput(string $entity): InputDataTransformer
    {
        $class = self::DATA_TRANSFORMER_INPUT_PREFIX.ucfirst($entity).self::DATA_TRANSFORMER_SUFFIX;
        if (!class_exists($class)) {
            $this->logger->error(DataTransformationFactoryLogger::ERROR_ENTITY_NOT_FOUND, ['class' => $entity]);
            throw new NotFoundHttpException();
        }

        $input = $this->container->get($class);

        if (!$input instanceof InputDataTransformer) {
            $this->logger->error(DataTransformationFactoryLogger::ERROR_OUTPUT_DATA_TRANSFORMER, [
                'outputClass' => $class,
            ]);
            throw new EntityOutputException();
        }

        return $input;
    }

    /**
     * @throws EntityOutputException
     */
    public function get(string $entity, ?string $id = null): ?string
    {
        $output = $this->getOutput($entity);

        return $output->get($id);
    }

    /**
     * @return string|null
     *
     * @throws EntityOutputException
     */
    public function post(string $entity, array $data): array|string
    {
        $data = $this->getInput($entity)->post($data);

        return ($data instanceof AbstractEntity) ? $this->get($entity, $data->getUuid()) : $data;
    }

    /**
     * @throws EntityOutputException
     */
    public function put(string $entity, string $id, array $body): string|array
    {
        $data = $this->getInput($entity)->put($id, $body);

        return ($data instanceof AbstractEntity) ? $this->get($entity, $data->getUuid()) : $data;
    }

    /**
     * @throws EntityOutputException
     */
    public function delete(string $entity, int|string $id): null|array|AbstractEntity
    {
        return $this->getInput($entity)->delete($id);
    }
}
