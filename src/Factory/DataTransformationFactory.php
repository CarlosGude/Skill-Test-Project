<?php

namespace App\Factory;

use App\DataTransformer\Input\AbstractInputDataTransformer;
use App\DataTransformer\Output\AbstractOutputDataTransformer;
use App\Entity\AbstractEntity;
use App\Exceptions\EntityOutputException;
use App\Logger\DataTransformationFactoryLogger;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DataTransformationFactory implements DataTransformationFactoryInterface
{
    public function __construct(
        protected LoggerInterface $logger,
        protected ValidatorInterface $validator,
        protected ContainerInterface $container,
        protected string $outputNamespace = 'App\DataTransformer\Output\\',
        protected string $inputNamespace = 'App\DataTransformer\Input\\',
        protected string $outputNamespaceSubFix = 'OutputDataTransformer',
        protected string $inputNamespaceSubFix = 'InputDataTransformer'
    ) {
    }

    protected function getOutput(string $entity): AbstractOutputDataTransformer
    {
        $class = $this->outputNamespace.ucfirst($entity).$this->outputNamespaceSubFix;
        if (!class_exists($class)) {
            $this->logger->error(DataTransformationFactoryLogger::ERROR_ENTITY_NOT_FOUND, ['class' => $entity]);
            throw new NotFoundHttpException();
        }

        $output = $this->container->get($class);

        if (!$output instanceof AbstractOutputDataTransformer) {
            $this->logger->error(DataTransformationFactoryLogger::ERROR_OUTPUT_DATA_TRANSFORMER, [
                'outputClass' => $class,
            ]);
            throw new EntityOutputException();
        }

        return $output;
    }

    protected function getInput(string $entity): AbstractInputDataTransformer
    {
        $class = $this->inputNamespace.ucfirst($entity).$this->inputNamespaceSubFix;
        if (!class_exists($class)) {
            $this->logger->error(DataTransformationFactoryLogger::ERROR_ENTITY_NOT_FOUND, ['class' => $entity]);
            throw new NotFoundHttpException();
        }

        $input = $this->container->get($class);

        if (!$input instanceof AbstractInputDataTransformer) {
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
