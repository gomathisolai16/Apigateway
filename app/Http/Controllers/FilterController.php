<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Services\FilterService;

class FilterController extends Controller
{
    use ApiResponser;

    /**
     *
     * @lrd:start
     *  To get filter options
     * @lrd:end
     *
     * @QAparam pageName string required staff
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getFilterParam(Request $request)
    {
        $filterService = new FilterService;
        return $filterService->getFilterParams($request);
    }

    /**
     *
     * @lrd:start
     *  To get filter results
     * @lrd:end
     *
     * @QAparam pageName string required staff
     * @QAparam program string [1,2]
     * @QAparam gender string [1,2]
     * @QAparam site string [1,2]
     * @QAparam position string [1,2]

     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getFilterResult(Request $request)
    {
        $request->request->add([
            'auth_user_id' => $request->user()->id,
        ]);
        $filterService = new FilterService;
        return $filterService->getFilterResult($request);
    }
}
