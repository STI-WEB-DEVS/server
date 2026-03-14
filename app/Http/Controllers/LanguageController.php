<?php

namespace App\Http\Controllers;

use App\Service\LanguageService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class LanguageController extends Controller
{
    private LanguageService $languageService;

    public function __construct(LanguageService $languageService)
    {
        $this->languageService = $languageService;
    }

    public function index(Request $request)
    {
        return $this->languageService->listLanguage($request->input('per_page', 15));
    }

    public function store(Request $request)
    {
        return $this->languageService->createLanguage($request->all());
    }

    public function show(string $uuid)
    {
        return $this->languageService->getLanguage($uuid);
    }

    public function update(Request $request, string $uuid)
    {
        return $this->languageService->updateLanguage($uuid, $request->all());
    }

    public function destroy(string $uuid)
    {
        $this->languageService->deleteLanguage($uuid);
        return response()->json(['message' => 'Deleted successfully'], 200);
    }
    
    public function restore(string $uuid)
    {
        return $this->languageService->restoreLanguage($uuid);
    }
}