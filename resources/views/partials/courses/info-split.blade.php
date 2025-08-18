@php
    $infoSplit = \App\Models\InfoSplit::active()->first();
@endphp

@if ($infoSplit)
    <!-- Info Split Section -->
    <section class="info-split-section bg-light-eaf">
        <div class="container">
            <div class="row align-items-center justify-content-center">
                <div class="col-lg-6 mb-4 px-4 mb-lg-0">
                    @if ($infoSplit->image)
                        <img src="{{ $infoSplit->image_url }}" alt="Info Split"
                            class="img-fluid rounded-4 w-100">
                    @endif
                </div>
                <div class="col-lg-6">
                    <h2 class="fw-bold mb-3">{!! nl2br(e($infoSplit->getDisplayTitle())) !!}</h2>
                    <p class="mb-4 text-blue-247 fs-11">
                        {{ $infoSplit->getDisplayDescription() }}
                    </p>
                    @if ($infoSplit->button_url && $infoSplit->button_text)
                        <a href="{{ $infoSplit->button_url }}" class="btn btn-primary px-4 py-3 rounded-3">
                            {{ $infoSplit->getDisplayButtonText() }} &rarr;
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endif
