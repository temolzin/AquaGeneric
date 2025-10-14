@extends('adminlte::page')

@section('title', config('adminlte.title') . ' | Mis Deudas')

@section('content')
<section class="content">
    <div class="right_col" role="main">
        <div class="col-md-12 col-sm-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Mis Deudas</h2>
                    <div class="row mb-2">
                        <div class="col-lg-12">
                            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-center gap-3">
                                <form method="GET" action="{{ route('viewCustomerDebts.index') }}" class="flex-grow-1 mt-2" style="min-width: 328px; max-width: 40%;">
                                    <div class="input-group">
                                        <input type="text" name="search" class="form-control" placeholder="Buscar..." value="{{ request('search') }}">
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-primary" title="Buscar Toma">
                                                <i class="fas fa-search d-lg-none"></i>
                                                <span class="d-none d-lg-inline">Buscar</span>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card-box table-responsive">
                                <table id="waterConnectionsTable" class="table table-striped display responsive nowrap" style="width:100%; max-width: 100%; margin: 0 auto; margin-top: 30px;">
                                    <thead>
                                        <tr>
                                            <th>Tomas de Agua</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($waterConnections->count() == 0)
                                            <tr>
                                                <td colspan="1" class="text-center">
                                                    <div class="text-center py-4">
                                                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                                        <h5>¡No tienes deudas pendientes!</h5>
                                                        <p class="text-muted">Todas tus tomas de agua están al corriente.</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        @else
                                            @foreach($waterConnections as $connection)
                                                <tr class="clickable-row" data-toggle="collapse" data-target="#collapse-{{ $connection->id }}">
                                                    <td>
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <strong class="text-dark">
                                                                    {{ $connection->name ?: 'Toma #' . $connection->id }}
                                                                </strong>                                                                
                                                                <div class="connection-details mt-1">
                                                                    @if($connection->street || $connection->exterior_number)
                                                                        <div>
                                                                            <small class="text-muted">
                                                                                <i class="fas fa-map-marker-alt"></i>
                                                                                @if($connection->street)
                                                                                    Calle {{ $connection->street }}
                                                                                @endif
                                                                                @if($connection->exterior_number)
                                                                                    #{{ $connection->exterior_number }}
                                                                                @endif
                                                                                @if($connection->interior_number)
                                                                                    Int. {{ $connection->interior_number }}
                                                                                @endif
                                                                            </small>
                                                                        </div>
                                                                    @endif
                                                                    
                                                                    @if($connection->type)
                                                                        <small class="badge badge-secondary mt-1">
                                                                            {{ $connection->type === 'residencial' ? 'Residencial' : 'Comercial' }}
                                                                        </small>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <div class="badges">
                                                                <i class="fas fa-chevron-down rotate-icon ml-2"></i>
                                                            </div>
                                                        </div>                                                        
                                                        <div id="collapse-{{ $connection->id }}" class="collapse mt-3">
                                                            <div class="table-responsive">
                                                                <table class="table table-bordered table-sm">
                                                                    <thead class="bg-light">
                                                                        <tr>
                                                                            <th>ID</th>
                                                                            <th>Fecha de Inicio</th>
                                                                            <th>Fecha de Fin</th>
                                                                            <th>Monto</th>
                                                                            <th>Pendiente</th>
                                                                            <th>Estatus</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @php
                                                                            $unpaidDebts = $connection->debts->where('status', '!=', 'paid');
                                                                        @endphp
                                                                        @if($unpaidDebts->count() > 0)
                                                                            @foreach($unpaidDebts as $debt)
                                                                            <tr>
                                                                                <td>{{ $debt->id }}</td>
                                                                                <td>{{ \Carbon\Carbon::parse($debt->start_date)->format('d/m/Y') }}</td>
                                                                                <td>{{ \Carbon\Carbon::parse($debt->end_date)->format('d/m/Y') }}</td>
                                                                                <td>${{ number_format($debt->amount, 2) }}</td>
                                                                                <td>
                                                                                    <span class="text font-weight">
                                                                                        ${{ number_format($debt->amount - $debt->debt_current, 2) }}
                                                                                    </span>
                                                                                </td>
                                                                                <td>
                                                                                    @php
                                                                                        $badgeClass = [
                                                                                            'pending' => 'danger',
                                                                                            'partial' => 'warning', 
                                                                                            'paid' => 'success'
                                                                                        ][$debt->status] ?? 'secondary';
                                                                                        
                                                                                        $statusLabels = [
                                                                                            'pending' => 'No pagada',
                                                                                            'partial' => 'Abonada',
                                                                                            'paid' => 'Pagada'
                                                                                        ];
                                                                                    @endphp
                                                                                    <span class="badge badge-{{ $badgeClass }}">
                                                                                        {{ $statusLabels[$debt->status] ?? $debt->status }}
                                                                                    </span>
                                                                                </td>
                                                                            </tr>
                                                                            @endforeach
                                                                        @else
                                                                            <tr>
                                                                                <td colspan="6" class="text-center text-muted">
                                                                                    No hay deudas pendientes para esta toma
                                                                                </td>
                                                                            </tr>
                                                                        @endif
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                                @if($waterConnections->total() > $waterConnections->perPage())
                                <div class="d-flex justify-content-center mt-3">
                                    {!! $waterConnections->links() !!}
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<style>
    .rotate-icon {
        transition: transform 0.3s ease;
        transform-origin: center;
    }
    .collapse.show .rotate-icon {
        transform: rotate(180deg);
    }
    .clickable-row {
        cursor: pointer;
    }
    .clickable-row:hover {
        background-color: #f5f5f5;
    }
    .connection-details {
        font-size: 0.85rem;
    }
    .connection-details .badge {
        font-size: 0.7rem;
        margin-right: 5px;
    }
    .table-bordered {
        border: 1px solid #dee2e6;
    }
    .table-bordered th,
    .table-bordered td {
        border: 1px solid #dee2e6;
    }
    .bg-light {
        background-color: #f8f9fa !important;
    }
    .table-sm th,
    .table-sm td {
        padding: 0.5rem;
        font-size: 0.875rem;
    }
    .table-info {
        background-color: #d1ecf1 !important;
    }
</style>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        $('#waterConnectionsTable').DataTable({
            responsive: true,
            paging: false,
            info: false,
            searching: false,
            order: [[0, 'asc']]
        });

        $('#waterConnectionsTable').on('click', '.clickable-row', function(e) {
            if (!$(e.target).is('a') && !$(e.target).closest('a').length) {
                var target = $(this).data('target');
                var $target = $(target);
                var icon = $(this).find('.rotate-icon');
                if ($target.length) {
                    $target.collapse('toggle');
                    if ($target.hasClass('show')) {
                        icon.css('transform', 'rotate(180deg)');
                    } else {
                        icon.css('transform', 'rotate(0deg)');
                    }
                }
            }
        });
    });
</script>
@endsection
