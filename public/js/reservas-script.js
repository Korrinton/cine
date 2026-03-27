    let seleccionados = [];

    function gestionarAsiento(el, fila, asiento) {
        const asientoId = `${fila}-${asiento}`;
        const index = seleccionados.findIndex(a => a.id === asientoId);

        if (index > -1) {
            seleccionados.splice(index, 1);
            el.classList.remove('seleccionada');
        } else {
            //Límite de 6, se elimina la más antigua al seleccionar una séptima
            if (seleccionados.length >= 6) {
                const antiguo = seleccionados.shift();
                document.getElementById(`silla-${antiguo.id}`).classList.remove('seleccionada');
            }
            seleccionados.push({ id: asientoId, f: fila, s: asiento });
            el.classList.add('seleccionada');
        }
        actualizarUI();
    }

    function actualizarUI() {
        const btn = document.getElementById('btn-confirmar');
        const txtContador = document.getElementById('contador-asientos');
        const txtLista = document.getElementById('lista-asientos');
        const inputJson = document.getElementById('asientos_json');

        txtContador.innerText = seleccionados.length;
        btn.disabled = seleccionados.length === 0;
        
        if (seleccionados.length > 0) {
            txtLista.innerText = seleccionados.map(a => `F${a.f}-S${a.s}`).join(', ');
            inputJson.value = JSON.stringify(seleccionados);
        } else {
            txtLista.innerText = "Ninguno seleccionado";
            inputJson.value = "";
        }
    }