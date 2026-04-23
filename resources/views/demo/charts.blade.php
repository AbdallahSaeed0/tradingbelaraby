@extends('layouts.app')

@section('title', 'Charts preview (demo)')

@push('styles')
<style>
    .demo-chart-page .tv-shell {
        font-size: 12px;
        color: #131722;
        background: #fff;
        border: 1px solid #e0e3eb;
        border-radius: 4px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,.06);
    }
    .demo-chart-page .tv-disclaimer {
        font-size: 0.85rem;
    }
    .demo-chart-page .tv-mock-interactive {
        pointer-events: none;
        user-select: none;
    }
    .demo-chart-page .tv-top {
        background: #fff;
        border-bottom: 1px solid #e0e3eb;
    }
    .demo-chart-page .tv-ohlc {
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        font-size: 11px;
        color: #787b86;
    }
    .demo-chart-page .tv-ohlc .pos { color: #089981; }
    .demo-chart-page .tv-pill-sell {
        background: #f23645;
        color: #fff;
        border-radius: 4px;
        padding: 6px 14px;
        font-weight: 600;
        font-size: 11px;
        min-width: 88px;
        text-align: center;
    }
    .demo-chart-page .tv-pill-buy {
        background: #2962ff;
        color: #fff;
        border-radius: 4px;
        padding: 6px 14px;
        font-weight: 600;
        font-size: 11px;
        min-width: 88px;
        text-align: center;
    }
    .demo-chart-page .tv-spread { color: #787b86; font-size: 11px; min-width: 28px; text-align: center; }
    .demo-chart-page .tv-sidebar {
        width: 38px;
        background: #f8f9fd;
        color: #787b86;
        font-size: 14px;
        border-bottom: 1px solid #e0e3eb;
    }
    @media (min-width: 768px) {
        .demo-chart-page .tv-sidebar {
            border-bottom: none;
            border-right: 1px solid #e0e3eb;
        }
    }
    .demo-chart-page .tv-sidebar i { display: block; padding: 6px 0; text-align: center; }
    .demo-chart-page .tv-main { background: #fafafa; position: relative; min-height: 440px; }
    .demo-chart-page .tv-scale {
        width: 62px;
        background: #fff;
        border-left: 1px solid #e0e3eb;
        color: #787b86;
        font-size: 11px;
        font-family: ui-monospace, Menlo, monospace;
    }
    .demo-chart-page .tv-scale .tick { padding: 2px 6px; text-align: right; }
    .demo-chart-page .tv-scale .tick-current {
        background: #f23645;
        color: #fff;
        font-weight: 600;
        margin: 4px 0;
        border-radius: 2px;
    }
    .demo-chart-page .tv-scale .tick-pm {
        background: #2962ff;
        color: #fff;
        font-size: 10px;
        border-radius: 2px;
        padding: 2px 4px;
        margin-top: 4px;
    }
    .demo-chart-page .tv-foot {
        background: #fff;
        border-top: 1px solid #e0e3eb;
        color: #787b86;
        font-size: 11px;
    }
    .demo-chart-page .tv-foot .tf span {
        padding: 0 5px;
        cursor: default;
    }
    .demo-chart-page .tv-foot .tf span.active { color: #2962ff; font-weight: 600; }
    .demo-chart-page .tv-events {
        position: absolute;
        bottom: 52px;
        left: 44px;
        right: 70px;
        height: 18px;
        display: flex;
        align-items: center;
        gap: 28px;
        pointer-events: none;
    }
    .demo-chart-page .tv-events .ev {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        font-size: 9px;
        font-weight: 700;
        display: grid;
        place-items: center;
        color: #fff;
    }
    .demo-chart-page .tv-events .ev.e { background: #089981; }
    .demo-chart-page .tv-events .ev.d { background: #2962ff; }
    @media (max-width: 767px) {
        .demo-chart-page .tv-scale { width: 48px; font-size: 10px; }
        .demo-chart-page .tv-sidebar { width: 32px; font-size: 12px; }
    }
</style>
@endpush

@section('content')
    <div class="demo-chart-page">
        <section class="contact-banner position-relative d-flex align-items-center justify-content-center">
            <img src="{{ asset('images/breadcrumb-bg.png') }}" alt=""
                class="contact-banner-bg position-absolute w-100 h-100 top-0 start-0" width="1920" height="400">
            <div class="contact-banner-overlay position-absolute w-100 h-100 top-0 start-0"></div>
            <div class="container position-relative z-3 text-center">
                <h1 class="display-5 fw-bold text-white mb-3">Charts preview</h1>
                <div class="d-flex justify-content-center flex-wrap gap-2 mb-2">
                    <span class="contact-label px-4 py-2 rounded-pill bg-white text-dark fw-semibold shadow">
                        <a href="{{ route('home') }}" class="text-dark text-decoration-none hover-primary">{{ custom_trans('home', 'front') }}</a>
                        &nbsp;|&nbsp;
                        <span class="text-muted">Charts</span>
                    </span>
                </div>
            </div>
        </section>

        <section class="py-4 bg-light">
            <div class="container">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                    <p class="demo-chart-page tv-disclaimer mb-0 text-muted">
                        <strong>Illustrative UI only</strong> — non-interactive mock for layout review (e.g. TradingView widget / API discussion).
                        Not affiliated with TradingView Inc. No live or delayed market data.
                    </p>
                    <a href="{{ route('home') }}" class="btn btn-outline-primary btn-sm" style="pointer-events:auto">
                        <i class="fas fa-arrow-left me-1"></i>{{ custom_trans('home', 'front') }}
                    </a>
                </div>

                <div class="tv-shell tv-mock-interactive" dir="ltr">
                    <div class="tv-top px-2 py-2">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                            <div>
                                <div class="fw-semibold" style="font-size:14px;">Apple Inc. · 1D · NASDAQ</div>
                                <div class="text-muted mt-1" style="font-size:11px;">Vol · 33.38M</div>
                            </div>
                            <div class="tv-ohlc d-none d-md-block">
                                O 275.05 H 275.77 L 271.65 C 273.43
                                <span class="pos">+0.26 (+0.10%)</span>
                            </div>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <div class="tv-pill-sell">SELL<br><span style="font-size:13px">272.31</span></div>
                                <span class="tv-spread">0.15</span>
                                <div class="tv-pill-buy">BUY<br><span style="font-size:13px">272.46</span></div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-column flex-md-row" style="min-height: 460px;">
                        <div class="tv-sidebar d-flex flex-md-column flex-row flex-md-nowrap justify-content-md-start justify-content-around py-md-2 py-2 flex-shrink-0">
                            <i class="fas fa-crosshairs" title="Crosshair"></i>
                            <i class="fas fa-slash" title="Trend line"></i>
                            <i class="fas fa-chart-gantt" title="Gann / Fib"></i>
                            <i class="fas fa-shapes" title="Shapes"></i>
                            <i class="fas fa-font" title="Text"></i>
                            <i class="fas fa-pencil-alt" title="Brush"></i>
                            <i class="fas fa-magnet" title="Magnet"></i>
                            <i class="fas fa-lock" title="Lock"></i>
                            <i class="fas fa-eye-slash" title="Hide"></i>
                            <i class="fas fa-trash-alt" title="Delete"></i>
                        </div>

                        <div class="flex-grow-1 d-flex flex-column flex-md-row min-w-0">
                            <div class="tv-main flex-grow-1 position-relative order-1 order-md-0">
                                <svg viewBox="0 0 900 320" preserveAspectRatio="none" class="w-100 h-100 position-absolute top-0 start-0" style="min-height:360px" aria-hidden="true">
                                    <defs>
                                        <pattern id="tvGrid" width="36" height="36" patternUnits="userSpaceOnUse">
                                            <path d="M 36 0 L 0 0 0 36" fill="none" stroke="#e0e3eb" stroke-width="1"/>
                                        </pattern>
                                    </defs>
                                    <rect width="900" height="320" fill="#fafafa"/>
                                    <rect width="900" height="320" fill="url(#tvGrid)"/>
                                    {{-- Static illustrative candles (O/H/L/C order in comments only; shapes are decorative) --}}
                                    <g stroke-width="1">
                                        <g fill="#089981" stroke="#089981">
                                            <line x1="34" y1="200" x2="34" y2="168"/><rect x="31" y="172" width="6" height="22"/>
                                            <line x1="52" y1="210" x2="52" y2="178"/><rect x="49" y="180" width="6" height="24"/>
                                            <line x1="88" y1="185" x2="88" y2="150"/><rect x="85" y="152" width="6" height="28"/>
                                            <line x1="124" y1="175" x2="124" y2="138"/><rect x="121" y="140" width="6" height="30"/>
                                            <line x1="160" y1="165" x2="160" y2="128"/><rect x="157" y="130" width="6" height="30"/>
                                            <line x1="214" y1="155" x2="214" y2="115"/><rect x="211" y="118" width="6" height="32"/>
                                            <line x1="268" y1="145" x2="268" y2="108"/><rect x="265" y="110" width="6" height="30"/>
                                            <line x1="322" y1="138" x2="322" y2="98"/><rect x="319" y="100" width="6" height="32"/>
                                            <line x1="394" y1="128" x2="394" y2="88"/><rect x="391" y="90" width="6" height="32"/>
                                            <line x1="466" y1="118" x2="466" y2="82"/><rect x="463" y="84" width="6" height="30"/>
                                            <line x1="556" y1="108" x2="556" y2="72"/><rect x="553" y="74" width="6" height="30"/>
                                            <line x1="646" y1="100" x2="646" y2="68"/><rect x="643" y="70" width="6" height="26"/>
                                            <line x1="736" y1="95" x2="736" y2="62"/><rect x="733" y="64" width="6" height="26"/>
                                            <line x1="826" y1="92" x2="826" y2="58"/><rect x="823" y="60" width="6" height="26"/>
                                        </g>
                                        <g fill="#f23645" stroke="#f23645">
                                            <line x1="70" y1="205" x2="70" y2="188"/><rect x="67" y="190" width="6" height="12"/>
                                            <line x1="106" y1="198" x2="106" y2="172"/><rect x="103" y="174" width="6" height="18"/>
                                            <line x1="142" y1="192" x2="142" y2="165"/><rect x="139" y="167" width="6" height="20"/>
                                            <line x1="178" y1="188" x2="178" y2="158"/><rect x="175" y="160" width="6" height="22"/>
                                            <line x1="232" y1="178" x2="232" y2="152"/><rect x="229" y="154" width="6" height="18"/>
                                            <line x1="286" y1="172" x2="286" y2="148"/><rect x="283" y="150" width="6" height="16"/>
                                            <line x1="340" y1="168" x2="340" y2="142"/><rect x="337" y="144" width="6" height="18"/>
                                            <line x1="358" y1="175" x2="358" y2="155"/><rect x="355" y="157" width="6" height="14"/>
                                            <line x1="412" y1="165" x2="412" y2="138"/><rect x="409" y="140" width="6" height="20"/>
                                            <line x1="430" y1="172" x2="430" y2="148"/><rect x="427" y="150" width="6" height="18"/>
                                            <line x1="484" y1="158" x2="484" y2="132"/><rect x="481" y="134" width="6" height="20"/>
                                            <line x1="502" y1="168" x2="502" y2="145"/><rect x="499" y="147" width="6" height="16"/>
                                            <line x1="538" y1="162" x2="538" y2="138"/><rect x="535" y="140" width="6" height="18"/>
                                            <line x1="574" y1="158" x2="574" y2="128"/><rect x="571" y="130" width="6" height="22"/>
                                            <line x1="610" y1="152" x2="610" y2="125"/><rect x="607" y="127" width="6" height="20"/>
                                            <line x1="628" y1="160" x2="628" y2="138"/><rect x="625" y="140" width="6" height="16"/>
                                            <line x1="664" y1="155" x2="664" y2="130"/><rect x="661" y="132" width="6" height="18"/>
                                            <line x1="682" y1="162" x2="682" y2="142"/><rect x="679" y="144" width="6" height="14"/>
                                            <line x1="700" y1="158" x2="700" y2="135"/><rect x="697" y="137" width="6" height="16"/>
                                            <line x1="718" y1="165" x2="718" y2="145"/><rect x="715" y="147" width="6" height="14"/>
                                            <line x1="754" y1="160" x2="754" y2="138"/><rect x="751" y="140" width="6" height="16"/>
                                            <line x1="772" y1="168" x2="772" y2="152"/><rect x="769" y="154" width="6" height="12"/>
                                            <line x1="790" y1="172" x2="790" y2="158"/><rect x="787" y="160" width="6" height="10"/>
                                            <line x1="862" y1="165" x2="862" y2="148"/><rect x="859" y="150" width="6" height="12"/>
                                            <line x1="880" y1="170" x2="880" y2="155"/><rect x="877" y="157" width="6" height="10"/>
                                        </g>
                                    </g>
                                    <line x1="0" y1="118" x2="900" y2="118" stroke="#f23645" stroke-width="1" stroke-dasharray="4 3" opacity="0.9"/>
                                    <rect x="0" y="252" width="900" height="68" fill="#ffffff" opacity="0.94"/>
                                    <g opacity="0.55">
                                        <rect x="28" y="288" width="4" height="22" fill="#089981"/><rect x="46" y="292" width="4" height="18" fill="#f23645"/>
                                        <rect x="64" y="285" width="4" height="25" fill="#089981"/><rect x="82" y="290" width="4" height="20" fill="#f23645"/>
                                        <rect x="100" y="282" width="4" height="28" fill="#089981"/><rect x="118" y="288" width="4" height="22" fill="#f23645"/>
                                        <rect x="136" y="278" width="4" height="32" fill="#089981"/><rect x="154" y="285" width="4" height="25" fill="#f23645"/>
                                        <rect x="172" y="275" width="4" height="35" fill="#089981"/><rect x="190" y="282" width="4" height="28" fill="#f23645"/>
                                        <rect x="208" y="272" width="4" height="38" fill="#089981"/><rect x="226" y="278" width="4" height="32" fill="#f23645"/>
                                        <rect x="244" y="268" width="4" height="42" fill="#089981"/><rect x="262" y="275" width="4" height="35" fill="#f23645"/>
                                        <rect x="280" y="265" width="4" height="45" fill="#089981"/><rect x="298" y="272" width="4" height="38" fill="#f23645"/>
                                        <rect x="316" y="262" width="4" height="48" fill="#089981"/><rect x="334" y="268" width="4" height="42" fill="#f23645"/>
                                        <rect x="352" y="258" width="4" height="52" fill="#089981"/><rect x="370" y="265" width="4" height="45" fill="#f23645"/>
                                        <rect x="388" y="255" width="4" height="55" fill="#089981"/><rect x="406" y="262" width="4" height="48" fill="#f23645"/>
                                        <rect x="424" y="252" width="4" height="58" fill="#089981"/><rect x="442" y="258" width="4" height="52" fill="#f23645"/>
                                        <rect x="460" y="248" width="4" height="62" fill="#089981"/><rect x="478" y="255" width="4" height="55" fill="#f23645"/>
                                        <rect x="496" y="245" width="4" height="65" fill="#089981"/><rect x="514" y="252" width="4" height="58" fill="#f23645"/>
                                        <rect x="532" y="242" width="4" height="68" fill="#089981"/><rect x="550" y="248" width="4" height="62" fill="#f23645"/>
                                        <rect x="568" y="238" width="4" height="72" fill="#089981"/><rect x="586" y="245" width="4" height="65" fill="#f23645"/>
                                        <rect x="604" y="235" width="4" height="75" fill="#089981"/><rect x="622" y="242" width="4" height="68" fill="#f23645"/>
                                        <rect x="640" y="232" width="4" height="78" fill="#089981"/><rect x="658" y="238" width="4" height="72" fill="#f23645"/>
                                        <rect x="676" y="228" width="4" height="82" fill="#089981"/><rect x="694" y="235" width="4" height="75" fill="#f23645"/>
                                        <rect x="712" y="225" width="4" height="85" fill="#089981"/><rect x="730" y="232" width="4" height="78" fill="#f23645"/>
                                        <rect x="748" y="222" width="4" height="88" fill="#089981"/><rect x="766" y="228" width="4" height="82" fill="#f23645"/>
                                        <rect x="784" y="218" width="4" height="92" fill="#089981"/><rect x="802" y="225" width="4" height="85" fill="#f23645"/>
                                        <rect x="820" y="215" width="4" height="95" fill="#089981"/><rect x="838" y="222" width="4" height="88" fill="#f23645"/>
                                        <rect x="856" y="212" width="4" height="98" fill="#089981"/><rect x="874" y="218" width="4" height="92" fill="#f23645"/>
                                    </g>
                                </svg>

                                <div class="tv-events d-none d-sm-flex">
                                    <span class="ev e">E</span>
                                    <span class="ev d">D</span>
                                    <span class="ev e">E</span>
                                    <span class="ev e">E</span>
                                    <span class="ev d">D</span>
                                </div>
                            </div>

                            <div class="tv-scale d-flex flex-md-column flex-row flex-wrap justify-content-between justify-content-md-start py-md-2 py-2 px-1 order-0 order-md-1 flex-shrink-0">
                                <div class="tick">320.00</div>
                                <div class="tick">300.00</div>
                                <div class="tick">280.00</div>
                                <div class="tick d-none d-md-block">260.00</div>
                                <div class="tick d-none d-md-block">240.00</div>
                                <div class="tick d-none d-md-block">220.00</div>
                                <div class="tick d-none d-md-block">200.00</div>
                                <div class="tick d-none d-md-block">180.00</div>
                                <div class="tick d-none d-md-block">160.00</div>
                                <div class="ms-md-auto mt-md-auto w-100">
                                    <div class="tick-current">273.43</div>
                                    <div class="tick-pm text-center">272.32</div>
                                    <div class="text-center text-muted mt-1" style="font-size:9px;">Post-mkt</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tv-foot px-2 py-2 d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="fw-bold text-muted" style="font-size:10px;letter-spacing:.02em;">CHART PREVIEW</span>
                        </div>
                        <div class="tf d-none d-md-flex flex-wrap justify-content-center">
                            <span class="active">1D</span><span>5D</span><span>1M</span><span>3M</span><span>6M</span><span>YTD</span><span>1Y</span><span>5Y</span><span>All</span>
                        </div>
                        <div class="d-flex align-items-center gap-2 small">
                            <span class="d-none d-lg-inline font-monospace">23:07:06 UTC</span>
                            <span class="badge bg-light text-dark border">ADJ</span>
                            <i class="fas fa-cog text-muted"></i>
                        </div>
                    </div>

                    <div class="px-2 py-1 border-top text-muted d-flex flex-wrap justify-content-between" style="font-size:10px;background:#f8f9fd;">
                        <span>Jun · Jul · Aug · Sep · Oct · Nov · Dec · 2026 · Feb · Mar · Apr · May</span>
                        <span>Time axis — illustrative</span>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
