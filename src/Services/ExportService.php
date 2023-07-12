<?php

declare(strict_types=1);

namespace App\Services;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use InvalidArgumentException;
use Symfony\Component\Serializer\SerializerInterface;

final readonly class ExportService
{
    private const QUANTITY = 3;
    public function __construct(
        private SerializerInterface $serializer,
        private EntityManagerInterface $em,
    ) {
    }

    /**
    public function contentToCsv(string $className): array
    {
        $csvLine[] = $this->headerEntity($className);
        $allContent = $this->em->getRepository($className)->findAll();
        foreach ($allContent as $instance) {
            $csvLine[] = $this->entityToCsv($instance);
        }
        return $csvLine;

        $fp = fopen($className.'.csv', 'w');
        foreach ($csvLine as $fields) {
            fputcsv($fp, $fields);
        }
        fclose($fp);
        return file_get_contents() ;
    }
    **/

    public function formatContentToCsv(string $className): array
    {
        $arrContent[] = $this->normalizeHeaderEntity($className);

        $repository = $this->em->getRepository($className);

        $page = 0;

        while (true) {
            $query = $repository->createQueryBuilder('h')
                ->setFirstResult($page * self::QUANTITY)
                ->setMaxResults(self::QUANTITY)
                ->getQuery();

            $paginator = new Paginator($query);

            foreach ($paginator as $instance) {
                $arrContent[] = $this->normalizeValuesEntity($instance);
            }

            if (count($paginator->getIterator()) < self::QUANTITY) {
                break;
            }
            $this->em->clear();

            ++$page;
        }

        $fileName = $this->ArrayContentToCsvFile($arrContent, $className);

        return ['file' => $fileName, 'content' => $arrContent];
    }


    public function normalizeValuesEntity(Object $object): array
    {
        $arrayEntity = $this->serializer->normalize($object, 'csv', ['groups' => 'export']);
        return array_values($arrayEntity);
    }

    public function normalizeHeaderEntity(string $className): array
    {
        if(!class_exists($className)) {
            throw new InvalidArgumentException($className.' do not exist');
        }
        return $this->em->getClassMetadata($className)->getFieldNames();
    }

    public function ArrayContentToCsvFile(array $arrayContent, string $className): string
    {
        $classFileName = explode('\\', $className)[2];
        $fileName = '/var/www/exportFiles/'.$classFileName.'.csv';

        $fullStringContent = $this->replaceBoolValueToString($arrayContent);
        $fp = fopen($fileName, 'w');
        foreach ($fullStringContent as $fields) {
            fputcsv($fp, $fields);
        }
        fclose($fp);
        return explode('/', $fileName)[4] ;
    }

    public function replaceBoolValueToString(array $arrayContent): array
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

}
