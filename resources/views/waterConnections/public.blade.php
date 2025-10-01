<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toma de Agua #{{ $connection->id }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 15px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .main-card {
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            margin-top: 20px;
            animation: slideUp 0.5s ease-out;
            overflow: hidden;
            background: white;
        }
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            font-size: 1.5rem;
            margin: 10px 0 5px 0;
            font-weight: 700;
        }
        .header .icon {
            font-size: 3rem;
            margin-bottom: 10px;
        }
        .id-badge {
            background: rgba(255,255,255,0.2);
            padding: 10px 20px;
            border-radius: 50px;
            display: inline-block;
            margin-top: 10px;
            font-size: 1.1rem;
            font-weight: 600;
        }
        .info-list {
            padding: 0;
            margin: 0;
        }
        .info-item {
            padding: 18px 20px;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            align-items: flex-start;
            transition: background 0.2s;
        }
        .info-item:hover {
            background: #f8f9fa;
        }
        .info-item:last-child {
            border-bottom: none;
        }
        .info-icon {
            color: #667eea;
            font-size: 1.3rem;
            margin-right: 15px;
            min-width: 25px;
            margin-top: 2px;
        }
        .info-content {
            flex: 1;
        }
        .info-label {
            font-size: 0.85rem;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
            margin-bottom: 3px;
        }
        .info-value {
            color: #2d3748;
            font-size: 1.05rem;
            font-weight: 500;
            word-wrap: break-word;
        }
        .badge-custom {
            padding: 8px 16px;
            border-radius: 50px;
            font-size: 0.9rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .badge-residencial {
            background: #e3f2fd;
            color: #1565c0;
        }
        .badge-comercial {
            background: #e8f5e9;
            color: #2e7d32;
        }
        .badge-activo {
            background: #e8f5e9;
            color: #2e7d32;
        }
        .badge-inactivo {
            background: #ffebee;
            color: #c62828;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: white;
            margin-top: 20px;
        }
        .footer-text {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        .timestamp {
            font-size: 0.8rem;
            opacity: 0.8;
            margin-top: 5px;
        }
        @media (max-width: 576px) {
            .header h1 {
                font-size: 1.3rem;
            }
            .info-value {
                font-size: 0.95rem;
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
                                <br>Colonia: {{ $connection->block }}
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

                <!-- Localidad -->
                @if($connection->locality && $connection->locality->name)
                <div class="info-item">
                    <i class="fas fa-location-dot info-icon"></i>
                    <div class="info-content">
                        <div class="info-label">Localidad</div>
                        <div class="info-value">{{ $connection->locality->name }}</div>
                    </div>
                </div>
                @endif

                @if($connection->type)
                <div class="info-item">
                    <i class="fas fa-building info-icon"></i>
                    <div class="info-content">
                        <div class="info-label">Tipo de Toma</div>
                        <div class="info-value">
                            @if(strtolower($connection->type) == 'residencial')
                                <span class="badge-custom badge-residencial">
                                    <i class="fas fa-home"></i> Residencial
                                </span>
                            @elseif(strtolower($connection->type) == 'comercial')
                                <span class="badge-custom badge-comercial">
                                    <i class="fas fa-store"></i> Comercial
                                </span>
                            @else
                                <span class="badge-custom badge-residencial">
                                    {{ ucfirst($connection->type) }}
                                </span>
                            @endif
                        </div>
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
                                    echo 'Todos los días';
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

                @if(isset($connection->has_water_pressure))
                <div class="info-item">
                    <i class="fas fa-gauge-high info-icon"></i>
                    <div class="info-content">
                        <div class="info-label">Presión de Agua</div>
                        <div class="info-value">
                            @if($connection->has_water_pressure)
                                <span class="badge-custom badge-activo">
                                    <i class="fas fa-check"></i> Sí tiene
                                </span>
                            @else
                                <span class="badge-custom badge-inactivo">
                                    <i class="fas fa-times"></i> No tiene
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                @if(isset($connection->has_cistern))
                <div class="info-item">
                    <i class="fas fa-water info-icon"></i>
                    <div class="info-content">
                        <div class="info-label">Cisterna</div>
                        <div class="info-value">
                            @if($connection->has_cistern)
                                <span class="badge-custom badge-activo">
                                    <i class="fas fa-check"></i> Sí tiene
                                </span>
                            @else
                                <span class="badge-custom badge-inactivo">
                                    <i class="fas fa-times"></i> No tiene
                                </span>
                            @endif
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
                        <div class="info-value">{{ $connection->note }}</div>
                    </div>
                </div>
                @endif

                <div class="info-item">
                    <i class="fas fa-clock info-icon"></i>
                    <div class="info-content">
                        <div class="info-label">Fecha de Registro</div>
                        <div class="info-value">
                            {{ $connection->created_at ? $connection->created_at->format('d/m/Y H:i') : 'No disponible' }}
                        </div>
                    </div>
                </div>
            </div>
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
</body>
</html>
