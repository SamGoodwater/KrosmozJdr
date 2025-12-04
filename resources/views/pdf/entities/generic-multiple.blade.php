<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ ucfirst($entityType) }}s - PDF</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            line-height: 1.5;
            color: #333;
            padding: 20px;
        }
        
        .header {
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        
        .header h1 {
            font-size: 20px;
            margin-bottom: 5px;
        }
        
        .header .meta {
            font-size: 10px;
            color: #666;
        }
        
        .entity {
            page-break-inside: avoid;
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        
        .entity-header {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 8px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }
        
        .entity-content {
            font-size: 10px;
        }
        
        .field {
            margin-bottom: 4px;
        }
        
        .field-label {
            font-weight: bold;
            display: inline-block;
            width: 100px;
        }
        
        .field-value {
            display: inline-block;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ccc;
            font-size: 10px;
            color: #666;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ ucfirst($entityType) }}s</h1>
        <div class="meta">
            Nombre d'entités: {{ count($entities) }} | 
            Généré le: {{ now()->format('d/m/Y à H:i') }}
        </div>
    </div>
    
    <div class="content">
        @foreach($entities as $entity)
        <div class="entity">
            <div class="entity-header">
                {{ $entity['name'] }} (ID: {{ $entity['id'] }})
            </div>
            <div class="entity-content">
                @if(!empty($entity['description']))
                    <div class="field">
                        <span class="field-label">Description:</span>
                        <span class="field-value">{{ Str::limit($entity['description'], 100) }}</span>
                    </div>
                @endif
                
                @foreach($entity as $key => $value)
                    @if(!in_array($key, ['id', 'name', 'description', 'created_at', 'created_by']) && $value !== null && $value !== '')
                        <div class="field">
                            <span class="field-label">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
                            <span class="field-value">
                                @if(is_bool($value))
                                    {{ $value ? 'Oui' : 'Non' }}
                                @elseif(is_array($value))
                                    {{ implode(', ', $value) }}
                                @else
                                    {{ \Illuminate\Support\Str::limit((string)$value, 50) }}
                                @endif
                            </span>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
    
    <div class="footer">
        Généré le {{ now()->format('d/m/Y à H:i') }} - Krosmoz JDR
    </div>
</body>
</html>

