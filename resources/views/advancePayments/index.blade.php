@extends('adminlte::page')

@section('title', config('adminlte.title') . ' | Pagos adelantados')

@section('content')

<h2>Pagos adelantados</h2>

<div class="row">
    <div class="col-lg-12 text-right">
        <div class="btn-group" role="group" aria-label="Acciones de gráfica de pagos">
            <button class="btn btn-primary mr-2" id="advencePaymentGraph" data-toggle='modal' data-target="#paymentChart">
                <i class="fa fa-money-bill"></i> Gráfica de pagos
            </button>

            <a type="button" class="btn btn-success mr-2" target="_blank" title="Historial de pagos" href="#">
                <i class="fas fa-clipboard"></i> Historial de pagos
            </a>

            <button class="btn btn-secondary mr-2" data-toggle='modal' data-target="#paymentChart">
                <i class="fa fa-dollar-sign"></i> Comprobante de pagos
            </button>
        </div>
    </div>
</div>

@endsection

@section('js')
<script>
    document.getElementById('advencePaymentGraph').addEventListener('click', async () => {
        const chartIds = ['barChart', 'lineChart', 'pieChart'];
        const chartImages = chartIds.map(id => {
            const canvas = document.getElementById(id);
            return canvas.toDataURL('image/png');
        });

        const response = await fetch('{{ route('reports.advancePaymentGraphReport') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ charts: chartImages })
        });

        if (response.ok) {
            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            window.open(url, '_blank');
        } 
    });
</script>
@endsection
