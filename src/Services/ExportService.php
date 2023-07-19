<?php

declare(strict_types=1);

namespace App\Services;

use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\SerializerInterface;

final readonly class ExportService
{
    private const QUANTITY = 3;
    public const AVAILABLE_FORMATS = ['csv', 'json', 'xml'];
    private string $currentFormat;
    public function __construct(
        private SerializerInterface $serializer,
        private EntityManagerInterface $em,
    ) {
    }

    public function formatContent(string $className, string $format): array
    {
        $this->validateAndSetFormat($format);
        $arrContent[] = $this->normalizeHeaderEntity($className);

        $repository = $this->em->getRepository($className);

        $page = 0;

        while (true) {
            $query = $repository->createQueryBuilder('h')
                ->setFirstResult($page * self::QUANTITY)
                ->setMaxResults(self::QUANTITY)
                ->getQuery();

            $result = $query->getResult();
            foreach ($result as $instance) {
                $arrContent[] = $this->normalizeValuesEntity($instance);
            }
            if(count($result) < self::QUANTITY) {
                break;
            }

            $this->em->clear();

            ++$page;
        }

        $fileName = $this->ArrayContentToFile($arrContent, $className);

        return ['file' => $fileName, 'content' => $arrContent];

    }


    private function normalizeValuesEntity(Object $object): array
    {
        $arrayEntity = $this->serializer->normalize($object, null, ['groups' => 'export']);
        return array_values($arrayEntity);
    }

    private function normalizeHeaderEntity(string $className): array
    {
        if(!class_exists($className)) {
            throw new \LogicException(sprintf('The class %s does not exist', $className));
        }
        return $this->em->getClassMetadata($className)->getFieldNames();
    }

    private function ArrayContentToFile(array $arrayContent, string $className): string
    {
        $classFileName = explode('\\', $className)[2];
        $fileName = '/var/www/exportFiles/'.$classFileName.'.'.$this->currentFormat;

        $options = null;
        if ($this->currentFormat === 'csv'){
            $options = [CsvEncoder::NO_HEADERS_KEY => true];
        }

        $fullStringContent = $this->replaceBoolValueToString($arrayContent);
        $contentFormatted = $this->serializer->encode($fullStringContent, $this->currentFormat,$options);

        $bytesWritten = file_put_contents($fileName, $contentFormatted);
        if ($bytesWritten === false) {
            throw new \RuntimeException(sprintf('Could not write to file %s', $fileName));
        }

        // file_put_contents($fileName, $contentFormatted);
        return explode('/', $fileName)[4] ;
    }

    private function replaceBoolValueToString(array $arrayContent): array
    {
        foreach ($arrayContent as &$line) {
            foreach ($line as $key => $value) {
                if (is_bool($value)) {
                    $line[$key] = $value ? 'true' : 'false';
                }
            }
        }
        return $arrayContent;
    }

    private function validateAndSetFormat(string $format): void
    {
        if (!in_array($format, self::AVAILABLE_FORMATS)) {
            throw new InvalidArgumentException('Format not supported');
        }
        $this->currentFormat = $format;
    }

}
