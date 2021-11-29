@extends('layouts.master')

@section('body')
    <nz-navbar inline-template>
        <nav class="navbar shadow border-t-4 border-primary">
            <div class="container is-fluid">
                <div class="navbar-brand">
                    <a class="navbar-item" href="{{ url('/') }}" title="{{ config('app.name') }}">
                        <img src="{{ asset('img/logo.svg') }}" alt="{{ config('app.name') }}" class="navbar-logo">
                    </a>

                    <div class="navbar-burger" :class="{ 'is-active': active }" @click="toggle">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </div>

                <div class="navbar-menu" :class="{ 'is-active': active }">
                    <div class="navbar-end">
                        <div class="navbar-item has-dropdown is-hoverable">
                            <a class="navbar-link is-hidden-touch">
                                <span>{{ __('navigation.about') }}</span>
                            </a>

                            <div class="navbar-dropdown is-boxed is-right">
                                <a class="navbar-item" href="{{ route('pages.about.about-project') }}">
                                    {{ __('navigation.about_project') }}
                                </a>
                                <a class="navbar-item" href="{{ route('pages.about.project-team') }}">
                                    {{ __('navigation.project_team') }}
                                </a>
                                <a class="navbar-item" href="{{ route('pages.about.organisations') }}">
                                    {{ __('navigation.organisations') }}
                                </a>
                                <a class="navbar-item" href="{{ route('pages.about.local-community') }}">
                                    {{ __('navigation.local_community') }}
                                </a>
                                <a class="navbar-item" href="{{ route('pages.about.biodiversity-data') }}">
                                    {{ __('navigation.biodiversity_data') }}
                                </a>
                                <a class="navbar-item" href="{{ route('pages.about.development-supporters') }}">
                                    {{ __('navigation.development_supporters') }}
                                </a>
                                <a class="navbar-item" href="{{ route('pages.about.stats') }}">
                                    {{ __('navigation.stats') }}
                                </a>
                            </div>
                        </div>

                        @auth
                            <div class="navbar-item has-dropdown is-hoverable">
                                <a class="navbar-link is-hidden-touch">
                                    @include('components.icon', ['icon' => 'user'])
                                </a>

                                <div class="navbar-dropdown is-boxed is-right">
                                    <div class="navbar-item is-hidden-touch">
                                        <b class="is-size-6">{{ auth()->user()->full_name }}</b>
                                    </div>
                                    <hr class="navbar-divider">
                                    <a class="navbar-item" href="{{ route('contributor.index') }}">
                                        @include('components.icon', ['icon' => 'dashboard'])
                                        <span>{{ __('navigation.contributor_area') }}</span>
                                    </a>
                                    <a class="navbar-item" href="{{ route('preferences.index') }}">
                                        @include('components.icon', ['icon' => 'cog'])
                                        <span>{{ __('navigation.preferences.index') }}</span>
                                    </a>
                                    <hr class="navbar-divider">
                                    <a href="{{ route('logout') }}"
                                        class="navbar-item"
                                        onclick="event.preventDefault();
                                            document.getElementById('logout-form').submit();">
                                        {{ trans('navigation.logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        {{ csrf_field() }}
                                    </form>
                                </div>
                            </div>
                        @else
                            <div class="navbar-item">
                                <div class="field is-grouped">
                                    <div class="control">
                                        <a href="{{ route('login') }}" class="button is-primary">
                                            {{ __('navigation.login') }}
                                        </a>
                                    </div>

                                    <div class="control">
                                        <a href="{{ route('register') }}" class="button is-outlined is-secondary">
                                            {{ __('navigation.register') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>
    </nz-navbar>

    <div class="flex-1">
        @if(session('status'))
            <div class="container py-8 px-4 desktop:px-16">
                <article class="message shadow is-info">
                    <div class="message-body">
                        {{ session('status') }}
                    </div>
                </article>
            </div>
        @endif

        @yield('content')
    </div>

    @include('partials.footer')

@endsection
