<?php

namespace App\Http\Controllers\Api;

use App\Importing\TaxonImport;

class TaxonImportsController extends BaseImportController
{
    /**
     * Import type.
     *
     * @return string
     */
    protected function type()
    {
        return TaxonImport::class;
    }
}
