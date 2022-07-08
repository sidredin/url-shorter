<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Link;
use App\Models\Stat;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ShrtController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Redirector|RedirectResponse
     */
    public function show(Request $request, $id): Redirector|RedirectResponse
    {
        $link = Link::find($id);
        if ($link === null) throw new HttpException(404, 'Ссылка не найдена');

        $stat = Stat::create([
            'ip' => $request->ip(),
            'user_agent' => $request->header('user-agent'),
            'link_id' => $id,
        ]);


        return redirect($link->long_url);
    }
}
