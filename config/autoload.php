<?php

require_once __DIR__."/../vendor/autoload.php";

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

define("PROJECT_ROOT_DIR", __DIR__."/../");

$builder = new DI\ContainerBuilder();
$builder->useAnnotations(true);
$builder->addDefinitions([
    Jarenal\Core\Config::class => DI\Factory(function () {
        return new \Jarenal\Core\Config(__DIR__ . "/config.yaml");
    }),
    Jarenal\Core\Database::class => DI\Factory(function () {
        return new \Jarenal\Core\Database(new \mysqli(), new \Jarenal\Core\Config(__DIR__ . "/config.yaml"));
    }),
    Symfony\Component\Serializer\Serializer::class => DI\Factory(function () {
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $normalizers[0]->setCircularReferenceHandler(function ($object, string $format = null, array $context = []) {
            return $object->getId();
        });
        return new Symfony\Component\Serializer\Serializer($normalizers, $encoders);
    })
]);

$container = $builder->build();
