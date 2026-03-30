@if($transfers->isEmpty())
    <div class="alert alert-info mb-0">
        No hay transferencias registradas para esta toma.
    </div>
@else
    <div class="table-responsive">
        <table class="table table-sm table-bordered mb-0">
            <thead class="thead-light">
                <tr>
                    <th>Fecha</th>
                    <th>Titular anterior</th>
                    <th>Nuevo titular</th>
                    <th>Realizó</th>
                    <th>Nota</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transfers as $t)
                    <tr>
                        <td>
                            {{ $t->effective_date ?? optional($t->created_at)->format('Y-m-d') }}
                        </td>
                        <td>
                            {{ optional($t->oldCustomer)->name }} {{ optional($t->oldCustomer)->last_name }}
                            <small class="text-muted">(ID: {{ $t->old_customer_id }})</small>
                        </td>
                        <td>
                            {{ optional($t->newCustomer)->name }} {{ optional($t->newCustomer)->last_name }}
                            <small class="text-muted">(ID: {{ $t->new_customer_id }})</small>
                        </td>
                        <td>
                            {{ optional($t->creator)->name }} {{ optional($t->creator)->last_name }}
                        </td>
                        <td>
                            {{ $t->note ?? '-' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
