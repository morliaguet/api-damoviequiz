<?php

namespace App\Service;

use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class CustomObjectNormalizer extends ObjectNormalizer
{
    public function normalize($object, $format = null, array $context = [])
    {
        $data = parent::normalize($object, $format, $context);

        $data = array_map(function($value) {
            if (is_numeric($value)) {
                $value = (int)$value;
            }
            return $value;
        }, $data);

        return array_filter($data, function ($value) {
            return null !== $value;
        });
    }
}