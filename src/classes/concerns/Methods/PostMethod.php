<?php

namespace ASharifnezhad\ApiDoc\classes\concerns\Methods;

use Mpociot\Reflection\DocBlock;
use Mpociot\Reflection\DocBlock\Tag;

class PostMethod extends Method
{
    public function __construct($config)
    {
        parent::__construct($config);
    }

    public function methodParams($route, Docblock $classData, Docblock $methodData): array
    {
        return [
            'post' => [
                'security' => [$this->setSecurity($methodData->getTagsByName('authenticated'))],
                'tags' => [$classData->getTagsByName('group')[0]->getContent()],
                'operationId' => $methodData->getShortDescription(),
                'description' => $methodData->getLongDescription()->getContents() ?? '',
                'requestBody' => $methodData->hasTag('bodyParam') ? $this->setParameters($methodData->getTagsByName('bodyParam')) : [],
                'responses' => $methodData->hasTag('response') ? $this->setResponses($methodData->getTagsByName('response')) : null,
                'x-code-samples' => $this->codeSample($route)

            ]
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
