<?php

namespace Api\Controllers;

use App\PoliticianCategory;
use App\Http\Requests;
use Illuminate\Http\Request;
use Api\Transformers\PoliticianCategoryTransformer;

/**
 * @Resource('PoliticianCategorys', uri='/politicians')
 */
class PoliticianCategoryController extends BaseController
{

    /**
     * Show all politicians
     *
     * Get a JSON representation of all the politicians
     * 
     * @Get('/')
     */
    public function index()
    {
        return $this->response->collection(PoliticianCategory::all(), new PoliticianCategoryTransformer);
    }

    /**
     * Store a new dog in the database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return PoliticianCategory::create($request->only([
            'name'
        ]));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->item(PoliticianCategory::findOrFail($id), new PoliticianCategoryTransformer);
    }

    /**
     * Update the PoliticianCategory in the database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $candidate = PoliticianCategory::findOrFail($id);
        $candidate->update($request->only([
            'name'
        ]));
        return $candidate;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return PoliticianCategory::destroy($id);
    }
}