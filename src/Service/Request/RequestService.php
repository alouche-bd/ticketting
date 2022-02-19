<?php

declare(strict_types=1);

namespace App\Service\Request;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Response;


class RequestService
{
    /**
     * @return mixed
     */
    public static function getField(Request $request, string $fieldName, bool $isRequired = true, bool $isArray = false)
    {
        $requestData = \json_decode($request->getContent(), true);

        if ($isArray) {
            $arrayData = self::arrayFlatten($requestData);

            foreach ($arrayData as $key => $value) {
                if ($fieldName === $key) {
                    return $value;
                }
            }

            if ($isRequired) {
                throw new BadRequestHttpException(\sprintf('Missing field %s', $fieldName));
            }

            return null;
        }

        if (\array_key_exists($fieldName, $requestData)) {
            return $requestData[$fieldName];
        }

        if ($isRequired) {
            throw new BadRequestHttpException(\sprintf('Missing field %s', $fieldName));
        }

        return null;
    }

    public static function arrayFlatten(array $array): array
    {
        $return = [];

        foreach ($array as $key => $value) {
            if (\is_array($value)) {
                $return = \array_merge($return, self::arrayFlatten($value));
            } else {
                $return[$key] = $value;
            }
        }

        return $return;
    }

    public static function returnJsonData($data)
    {
        $encoders = [new JsonEncoder()];

        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);

        $jsonContent = $serializer->serialize($data, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);

        $response = new Response($jsonContent, 200);

        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
