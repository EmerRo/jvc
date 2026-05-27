// public/js/components/almacen-select.js
// Componente Vue 2 reutilizable para seleccionar almacén.
// Uso:  <almacen-select class="form-control" v-model="producto.almacen" @input="miHandler"></almacen-select>
// - Carga los almacenes desde /ajs/almacenes/listar automáticamente.
// - Marca con ★ el almacén principal.
// - Si el valor actual no está en la lista, preselecciona el principal.

Vue.component('almacen-select', {
    inheritAttrs: true,
    props: {
        value: { type: String, default: '' }
    },
    data() {
        return { almacenes: [] };
    },
    template: `
        <select :value="value" @change="$emit('input', $event.target.value)">
            <option v-for="alm in almacenes" :key="alm.id_almacen" :value="String(alm.id_almacen)">
                {{ alm.nombre }}{{ alm.principal == 1 ? ' ★' : '' }}
            </option>
        </select>
    `,
    mounted() {
        _get('/ajs/almacenes/listar', (res) => {
            if (!res.estado) return;
            this.almacenes = res.almacenes;
            var ids = res.almacenes.map(function(a) { return String(a.id_almacen); });
            if (!this.value || !ids.includes(this.value)) {
                var principal = res.almacenes.find(function(a) { return a.principal == 1; }) || res.almacenes[0];
                if (principal) this.$emit('input', String(principal.id_almacen));
            }
        });
    }
});
