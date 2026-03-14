<?php

namespace App\Repository;

use App\Models\Language;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class LanguageRepository
{
    public function paginate(int $perPage = 15)
    {
        return Language::latest()->paginate($perPage);
    }

    public function create(array $payload)
    {
        return Language::create($payload);
    }

    public function findByUuid(string $uuid)
    {
        return Language::where('uuid', $uuid)->firstOrFail();
    }

    public function findByField(string $field, $value)
    {
        return Language::where($field, $value)->firstOrFail();
    }

    public function update(string $uuid, array $payload)
    {
        $model = $this->findByUuid($uuid);
        $model->update($payload);
        return $model;
    }

    public function delete(string $uuid)
    {
        $model = $this->findByUuid($uuid);
        return $model->delete();
    }

    public function restore(string $uuid)
    {
        $model = Language::withTrashed()->where('uuid', $uuid)->firstOrFail();
        $model->restore();
        return $model;
    }
}