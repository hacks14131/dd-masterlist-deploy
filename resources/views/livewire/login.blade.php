<div class="login-page d-flex align-items-center justify-content-center py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <form wire:submit.prevent="login" class="w-100">
                    <div class="card shadow-sm border-0 rounded-lg">
                        <!-- Header -->
                        <div class="card-header bg-white text-center py-4 border-bottom-0">
                            <h1 class="login-title mb-0">Masterlist Validator</h1>
                            <p class="login-subtitle small mt-2">
                                Please enter your credentials to continue
                            </p>
                        </div>

                        <div class="card-body p-4 p-md-5">
                            @error('login')
                                <div class="alert alert-danger mb-4" role="alert">
                                    {{ $message }}
                                </div>
                            @enderror
                            <div class="form-group mb-4">
                                <label for="username" class="login-label mb-2">Username</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white input-icon">
                                        <i class="bi bi-person-fill"></i>
                                    </span>
                                    <input
                                        id="username"
                                        type="text"
                                        wire:model.defer="username"
                                        class="form-control form-control-lg border-start-0"
                                        autocomplete="username"
                                        placeholder="Enter your username"
                                        aria-label="Username" />
                                </div>
                                @error('username')
                                    <div class="text-danger mt-2 error-text">
                                        <i class="bi bi-exclamation-circle me-1"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-group mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label for="password" class="login-label mb-0">Password</label>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-text bg-white input-icon">
                                        <i class="bi bi-lock-fill"></i>
                                    </span>
                                    <input
                                        id="password"
                                        type="password"
                                        wire:model.defer="password"
                                        class="form-control form-control-lg border-start-0"
                                        placeholder="Enter your password"
                                        aria-label="Password" />
                                    <button type="button"
                                            class="input-group-text bg-white border-start-0 input-icon"
                                            onclick="togglePasswordVisibility()"
                                            aria-label="Show/Hide Password">
                                        <i class="bi bi-eye-slash" id="togglePassword"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <div class="text-danger mt-2 error-text">
                                        <i class="bi bi-exclamation-circle me-1"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <button type="submit"
                                        class="btn btn-brand btn-info py-3 fw-bold w-100">
                                    <span wire:loading.remove>Login</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('togglePassword');
            const isHidden = passwordInput.type === 'password';
    
            passwordInput.type = isHidden ? 'text' : 'password';
            toggleIcon.classList.toggle('bi-eye', isHidden);
            toggleIcon.classList.toggle('bi-eye-slash', !isHidden);
        }
    </script>
</div>