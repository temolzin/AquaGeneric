@if($debts->isEmpty())
    <div class="alert alert-info mb-0">
        Esta toma no tiene deudas pendientes o abonadas.
    </div>
@else
    <div class="mb-3">
        <div class="row">
            <div class="col-md-4">
                <div class="small text-muted">Total deuda</div>
                <div><strong>${{ number_format($totalAmount, 2) }}</strong></div>
            </div>
            <div class="col-md-4">
                <div class="small text-muted">Total pagado</div>
                <div><strong>${{ number_format($totalPaid, 2) }}</strong></div>
            </div>
            <div class="col-md-4">
                <div class="small text-muted">Total pendiente</div>
                <div><strong>${{ number_format($totalPending, 2) }}</strong></div>
            </div>
        </div>
        <hr class="my-3">
    </div>

    <div class="table-responsive">
        <table class="table table-sm table-bordered mb-0">
            <thead class="thead-light">
                <tr>
                    <th>Periodo</th>
                    <th>Monto</th>
                    <th>Pagado</th>
                    <th>Saldo</th>
                    <th>Estado</th>
                    <th>Nota</th>
                    <th>Registró</th>
                </tr>
            </thead>
            <tbody>
                @foreach($debts as $d)
                    @php
                        $paid = (float)($d->payments_sum_amount ?? 0);
                        $pending = (float)$d->amount - $paid;

                        if ($pending <= 0) {
                            $statusText = 'Pagada';
                            $badge = 'badge-success';
                        } elseif ($paid > 0) {
                            $statusText = 'Abonada';
                            $badge = 'badge-warning';
                        } else {
                            $statusText = 'Pendiente';
                            $badge = 'badge-danger';
                        }
                    @endphp

                    <tr>
                        <td>
                            {{ \Carbon\Carbon::parse($d->start_date)->format('Y-m-d') }}
                            <span class="text-muted">a</span>
                            {{ \Carbon\Carbon::parse($d->end_date)->format('Y-m-d') }}
                        </td>
                        <td>${{ number_format($d->amount, 2) }}</td>
                        <td>${{ number_format($paid, 2) }}</td>
                        <td>${{ number_format(max($pending, 0), 2) }}</td>
                        <td><span class="badge {{ $badge }}">{{ $statusText }}</span></td>
                        <td>{{ $d->note ?? '-' }}</td>
                        <td>{{ optional($d->creator)->name }} {{ optional($d->creator)->last_name }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
