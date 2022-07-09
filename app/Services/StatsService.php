<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Stat;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class StatsService
{
    public static function getStats($request, $id): Collection|Stat
    {
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

        return ($result);
    }
}
