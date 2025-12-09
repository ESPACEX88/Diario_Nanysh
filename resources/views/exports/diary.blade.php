<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exportación de Diario - {{ $user->name }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            color: #333;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #EC4899;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #EC4899;
            margin: 0;
        }
        .section {
            margin-bottom: 40px;
            page-break-inside: avoid;
        }
        .section-title {
            color: #EC4899;
            font-size: 24px;
            margin-bottom: 15px;
            border-left: 4px solid #EC4899;
            padding-left: 10px;
        }
        .entry {
            margin-bottom: 20px;
            padding: 15px;
            background: #f9f9f9;
            border-radius: 5px;
        }
        .entry-date {
            color: #666;
            font-size: 12px;
            margin-bottom: 5px;
        }
        .entry-title {
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }
        .entry-content {
            color: #555;
            margin-bottom: 10px;
        }
        .tags {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            margin-top: 10px;
        }
        .tag {
            background: #EC4899;
            color: white;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 11px;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #EC4899;
            color: #666;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #EC4899;
            color: white;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📔 Mi Diario Personal</h1>
        <p>Exportado el {{ now()->format('d/m/Y H:i') }}</p>
        <p>Usuario: {{ $user->name }}</p>
    </div>

    @if($diary_entries->count() > 0)
    <div class="section">
        <h2 class="section-title">📝 Entradas del Diario</h2>
        @foreach($diary_entries as $entry)
        <div class="entry">
            <div class="entry-date">{{ $entry->date->format('d/m/Y') }}</div>
            <div class="entry-title">{{ $entry->title }}</div>
            <div class="entry-content">{!! nl2br(e($entry->content)) !!}</div>
            @if($entry->mood)
            <div><strong>Estado de ánimo:</strong> {{ $entry->mood }}</div>
            @endif
            @if($entry->tags && $entry->tags->count() > 0)
            <div class="tags">
                @foreach($entry->tags as $tag)
                <span class="tag">{{ $tag->name }}</span>
                @endforeach
            </div>
            @endif
        </div>
        @endforeach
    </div>
    @endif

    @if($notes->count() > 0)
    <div class="section">
        <h2 class="section-title">📄 Notas</h2>
        @foreach($notes as $note)
        <div class="entry">
            <div class="entry-date">{{ $note->created_at->format('d/m/Y H:i') }}</div>
            <div class="entry-title">{{ $note->title }}</div>
            <div class="entry-content">{!! nl2br(e($note->content)) !!}</div>
        </div>
        @endforeach
    </div>
    @endif

    @if($goals->count() > 0)
    <div class="section">
        <h2 class="section-title">🎯 Metas</h2>
        <table>
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Descripción</th>
                    <th>Estado</th>
                    <th>Fecha límite</th>
                </tr>
            </thead>
            <tbody>
                @foreach($goals as $goal)
                <tr>
                    <td>{{ $goal->title }}</td>
                    <td>{{ $goal->description }}</td>
                    <td>{{ $goal->is_completed ? 'Completada' : 'En progreso' }}</td>
                    <td>{{ $goal->deadline ? $goal->deadline->format('d/m/Y') : '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if($gratitudes->count() > 0)
    <div class="section">
        <h2 class="section-title">🙏 Agradecimientos</h2>
        @foreach($gratitudes as $gratitude)
        <div class="entry">
            <div class="entry-date">{{ $gratitude->date->format('d/m/Y') }}</div>
            <div class="entry-content">{{ $gratitude->content }}</div>
        </div>
        @endforeach
    </div>
    @endif

    <div class="footer">
        <p>Generado el {{ now()->format('d/m/Y H:i:s') }}</p>
        <p>Diario de Nahysh - Todos los derechos reservados</p>
    </div>
</body>
</html>

