<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\ParameterBag;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UrlShorterService
{

    private ParameterBag $jsonData;

    public function __construct(ParameterBag $jsonData)
    {
        $this->jsonData = $jsonData;
    }


    public function run()
    {
        $jsonDataArray = $this->jsonData->all();

        if (array_key_exists('long_url', $jsonDataArray)) {
            return $this->processData($jsonDataArray);
        }

        return array_map([$this, 'processData'], $jsonDataArray);
    }

    private function processData(mixed $linkData)
    {
        if (!is_array($linkData)) throw new HttpException('400', 'Неправильный формат данных');
        //todo: добавить более подробную информацию

        $linkDataValidated = $this->validateData($linkData);
        return $linkDataValidated;
    }

    private function validateData(array $linkData)
    {

        $validator = Validator::make($linkData, [
            'long_url' => 'required',
            'tags' => 'nullable',
            'title' => 'nullable',
        ]);

        if ($validator->fails()) {
            return $validator->errors()->all();
        }

        $linkData = $validator->validated();
        return $linkData;


    }

    private function shortenUrl(array $linkData)
    {


    }
}
