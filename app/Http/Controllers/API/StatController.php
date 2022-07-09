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

        $whereClause = [['link_id', '=', $id]];

        if (!empty($request->from)) $whereClause[] = ['created_at', '>=', $request->from];
        if (!empty($request->to)) $whereClause[] = ['created_at', '<=', $request->to];

        $selectClause = [
            DB::raw('COUNT(*) as total_views'),
            DB::raw('COUNT(DISTINCT ip, user_agent) as unique_views')
        ];
        $dateInQuery = 'date(created_at)';
        if (empty($request->from) && empty($request->from)) $selectClause[] = DB::raw($dateInQuery . ' as date');

        $query =
            Stat::select($selectClause)
                ->where($whereClause);

        if (empty($request->from) && empty($request->from))
            $query = $query
                ->groupByRaw($dateInQuery)
                ->orderBy(DB::raw($dateInQuery), 'desc');


        $result = $query
            ->get();

        if (!empty($request->from) || !empty($request->to))
            $result = $result
                ->first();

        return new JsonResponse($result);
    }
}
