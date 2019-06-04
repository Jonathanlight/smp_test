<?php

namespace App\Services;

use JMS\Serializer\SerializerBuilder;

class SerializerService
{
    /**
     * @param mixed  $data
     * @param string $format
     *
     * @return string
     */
    public function serialize($data, string $format = 'json'): string
    {
        $serializer = SerializerBuilder::create()->build();

        return $serializer->serialize($data, $format);
    }
}
