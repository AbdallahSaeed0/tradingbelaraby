<!DOCTYPE html>
<html lang="{{ get_current_language_code() }}" dir="{{ \App\Helpers\TranslationHelper::getCurrentLanguage()->direction }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Language Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Language Switching Test</h1>
        
        <div class="row">
            <div class="col-md-6">
                <h3>Current Language Info:</h3>
                <ul>
                    <li><strong>Language Code:</strong> {{ get_current_language_code() }}</li>
                    <li><strong>Direction:</strong> {{ \App\Helpers\TranslationHelper::getCurrentLanguage()->direction }}</li>
                    <li><strong>Name:</strong> {{ \App\Helpers\TranslationHelper::getCurrentLanguage()->name }}</li>
                    <li><strong>Native Name:</strong> {{ \App\Helpers\TranslationHelper::getCurrentLanguage()->native_name }}</li>
                </ul>
                
                <h3>Language Switcher:</h3>
                <div class="btn-group" role="group">
                    <a href="{{ route('language.switch', 'en') }}" class="btn btn-primary">English</a>
                    <a href="{{ route('language.switch', 'ar') }}" class="btn btn-success">العربية</a>
                </div>
            </div>
            
            <div class="col-md-6">
                <h3>Slider Content Test:</h3>
                @php
                    $slider = \App\Models\Slider::first();
                @endphp
                @if($slider)
                    <div class="card">
                        <div class="card-body">
                            <h5>Title:</h5>
                            <p>{{ get_current_language_code() === 'ar' && $slider->title_ar ? $slider->title_ar : $slider->title }}</p>
                            
                            <h5>Welcome Text:</h5>
                            <p>{{ get_current_language_code() === 'ar' && $slider->welcome_text_ar ? $slider->welcome_text_ar : $slider->welcome_text }}</p>
                            
                            <h5>Subtitle:</h5>
                            <p>{{ get_current_language_code() === 'ar' && $slider->subtitle_ar ? $slider->subtitle_ar : $slider->subtitle }}</p>
                            
                            <h5>Button Text:</h5>
                            <p>{{ get_current_language_code() === 'ar' && $slider->button_text_ar ? $slider->button_text_ar : $slider->button_text }}</p>
                        </div>
                    </div>
                @else
                    <p class="text-muted">No slider found</p>
                @endif
                
                <h3>Hero Features Test:</h3>
                @php
                    $heroFeatures = \App\Models\HeroFeature::active()->ordered()->get();
                @endphp
                @if($heroFeatures->count() > 0)
                    <div class="card">
                        <div class="card-body">
                            @foreach($heroFeatures as $feature)
                                <div class="mb-3">
                                    <h6>Feature {{ $loop->iteration }}:</h6>
                                    <p><strong>Title:</strong> {{ get_current_language_code() === 'ar' && $feature->title_ar ? $feature->title_ar : $feature->title }}</p>
                                    <p><strong>Subtitle:</strong> {{ get_current_language_code() === 'ar' && $feature->subtitle_ar ? $feature->subtitle_ar : $feature->subtitle }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <p class="text-muted">No hero features found</p>
                @endif
            </div>
        </div>
    </div>
</body>
</html> 