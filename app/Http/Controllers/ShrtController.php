<?php

namespace App\Http\Controllers;

use App\Models\Link;
use Illuminate\Http\RedirectResponse;
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
    public function show($id): Redirector|RedirectResponse
    {
        $link = Link::find($id);
        if ($link === null) throw new HttpException(404, 'Ссылка не найдена');


        return redirect($link->long_url);
    }
}
