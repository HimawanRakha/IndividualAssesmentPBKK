{{--
    Form pencarian tabel admin (GET), sehingga kata kunci tersimpan di query string
    dan ikut terbawa oleh withQueryString() saat berpindah halaman.

    Variabel: $action, $placeholder, $filterName, $filterLabel, $filterOptions.
--}}
<form method="GET" action="{{ $action }}" class="row g-2 align-items-center" role="search">
    <div class="col-md-6">
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-search" aria-hidden="true"></i></span>
            <input type="search" name="q" value="{{ request('q') }}" class="form-control"
                placeholder="{{ $placeholder }}" aria-label="Kata kunci pencarian" maxlength="100">
        </div>
    </div>
    <div class="col-md-4">
        <select name="{{ $filterName }}" class="form-select" aria-label="{{ $filterLabel }}">
            <option value="">Semua {{ Str::lower($filterLabel) }}</option>
            @foreach ($filterOptions as $value => $label)
                <option value="{{ $value }}" @selected(request($filterName) === (string) $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2 d-flex gap-2">
        <button type="submit" class="btn btn-primary flex-fill">Cari</button>
        @if (request()->hasAny(['q', $filterName]))
            <a href="{{ $action }}" class="btn btn-outline-secondary" title="Reset filter">
                <i class="bi bi-x-lg" aria-hidden="true"></i>
            </a>
        @endif
    </div>
</form>
