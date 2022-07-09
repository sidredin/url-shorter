<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Link;
use App\Models\Stat;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;

class StatController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(Request $request, $id): JsonResponse
    {
        $link = Link::find($id);
        if ($link === null) throw new HttpException(404, 'Ссылка не найдена');


        $result =
            Stat::select(
                [
                    DB::raw('COUNT(*) as total_views'),
                    DB::raw('COUNT(DISTINCT ip, user_agent) as unique_views'),
                    DB::raw('date(created_at) as date'),
                ],
            )
                ->where('link_id', '=', $id)
                ->groupByRaw('date(created_at)')
                ->orderBy(DB::raw('date(created_at)'), 'desc')
                ->get();

        return new JsonResponse($result);
    }
}
