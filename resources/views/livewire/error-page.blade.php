<div class="error text-center">
    <h1 class="display-4 text-primary mb-3">
        <i class="fas fa-exclamation-triangle"></i>
    </h1>

    <h2 class="h4 mb-2">Oops! Something went wrong.</h2>

    <p class="text-muted mb-4">
        We're working to fix it. Please try again in a moment.
    </p>

    {{-- NEW: Go to /home --}}
    <a href="{{ url('/home') }}" class="btn btn-outline-primary mb-2">
        <i class="fas fa-tachometer-alt mr-1"></i> Go to Home
    </a>
</div>