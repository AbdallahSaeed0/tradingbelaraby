@extends('layouts.app')

@section('title', 'التحقق من الشهادة - ' . $number)

@section('content')
<div class="container py-5" dir="rtl" style="font-family:'Cairo',sans-serif">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card shadow-sm border-0" style="border-top:5px solid #d9a229 !important">
                <div class="card-body p-4 p-md-5 text-center">
                    <h1 class="h4 fw-bold mb-1" style="color:#0b2a63">التحقق من الشهادة</h1>
                    <p class="text-muted mb-4" dir="ltr">{{ $number }}</p>

                    @if (!$certificate)
                        <div class="alert alert-danger mb-0">
                            لم يتم العثور على شهادة بهذا الرقم. تأكد من الرقم المطبوع على الشهادة.
                        </div>
                    @else
                        @if ($certificate['valid'])
                            <span class="badge rounded-pill bg-success px-4 py-2 fs-6 mb-4">شهادة سارية ✓</span>
                        @else
                            <span class="badge rounded-pill bg-danger px-4 py-2 fs-6 mb-4">شهادة ملغاة</span>
                        @endif

                        <table class="table text-end align-middle mb-4">
                            <tbody>
                                <tr>
                                    <th class="text-muted fw-normal" style="width:35%">اسم المشارك</th>
                                    <td class="fw-bold">
                                        {{ $certificate['name'] }}
                                        @if ($certificate['name_en'])<div class="small text-muted fw-normal" dir="ltr">{{ $certificate['name_en'] }}</div>@endif
                                    </td>
                                </tr>
                                <tr>
                                    <th class="text-muted fw-normal">الكورس</th>
                                    <td class="fw-bold">
                                        {{ $certificate['course'] }}
                                        @if ($certificate['course_en'])<div class="small text-muted fw-normal" dir="ltr">{{ $certificate['course_en'] }}</div>@endif
                                    </td>
                                </tr>
                                <tr>
                                    <th class="text-muted fw-normal">تاريخ الإصدار</th>
                                    <td class="fw-bold" dir="ltr" style="text-align:right">{{ $certificate['date'] }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted fw-normal">رقم الشهادة</th>
                                    <td class="fw-bold" dir="ltr" style="text-align:right">{{ $certificate['number'] }}</td>
                                </tr>
                            </tbody>
                        </table>

                        <p class="small text-muted mb-0">
                            شهادة مشاركة تعليمية صادرة عن أكاديمية تداول بالعربي، وليست اعتمادًا مهنيًا ولا ترخيصًا بمزاولة أي نشاط مالي.
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
