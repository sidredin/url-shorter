<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\UrlShorterService;
use Symfony\Component\HttpKernel\Exception\HttpException;

class LinkController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        return $this->storeOrUpdate($request);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, $id)
    {
        return $this->storeOrUpdate($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id)
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

    private function storeOrUpdate(Request $request, $linkId = null)
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
