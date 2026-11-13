@extends('layouts.app')

@section('title', custom_trans('Purchase History', 'front') . ' - ' . (\App\Models\MainContentSettings::getActive()?->site_name ?? 'Site Name'))

@php
    $currency = config('app.currency', 'SAR');
    $statusMeta = [
        'active' => ['label' => custom_trans('Active', 'front'), 'class' => 'is-active', 'icon' => 'fa-circle-play'],
        'completed' => ['label' => custom_trans('Completed', 'front'), 'class' => 'is-completed', 'icon' => 'fa-circle-check'],
        'expired' => ['label' => custom_trans('Expired', 'front'), 'class' => 'is-expired', 'icon' => 'fa-clock'],
        'cancelled' => ['label' => custom_trans('Cancelled', 'front'), 'class' => 'is-expired', 'icon' => 'fa-ban'],
    ];
@endphp

@section('content')
    <section class="purchases-page">
        <div class="container">
            <header class="purchases-head">
                <div>
                    <h1 class="purchases-title">{{ custom_trans('Purchase History', 'front') }}</h1>
                    <p class="purchases-sub">{{ custom_trans('All the courses you have enrolled in or purchased', 'front') }}</p>
                </div>
                @if ($purchases->total() > 0)
                    <div class="purchases-stat">
                        <i class="fas fa-graduation-cap" aria-hidden="true"></i>
                        <span><strong>{{ $purchases->total() }}</strong> {{ custom_trans('courses_count_label', 'front') }}</span>
                    </div>
                @endif
            </header>

            @forelse ($purchases as $purchase)
                @php
                    $course = $purchase->course;
                    $status = $purchase->status ?? 'active';
                    $meta = $statusMeta[$status] ?? ['label' => ucfirst($status), 'class' => 'is-active', 'icon' => 'fa-circle'];
                    $paid = (float) ($purchase->amount_paid ?? 0);
                    $progress = max(0, min(100, (int) round($purchase->progress_percentage ?? 0)));
                    $canLearn = $course && in_array($status, ['active', 'completed'], true);
                    $date = $purchase->enrolled_at ?? $purchase->created_at;
                @endphp
                <article class="purchase-card">
                    <a href="{{ $course ? route('courses.show', $course->id) : '#' }}" class="purchase-thumb" tabindex="-1" aria-hidden="true">
                        @if ($course && $course->image)
                            <img src="{{ optimized_image_url($course->image, 320, 200) }}" alt="" width="320" height="200" loading="lazy">
                        @elseif ($course)
                            <img src="{{ $course->image_url }}" alt="" width="320" height="200" loading="lazy">
                        @else
                            <span class="purchase-thumb-empty"><i class="fas fa-book-open"></i></span>
                        @endif
                    </a>

                    <div class="purchase-body">
                        <div class="purchase-top">
                            <span class="purchase-status {{ $meta['class'] }}">
                                <i class="fas {{ $meta['icon'] }}" aria-hidden="true"></i>{{ $meta['label'] }}
                            </span>
                            <span class="purchase-price {{ $paid > 0 ? '' : 'is-free' }}">
                                {{ $paid > 0 ? number_format($paid, 2) . ' ' . $currency : custom_trans('Free', 'front') }}
                            </span>
                        </div>

                        <h2 class="purchase-name">
                            @if ($course)
                                <a href="{{ route('courses.show', $course->id) }}">{{ $course->localized_name }}</a>
                            @else
                                {{ custom_trans('Course no longer available', 'front') }}
                            @endif
                        </h2>

                        <ul class="purchase-meta">
                            <li><i class="far fa-calendar" aria-hidden="true"></i>{{ custom_trans('Purchased on', 'front') }} {{ $date->format('d-m-Y') }}</li>
                            @if ($purchase->access_type === 'subscription' && $purchase->expires_at)
                                <li><i class="far fa-clock" aria-hidden="true"></i>{{ custom_trans('Access until', 'front') }} {{ $purchase->expires_at->format('d-m-Y') }}</li>
                            @elseif ($purchase->access_type === 'lifetime' || !$purchase->expires_at)
                                <li><i class="fas fa-infinity" aria-hidden="true"></i>{{ custom_trans('Lifetime access', 'front') }}</li>
                            @endif
                            @if ($purchase->payment_method)
                                <li><i class="far fa-credit-card" aria-hidden="true"></i>{{ ucfirst(str_replace('_', ' ', $purchase->payment_method)) }}</li>
                            @endif
                        </ul>

                        <div class="purchase-bottom">
                            <div class="purchase-progress" title="{{ $progress }}%">
                                <div class="purchase-progress-label">
                                    <span>{{ custom_trans('Progress', 'front') }}</span><strong>{{ $progress }}%</strong>
                                </div>
                                <div class="purchase-progress-bar" role="progressbar" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100">
                                    <span style="width: {{ $progress }}%"></span>
                                </div>
                            </div>
                            @if ($canLearn)
                                <a href="{{ route('courses.learn', $course->id) }}" class="purchase-btn">
                                    {{ $status === 'completed' ? custom_trans('Review course', 'front') : custom_trans('Continue learning', 'front') }}
                                    <i class="fas fa-arrow-right purchase-btn-arrow" aria-hidden="true"></i>
                                </a>
                            @elseif ($course)
                                <a href="{{ route('courses.show', $course->id) }}" class="purchase-btn is-outline">
                                    {{ custom_trans('Renew', 'front') }}
                                </a>
                            @endif
                        </div>
                    </div>
                </article>
            @empty
                <div class="purchases-empty">
                    <span class="purchases-empty-icon"><i class="fas fa-receipt"></i></span>
                    <h2>{{ custom_trans('No purchases yet', 'front') }}</h2>
                    <p>{{ custom_trans('Courses you enroll in or buy will appear here.', 'front') }}</p>
                    <a href="{{ route('courses.search') }}" class="purchase-btn">{{ custom_trans('Browse courses', 'front') }}</a>
                </div>
            @endforelse

            @if ($purchases->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $purchases->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection

@push('styles')
    <style>
        .purchases-page { padding: 2.5rem 0 3.5rem; background: #f7f9fc; }
        .purchases-head {
            display: flex; flex-wrap: wrap; align-items: flex-end; justify-content: space-between;
            gap: 1rem; margin-bottom: 1.75rem;
        }
        .purchases-title { margin: 0 0 .25rem; font-size: 1.75rem; font-weight: 800; color: #1e293b; }
        .purchases-sub { margin: 0; color: #64748b; }
        .purchases-stat {
            display: inline-flex; align-items: center; gap: .6rem; padding: .6rem 1rem;
            background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; color: #334155;
        }
        .purchases-stat i { color: #156481; font-size: 1.1rem; }
        .purchases-stat strong { font-size: 1.1rem; color: #156481; }

        .purchase-card {
            display: flex; gap: 1.25rem; padding: 1rem; margin-bottom: 1rem;
            background: #fff; border: 1px solid #e8edf3; border-radius: 16px;
            box-shadow: 0 2px 10px rgba(15, 23, 42, .04);
            transition: box-shadow .2s ease, border-color .2s ease;
        }
        .purchase-card:hover { border-color: #d5dee8; box-shadow: 0 10px 24px rgba(15, 23, 42, .08); }
        .purchase-thumb {
            flex: 0 0 220px; aspect-ratio: 16 / 10; border-radius: 12px; overflow: hidden; background: #eef2f7;
            display: block;
        }
        .purchase-thumb img { width: 100%; height: 100%; object-fit: cover; transition: transform .35s ease; }
        .purchase-card:hover .purchase-thumb img { transform: scale(1.04); }
        .purchase-thumb-empty { display: flex; width: 100%; height: 100%; align-items: center; justify-content: center; color: #94a3b8; font-size: 2rem; }

        .purchase-body { flex: 1 1 auto; min-width: 0; display: flex; flex-direction: column; gap: .55rem; }
        .purchase-top { display: flex; align-items: center; justify-content: space-between; gap: .75rem; }
        .purchase-status {
            display: inline-flex; align-items: center; gap: .4rem; padding: .25rem .7rem;
            border-radius: 999px; font-size: .78rem; font-weight: 700;
        }
        .purchase-status.is-active { background: #e0f2fe; color: #0369a1; }
        .purchase-status.is-completed { background: #dcfce7; color: #15803d; }
        .purchase-status.is-expired { background: #fee2e2; color: #b91c1c; }
        .purchase-price { font-weight: 800; color: #1e293b; white-space: nowrap; }
        .purchase-price.is-free { color: #16a34a; }
        .purchase-name { margin: 0; font-size: 1.1rem; font-weight: 700; line-height: 1.5; }
        .purchase-name a { color: #1e293b; text-decoration: none; }
        .purchase-name a:hover { color: #156481; }
        .purchase-meta {
            display: flex; flex-wrap: wrap; gap: .35rem 1.1rem; margin: 0; padding: 0; list-style: none;
            font-size: .84rem; color: #64748b;
        }
        .purchase-meta li { display: inline-flex; align-items: center; gap: .4rem; }
        .purchase-meta i { color: #94a3b8; }

        .purchase-bottom { display: flex; align-items: center; gap: 1.25rem; margin-top: auto; padding-top: .35rem; }
        .purchase-progress { flex: 1 1 auto; min-width: 0; }
        .purchase-progress-label { display: flex; justify-content: space-between; font-size: .8rem; color: #64748b; margin-bottom: .3rem; }
        .purchase-progress-label strong { color: #156481; }
        .purchase-progress-bar { height: 8px; border-radius: 999px; background: #eef2f7; overflow: hidden; }
        .purchase-progress-bar span { display: block; height: 100%; border-radius: inherit; background: linear-gradient(90deg, #156481, #1f86ad); }
        [dir="rtl"] .purchase-progress-bar span { background: linear-gradient(270deg, #156481, #1f86ad); }

        .purchase-btn {
            display: inline-flex; align-items: center; gap: .5rem; flex: 0 0 auto;
            padding: .6rem 1.1rem; border-radius: 10px; background: #ff7043; color: #fff;
            font-weight: 700; font-size: .9rem; text-decoration: none; white-space: nowrap;
            transition: background-color .2s ease;
        }
        .purchase-btn:hover { background: #156481; color: #fff; }
        .purchase-btn.is-outline { background: #fff; color: #156481; border: 1px solid #156481; }
        .purchase-btn.is-outline:hover { background: #156481; color: #fff; }
        .purchase-btn-arrow { font-size: .8rem; }
        [dir="rtl"] .purchase-btn-arrow { transform: scaleX(-1); }

        .purchases-empty {
            text-align: center; padding: 3.5rem 1rem; background: #fff;
            border: 1px dashed #cbd5e1; border-radius: 16px;
        }
        .purchases-empty-icon {
            display: inline-flex; width: 72px; height: 72px; align-items: center; justify-content: center;
            margin-bottom: 1rem; border-radius: 50%; background: #eef6fa; color: #156481; font-size: 1.8rem;
        }
        .purchases-empty h2 { font-size: 1.3rem; font-weight: 700; color: #1e293b; }
        .purchases-empty p { color: #64748b; margin-bottom: 1.25rem; }

        @media (max-width: 767.98px) {
            .purchases-title { font-size: 1.4rem; }
            .purchase-card { flex-direction: column; gap: .9rem; }
            .purchase-thumb { flex-basis: auto; width: 100%; }
            .purchase-bottom { flex-direction: column; align-items: stretch; gap: .85rem; }
            .purchase-btn { justify-content: center; }
        }
    </style>
@endpush
