<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seleccionar asientos — {{ $evento->nombre }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background: #0f0f0f;
            color: #f0ead6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 2rem 1rem 4rem;
        }

        h1 {
            font-size: 1.4rem;
            font-weight: 600;
            letter-spacing: 0.02em;
            margin-bottom: 0.25rem;
        }

        .meta {
            font-size: 0.85rem;
            color: #888;
            margin-bottom: 2rem;
        }

        /* Pantalla */
        .screen-wrap { text-align: center; margin-bottom: 2.5rem; width: 100%; max-width: 600px; }
        .screen {
            height: 5px;
            background: linear-gradient(90deg, transparent, #e8c96b, transparent);
            border-radius: 0 0 50% 50%;
            width: 75%;
            margin: 0 auto 8px;
            box-shadow: 0 0 18px 2px rgba(232,201,107,0.25);
        }
        .screen-label { font-size: 0.7rem; color: #555; letter-spacing: 0.15em; text-transform: uppercase; }

        /* Grid */
        .grid { display: flex; flex-direction: column; gap: 7px; align-items: center; }
        .row { display: flex; align-items: center; gap: 5px; }
        .row-label { font-size: 0.7rem; color: #555; width: 18px; text-align: center; flex-shrink: 0; }

        .seat {
            width: 30px; height: 28px;
            border-radius: 5px 5px 3px 3px;
            border: 1px solid #333;
            background: #1e1e1e;
            cursor: pointer;
            transition: transform 0.12s, background 0.12s, border-color 0.12s;
            position: relative;
            flex-shrink: 0;
        }
        .seat::after {
            content: '';
            position: absolute;
            top: 3px; left: 4px; right: 4px;
            height: 7px;
            border-radius: 2px;
            background: rgba(255,255,255,0.06);
        }
        .seat:hover:not(.occupied) { transform: scale(1.15); background: #2a2a2a; border-color: #555; }
        .seat.selected { background: #e8c96b; border-color: #c9a840; }
        .seat.selected::after { background: rgba(255,255,255,0.2); }
        .seat.occupied { background: #1a1a1a; border-color: #2a2a2a; cursor: not-allowed; opacity: 0.4; }

        .aisle { width: 20px; flex-shrink: 0; }

        /* Leyenda */
        .legend { display: flex; gap: 20px; margin-top: 1.75rem; flex-wrap: wrap; justify-content: center; }
        .legend-item { display: flex; align-items: center; gap: 7px; font-size: 0.78rem; color: #888; }
        .legend-box { width: 18px; height: 16px; border-radius: 3px 3px 2px 2px; }
        .lb-free  { background: #1e1e1e; border: 1px solid #333; }
        .lb-sel   { background: #e8c96b; border: 1px solid #c9a840; }
        .lb-occ   { background: #1a1a1a; border: 1px solid #2a2a2a; opacity: 0.4; }

        /* Summary bar */
        .summary {
            position: fixed;
            bottom: 0; left: 0; right: 0;
            background: #181818;
            border-top: 1px solid #2a2a2a;
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
        }
        .summary-info { font-size: 0.88rem; color: #aaa; }
        .summary-info strong { color: #f0ead6; font-weight: 600; font-size: 1rem; }
        .summary-price { font-size: 0.78rem; color: #e8c96b; margin-top: 3px; }

        .btn-reservar {
            padding: 10px 28px;
            background: #e8c96b;
            border: none;
            border-radius: 8px;
            color: #1a1000;
            font-weight: 700;
            font-size: 0.9rem;
            cursor: pointer;
            transition: opacity 0.15s, transform 0.1s;
        }
        .btn-reservar:disabled { opacity: 0.3; cursor: not-allowed; transform: none; }
        .btn-reservar:not(:disabled):hover { opacity: 0.88; }
        .btn-reservar:not(:disabled):active { transform: scale(0.97); }

        /* Alert */
        .alert-error {
            background: #2a1010;
            border: 1px solid #5a2020;
            color: #f08080;
            padding: 10px 16px;
            border-radius: 8px;
            font-size: 0.85rem;
            margin-bottom: 1.5rem;
            max-width: 580px;
            width: 100%;
        }

        /* Input hidden asientos */
        #asientos-input { display: none; }
    </style>
</head>
<body>

    <h1>{{ $evento->nombre }}</h1>
    <p class="meta">
        {{ $evento->sala->nombre }} &nbsp;·&nbsp;
        {{ \Carbon\Carbon::parse($evento->fecha_estreno)->translatedFormat('l d M') }}
        &nbsp;·&nbsp; {{ number_format($evento->precio, 0, ',', '.') }} € / butaca
    </p>

    @if(session('error'))
        <div class="alert-error">{{ session('error') }}</div>
    @endif

    <div class="screen-wrap">
        <div class="screen"></div>
        <div class="screen-label">pantalla</div>
    </div>

    <div class="grid" id="seat-grid"></div>

    <div class="legend">
        <div class="legend-item"><div class="legend-box lb-free"></div> Libre</div>
        <div class="legend-item"><div class="legend-box lb-sel"></div> Seleccionada</div>
        <div class="legend-item"><div class="legend-box lb-occ"></div> Ocupada</div>
    </div>

    <form method="POST" action="{{ route('reservas.reservar', $evento->id_eventos) }}" id="form-reserva">
        @csrf
        <div id="asientos-input"></div>
    </form>

    <div class="summary">
        <div class="summary-info">
            <div id="lbl-seleccion" style="color:#555">Ninguna butaca seleccionada</div>
            <div class="summary-price" id="lbl-precio"></div>
        </div>
        <button class="btn-reservar" id="btn-reservar" disabled onclick="enviarFormulario()">
            Confirmar reserva →
        </button>
    </div>

<script>
    const FILAS      = {{ $evento->sala->filas }};
    const COLUMNAS   = {{ $evento->sala->sillas }};
    const PRECIO     = {{ $evento->precio }};
    const OCUPADAS   = new Set(@json($sillasOcupadas));
    const rowLabels  = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.slice(0, FILAS).split('');
    const selected   = new Set();

    function buildGrid() {
        const grid = document.getElementById('seat-grid');
        for (let r = 0; r < FILAS; r++) {
            const row = document.createElement('div');
            row.className = 'row';

            const lbl = document.createElement('div');
            lbl.className = 'row-label';
            lbl.textContent = rowLabels[r];
            row.appendChild(lbl);

            for (let c = 1; c <= COLUMNAS; c++) {
                // Pasillo central
                if (c === Math.floor(COLUMNAS / 3) + 1 || c === Math.floor(2 * COLUMNAS / 3) + 1) {
                    const aisle = document.createElement('div');
                    aisle.className = 'aisle';
                    row.appendChild(aisle);
                }
                const id = rowLabels[r] + c;
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'seat' + (OCUPADAS.has(id) ? ' occupied' : '');
                btn.dataset.id = id;
                btn.title = 'Butaca ' + id;
                if (!OCUPADAS.has(id)) btn.addEventListener('click', () => toggleSeat(id, btn));
                row.appendChild(btn);
            }
            grid.appendChild(row);
        }
    }

    function toggleSeat(id, btn) {
        if (selected.has(id)) {
            selected.delete(id);
            btn.classList.remove('selected');
        } else {
            if (selected.size >= 8) {
                alert('Máximo 8 butacas por reserva.');
                return;
            }
            selected.add(id);
            btn.classList.add('selected');
        }
        updateSummary();
    }

    function updateSummary() {
        const n = selected.size;
        const lbl = document.getElementById('lbl-seleccion');
        const precio = document.getElementById('lbl-precio');
        const btn = document.getElementById('btn-reservar');

        if (n === 0) {
            lbl.textContent = 'Ninguna butaca seleccionada';
            lbl.style.color = '#555';
            precio.textContent = '';
            btn.disabled = true;
        } else {
            const ids = Array.from(selected).sort().join(', ');
            lbl.innerHTML = '<strong>' + n + ' butaca' + (n > 1 ? 's' : '') + '</strong>: ' + ids;
            lbl.style.color = '#f0ead6';
            precio.textContent = 'Total: ' + (n * PRECIO) + ' €';
            btn.disabled = false;
        }
    }

    function enviarFormulario() {
        if (selected.size === 0) return;
        const container = document.getElementById('asientos-input');
        container.innerHTML = '';
        selected.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'asientos[]';
            input.value = id;
            container.appendChild(input);
        });
        document.getElementById('form-reserva').submit();
    }

    buildGrid();
</script>
</body>
</html>