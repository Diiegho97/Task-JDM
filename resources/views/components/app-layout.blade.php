<div class="min-vh-100 bg-light">
    <!-- Header -->
    @isset($header)
        <header class="bg-white shadow mb-4">
            <div class="container py-4">
                {{ $header }}
            </div>
        </header>
    @endisset

    <!-- Main Content -->
    <main>
        <div class="container">
            {{ $slot }}
        </div>
    </main>
</div>
