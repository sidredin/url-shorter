<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Link;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\UrlShorterService;
use Symfony\Component\HttpKernel\Exception\HttpException;

class LinkController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            return new JsonResponse(Link::all());
        } catch (HttpException $e) {
            return new JsonResponse([
                'success' => false,
                'errors' => [$e->getMessage()],
            ], $e->getStatusCode());
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'errors' => [$e->getMessage()],
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        return $this->storeOrUpdate($request);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        try {
            $link = Link::find($id);
            if ($link === null) throw new HttpException(404, 'Ссылка не найдена');
            return new JsonResponse($link);
        } catch (HttpException $e) {
            return new JsonResponse([
                'success' => false,
                'errors' => [$e->getMessage()],
            ], $e->getStatusCode());
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'errors' => [$e->getMessage()],
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        return $this->storeOrUpdate($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        try {
            return new JsonResponse(UrlShorterService::deleteLink($id));
        } catch (HttpException $e) {
            return new JsonResponse([
                'success' => false,
                'errors' => [$e->getMessage()],
            ], $e->getStatusCode());
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'errors' => [$e->getMessage()],
            ], $e->getStatusCode());
        }
    }

    private function storeOrUpdate(Request $request, $linkId = null): JsonResponse
    {
        try {
            $urlShorterService = new UrlShorterService($request->json());
            $result = $urlShorterService->storeOrUpdate($linkId);
            return new JsonResponse($result);
        } catch (HttpException $e) {
            return new JsonResponse([
                'success' => false,
                'errors' => [$e->getMessage()],
            ], $e->getStatusCode());
        } catch (\Exception $e) {
            return new JsonResponse($e->getMessage());
        }
    }
}
