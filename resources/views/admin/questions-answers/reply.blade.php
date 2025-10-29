@extends('admin.layout')

@section('title', $questionsAnswer->answer_content ? 'Edit Reply' : 'Add Reply')

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">{{ $questionsAnswer->answer_content ? 'Edit Reply' : 'Add Reply' }}</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.questions-answers.index') }}">Q&A Management</a>
                        </li>
                        <li class="breadcrumb-item"><a
                                href="{{ route('admin.questions-answers.show', $questionsAnswer) }}">Question Details</a>
                        </li>
                        <li class="breadcrumb-item active">
                            {{ $questionsAnswer->answer_content ? 'Edit Reply' : 'Add Reply' }}</li>
                    </ul>
                </div>
                <div class="col-auto">
                    <a href="{{ route('admin.questions-answers.show', $questionsAnswer) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Question
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Question Preview -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Question Preview</h5>
                    </div>
                    <div class="card-body">
                        <div class="question-preview">
                            <h6 class="mb-3">{{ $questionsAnswer->question_title }}</h6>

                            <div class="question-meta mb-3">
                                <div class="meta-item">
                                    <strong>Asked by:</strong>
                                    @if ($questionsAnswer->is_anonymous)
                                        <span class="text-muted">Anonymous Student</span>
                                    @else
                                        <span>{{ $questionsAnswer->user->name ?? 'Unknown User' }}</span>
                                    @endif
                                </div>
                                <div class="meta-item">
                                    <strong>Course:</strong>
                                    <span>{{ $questionsAnswer->course->name ?? 'N/A' }}</span>
                                </div>
                                <div class="meta-item">
                                    <strong>Type:</strong>
                                    <span
                                        class="badge badge-light">{{ ucfirst(str_replace('_', ' ', $questionsAnswer->question_type)) }}</span>
                                </div>
                                <div class="meta-item">
                                    <strong>Priority:</strong>
                                    @switch($questionsAnswer->priority)
                                        @case('urgent')
                                            <span class="badge badge-danger">Urgent</span>
                                        @break

                                        @case('high')
                                            <span class="badge badge-warning">High</span>
                                        @break

                                        @case('normal')
                                            <span class="badge badge-info">Normal</span>
                                        @break

                                        @case('low')
                                            <span class="badge badge-secondary">Low</span>
                                        @break
                                    @endswitch
                                </div>
                                <div class="meta-item">
                                    <strong>Asked on:</strong>
                                    <span>{{ $questionsAnswer->formatted_question_date }}</span>
                                </div>
                            </div>

                            <div class="question-content">
                                <strong>Question:</strong>
                                <div class="content-preview">
                                    {{ Str::limit(strip_tags($questionsAnswer->question_content), 200) }}
                                </div>
                            </div>

                            @if ($questionsAnswer->lecture)
                                <div class="lecture-info mt-3">
                                    <strong>Related Lecture:</strong>
                                    <div class="content-preview">
                                        {{ $questionsAnswer->lecture->title }}
                                    </div>
                                </div>
                            @endif

                            @if ($questionsAnswer->section)
                                <div class="section-info mt-2">
                                    <strong>Section:</strong>
                                    <div class="content-preview">
                                        {{ $questionsAnswer->section->title }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Reply Guidelines -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Reply Guidelines</h5>
                    </div>
                    <div class="card-body">
                        <div class="guidelines">
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <i class="fas fa-check-circle text-success"></i>
                                    Be clear and concise in your response
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check-circle text-success"></i>
                                    Provide specific examples when possible
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check-circle text-success"></i>
                                    Use a professional and helpful tone
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check-circle text-success"></i>
                                    Reference course materials if relevant
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check-circle text-success"></i>
                                    Encourage further questions if needed
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reply Form -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ $questionsAnswer->answer_content ? 'Edit Reply' : 'Add Reply' }}
                        </h5>
                    </div>
                    <div class="card-body">
                        @if ($questionsAnswer->answer_content)
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                You are editing an existing reply. The original reply will be replaced with your new
                                content.
                            </div>
                        @endif

                        <form
                            action="{{ $questionsAnswer->answer_content ? route('admin.questions-answers.update_reply', $questionsAnswer) : route('admin.questions-answers.store_reply', $questionsAnswer) }}"
                            method="POST">
                            @csrf
                            @if ($questionsAnswer->answer_content)
                                @method('PUT')
                            @endif

                            <div class="form-group">
                                <label for="answer_content">Your Reply <span class="text-danger">*</span></label>
                                <textarea name="answer_content" id="answer_content" class="form-control" rows="12" required
                                    placeholder="Write your detailed reply here...">{{ old('answer_content', $questionsAnswer->answer_content) }}</textarea>
                                <small class="form-text text-muted">
                                    Minimum 10 characters. You can use basic formatting like <strong>bold</strong>,
                                    <em>italic</em>, and line breaks.
                                </small>
                                @error('answer_content')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Audio Recording Section -->
                            <div class="form-group">
                                <label>Audio Reply (Optional)</label>
                                <div class="audio-recording-section">
                                    <!-- Recording Controls -->
                                    <div class="recording-controls mb-3">
                                        <button type="button" id="startRecording" class="btn btn-outline-primary">
                                            <i class="fas fa-microphone"></i> Start Recording
                                        </button>
                                        <button type="button" id="stopRecording" class="btn btn-outline-danger"
                                            style="display: none;">
                                            <i class="fas fa-stop"></i> Stop Recording
                                        </button>
                                        <button type="button" id="playRecording" class="btn btn-outline-success"
                                            style="display: none;">
                                            <i class="fas fa-play"></i> Play
                                        </button>
                                        <button type="button" id="deleteRecording" class="btn btn-outline-secondary"
                                            style="display: none;">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </div>

                                    <!-- Recording Status -->
                                    <div id="recordingStatus" class="recording-status" style="display: none;">
                                        <div class="recording-indicator">
                                            <span class="recording-dot"></span>
                                            <span id="recordingDuration">00:00</span>
                                        </div>
                                    </div>

                                    <!-- Audio Preview -->
                                    <div id="audioPreview" class="audio-preview" style="display: none;">
                                        <audio id="audioPlayer" controls style="width: 100%;">
                                            Your browser does not support the audio element.
                                        </audio>
                                    </div>

                                    <!-- Existing Audio -->
                                    @if ($questionsAnswer->hasAudio())
                                        <div class="existing-audio mb-3">
                                            <label class="form-label">Current Audio:</label>
                                            <audio controls style="width: 100%;">
                                                <source src="{{ $questionsAnswer->audio_url }}" type="audio/webm">
                                                <source src="{{ $questionsAnswer->audio_url }}" type="audio/mp3">
                                                Your browser does not support the audio element.
                                            </audio>
                                            <div class="mt-2">
                                                <a href="{{ $questionsAnswer->audio_url }}" download
                                                    class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-download"></i> Download Audio
                                                </a>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Hidden input for audio data -->
                                    <input type="hidden" name="audio_data" id="audioData">
                                </div>
                                <small class="form-text text-muted">
                                    Record an audio reply to supplement your text answer. Maximum 10MB.
                                </small>
                                @error('audio_data')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="moderation_notes">Moderation Notes (Optional)</label>
                                <textarea name="moderation_notes" id="moderation_notes" class="form-control" rows="3"
                                    placeholder="Add any internal notes about this question or reply...">{{ old('moderation_notes', $questionsAnswer->moderation_notes) }}</textarea>
                                <small class="form-text text-muted">
                                    These notes are only visible to administrators and will not be shown to students.
                                </small>
                                @error('moderation_notes')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="mark_urgent"
                                        name="mark_urgent" value="1"
                                        {{ $questionsAnswer->priority === 'urgent' ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="mark_urgent">
                                        Mark as urgent priority
                                    </label>
                                </div>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane"></i>
                                    {{ $questionsAnswer->answer_content ? 'Update Reply' : 'Submit Reply' }}
                                </button>
                                <a href="{{ route('admin.questions-answers.show', $questionsAnswer) }}"
                                    class="btn btn-secondary">
                                    Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Preview Section -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Reply Preview</h5>
                    </div>
                    <div class="card-body">
                        <div id="replyPreview" class="reply-preview">
                            <p class="text-muted">Start typing your reply above to see a preview here...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const answerContent = document.getElementById('answer_content');
            const replyPreview = document.getElementById('replyPreview');

            function updatePreview() {
                const content = answerContent.value;
                if (content.trim() === '') {
                    replyPreview.innerHTML =
                        '<p class="text-muted">Start typing your reply above to see a preview here...</p>';
                } else {
                    // Simple formatting for preview
                    let formattedContent = content
                        .replace(/\n/g, '<br>')
                        .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                        .replace(/\*(.*?)\*/g, '<em>$1</em>');

                    replyPreview.innerHTML = formattedContent;
                }
            }

            answerContent.addEventListener('input', updatePreview);
            answerContent.addEventListener('keyup', updatePreview);

            // Initial preview
            updatePreview();

            // Character counter
            const charCounter = document.createElement('div');
            charCounter.className = 'text-muted mt-1';
            answerContent.parentNode.appendChild(charCounter);

            function updateCharCounter() {
                const count = answerContent.value.length;
                const min = 10;
                const color = count >= min ? 'text-success' : 'text-danger';
                charCounter.innerHTML = `<span class="${color}">${count} characters (minimum ${min})</span>`;
            }

            answerContent.addEventListener('input', updateCharCounter);
            updateCharCounter();

            // Audio Recording Functionality
            let mediaRecorder;
            let audioChunks = [];
            let recordingStartTime;
            let recordingInterval;
            let isRecording = false;

            const startRecordingBtn = document.getElementById('startRecording');
            const stopRecordingBtn = document.getElementById('stopRecording');
            const playRecordingBtn = document.getElementById('playRecording');
            const deleteRecordingBtn = document.getElementById('deleteRecording');
            const recordingStatus = document.getElementById('recordingStatus');
            const audioPreview = document.getElementById('audioPreview');
            const audioPlayer = document.getElementById('audioPlayer');
            const audioData = document.getElementById('audioData');
            const recordingDuration = document.getElementById('recordingDuration');

            // Check for MediaRecorder support
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                startRecordingBtn.disabled = true;
                startRecordingBtn.innerHTML = '<i class="fas fa-microphone-slash"></i> Recording Not Supported';
                startRecordingBtn.title = 'Your browser does not support audio recording';
            }

            // Start recording
            startRecordingBtn.addEventListener('click', async () => {
                try {
                    const stream = await navigator.mediaDevices.getUserMedia({
                        audio: true
                    });
                    mediaRecorder = new MediaRecorder(stream);
                    audioChunks = [];

                    mediaRecorder.ondataavailable = (event) => {
                        audioChunks.push(event.data);
                    };

                    mediaRecorder.onstop = () => {
                        const audioBlob = new Blob(audioChunks, {
                            type: 'audio/webm'
                        });
                        const audioUrl = URL.createObjectURL(audioBlob);

                        // Convert to base64 for form submission
                        const reader = new FileReader();
                        reader.onload = () => {
                            audioData.value = reader.result;
                        };
                        reader.readAsDataURL(audioBlob);

                        // Update UI
                        audioPlayer.src = audioUrl;
                        audioPreview.style.display = 'block';
                        playRecordingBtn.style.display = 'inline-block';
                        deleteRecordingBtn.style.display = 'inline-block';
                        startRecordingBtn.style.display = 'none';
                        stopRecordingBtn.style.display = 'none';
                        recordingStatus.style.display = 'none';

                        // Stop all tracks
                        stream.getTracks().forEach(track => track.stop());
                    };

                    mediaRecorder.start();
                    isRecording = true;
                    recordingStartTime = Date.now();

                    // Update UI
                    startRecordingBtn.style.display = 'none';
                    stopRecordingBtn.style.display = 'inline-block';
                    recordingStatus.style.display = 'block';

                    // Start duration timer
                    recordingInterval = setInterval(updateRecordingDuration, 1000);

                } catch (error) {
                    console.error('Error accessing microphone:', error);
                    alert('Error accessing microphone. Please check your permissions.');
                }
            });

            // Stop recording
            stopRecordingBtn.addEventListener('click', () => {
                if (mediaRecorder && isRecording) {
                    mediaRecorder.stop();
                    isRecording = false;
                    clearInterval(recordingInterval);
                }
            });

            // Play recording
            playRecordingBtn.addEventListener('click', () => {
                if (audioPlayer.paused) {
                    audioPlayer.play();
                    playRecordingBtn.innerHTML = '<i class="fas fa-pause"></i> Pause';
                } else {
                    audioPlayer.pause();
                    playRecordingBtn.innerHTML = '<i class="fas fa-play"></i> Play';
                }
            });

            // Delete recording
            deleteRecordingBtn.addEventListener('click', () => {
                if (confirm('Are you sure you want to delete this recording?')) {
                    audioData.value = '';
                    audioPreview.style.display = 'none';
                    playRecordingBtn.style.display = 'none';
                    deleteRecordingBtn.style.display = 'none';
                    startRecordingBtn.style.display = 'inline-block';
                    stopRecordingBtn.style.display = 'none';
                    recordingStatus.style.display = 'none';

                    // Reset audio player
                    audioPlayer.src = '';
                    playRecordingBtn.innerHTML = '<i class="fas fa-play"></i> Play';
                }
            });

            // Update recording duration
            function updateRecordingDuration() {
                if (isRecording && recordingStartTime) {
                    const elapsed = Math.floor((Date.now() - recordingStartTime) / 1000);
                    const minutes = Math.floor(elapsed / 60);
                    const seconds = elapsed % 60;
                    recordingDuration.textContent =
                        `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                }
            }

            // Handle audio player events
            audioPlayer.addEventListener('ended', () => {
                playRecordingBtn.innerHTML = '<i class="fas fa-play"></i> Play';
            });

            audioPlayer.addEventListener('pause', () => {
                playRecordingBtn.innerHTML = '<i class="fas fa-play"></i> Play';
            });

            audioPlayer.addEventListener('play', () => {
                playRecordingBtn.innerHTML = '<i class="fas fa-pause"></i> Pause';
            });
        });
    </script>
@endpush
