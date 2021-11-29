<?php

namespace App\Http\Requests;

use App\Synonym;
use Illuminate\Foundation\Http\FormRequest;

class StoreSynonym extends FormRequest
{
    public function rules()
    {
        return [
            'taxon_id' => ['required', 'exists:taxa,id'],
            'name' => ['required', 'string'],
        ];
    }

    public function save(Synonym $synonym)
    {
        $synonym->fill(array_merge(
            array_map(
                'trim',
                $this->only(['name', 'taxon_id'])
            )
        ))->save();
        return $synonym;
    }

    public function store()
    {
        return Synonym::create(
            array_merge(
                array_map(
                    'trim',
                    $this->only(['name', 'taxon_id'])
                )
            )
        );
    }
}
