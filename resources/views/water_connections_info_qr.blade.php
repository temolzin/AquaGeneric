<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toma de Agua {{ $connection->id }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #0c2461 0%, #1e3799 25%, #4a69bd 50%, #6a89cc 75%, #82ccdd 100%);
            min-height: 100vh;
            padding: 15px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            position: relative;
            overflow-x: hidden;
        }
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 80%, rgba(120, 219, 226, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(250, 250, 210, 0.2) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
            z-index: -1;
        }
        .main-card {
            border-radius: 25px;
            box-shadow: 0 25px 80px rgba(0,0,0,0.4);
            margin-top: 20px;
            animation: slideUp 0.6s ease-out;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            position: relative;
        }
        .main-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #ff6b6b, #4ecdc4, #45b7d1, #96ceb4, #ffeaa7);
        }
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 35px 25px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 1%, transparent 1%);
            background-size: 20px 20px;
            animation: float 20s infinite linear;
        }
        @keyframes float {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .header h1 {
            font-size: 1.6rem;
            margin: 15px 0 8px 0;
            font-weight: 700;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
            position: relative;
        }
        .header .icon {
            font-size: 3.5rem;
            margin-bottom: 15px;
            filter: drop-shadow(0 4px 8px rgba(0,0,0,0.3));
            position: relative;
        }
        .id-badge {
            background: rgba(255,255,255,0.25);
            backdrop-filter: blur(10px);
            padding: 12px 25px;
            border-radius: 50px;
            display: inline-block;
            margin-top: 15px;
            font-size: 1.1rem;
            font-weight: 600;
            border: 1px solid rgba(255,255,255,0.3);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            position: relative;
        }
        .info-list {
            padding: 0;
            margin: 0;
        }
        .info-item {
            padding: 20px 25px;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            display: flex;
            align-items: flex-start;
            transition: all 0.3s ease;
            background: transparent;
        }
        .info-item:hover {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
            transform: translateX(8px);
            border-left: 4px solid #667eea;
        }
        .info-item:last-child {
            border-bottom: none;
        }
        .info-icon {
            color: #667eea;
            font-size: 1.4rem;
            margin-right: 18px;
            min-width: 30px;
            margin-top: 2px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            filter: drop-shadow(0 2px 4px rgba(102, 126, 234, 0.3));
        }
        .info-content {
            flex: 1;
        }
        .info-label {
            font-size: 0.8rem;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        .info-value {
            color: #2d3748;
            font-size: 1.1rem;
            font-weight: 600;
            word-wrap: break-word;
            line-height: 1.4;
        }
        .badge-custom {
            padding: 10px 18px;
            border-radius: 50px;
            font-size: 0.9rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transition: all 0.3s ease;
        }
        .badge-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.2);
        }
        .badge-activo {
            background: linear-gradient(135deg, #e8f5e9, #c8e6c9);
            color: #2e7d32;
            border: 1px solid #a5d6a7;
        }
        .badge-inactivo {
            background: linear-gradient(135deg, #ffebee, #ffcdd2);
            color: #c62828;
            border: 1px solid #ef9a9a;
        }
        .footer {
            text-align: center;
            padding: 25px;
            color: white;
            margin-top: 20px;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.8) 0%, rgba(118, 75, 162, 0.8) 100%);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            margin: 20px;
            border: 1px solid rgba(255,255,255,0.2);
        }
        .footer-text {
            font-size: 0.95rem;
            opacity: 0.9;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .timestamp {
            font-size: 0.85rem;
            opacity: 0.8;
            margin-top: 8px;
            font-weight: 500;
        }
        .water-wave {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 20px;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none"><path d="M0,0V46.29c47.79,22.2,103.59,32.17,158,28,70.36-5.37,136.33-33.31,206.8-37.5C438.64,32.43,512.34,53.67,583,72.05c69.27,18,138.3,24.88,209.4,13.08,36.15-6,69.85-17.84,104.45-29.34C989.49,25,1113-14.29,1200,52.47V0Z" opacity=".25" fill="%23ffffff"/><path d="M0,0V15.81C13,36.92,27.64,56.86,47.69,72.05,99.41,111.27,165,111,224.58,91.58c31.15-10.15,60.09-26.07,89.67-39.8,40.92-19,84.73-46,130.83-49.67,36.26-2.85,70.9,9.42,98.6,31.56,31.77,25.39,62.32,62,103.63,73,40.44,10.79,81.35-6.69,119.13-24.28s75.16-39,116.92-43.05c59.73-5.85,113.28,22.88,168.9,38.84,30.2,8.66,59,6.17,87.09-7.5,22.43-10.89,48-26.93,60.65-49.24V0Z" opacity=".5" fill="%23ffffff"/><path d="M0,0V5.63C149.93,59,314.09,71.32,475.83,42.57c43-7.64,84.23-20.12,127.61-26.46,59-8.63,112.48,12.24,165.56,35.4C827.93,77.22,886,95.24,951.2,90c86.53-7,172.46-45.71,248.8-84.81V0Z" fill="%23ffffff"/></svg>');
            background-size: cover;
        }
        @media (max-width: 576px) {
            .header h1 {
                font-size: 1.4rem;
            }
            .header {
                padding: 25px 20px;
            }
            .info-item {
                padding: 18px 20px;
            }
            .info-value {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="container" style="max-width: 600px;">
        <div class="main-card">
            <div class="header">
                <div class="icon">💧</div>
                <h1>Información de Toma de Agua</h1>
                <div class="id-badge">
                    <i class="fas fa-hashtag"></i> ID: {{ $connection->id }}
                </div>
                <div class="water-wave"></div>
            </div>

            <div class="info-list">
                <div class="info-item">
                    <i class="fas fa-droplet info-icon"></i>
                    <div class="info-content">
                        <div class="info-label">Nombre de la Toma</div>
                        <div class="info-value">{{ $connection->name ?? 'Sin nombre' }}</div>
                    </div>
                </div>

                <div class="info-item">
                    <i class="fas fa-user info-icon"></i>
                    <div class="info-content">
                        <div class="info-label">Propietario</div>
                        <div class="info-value">
                            @if($connection->customer)
                                {{ $connection->customer->name }} {{ $connection->customer->last_name }}
                            @else
                                <span style="color: #8e9aaf;">No asignado</span>
                            @endif
                        </div>
                    </div>
                </div>

                @if($connection->street)
                <div class="info-item">
                    <i class="fas fa-map-marker-alt info-icon"></i>
                    <div class="info-content">
                        <div class="info-label">Dirección</div>
                        <div class="info-value">
                            {{ $connection->street }}
                            @if($connection->exterior_number)
                                #{{ $connection->exterior_number }}
                            @endif
                            @if($connection->interior_number)
                                Int. {{ $connection->interior_number }}
                            @endif
                            @if($connection->block)
                                <br><small style="color: #667eea;">Colonia: {{ $connection->block }}</small>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                @if($connection->locality && $connection->locality->zip_code)
                <div class="info-item">
                    <i class="fas fa-mail-bulk info-icon"></i>
                    <div class="info-content">
                        <div class="info-label">Código Postal</div>
                        <div class="info-value">{{ $connection->locality->zip_code }}</div>
                    </div>
                </div>
                @endif

                @if($connection->locality && $connection->locality->name)
                <div class="info-item">
                    <i class="fas fa-location-dot info-icon"></i>
                    <div class="info-content">
                        <div class="info-label">Localidad</div>
                        <div class="info-value">{{ $connection->locality->name }}</div>
                    </div>
                </div>
                @endif

                @if($connection->water_days)
                <div class="info-item">
                    <i class="fas fa-calendar-days info-icon"></i>
                    <div class="info-content">
                        <div class="info-label">Días de Suministro</div>
                        <div class="info-value">
                            @php
                                $daysMap = [
                                    'monday' => 'Lunes',
                                    'tuesday' => 'Martes',
                                    'wednesday' => 'Miércoles',
                                    'thursday' => 'Jueves',
                                    'friday' => 'Viernes',
                                    'saturday' => 'Sábado',
                                    'sunday' => 'Domingo'
                                ];
                                
                                $waterDays = json_decode($connection->water_days, true);
                                
                                if ($connection->water_days === 'all' || $waterDays === 'all') {
                                    echo '<span style="color: #2e7d32; font-weight: 700;">Todos los días</span>';
                                } elseif (is_array($waterDays)) {
                                    $translatedDays = array_map(function($day) use ($daysMap) {
                                        return $daysMap[$day] ?? ucfirst($day);
                                    }, $waterDays);
                                    echo implode(', ', $translatedDays);
                                } else {
                                    echo 'No definido';
                                }
                            @endphp
                        </div>
                    </div>
                </div>
                @endif

                <div class="info-item">
                    <i class="fas fa-circle-info info-icon"></i>
                    <div class="info-content">
                        <div class="info-label">Estado</div>
                        <div class="info-value">
                            @if($connection->is_canceled)
                                <span class="badge-custom badge-inactivo">
                                    <i class="fas fa-ban"></i> Cancelada
                                </span>
                            @else
                                <span class="badge-custom badge-activo">
                                    <i class="fas fa-check-circle"></i> Activa
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                @if($connection->note)
                <div class="info-item">
                    <i class="fas fa-note-sticky info-icon"></i>
                    <div class="info-content">
                        <div class="info-label">Notas</div>
                        <div class="info-value" style="font-style: italic; color: #5a6268;">{{ $connection->note }}</div>
                    </div>
                </div>
                @endif
            </div>

            <div class="footer">
                <div class="footer-text">
                    <i class="fas fa-shield-alt"></i> 
                    Sistema Oficial de Agua
                </div>
                <div class="timestamp">
                    Consultado: {{ now()->format('d/m/Y H:i') }}
                </div>
            </div>
        </div>
    </div>
</body>
</html>
