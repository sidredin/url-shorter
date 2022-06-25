<?php

namespace App\Services;

use App\Models\Link;
use App\Models\Tag;
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


    public function storeOrUpdate($linkId)
    {
        $jsonDataArray = $this->jsonData->all();

        if ($linkId !== null || array_key_exists('long_url', $jsonDataArray)) {
            return $this->processData($jsonDataArray, $linkId);
        }

        return array_map([$this, 'processData'], $jsonDataArray);
    }

    private function processData(mixed $linkData, $linkId = null)
    {
        if (!is_array($linkData)) throw new HttpException(400,
            'Неправильный формат данных. Возможно, отсутствует параметр long_url');

        if (array_key_exists('long_url', $linkData) && !$this->isValidUri($linkData['long_url'])) return [
            'success' => false,
            'errors' => ["Ссылка {$linkData['long_url']} нерабочая"],
        ];

        $linkDataValidated = $this->validateData($linkData);

        if (array_key_exists('success', $linkDataValidated) && $linkDataValidated['success'] === false) {
            return $linkDataValidated;
        }

        $linkDataValidated['id'] = $this->generateUrlId();

        if ($linkId === null) $linkDataValidated['id'] = $this->generateUrlId();

        return $this->createResponse($this->addDataToDbOrUpdate($linkDataValidated, $linkId));


    }

    function isValidUri($uri)
    {
        $hds = @get_headers($uri);
        return (!$hds || (strpos($hds[0], ' 404 ') !== false)) ? false : true;
    }

    private function validateData(array $linkData)
    {

        $validator = Validator::make($linkData, [
            'long_url' => 'required',
            'tags' => 'nullable',
            'title' => 'nullable',
        ]);

        if ($validator->fails()) {

            return [
                'success' => false,
                'errors' => $validator->errors()->all(),
            ];
        }

        $linkData = $validator->validated();

        return $linkData;
    }

    private function generateUrlId()
    {
        return uniqid();
    }

    private function addDataToDbOrUpdate($linkData, $linkId)
    {
        if ($linkId !== null) {
            $link = Link::find($linkId);
            if ($link === null) throw new HttpException(404, 'Ссылка не найдена');
            $link->update($linkData);
        } else {
            $link = Link::create($linkData);
        }

        if (!empty($linkData['tags'])) {
            $tags = [];
            foreach ($linkData['tags'] as $tag) {
                $link->tags()->attach(Tag::firstOrCreate([
                    'title' => $tag
                ])['id']);
            }
        }

        return $link;
    }

    private function createResponse($linkData)
    {
        $linkData['success'] = true;
        $linkData['short_link'] = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '/' . $linkData['id'];
        return $linkData;
    }
}
