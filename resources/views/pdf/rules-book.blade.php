<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        @page {
            size: A4;
            margin: 12mm 11mm 14mm 11mm;
        }
        html, body {
            margin: 0;
            padding: 0;
        }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 8.5pt;
            line-height: 1.22;
            color: #222;
        }
        h1 {
            font-size: 13.5pt;
            margin: 0 0 6pt 0;
            page-break-before: always;
            page-break-after: avoid;
            break-after: avoid;
        }
        h1:first-of-type {
            page-break-before: auto;
            font-size: 16pt;
            margin: 0 0 5pt 0;
        }
        h2 {
            font-size: 10pt;
            margin: 8pt 0 3pt 0;
            page-break-after: avoid;
            break-after: avoid;
        }
        h3 {
            font-size: 9pt;
            margin: 6pt 0 2pt 0;
            page-break-after: avoid;
            break-after: avoid;
        }
        h4, h5, h6 {
            font-size: 8.5pt;
            margin: 5pt 0 2pt 0;
            page-break-after: avoid;
        }
        p {
            margin: 0 0 3.5pt 0;
            orphans: 3;
            widows: 3;
        }
        ul, ol {
            margin: 0 0 3.5pt 12pt;
            padding: 0;
        }
        li {
            margin: 0 0 1pt 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0 0 4pt 0;
            font-size: 7.5pt;
        }
        th, td {
            border: 1px solid #bbb;
            padding: 1.5pt 3pt;
            vertical-align: top;
        }
        th { background: #eee; text-align: left; }
        tr { break-inside: avoid; }
        blockquote {
            margin: 0 0 3.5pt 6pt;
            padding: 1.5pt 5pt;
            border-left: 2px solid #888;
            color: #444;
            break-inside: avoid;
        }
        code, pre {
            font-family: DejaVu Sans Mono, monospace;
            font-size: 7.5pt;
        }
        pre {
            background: #f4f4f4;
            padding: 3pt;
            margin: 0 0 3.5pt 0;
            white-space: pre-wrap;
        }
        hr {
            border: 0;
            border-top: 1px solid #ccc;
            margin: 6pt 0;
        }
        .meta {
            font-size: 7.5pt;
            color: #555;
            margin-bottom: 8pt;
        }
    </style>
</head>
<body>
    {!! $html !!}
</body>
</html>
