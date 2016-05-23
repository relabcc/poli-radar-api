<?php

namespace Api\Transformers;

use App\PoliticianCategory;
use League\Fractal\TransformerAbstract;

class PoliticianCategoryTransformer extends TransformerAbstract
{
	public function transform(PoliticianCategory $pCat)
	{
		return [
			'id' => (int) $pCat->id,
            'name' => $pCat->name,
            'event_category_root' => $pCat->event_category_id,
		];
	}
}