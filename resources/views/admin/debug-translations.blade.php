@extends('admin.layout')

@section('title', 'Translation Debug')

@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Translation Debug Information</h5>
                    </div>
                    <div class="card-body">
                        @php
                            $currentLanguage = \App\Helpers\TranslationHelper::getCurrentLanguage();
                            $allTranslations = \App\Models\Translation::with('language')->get();
                            $coursesTranslation = \App\Helpers\TranslationHelper::translate('courses', 'general');
                        @endphp

                        <h6>Current Language:</h6>
                        <ul>
                            <li>Name: {{ $currentLanguage->name }}</li>
                            <li>Code: {{ $currentLanguage->code }}</li>
                            <li>Direction: {{ $currentLanguage->direction }}</li>
                            <li>Active: {{ $currentLanguage->is_active ? 'Yes' : 'No' }}</li>
                            <li>Default: {{ $currentLanguage->is_default ? 'Yes' : 'No' }}</li>
                        </ul>

                        <h6>Test Translation for 'courses':</h6>
                        <p><strong>Result:</strong> "{{ $coursesTranslation }}"</p>

                        <h6>All Translations in Database:</h6>
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Language</th>
                                    <th>Key</th>
                                    <th>Value</th>
                                    <th>Group</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($allTranslations as $translation)
                                    <tr>
                                        <td>{{ $translation->language->name }} ({{ $translation->language->code }})</td>
                                        <td><code>{{ $translation->translation_key }}</code></td>
                                        <td>{{ $translation->translation_value }}</td>
                                        <td><span class="badge bg-secondary">{{ $translation->group }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <h6>Test Different Groups:</h6>
                        <ul>
                            <li>General: "{{ \App\Helpers\TranslationHelper::translate('courses', 'general') }}"</li>
                            <li>Admin: "{{ \App\Helpers\TranslationHelper::translate('courses', 'admin') }}"</li>
                            <li>Front: "{{ \App\Helpers\TranslationHelper::translate('courses', 'front') }}"</li>
                        </ul>

                        <h6>Custom Function Test (Auto-detect):</h6>
                        <p>custom_trans('courses'): "{{ custom_trans('courses') }}" (auto-detected as admin)</p>
                        <p>custom_trans('admins'): "{{ custom_trans('admins') }}" (auto-detected as admin)</p>
                        <p>custom_trans('home'): "{{ custom_trans('home') }}" (auto-detected as admin)</p>

                        <h6>Route Information:</h6>
                        <p>Current route name: {{ request()->route() ? request()->route()->getName() : 'No route' }}</p>
                        <p>Is admin route: {{ str_starts_with(request()->route() ? request()->route()->getName() : '', 'admin.') ? 'Yes' : 'No' }}</p>

                        <h6>Session Information:</h6>
                        <p>Current locale in session: {{ session('locale', 'not set') }}</p>
                        <p>App locale: {{ app()->getLocale() }}</p>

                        <div class="mt-4">
                            <a href="{{ route('admin.translations.create') }}" class="btn btn-primary">
                                Add New Translation
                            </a>
                            <a href="{{ route('admin.translations.index') }}" class="btn btn-secondary">
                                View All Translations
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
