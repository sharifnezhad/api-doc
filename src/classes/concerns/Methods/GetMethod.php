<?php

namespace ASharifnezhad\ApiDoc\classes\concerns\Methods;

use Mpociot\Reflection\DocBlock;
use Mpociot\Reflection\DocBlock\Tag;

class GetMethod extends Method
{
    public function __construct($config)
    {
        parent::__construct($config);
    }

    public function methodParams( $route, Docblock $classData, Docblock $methodData): array
    {

        return [
            'get' => [
                'security' => [$this->setSecurity($methodData->getTagsByName('authenticated'))],
                'tags' => [$classData->getTagsByName('group')[0]->getContent()],
                'operationId' => $methodData->getShortDescription(),
                'description' => $methodData->getLongDescription()->getContents() ?? '',
                'parameters' => $methodData->hasTag('pathParam') ? $this->setParameters($methodData->getTagsByName('pathParam')) : [],
                'responses' => $methodData->hasTag('response') ? $this->setResponses($methodData->getTagsByName('response')) : null,
                'x-code-samples' => $this->codeSample($route)
            ]
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
