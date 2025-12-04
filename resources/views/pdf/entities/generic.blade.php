<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $entity['name'] }} - PDF</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            padding: 20px;
        }
        
        .header {
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        
        .header h1 {
            font-size: 24px;
            margin-bottom: 5px;
        }
        
        .header .meta {
            font-size: 10px;
            color: #666;
        }
        
        .content {
            margin-bottom: 20px;
        }
        
        .section {
            margin-bottom: 15px;
        }
        
        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 3px;
        }
        
        .section-content {
            padding-left: 10px;
        }
        
        .field {
            margin-bottom: 8px;
        }
        
        .field-label {
            font-weight: bold;
            display: inline-block;
            width: 120px;
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
        <h1>{{ $entity['name'] }}</h1>
        <div class="meta">
            ID: {{ $entity['id'] }} | 
            Type: {{ ucfirst($entityType) }} | 
            Créé le: {{ $entity['created_at'] ?? 'N/A' }} | 
            Par: {{ $entity['created_by'] ?? 'Système' }}
        </div>
    </div>
    
    <div class="content">
        @if(!empty($entity['description']))
        <div class="section">
            <div class="section-title">Description</div>
            <div class="section-content">
                {!! nl2br(e($entity['description'])) !!}
            </div>
        </div>
        @endif
        
        <div class="section">
            <div class="section-title">Informations</div>
            <div class="section-content">
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
                                    {{ $value }}
                                @endif
                            </span>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
    
    <div class="footer">
        Généré le {{ now()->format('d/m/Y à H:i') }} - Krosmoz JDR
    </div>
</body>
</html>

