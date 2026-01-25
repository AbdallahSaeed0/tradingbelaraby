@extends('layouts.app')

@section('title', 'Request Certificate - ' . $course->localized_name)

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-certificate me-2"></i>
                            {{ custom_trans('Request Certificate', 'front') }}
                        </h4>
                    </div>
                    <div class="card-body p-4">
                        <div class="alert alert-success mb-4">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>{{ custom_trans('Congratulations!', 'front') }}</strong>
                            {{ custom_trans('You have successfully completed the course', 'front') }}: 
                            <strong>{{ $course->localized_name }}</strong>
                        </div>

                        <p class="mb-4">
                            {{ custom_trans('Please enter the name you would like to appear on your certificate. This name will be printed exactly as you enter it.', 'front') }}
                        </p>

                        <form action="{{ route('certificate.store', $course->id) }}" method="POST">
                            @csrf
                            
                            <div class="mb-4">
                                <label for="certificate_name" class="form-label fw-bold">
                                    {{ custom_trans('Name on Certificate', 'front') }} <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                    name="certificate_name" 
                                    id="certificate_name" 
                                    class="form-control form-control-lg @error('certificate_name') is-invalid @enderror"
                                    value="{{ old('certificate_name', Auth::user()->name) }}"
                                    placeholder="{{ custom_trans('Enter your full name', 'front') }}"
                                    required
                                    autofocus>
                                @error('certificate_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    {{ custom_trans('This name will appear on your certificate. Make sure it is spelled correctly.', 'front') }}
                                </small>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('courses.learn', $course->id) }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>
                                    {{ custom_trans('Back to Course', 'front') }}
                                </a>
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-certificate me-2"></i>
                                    {{ custom_trans('Generate Certificate', 'front') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
