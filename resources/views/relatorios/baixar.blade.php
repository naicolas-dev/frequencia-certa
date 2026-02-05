<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Relatório da Frequência</title>
    <style>
        @page {
            margin: 40px;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #374151;
            font-size: 14px;
            line-height: 1.5;
        }

        /* Cabeçalho */
        .header {
            text-align: center;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .logo {
            font-size: 28px;
            font-weight: 900;
            color: #2563eb;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .subtitle {
            font-size: 12px;
            color: #6b7280;
            margin-top: 5px;
            text-transform: uppercase;
        }

        /* Card de Informações */
        .info-box {
            width: 100%;
            margin-bottom: 30px;
            background-color: #f3f4f6;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }

        .info-table td {
            padding: 4px;
            border: none;
            font-size: 13px;
        }

        .label {
            font-weight: bold;
            color: #4b5563;
            width: 120px;
        }

        /* Tabela Principal */
        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .main-table th {
            background-color: #2563eb;
            color: white;
            padding: 12px;
            text-align: left;
            font-size: 12px;
            text-transform: uppercase;
        }

        .main-table td {
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 13px;
            vertical-align: middle;
        }

        .main-table tr:nth-child(even) {
            background-color: #f9fafb;
        }

        /* Status e Cores */
        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            display: inline-block;
            text-transform: uppercase;
        }

        .bg-green {
            background-color: #d1fae5;
            color: #065f46;
        }

        .bg-yellow {
            background-color: #fef3c7;
            color: #92400e;
        }

        .bg-red {
            background-color: #fee2e2;
            color: #991b1b;
        }

        /* Barra de Progresso (Modo Compatível com PDF) */
        .progress-wrapper {
            white-space: nowrap;
        }

        .progress-text {
            display: inline-block;
            width: 35px;
            font-weight: bold;
            font-size: 11px;
            vertical-align: middle;
        }

        .progress-container {
            display: inline-block;
            width: 100px;
            background-color: #e5e7eb;
            border-radius: 4px;
            height: 8px;
            overflow: hidden;
            vertical-align: middle;
        }

        .progress-bar {
            height: 100%;
            border-radius: 4px;
        }

        /* Lista de Faltas Detalhada */
        .falta-item {
            margin-bottom: 6px;
            font-size: 12px;
            color: #4b5563;
            border-bottom: 1px dashed #e5e7eb;
            padding-bottom: 4px;
        }

        .date-chip {
            display: inline-block;
            background-color: #fee2e2;
            color: #991b1b;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
            margin-left: 5px;
            border: 1px solid #fecaca;
        }

    </style>
</head>

<body>

    <div class="header">
        <div class="logo">Frequência Certa</div>
        <div class="subtitle">Relatório Oficial de Acompanhamento Acadêmico</div>
    </div>

    <div class="info-box">
        <table class="info-table" width="100%">
            <tr>
                <td class="label">ALUNO:</td>
                <td><strong>{{ strtoupper($user->name) }}</strong></td>
                <td class="label" style="text-align: right;">EMISSÃO:</td>
                <td style="text-align: right; width: 150px;">{{ date('d/m/Y \à\s H:i') }}</td>
            </tr>
            <tr>
                <td class="label">EMAIL:</td>
                <td>{{ $user->email }}</td>
                <td class="label" style="text-align: right;">PERÍODO:</td>
                <td style="text-align: right;">{{ date('Y') }}</td>
            </tr>
        </table>
    </div>

    <h3 style="color: #111827; border-left: 5px solid #2563eb; padding-left: 10px; margin-bottom: 15px;">
        Resumo de Desempenho
    </h3>

    <table class="main-table">
        <thead>
            <tr>
                <th width="25%">Disciplina</th>
                <th width="12%" style="text-align: center">Aulas Previstas</th>
                <th width="12%" style="text-align: center">Aulas Realizadas</th>
                <th width="12%" style="text-align: center">Faltas</th>
                <th width="23%">Frequência</th>
                <th width="16%" style="text-align: right">Situação</th>
            </tr>
        </thead>
        <tbody>
            @foreach($disciplinas as $disciplina)
                @php
                    $total = $disciplina->total_aulas_realizadas ?? 0;
                    $faltas = $disciplina->total_faltas ?? 0;
                    $previstas = $disciplina->getAttribute('total_aulas_previstas_cache') ?? 0;
                    $presenca = 100;

                    if ($total > 0) {
                        $presenca = round((($total - $faltas) / $total) * 100);
                    }

                    $corBarra = '#10b981'; // Verde
                    if ($presenca < 75)
                        $corBarra = '#ef4444'; // Vermelho
                    elseif ($presenca < 85)
                        $corBarra = '#f59e0b'; // Amarelo
                @endphp
                <tr>
                    <td style="font-weight: bold; color: #374151;">
                        {{ $disciplina->nome }}
                    </td>
                    <td style="text-align: center;">
                        <span style="font-size: 16px; font-weight: bold; color: #6b7280;">{{ $previstas }}</span>
                    </td>
                    <td style="text-align: center;">
                        @if($total > 0)
                            <span style="font-size: 16px; font-weight: bold; color: #2563eb;">{{ $total }}</span>
                        @else
                            <span style="font-size: 14px; color: #d1d5db;">—</span>
                        @endif
                    </td>
                    <td style="text-align: center;">
                        @if($total > 0)
                            <span style="font-size: 16px; font-weight: bold; color: #ef4444;">{{ $faltas }}</span>
                        @else
                            <span style="font-size: 14px; color: #d1d5db;">—</span>
                        @endif
                    </td>
                    <td>
                        @if($total > 0)
                            <div class="progress-wrapper">
                                <span class="progress-text">{{ $presenca }}%</span>
                                <div class="progress-container">
                                    <div class="progress-bar"
                                        style="width: {{ $presenca }}%; background-color: {{ $corBarra }};"></div>
                                </div>
                            </div>
                        @else
                            <span style="font-size: 12px; color: #9ca3af; font-style: italic;">Sem registros</span>
                        @endif
                    </td>
                    <td style="text-align: right;">
                        @if($total > 0)
                            @if($presenca >= 85)
                                <span class="badge bg-green">Aprovado</span>
                            @elseif($presenca >= 75)
                                <span class="badge bg-yellow">Atenção</span>
                            @else
                                <span class="badge bg-red">Reprovando</span>
                            @endif
                        @else
                            <span class="badge" style="background-color: #f3f4f6; color: #6b7280;">Sem Dados</span>
                        @endif
                    </td>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h3
        style="color: #111827; border-left: 5px solid #ef4444; padding-left: 10px; margin-top: 40px; margin-bottom: 15px;">
        Extrato Detalhado de Ausências
    </h3>
    <p style="font-size: 12px; color: #6b7280; margin-bottom: 20px;">
        Registro oficial de datas com ausência confirmada.
    </p>

    <table width="100%">
        @foreach($disciplinas->chunk(2) as $chunk)
            <tr>
                @foreach($chunk as $disciplina)
                    @php $diasFalta = $disciplina->frequencias->where('presente', false); @endphp
                    <td width="50%" style="vertical-align: top; padding-right: 20px; padding-bottom: 20px;">
                        @if($diasFalta->count() > 0)
                            <div style="background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 15px;">
                                <div
                                    style="font-weight: bold; color: #374151; margin-bottom: 10px; border-bottom: 1px solid #eee; padding-bottom: 5px;">
                                    {{ $disciplina->nome }}
                                </div>
                                <div>
                                    @foreach($diasFalta as $falta)
                                        <div class="falta-item">
                                            Falta registrada no dia:
                                            <span class="date-chip">
                                                {{ \Carbon\Carbon::parse($falta->data)->format('d/m/y') }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </td>
                @endforeach
                {{-- Se o chunk for ímpar, adiciona célula vazia para manter alinhamento --}}
                @if($chunk->count() < 2)
                    <td width="50%"></td>
                @endif
            </tr>
        @endforeach
    </table>

    <div class="footer">
        Frequência Certa • Documento gerado eletronicamente em {{ date('d/m/Y') }}
    </div>

</body>

</html>