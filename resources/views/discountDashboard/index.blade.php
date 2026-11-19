@extends('layouts.adminlte')

@section('title', config('adminlte.title') . ' | Panel de Descuentos')

@section('content')
<section class="content">
    <div class="right_col" role="main">
        <div class="col-md-12 col-sm-12">
            <div class="x_panel">
                <div class="x_title mb-3">
                    <h2>Panel de Descuentos</h2>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="d-flex justify-content-end align-items-center flex-wrap">
                                <div class="responsive-actions">
                                    <button class="btn btn-primary" id="btnGenerateReport" title="Generar reporte">
                                        <i class="fa fa-chart-pie mr-1"></i>
                                        <span class="d-none d-md-inline">Generar reporte</span>
                                        <span class="d-inline d-md-none">Reporte</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="x_content">
                    <div class="row">
                        <div class="col-lg-12 mb-4">
                            <div class="card card-primary card-outline">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h3 class="card-title">
                                        Uso general de descuentos
                                    </h3>
                                    <button class="btn btn-sm btn-outline-dark download-btn" data-canvas="discountChart">
                                        <i class="fas fa-download"></i> Descargar
                                    </button>
                                </div>
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-md-6 d-flex justify-content-center">
                                            <div style="width: 350px; height: 350px;">
                                                <canvas id="discountChart"></canvas>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div id="discountLegend"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 mb-4">
                            <div class="card card-success card-outline">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h3 class="card-title">
                                        Descuentos aplicados en pagos
                                    </h3>
                                    <div class="d-flex align-items-center">
                                        <select class="form-control form-control-sm mr-2" id="paymentMonth">
                                            <option value="">Mes</option>
                                            @foreach(['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'] as $number => $name)
                                                <option value="{{ $number + 1 }}" {{ $selectedMonth === $number + 1 ? 'selected' : '' }}>{{ $name }}</option>
                                            @endforeach
                                        </select>
                                        <select class="form-control form-control-sm mr-2" id="paymentYear">
                                            @for($i = date('Y'); $i >= date('Y')-5; $i--)
                                                <option value="{{ $i }}" {{ $selectedYear === $i ? 'selected' : '' }}>{{ $i }}</option>
                                            @endfor
                                        </select>
                                        <button class="btn btn-sm btn-outline-dark download-btn" data-canvas="paymentChart">
                                            <i class="fas fa-download"></i> Descargar
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <canvas id="paymentChart" height="350"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-4">
                            <div class="card card-danger card-outline">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h3 class="card-title">
                                        Descuentos aplicados en deudas
                                    </h3>
                                    <div class="d-flex align-items-center">
                                        <select class="form-control form-control-sm mr-2" id="debtMonth">
                                            <option value="">Mes</option>
                                            @foreach(['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'] as $number => $name)
                                                <option value="{{ $number + 1 }}" {{ $selectedMonth === $number + 1 ? 'selected' : '' }}>{{ $name }}</option>
                                            @endforeach
                                        </select>
                                        <select class="form-control form-control-sm mr-2" id="debtYear">
                                            @for($i = date('Y'); $i >= date('Y')-5; $i--)
                                                <option value="{{ $i }}" {{ $selectedYear === $i ? 'selected' : '' }}>{{ $i }}</option>
                                            @endfor
                                        </select>
                                        <button class="btn btn-sm btn-outline-dark download-btn" data-canvas="debtChart">
                                            <i class="fas fa-download"></i> Descargar
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <canvas id="debtChart" height="350"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<style>
    #btnGenerateReport:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }
    @media (max-width: 767.98px) {
        .responsive-actions{
            display: flex !important;
            flex-direction: column;
            width: 100%;
            gap: .5rem;
            margin-top: .5rem;
        }
        .responsive-actions .btn{
            width: 100%;
            margin-right: 0 !important;
            margin-left: 0 !important;
        }
    }
    @media (min-width: 768px) {
        .responsive-actions{
            display: flex !important;
            flex-direction: row;
            justify-content: flex-end;
            align-items: center;
            flex-wrap: nowrap;
            gap: .5rem;
        }
        .responsive-actions .btn{
            width: auto;
        }   
    }
</style>
@endsection
@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const baseColors = [
        @for ($i = 0; $i < count($discountLabels); $i++)
            "{{ pdf_color(color($i)) }}",
        @endfor
    ];

    function generateColors(total) {
        let colors = [];

        for (let i = 0; i < total; i++) {
            colors.push(baseColors[i % baseColors.length]);
        }

        return colors;
    }

    const paymentChart = new Chart(document.getElementById('paymentChart'), {
        type: 'bar',

        data: {
            labels: @json($paymentLabels),
            datasets: [{
                label: 'Pagos',
                data: @json($paymentData),
                backgroundColor: generateColors(@json($paymentLabels).length)
            }]
        },

        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    const debtChart = new Chart(document.getElementById('debtChart'), {
        type:'bar',

        data:{
            labels:@json($debtLabels),
            datasets:[{
                label:'Deudas',
                data:@json($debtData),
                backgroundColor: generateColors(@json($debtLabels).length)
            }]
        },

        options:{
            responsive:true,
            maintainAspectRatio:false,
            scales:{
                y:{
                    beginAtZero:true
                }
            }
        }
    });

    const discountChart = new Chart(document.getElementById('discountChart'), {
        type: 'pie',

        data: {
            labels: @json($discountLabels),
            datasets: [{
                data: @json($discountData),
                backgroundColor: generateColors(@json($discountLabels).length),
                borderColor: '#fff',
                borderWidth: 2
            }]
        },

        options: {
            responsive: true,
            maintainAspectRatio: false,
            radius: '85%',
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    const legendColors = generateColors(discountChart.data.labels.length);

    let legend = '<ul class="list-unstyled mb-0">';

    discountChart.data.labels.forEach((label, index) => {
        legend += `
            <li class="mb-3 d-flex align-items-center">
                <span style="
                    width:18px;
                    height:18px;
                    background:${legendColors[index]};
                    display:inline-block;
                    border-radius:4px;
                    margin-right:12px;
                "></span>

                <span style="font-size:16px;">${label}</span>
            </li>
        `;
    });

    legend += '</ul>';
    document.getElementById('discountLegend').innerHTML = legend;

    $('#paymentMonth, #paymentYear').change(function () {
        console.log("{{ route('discountDashboard.getPaymentChart') }}");
        $.ajax({
            url: "{{ route('discountDashboard.getPaymentChart') }}",
            type: "GET",
            data: {
                payment_month: $('#paymentMonth').val(),
                payment_year: $('#paymentYear').val()
            },

            success: function(response) {

                paymentChart.data.labels = response.labels;
                paymentChart.data.datasets[0].data = response.data;
                paymentChart.data.datasets[0].backgroundColor = generateColors(response.labels.length);

                paymentChart.update();
            },

            error: function(xhr) {
                console.log(xhr.responseText);
            }
        });
    });

    $('#debtMonth, #debtYear').change(function () { 
        $.ajax({
            url: "{{ route('discountDashboard.getDebtChart') }}",
            type: "GET",
            data: {
                debt_month: $('#debtMonth').val(),
                debt_year: $('#debtYear').val()
            },

            success: function(response) {

                debtChart.data.labels = response.labels;
                debtChart.data.datasets[0].data = response.data;
                debtChart.data.datasets[0].backgroundColor = generateColors(response.labels.length);

                debtChart.update();                
            },

            error: function(xhr) {
                console.log(xhr.responseText);
            }
        });
    });

    document.querySelectorAll('.download-btn').forEach(button => {
        button.addEventListener('click', function () {
            const canvasId = this.dataset.canvas;
            const canvas = document.getElementById(canvasId);
            if (canvas) {
                const link = document.createElement('a');
                link.href = canvas.toDataURL('image/png');
                link.download = `${canvasId}.png`;
                link.click();
            }
        });
    });
    
    document.getElementById('btnGenerateReport')?.addEventListener('click', () => {
        const chartImages = {
            discountChart: document.getElementById('discountChart')?.toDataURL('image/png') || '',
            paymentChart: document.getElementById('paymentChart')?.toDataURL('image/png') || '',
            debtChart: document.getElementById('debtChart')?.toDataURL('image/png') || ''
        };
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route('discountDashboard.generateReport') }}';
        form.target = '_blank';
        const tokenInput = document.createElement('input');
        tokenInput.type = 'hidden';
        tokenInput.name = '_token';
        tokenInput.value = '{{ csrf_token() }}';
        form.appendChild(tokenInput);
        const chartsInput = document.createElement('input');
        chartsInput.type = 'hidden';
        chartsInput.name = 'charts';
        chartsInput.value = JSON.stringify(chartImages);
        form.appendChild(chartsInput);
        const paymentMonthInput = document.createElement('input');
        paymentMonthInput.type = 'hidden';
        paymentMonthInput.name = 'payment_month';
        paymentMonthInput.value = $('#paymentMonth').val() || '';
        form.appendChild(paymentMonthInput);
        const paymentYearInput = document.createElement('input');
        paymentYearInput.type = 'hidden';
        paymentYearInput.name = 'payment_year';
        paymentYearInput.value = $('#paymentYear').val() || '';
        form.appendChild(paymentYearInput);
        const debtMonthInput = document.createElement('input');
        debtMonthInput.type = 'hidden';
        debtMonthInput.name = 'debt_month';
        debtMonthInput.value = $('#debtMonth').val() || '';
        form.appendChild(debtMonthInput);
        const debtYearInput = document.createElement('input');
        debtYearInput.type = 'hidden';
        debtYearInput.name = 'debt_year';
        debtYearInput.value = $('#debtYear').val() || '';
        form.appendChild(debtYearInput);
        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);
    });
</script>
@endsection
