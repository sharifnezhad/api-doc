<?php

namespace ASharifnezhad\ApiDoc\Classes\Concerns\Methods;

use Mpociot\Reflection\DocBlock;
use Mpociot\Reflection\DocBlock\Tag;

abstract class Method
{
    protected array $config;
    protected Docblock $classData;
    protected Docblock $methodData;

    public function __construct($config)
    {
        $this->config = $config;
    }

    abstract public function methodParams(Docblock $classData, Docblock $methodData, $customData): array;

    abstract public function setParameters(array $pathParams);

    public function setResponses(array $getTags)
    {
        return collect($getTags)->mapWithKeys(function (Tag $tag) {
            $values = json_decode($tag->getContent());
            return [
                $values->status => [
                    "description" => "success",
                    "content" => [
                        "application\/json" => [
                            "schema" => [
                                "type" => "object",
                                "example" => $values
                            ]
                        ]
                    ]
                ]
            ];
        });
    }

    public function codeSample($route)
    {
        if (!$this->config['code_sample']['is_enable']) {
            return [];
        }
        $route['bodyParameters'] = collect($route['bodyParameters'])->mapWithKeys(function (Tag $tag) {
            $data = json_decode($tag->getContent());

            return [
                $data->key => [
                    'type' => 'object',
                    'value' => $data->example ?? null
                ]
            ];
        })->toArray();
        $route['queryParameters'] = collect($route['queryParameters'])->mapWithKeys(function (Tag $tag) {
            $data = json_decode($tag->getContent());

            return [
                $data->key => [
                    'type' => 'object',
                    'value' => $data->example ?? null
                ]
            ];
        })->toArray();

        return collect($this->config['code_sample']['language-tabs'])->map(function ($name, $lang) use ($route) {
            return [
                'lang' => $name,
                'source' => view('apidoc::Languages.' . $lang, compact('route'))->render(),
            ];
        })->values()->toArray();
    }


    public function setDefaultParams($customData)
    {
        return [
            'security' => [$this->setSecurity($this->methodData->getTagsByName('authenticated'))],
            'tags' => $this->setTags($this->classData->getTagsByName('group')),
            'operationId' => $this->methodData->getShortDescription(),
            'description' => $this->methodData->getLongDescription()->getContents() ?? '',
            'x-code-samples' => $this->codeSample($customData)
        ];
    }

    private function setTags($tags)
    {
        return collect($tags)->map(function (Tag $tag){
            return $tag->getContent();
        });
    }
    private function setSecurity($securityTag)
    {
        if (empty($securityTag)) {
            return [];

        }

        return collect($securityTag)->mapWithKeys(function (Tag $tag) {
            if (empty($tag->getContent())) {
                return [
                    array_key_first(config('apidoc.security')) => []
                ];
            }
            $authData = explode(' ', $tag->getContent());
            $scopes = isset($authData[1]) ? explode(':', $authData[1]) : null;
            $scopes = isset($scopes[1]) ? explode(',', $scopes[1]) : [];

            return [
                $authData[0] => $scopes
            ];
        });

    }


}
