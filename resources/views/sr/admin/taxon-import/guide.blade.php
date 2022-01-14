@extends('layouts.dashboard', ['title' => __('navigation.taxa_import')])

@section('content')
    <div class="box content">
        <h1>Упутство за увоз таксона</h1>

        <span>У изради</span>
    </div>
@endsection

@section('breadcrumbs')
    <div class="breadcrumb" aria-label="breadcrumbs">
        <ul>
            <li><a href="{{ route('contributor.index') }}">{{ __('navigation.dashboard') }}</a></li>
            <li><a href="{{ route('admin.taxa.index') }}">{{ __('navigation.taxa') }}</a></li>
            <li><a href="{{ route('admin.taxa-import.index') }}">{{ __('navigation.taxa_import') }}</a></li>
            <li class="is-active"><a>Упутство за увоз</a></li>
        </ul>
    </div>
@endsection
