document.addEventListener('DOMContentLoaded', function () {
    const base = document.querySelector('meta[name="base-url"]').content;
    let tasks = [];
    const escape = $.fn.dataTable.render.text().display;
    const form = document.getElementById('taskForm');
    const modal = new bootstrap.Modal(document.getElementById('taskModal'));
    const table = $('#tableTasks').DataTable({
        ajax: {url:`${base}/tasks/listing`, dataSrc:function (rows) { tasks=rows; return rows; }},
        columns:[
            {data:'id_task'},
            {data:'title',render:$.fn.dataTable.render.text()},
            {data:'requester',render:$.fn.dataTable.render.text()},
            {data:'priority',render:$.fn.dataTable.render.text()},
            {data:'due_date',render:function(value,type,row) {
                if (type !== 'display') return value || '';
                const date = value ? value.split('-').reverse().join('/') : 'Sin fecha';
                return escape(date)+(row.overdue ? ' <span class="badge bg-danger">Vencida</span>' : '');
            }},
            {data:'status',render:function(value,type) {
                return type === 'display' ? `<span class="badge ${value === 'Completada' ? 'bg-success' : 'bg-secondary'}">${escape(value)}</span>` : value;
            }},
            {data:'options'}
        ],
        language: languageDatatable(), order:[[0,'desc']], columnDefs:[{targets:6,orderable:false,searchable:false,responsivePriority:1}], responsive:true, pagingType:'simple_numbers', dom:'Bfrtilp', buttons:[
        {extend:'excelHtml5',text:'<i class="icon-file-excel"></i>',titleAttr:'Exportar a Excel',className:'btn btn-success',filename:'Tareas',exportOptions:{columns:[0,1,2,3,4,5]}},
        {extend:'pdfHtml5',text:'<i class="icon-file-pdf"></i>',titleAttr:'Exportar a PDF',className:'btn btn-danger',filename:'Tareas',exportOptions:{columns:[0,1,2,3,4,5]}},
        {extend:'print',text:'<i class="icon-print"></i>',titleAttr:'Imprimir',className:'btn btn-info',exportOptions:{columns:[0,1,2,3,4,5]}}
    ]});
    const filter = function (settings, data, index) {
        if (settings.nTable.id !== 'tableTasks') return true;
        const row = settings.aoData[index]._aData;
        const state = document.getElementById('task-filter-status').value;
        const priority = document.getElementById('task-filter-priority').value;
        return (!priority || row.priority === priority) && (!state || (state === 'overdue' ? row.overdue : row.status === state));
    };
    $.fn.dataTable.ext.search.push(filter);
    document.querySelectorAll('#task-filter-status, #task-filter-priority').forEach(el => el.addEventListener('change', () => table.draw()));
    function open(task, readOnly) {
        form.reset();
        form.elements.id_task.value = task ? task.id_task : 0;
        ['title','requester','priority','status','due_date','description','result'].forEach(key => {
            if (task) form.elements[key].value = task[key] || '';
        });
        document.getElementById('taskFields').disabled = readOnly;
        document.getElementById('task-save').hidden = readOnly;
        document.getElementById('taskModalTitle').textContent = readOnly ? 'Detalle de tarea' : (task ? 'Editar tarea' : 'Nueva tarea');
        const created = document.getElementById('task-created');
        created.hidden = !task;
        created.textContent = task ? `Registrada: ${task.created_at}` : '';
        modal.show();
    }
    document.getElementById('new-task')?.addEventListener('click', () => open(null,false));
    async function post(action, body) {
        const response = await fetch(`${base}/tasks/${action}`, {method:'POST', body});
        const result = await response.json();
        showMessage(result.type, result.message);
        if (result.status) {
            modal.hide();
            form.reset();
            table.ajax.reload(null, false);
        }
    }
    document.getElementById('tableTasks').addEventListener('click', async event => {
        const button = event.target.closest('[data-id]');
        if (!button) return;
        const task = tasks.find(item => Number(item.id_task) === Number(button.dataset.id));
        if (!task) return;
        if (button.classList.contains('js-task-delete')) {
            const choice = await Swal.fire({title:'¿Eliminar tarea?', text:task.title, icon:'warning', showCancelButton:true, confirmButtonText:'Eliminar', cancelButtonText:'Cancelar'});
            if (!choice.isConfirmed) return;
            button.disabled = true;
            try { const body=new FormData(); body.set('id_task',task.id_task); await post('remove',body); }
            catch { showMessage('error','No se pudo eliminar la tarea.'); }
            finally { button.disabled=false; }
        } else open(task,button.classList.contains('js-task-open'));
    });
    form.addEventListener('submit', async event => {
        event.preventDefault();
        const button = document.getElementById('task-save');
        button.disabled=true;
        try { await post('save',new FormData(form)); }
        catch { showMessage('error','No se pudo guardar la tarea.'); }
        finally { button.disabled=false; }
    });
});
