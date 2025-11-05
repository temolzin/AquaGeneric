<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toma de Agua {{ $connection->id }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-blue: #0066cc;
            --secondary-blue: #0099ff;
            --accent-teal: #00cccc;
            --light-blue: #e6f7ff;
            --dark-blue: #004d99;
            --text-dark: #2c3e50;
            --text-light: #5d6d7e;
            --white: #ffffff;
            --gradient-primary: linear-gradient(135deg, var(--primary-blue) 0%, var(--accent-teal) 100%);
            --gradient-secondary: linear-gradient(135deg, var(--secondary-blue) 0%, var(--primary-blue) 100%);
        }

        body {
            background: linear-gradient(135deg, #004d99 0%, #0066cc 25%, #0099ff 50%, #00b3b3 75%, #00cccc 100%);
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
                radial-gradient(circle at 20% 80%, rgba(0, 204, 204, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(102, 204, 255, 0.2) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
            z-index: -1;
        }
        .main-card {
            border-radius: 25px;
            box-shadow: 0 25px 80px rgba(0, 51, 102, 0.4);
            margin-top: 20px;
            animation: slideUp 0.6s ease-out;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            position: relative;
        }
        .main-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #0066cc, #0099ff, #00cccc, #66ccff, #99e6e6);
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
            background: var(--gradient-primary);
            color: white;
            padding: 35px 25px 25px 25px;
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
            background: radial-gradient(circle, rgba(255,255,255,0.15) 1%, transparent 1%);
            background-size: 25px 25px;
            animation: float 20s infinite linear;
        }
        @keyframes float {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .logo-container {
            margin-bottom: 25px;
            position: relative;
            z-index: 2;
            display: inline-block;
        }
        .logo-background {
            background: rgba(255, 255, 255, 0.95);
            padding: 8px;
            border-radius: 50%;
            display: inline-block;
            border: 2px solid rgba(255, 255, 255, 1);
            box-shadow: 
                0 4px 15px rgba(0, 0, 0, 0.25),
                inset 0 0 0 1px rgba(255, 255, 255, 0.9);
            transition: all 0.3s ease;
        }
        .company-logo {
            height: 100px;
            width: auto;
            filter: 
                drop-shadow(0 2px 8px rgba(0, 0, 0, 0.3))
                brightness(1.05)
                contrast(1.1);
            transition: all 0.3s ease;
            display: block;
        }
        .logo-container:hover .company-logo {
            transform: scale(1.05);
            filter: 
                drop-shadow(0 4px 12px rgba(0, 0, 0, 0.4))
                brightness(1.08)
                contrast(1.15);
        }
        .logo-container:hover .logo-background {
            background: rgba(255, 255, 255, 1);
            box-shadow: 
                0 6px 20px rgba(0, 0, 0, 0.35),
                inset 0 0 0 1px rgba(255, 255, 255, 1);
        }
        .header h1 {
            font-size: 1.8rem;
            margin: 15px 0 8px 0;
            font-weight: 700;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
            position: relative;
            z-index: 2;
        }
        .header .subtitle {
            font-size: 1rem;
            opacity: 0.95;
            margin-bottom: 20px;
            text-shadow: 0 1px 2px rgba(0,0,0,0.3);
            position: relative;
            z-index: 2;
            font-weight: 500;
        }
        .id-badge {
            background: rgba(255,255,255,0.3);
            backdrop-filter: blur(10px);
            padding: 12px 25px;
            border-radius: 50px;
            display: inline-block;
            font-size: 1.1rem;
            font-weight: 600;
            border: 1px solid rgba(255,255,255,0.4);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            position: relative;
            z-index: 2;
        }
        .info-list {
            padding: 0;
            margin: 0;
        }
        .info-item {
            padding: 20px 25px;
            border-bottom: 1px solid rgba(0, 102, 204, 0.1);
            display: flex;
            align-items: flex-start;
            transition: all 0.3s ease;
            background: transparent;
        }
        .info-item:hover {
            background: linear-gradient(135deg, rgba(0, 102, 204, 0.05) 0%, rgba(0, 204, 204, 0.05) 100%);
            transform: translateX(8px);
            border-left: 4px solid var(--primary-blue);
        }
        .info-item:last-child {
            border-bottom: none;
        }
        .info-icon {
            color: var(--primary-blue);
            font-size: 1.4rem;
            margin-right: 18px;
            min-width: 30px;
            margin-top: 2px;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            filter: drop-shadow(0 2px 4px rgba(0, 102, 204, 0.3));
        }
        .info-content {
            flex: 1;
        }
        .info-label {
            font-size: 0.8rem;
            color: var(--text-light);
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        .info-value {
            color: var(--text-dark);
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
        
        .footer {
            text-align: center;
            padding: 25px;
            color: white;
            margin-top: 20px;
            background: var(--gradient-secondary);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            margin: 20px;
            border: 1px solid rgba(255,255,255,0.4);
            box-shadow: 0 8px 32px rgba(0, 51, 102, 0.2);
        }
        .company-info {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            margin-bottom: 15px;
        }
        .company-links {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
            gap: 12px;
            font-size: 0.95rem;
        }
        .company-link {
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .company-link:hover {
            color: #e6f7ff;
            transform: translateY(-2px);
            text-shadow: 0 2px 8px rgba(255,255,255,0.4);
        }
        .company-link strong {
            font-weight: 700;
            background: linear-gradient(135deg, #ffffff, #e6f7ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        .logo-img {
            filter: brightness(0) invert(1);
            transition: all 0.3s ease;
        }
        .company-link:hover .logo-img {
            transform: scale(1.1);
            filter: brightness(0) invert(1) drop-shadow(0 2px 4px rgba(255,255,255,0.4));
        }
        .timestamp {
            font-size: 0.85rem;
            opacity: 0.95;
            margin-top: 12px;
            font-weight: 500;
            color: rgba(255,255,255,0.95);
            text-shadow: 0 1px 2px rgba(0,0,0,0.2);
            font-family: 'Courier New', monospace;
            letter-spacing: 0.5px;
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

        .badge-pagado {
            background: linear-gradient(135deg, #27ae60, #2ecc71);
            color: white;
            border: 1px solid #219653;
        }
        .badge-adeudo {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
            border: 1px solid #b03a2e;
        }
        .badge-adelantado {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            border: 1px solid #2475a0;
        }
        .badge-cancelado {
            background: linear-gradient(135deg, #95a5a6, #7f8c8d);
            color: white;
            border: 1px solid #6c7b7d;
        }

        @media (max-width: 576px) {
            .header h1 {
                font-size: 1.5rem;
            }
            .header .subtitle {
                font-size: 0.9rem;
            }
            .company-logo {
                height: 80px;
            }
            .logo-background {
                padding: 6px;
            }
            .header {
                padding: 30px 20px 20px 20px;
            }
            .info-item {
                padding: 18px 20px;
            }
            .info-value {
                font-size: 1rem;
            }
            .company-links {
                flex-direction: column;
                gap: 8px;
            }
            .footer {
                margin: 15px;
                padding: 20px 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container" style="max-width: 600px;">
        <div class="main-card">
            <div class="header">
                <div class="logo-container">
                    <div class="logo-background">
                        <img src="{{ asset('img/logo.png') }}" alt="AquaGeneric" class="company-logo">
                    </div>
                </div>
                
                <h1>Información de Toma de Agua</h1>
                <div class="subtitle">Sistema de Gestión de Agua Potable</div>
                
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
                                <br><small style="color: var(--primary-blue);">Colonia: {{ $connection->block }}</small>
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
                                    echo '<span style="color: #27ae60; font-weight: 700;">Todos los días</span>';
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
                    <i class="fas fa-credit-card info-icon"></i>
                    <div class="info-content">
                        <div class="info-label">Estado de Pago</div>
                        <div class="info-value">
                            @php
                                $status = $connection->getStatusCalculatedAttribute();
                                $statusIcons = [
                                    'Pagado' => 'fa-check-circle',
                                    'Adeudo' => 'fa-exclamation-triangle',
                                    'Adelantado' => 'fa-forward',
                                    'Cancelado' => 'fa-ban'
                                ];
                                $icon = $statusIcons[$status] ?? 'fa-circle-info';
                            @endphp
                            <span class="badge-custom" style="{{ $connection->calculated_style }}">
                                <i class="fas {{ $icon }}"></i> {{ $status }}
                            </span>
                        </div>
                    </div>
                </div>

                @if($connection->note)
                <div class="info-item">
                    <i class="fas fa-note-sticky info-icon"></i>
                    <div class="info-content">
                        <div class="info-label">Notas</div>
                        <div class="info-value" style="font-style: italic; color: var(--text-light);">{{ $connection->note }}</div>
                    </div>
                </div>
                @endif
            </div>

            <div class="footer">
                <div class="company-info">
                    <div class="company-links">
                        <a class="company-link" href="https://aquacontrol.rootheim.com/">
                            <strong>AquaControl</strong>
                        </a>
                        <span style="opacity: 0.7;">|</span>
                        <a class="company-link" href="https://rootheim.com/">
                            powered by <strong>Root Heim Company</strong>
                            <img src="{{ asset('img/rootheim.png') }}" width="20" height="15" class="logo-img" alt="Root Heim">
                        </a>
                    </div>
                </div>
                <div class="timestamp">
                    @php
                        // Formato: DD/MM/AAAA HH:MM:SS a. m./p. m.
                        $now = now();
                        $day = $now->format('d');
                        $month = $now->format('m');
                        $year = $now->format('Y');
                        $hour = $now->format('h');
                        $minute = $now->format('i');
                        $second = $now->format('s');
                        $ampm = $now->format('a') == 'am' ? 'a. m.' : 'p. m.';
                        
                        echo "{$day}/{$month}/{$year} {$hour}:{$minute}:{$second} {$ampm}";
                    @endphp
                </div>
            </div>
        </div>
    </div>
</body>
</html>
