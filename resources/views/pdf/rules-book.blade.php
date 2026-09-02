<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        @page {
            margin: 22mm 16mm 22mm 16mm;
        }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10.5pt;
            line-height: 1.45;
            color: #222;
        }
        h1 {
            font-size: 20pt;
            margin: 0 0 12pt 0;
            page-break-before: always;
        }
        h1:first-of-type {
            page-break-before: auto;
        }
        h2 {
            font-size: 14pt;
            margin: 18pt 0 8pt 0;
        }
        h3 {
            font-size: 12pt;
            margin: 14pt 0 6pt 0;
        }
        h4, h5, h6 {
            font-size: 11pt;
            margin: 12pt 0 4pt 0;
        }
        p { margin: 0 0 8pt 0; }
        ul, ol { margin: 0 0 8pt 18pt; padding: 0; }
        li { margin: 0 0 3pt 0; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0 0 10pt 0;
            font-size: 9pt;
        }
        th, td {
            border: 1px solid #bbb;
            padding: 3pt 5pt;
            vertical-align: top;
        }
        th { background: #eee; text-align: left; }
        blockquote {
            margin: 0 0 8pt 10pt;
            padding: 4pt 8pt;
            border-left: 3px solid #888;
            color: #444;
        }
        code, pre {
            font-family: DejaVu Sans Mono, monospace;
            font-size: 9pt;
        }
        pre {
            background: #f4f4f4;
            padding: 6pt;
            margin: 0 0 8pt 0;
            white-space: pre-wrap;
        }
        hr {
            border: 0;
            border-top: 1px solid #ccc;
            margin: 14pt 0;
        }
        .meta {
            font-size: 9pt;
            color: #555;
            margin-bottom: 18pt;
        }
    </style>
</head>
<body>
    {!! $html !!}
</body>
</html>
