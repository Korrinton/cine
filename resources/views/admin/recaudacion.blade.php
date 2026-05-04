@extends('layouts.app')

@section('content')
<div class="container py-4">

    {{-- Cabecera --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('admin.dashboard') }}" class="text-muted small text-decoration-none">
                <i class="bi bi-arrow-left me-1"></i>Panel admin
            </a>
            <h2 class="fw-bold mb-0 mt-1">
                <i class="bi bi-cash-coin me-2"></i>Recaudación
                <span class="text-muted fs-5 fw-normal ms-2">{{ $anio }}</span>
            </h2>
        </div>
        <form method="GET" action="{{ route('admin.recaudacion') }}" class="d-flex align-items-center gap-2">
            <label class="form-label mb-0 small text-muted">Año:</label>
            <select name="anio" class="form-select form-select-sm" style="width:auto;" onchange="this.form.submit()">
                @foreach($aniosDisponibles as $a)
                    <option value="{{ $a }}" {{ $a == $anio ? 'selected' : '' }}>{{ $a }}</option>
                @endforeach
            </select>
        </form>
    </div>

    @if(session('success_gasto'))
        <div class="alert alert-success alert-dismissible fade show py-2">
            {{ session('success_gasto') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ── RESUMEN FINANCIERO ────────────────────────────────── --}}
    @php $precioMedio = $entradasTotal > 0 ? $totalGlobal / $entradasTotal : 0; @endphp

    <div class="row g-3 mb-3">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center py-3">
                <div class="text-muted small mb-1">Ingresos brutos totales (IVA inc.)</div>
                <div class="fs-4 fw-bold text-success">{{ number_format($totalBruto, 2) }} €</div>
                <div class="text-muted" style="font-size:.72rem;">
                    Entradas {{ number_format($totalGlobal,2) }} €
                    + Extra {{ number_format($totalIngresoExtra,2) }} €
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center py-3">
                <div class="text-muted small mb-1">Base imponible (sin IVA 21%)</div>
                <div class="fs-4 fw-bold text-primary">{{ number_format($baseImponible, 2) }} €</div>
                <div class="text-muted" style="font-size:.72rem;">IVA a ingresar: {{ number_format($cuotaIva, 2) }} €</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center py-3">
                <div class="text-muted small mb-1">Total gastos</div>
                <div class="fs-4 fw-bold text-danger">{{ number_format($totalGastos, 2) }} €</div>
                <div class="text-muted" style="font-size:.72rem;">Fijos {{ number_format($totalFijos,2) }} € · Variables {{ number_format($totalVariables,2) }} €</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center py-3 border-2 {{ $beneficio >= 0 ? 'border-success' : 'border-danger' }}">
                <div class="text-muted small mb-1">Beneficio estimado</div>
                <div class="fs-4 fw-bold {{ $beneficio >= 0 ? 'text-success' : 'text-danger' }}">
                    {{ number_format($beneficio, 2) }} €
                </div>
                <div class="text-muted" style="font-size:.72rem;">Neto − Gastos</div>
            </div>
        </div>
    </div>

    {{-- Fórmula visual --}}
    <div class="alert alert-light border d-flex align-items-center gap-2 mb-4 py-2 px-3 small">
        <i class="bi bi-calculator text-muted fs-5"></i>
        <span>
            (<strong>{{ number_format($totalGlobal, 2) }} €</strong> entradas
            + <strong>{{ number_format($totalIngresoExtra, 2) }} €</strong> extra)
            = <strong>{{ number_format($totalBruto, 2) }} €</strong> brutos
            − IVA <strong>{{ number_format($cuotaIva, 2) }} €</strong>
            = <strong>{{ number_format($baseImponible, 2) }} €</strong> netos
            − gastos <strong>{{ number_format($totalGastos, 2) }} €</strong>
            = beneficio <strong class="{{ $beneficio >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($beneficio, 2) }} €</strong>
        </span>
    </div>

    <div class="row g-4">

        {{-- ── GASTOS ───────────────────────────────────────── --}}
        <div class="col-lg-5">

            {{-- Formulario añadir gasto --}}
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header fw-semibold bg-light">
                    <i class="bi bi-plus-circle me-1 text-primary"></i>Añadir gasto
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.gastos.guardar') }}">
                        @csrf
                        <input type="hidden" name="anio" value="{{ $anio }}">
                        <div class="mb-2">
                            <input type="text" name="nombre" class="form-control form-control-sm"
                                   placeholder="Descripción (ej: Alquiler local)" required>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <select name="tipo" id="tipoGasto" class="form-select form-select-sm"
                                        required onchange="togglePelicula()">
                                    <option value="fijo">Fijo (importe total anual)</option>
                                    <option value="variable">Variable (€ por sesión/pase)</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <div class="input-group input-group-sm">
                                    <input type="number" name="importe" class="form-control"
                                           step="0.01" min="0" placeholder="0.00" required>
                                    <span class="input-group-text">€</span>
                                </div>
                            </div>
                        </div>
                        <div id="selectPelicula" class="mb-2" style="display:none;">
                            <select name="id_pelicula" class="form-select form-select-sm">
                                <option value="">— Selecciona película —</option>
                                @foreach($peliculas as $p)
                                    <option value="{{ $p->id_pelicula }}">
                                        {{ $p->titulo }}
                                        ({{ $sesionesPorPelicula->get($p->id_pelicula, 0) }} sesiones)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <i class="bi bi-floppy me-1"></i>Guardar gasto
                        </button>
                    </form>
                </div>
            </div>

            {{-- Ingresos extra --}}
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header fw-semibold bg-light d-flex justify-content-between">
                    <span><i class="bi bi-plus-circle me-1 text-success"></i>Ingresos secundarios</span>
                    <span class="text-success fw-bold">{{ number_format($totalIngresoExtra, 2) }} €</span>
                </div>
                <div class="card-body pb-2">
                    <form method="POST" action="{{ route('admin.ingresos.guardar') }}" class="d-flex gap-2 mb-2">
                        @csrf
                        <input type="hidden" name="anio" value="{{ $anio }}">
                        <input type="text" name="nombre" class="form-control form-control-sm"
                               placeholder="Ej: Palomitas" required>
                        <div class="input-group input-group-sm" style="max-width:130px;">
                            <input type="number" name="importe" class="form-control"
                                   step="0.01" min="0" placeholder="0.00" required>
                            <span class="input-group-text">€</span>
                        </div>
                        <button type="submit" class="btn btn-success btn-sm">
                            <i class="bi bi-plus-lg"></i>
                        </button>
                    </form>
                    @if($ingresosExtra->isEmpty())
                        <p class="text-muted small text-center mb-1">Sin ingresos secundarios.</p>
                    @else
                        <ul class="list-group list-group-flush">
                            @foreach($ingresosExtra as $ing)
                                <li class="list-group-item d-flex justify-content-between align-items-center py-1 px-0">
                                    <span class="small">{{ $ing->nombre }}</span>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="fw-semibold small text-success">{{ number_format($ing->importe, 2) }} €</span>
                                        <form method="POST" action="{{ route('admin.ingresos.eliminar', $ing->id) }}"
                                              onsubmit="return confirm('¿Eliminar?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger py-0 px-1">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            {{-- Gastos fijos --}}
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header fw-semibold bg-light d-flex justify-content-between">
                    <span><i class="bi bi-building me-1 text-danger"></i>Gastos fijos</span>
                    <span class="text-danger fw-bold">{{ number_format($totalFijos, 2) }} €</span>
                </div>
                @if($gastosFijos->isEmpty())
                    <div class="card-body py-2 text-muted small text-center">Sin gastos fijos registrados.</div>
                @else
                    <ul class="list-group list-group-flush">
                        @foreach($gastosFijos as $g)
                            <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                                <span class="small">{{ $g->nombre }}</span>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="fw-semibold small">{{ number_format($g->importe, 2) }} €</span>
                                    <form method="POST" action="{{ route('admin.gastos.eliminar', $g->id) }}"
                                          onsubmit="return confirm('¿Eliminar este gasto?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger py-0 px-1">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            {{-- Gastos variables --}}
            <div class="card shadow-sm border-0">
                <div class="card-header fw-semibold bg-light d-flex justify-content-between">
                    <span><i class="bi bi-graph-up me-1 text-warning"></i>Gastos variables</span>
                    <span class="text-warning fw-bold">{{ number_format($totalVariables, 2) }} €</span>
                </div>
                @if($gastosVariables->isEmpty())
                    <div class="card-body py-2 text-muted small text-center">Sin gastos variables registrados.</div>
                @else
                    <ul class="list-group list-group-flush">
                        @foreach($gastosVariables as $g)
                            <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                                <div>
                                    <div class="small">{{ $g->nombre }}</div>
                                    <div class="text-muted" style="font-size:.72rem;">
                                        @if($g->pelicula)
                                            <span class="badge bg-secondary me-1">{{ $g->pelicula->titulo }}</span>
                                        @endif
                                        {{ number_format($g->importe, 2) }} €
                                        × {{ $sesionesPorPelicula->get($g->id_pelicula, 0) }} sesiones
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    @php $ses = $sesionesPorPelicula->get($g->id_pelicula, 0); @endphp
                                    <span class="fw-semibold small">{{ number_format($g->importe * $ses, 2) }} €</span>
                                    <form method="POST" action="{{ route('admin.gastos.eliminar', $g->id) }}"
                                          onsubmit="return confirm('¿Eliminar este gasto?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger py-0 px-1">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

        </div>

        {{-- ── RECAUDACIÓN POR PELÍCULA ─────────────────────── --}}
        <div class="col-lg-7">
            <div class="card shadow-sm border-0">
                <div class="card-header fw-semibold bg-light">
                    <i class="bi bi-film me-1 text-primary"></i>Recaudación por película
                </div>
                @if($porPelicula->isEmpty())
                    <div class="card-body text-center text-muted py-5">
                        <i class="bi bi-ticket-perforated" style="font-size:2.5rem"></i>
                        <p class="mt-2">Sin reservas en {{ $anio }}.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th style="width:55px"></th>
                                    <th>Película</th>
                                    <th class="text-center">Entradas</th>
                                    <th class="text-end">Recaudado</th>
                                    <th style="width:140px">% total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($porPelicula as $p)
                                    @php $pct = $totalGlobal > 0 ? ($p['total'] / $totalGlobal) * 100 : 0; @endphp
                                    <tr>
                                        <td>
                                            @if($p['imagen'])
                                                <img src="{{ asset('storage/' . $p['imagen']) }}"
                                                     style="width:40px;height:55px;object-fit:cover;border-radius:4px;">
                                            @else
                                                <div class="bg-secondary rounded d-flex align-items-center justify-content-center"
                                                     style="width:40px;height:55px;">
                                                    <i class="bi bi-film text-white small"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="fw-semibold small">{{ $p['titulo'] }}</div>
                                            @if($p['genero'])
                                                <small class="text-muted">{{ $p['genero'] }}</small>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-primary rounded-pill">{{ $p['entradas'] }}</span>
                                        </td>
                                        <td class="text-end fw-bold small">{{ number_format($p['total'], 2) }} €</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-1">
                                                <div class="progress flex-grow-1" style="height:8px;">
                                                    <div class="progress-bar bg-success" style="width:{{ $pct }}%"></div>
                                                </div>
                                                <small class="text-muted" style="width:34px;">{{ number_format($pct,1) }}%</small>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light fw-bold">
                                <tr>
                                    <td colspan="2" class="text-end small">Total</td>
                                    <td class="text-center">{{ $entradasTotal }}</td>
                                    <td class="text-end">{{ number_format($totalGlobal, 2) }} €</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>
<script>
    function togglePelicula() {
        const tipo = document.getElementById('tipoGasto').value;
        document.getElementById('selectPelicula').style.display = tipo === 'variable' ? '' : 'none';
    }
</script>
@endsection
