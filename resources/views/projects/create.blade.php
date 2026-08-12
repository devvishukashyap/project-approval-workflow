<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Project</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                
                <div class="card-header bg-primary text-white py-3">
                    <h4 class="mb-0">Submit New Project</h4>
                </div>

                <div class="card-body p-4">

                    @if(session('failed'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('failed') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

                    <!-- Project Form -->
                    <form id="projectForm" action="{{ route('projects.store') }}" method="POST" enctype="multipart/form-data" novalidate>
                        @csrf

                        <!-- Project Title Input -->
                        <div class="mb-3">
                            <label for="title" class="form-label fw-bold">Project Title <span class="text-danger">*</span></label>
                            <input 
                                type="text" 
                                name="title" 
                                id="title" 
                                class="form-control @error('title') is-invalid @enderror" 
                                value="{{ old('title') }}" 
                                placeholder="Enter project title"
                            >
                            <div class="invalid-feedback" id="titleError">
                                @error('title') {{ $message }} @else Title is required (min 5 characters). @enderror
                            </div>
                        </div>

                        <!-- Description Input -->
                        <div class="mb-3">
                            <label for="description" class="form-label fw-bold">Description <span class="text-danger">*</span></label>
                            <textarea 
                                name="description" 
                                id="description" 
                                class="form-control @error('description') is-invalid @enderror" 
                                rows="5" 
                                placeholder="Enter detailed project description"
                            >{{ old('description') }}</textarea>
                            <div class="invalid-feedback" id="descriptionError">
                                @error('description') {{ $message }} @else Description is required (min 20 characters). @enderror
                            </div>
                        </div>

                        <!-- File Upload Input -->
                        <div class="mb-4">
                            <label for="file" class="form-label fw-bold">Project Attachment</label>
                            <input 
                                type="file" 
                                name="file" 
                                id="file" 
                                class="form-control @error('file') is-invalid @enderror"
                            >
                            <small class="text-muted d-block mt-1">Allowed formats: PDF, DOCX, PNG, JPG (Max size: 2MB)</small>
                            <div class="invalid-feedback" id="fileError">
                                @error('file') {{ $message }} @enderror
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" id="submitBtn" class="btn btn-primary px-4 py-2" disabled>
                            <span class="spinner-border spinner-border-sm me-2 d-none" id="btnSpinner" role="status" aria-hidden="true"></span>
                            <span id="btnText">Submit Project</span>
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


<script>
document.addEventListener('DOMContentLoaded', function () {
    // HTML Elements Selection
    const form = document.getElementById('projectForm');
    const titleInput = document.getElementById('title');
    const descriptionInput = document.getElementById('description');
    const fileInput = document.getElementById('file');
    const submitBtn = document.getElementById('submitBtn');
    const btnSpinner = document.getElementById('btnSpinner');
    const btnText = document.getElementById('btnText');

    
    function setFieldState(input, isValid, errorMsg) {
        const errorDiv = input.nextElementSibling;
        if (isValid) {
            input.classList.remove('is-invalid');
            input.classList.add('is-valid');
            return true;
        } else {
            input.classList.remove('is-valid');
            input.classList.add('is-invalid');
            if (errorDiv && errorDiv.classList.contains('invalid-feedback')) {
                errorDiv.textContent = errorMsg;
            }
            return false;
        }
    }

    // 1. Real-time Title Check
    function validateTitle() {
        const val = titleInput.value.trim();
        if (val === '') {
            return setFieldState(titleInput, false, 'Project title is required.');
        } else if (val.length < 5) {
            return setFieldState(titleInput, false, 'Title must be at least 5 characters long.');
        } else {
            return setFieldState(titleInput, true, '');
        }
    }

    // 2. Real-time Description Check
    function validateDescription() {
        const val = descriptionInput.value.trim();
        if (val === '') {
            return setFieldState(descriptionInput, false, 'Description is required.');
        } else if (val.length < 20) {
            return setFieldState(descriptionInput, false, 'Description must be at least 20 characters long.');
        } else {
            return setFieldState(descriptionInput, true, '');
        }
    }

    // 3. Real-time File Check
    function validateFile() {
        if (fileInput.files.length > 0) {
            const file = fileInput.files[0];
            const maxMB = 2 * 1024 * 1024; // 2MB Limit

            if (file.size > maxMB) {
                return setFieldState(fileInput, false, 'File size exceeds 2MB limit.');
            } else {
                return setFieldState(fileInput, true, '');
            }
        } else {
            fileInput.classList.remove('is-invalid', 'is-valid');
            return true; // File optional hai
        }
    }

    // Overall Form Check (Controls Enable/Disable Submit Button)
    function checkForm() {
        const isTitleValid = validateTitle();
        const isDescValid = validateDescription();
        const isFileValid = validateFile();

        if (isTitleValid && isDescValid && isFileValid) {
            submitBtn.removeAttribute('disabled');
        } else {
            submitBtn.setAttribute('disabled', 'disabled');
        }
    }

    // Live Event Listeners
    titleInput.addEventListener('input', checkForm);
    descriptionInput.addEventListener('input', checkForm);
    fileInput.addEventListener('change', checkForm);

    // Form Submit Handler (Triggers Loading Feedback before Redirect)
    form.addEventListener('submit', function (e) {
        if (validateTitle() && validateDescription() && validateFile()) {
            btnSpinner.classList.remove('d-none');
            btnText.textContent = 'Submitting...';
            submitBtn.setAttribute('disabled', 'disabled');
        } else {
            e.preventDefault();
        }
    });
});
</script>

</body>
</html>