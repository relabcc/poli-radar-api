<?php

namespace Api\V2\Controllers;

use App\PoliticianCategory;
use Api\V2\Transformers\PoliticianCategoryTransformer;

/**
 * @Resource('PoliticianCategories', uri='/politician_categorys')
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
    // public function store(Request $request)
    // {
    // }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->response->item(PoliticianCategory::findOrFail($id), new PoliticianCategoryTransformer);
    }

    /**
     * Update the PoliticianCategory in the database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function update(Request $request, $id)
    // {
    // }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function destroy($id)
    // {
    // }
}
