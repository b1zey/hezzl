<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

abstract class RedisController extends AbstractController {
    /**
     * @var \Redis $redis
     */
    protected $redis;

    public function __construct()
    {
        $this->redis = RedisAdapter::createConnection('redis://localhost');
    }

    protected function getSerializer() : Serializer
    {
        return new Serializer([new ObjectNormalizer()], [new XmlEncoder(), new JsonEncoder()]);
    }
}