<?php

namespace App\Http\Controllers\Admin;

use App\Import;
use App\Importing\ImportStatus;
use App\Importing\TaxonImport;
use Illuminate\Http\Request;

class TaxaImportController
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        return view('admin.taxa-import.index', [
            'columns' => TaxonImport::columns($request->user()),
            'import' => Import::inProgress()->latest()->first(),
            'cancellableStatuses' => collect(ImportStatus::cancellableStatuses()),
        ]);
    }
}
