<?php

namespace ASharifnezhad\ApiDoc\Classes\Concerns\Methods;

use Mpociot\Reflection\DocBlock;
use Mpociot\Reflection\DocBlock\Tag;

class GetMethod extends Method
{
    public function __construct($config)
    {
        parent::__construct($config);
    }

    public function methodParams(Docblock $classData, Docblock $methodData, $customData): array
    {
        $this->classData = $classData;
        $this->methodData = $methodData;

        $getMethodParams = array_merge($this->setDefaultParams($customData), [
            'parameters' => $methodData->hasTag('pathParam') ? $this->setParameters($methodData->getTagsByName('pathParam')) : [],
            'responses' => $methodData->hasTag('response') ? $this->setResponses($methodData->getTagsByName('response')) : null,
        ]);

        return [
            'get' => $getMethodParams
        ];
    }

    public function setParameters(array $pathParams)
    {
        return collect($pathParams)->map(function (Tag $tag) {
            $values = json_decode($tag->getContent());

            return [
                'in' => 'path',
                'name' => $values->key,
                'description' => $values->description ?? null,
                'required' => $values->required ?? false,
                'schema' => [
                    'type' => $values->type ?? null,
                    'example' => $values->example ?? null
                ]
            ];
        });
    }
}
