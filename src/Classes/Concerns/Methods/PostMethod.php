<?php

namespace ASharifnezhad\ApiDoc\Classes\Concerns\Methods;

use Mpociot\Reflection\DocBlock;
use Mpociot\Reflection\DocBlock\Tag;

class PostMethod extends Method
{
    public function __construct($config)
    {
        parent::__construct($config);
    }

    public function methodParams(Docblock $classData, Docblock $methodData, $customData): array
    {
        $this->classData = $classData;
        $this->methodData = $methodData;

        $postMethodParams = array_merge($this->setDefaultParams($customData), [
            'requestBody' => $methodData->hasTag('bodyParam') ? $this->setParameters($methodData->getTagsByName('bodyParam')) : [],
            'responses' => $methodData->hasTag('response') ? $this->setResponses($methodData->getTagsByName('response')) : null,
        ]);
        return [
            'post' => $postMethodParams
        ];
    }

    public function setParameters(array $pathParams)
    {
        $requiredData = collect($pathParams)->filter(function (Tag $tag) {
            $values = json_decode($tag->getContent());
            return isset($values->required) && $values->required;
        })->map(function (Tag $tag) {
            $values = json_decode($tag->getContent());
            return $values;
        });

        $propertiesData = collect($pathParams)->mapWithKeys(function (Tag $tag) {
            $values = json_decode($tag->getContent());
            return [
                $values->key => [
                    'type' => $values->type,
                    'example' => $values->example ?? null,
                    'description' => $values->description ?? null,
                ]
            ];
        });
        return [
            'required' => true,
            'content' => [
                'application/json' => [
                    'schema' => [
                        'type' => 'object',
                        'required' => $requiredData->pluck('key'),
                        'properties' => $propertiesData
                    ],
                ]
            ]
        ];
    }
}
