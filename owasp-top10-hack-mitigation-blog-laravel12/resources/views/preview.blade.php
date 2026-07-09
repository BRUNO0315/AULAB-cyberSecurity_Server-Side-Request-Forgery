<x-layouts.app title="Anteprima URL">

    <div class="container py-5">

        <div class="card shadow">

            <div class="card-header">
                <h3>Anteprima di un URL</h3>
            </div>

            <div class="card-body">

                <form action="{{ route('preview.show') }}" method="POST">

                    @csrf

                    <div class="mb-3">

                        <label class="form-label">
                            Inserisci un URL
                        </label>

                        <input
                            type="url"
                            class="form-control"
                            name="url"
                            placeholder="https://example.com"
                            required>

                    </div>

                    <button class="btn btn-primary">
                        Visualizza anteprima
                    </button>

                </form>

                @if ($errors->any())

                    <div class="alert alert-danger mt-3">

                        {{ $errors->first() }}

                    </div>

                @endif

                @isset($preview)

                    <hr>

                    @if($preview['image'])

                        <img
                            src="{{ $preview['image'] }}"
                            class="img-fluid rounded mb-3">

                    @endif

                    <h3>{{ $preview['title'] }}</h3>

                    <p>{{ $preview['description'] }}</p>

                    <a
                        href="{{ $preview['url'] }}"
                        target="_blank">

                        {{ $preview['url'] }}

                    </a>

                @endisset

            </div>

        </div>

    </div>

</x-layouts.app>