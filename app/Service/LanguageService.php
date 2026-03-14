<?php

namespace App\Service;

use App\Repository\LanguageRepository;
use App\Http\Resources\LanguageResource;

class LanguageService
{
    private LanguageRepository $languageRepository;

    public function __construct(LanguageRepository $languageRepository) 
    {
        $this->languageRepository = $languageRepository;
    }

    public function listLanguage(int $perPage = 15)
    {
        $collection = $this->languageRepository->paginate($perPage);
        return LanguageResource::collection($collection);
    }

    public function createLanguage(array $payload)
    {
        $model = $this->languageRepository->create($payload);
        return new LanguageResource($model);
    }

    public function getLanguage(string $uuid)
    {
        $model = $this->languageRepository->findByUuid($uuid);
        return new LanguageResource($model);
    }

    public function getLanguageByField(string $field, $value)
    {
        $model = $this->languageRepository->findByField($field, $value);
        return new LanguageResource($model);
    }

    public function updateLanguage(string $uuid, array $payload)
    {
        $model = $this->languageRepository->update($uuid, $payload);
        return new LanguageResource($model);
    }

    public function deleteLanguage(string $uuid)
    {
        $this->languageRepository->delete($uuid);
        return true;
    }

    public function restoreLanguage(string $uuid)
    {
        $model = $this->languageRepository->restore($uuid);
        return new LanguageResource($model);
    }
}