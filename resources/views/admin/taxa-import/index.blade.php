@extends('layouts.dashboard', ['title' => __('navigation.taxa_import')])

@section('content')
    <div class="box">
        <div class="message">
            <div class="message-body">{{ __('pages.taxa_import.short_info') }}</div>
        </div>

        <nz-taxa-import
            :columns="{{ $columns }}"
            :running-import="{{ $import ?? 'null' }}"
            :cancellable-statuses="{{ $cancellableStatuses }}"
            @role(['admin', 'curator'])
            can-submit-for-user
            @endrole
        />
    </div>
@endsection

@section('breadcrumbs')
    <div class="breadcrumb" aria-label="breadcrumbs">
        <ul>
            <li><a href="{{ route('contributor.index') }}">{{ __('navigation.dashboard') }}</a></li>
            <li><a href="{{ route('admin.taxa.index') }}">{{ __('navigation.taxa') }}</a></li>
            <li class="is-active"><a>{{ __('navigation.import') }}</a></li>
        </ul>
    </div>
@endsection

@section('navigationActions')
    <a href="{{ route('admin.taxa-import.guide') }}"><i class="fa fa-question"></i> {{ __('Help') }}</a>
@endsection
