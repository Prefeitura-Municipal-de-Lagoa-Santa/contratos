<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ofício - {{ $oficio->code }}</title>
    <style>
        /* ========================================
           RESET & BASE
        ======================================== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            margin: 15mm 20mm 20mm 20mm;
            background: #ffffff;
        }

        body {
            font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif;
            font-size: 10pt;
            line-height: 1.5;
            color: #1a1a2e;
            background: #ffffff;
        }

        /* ========================================
           HEADER - Cabeçalho Institucional
        ======================================== */
        .header {
            text-align: center;
            padding-bottom: 12px;
            margin-bottom: 20px;
            border-bottom: 3px solid #4338ca;
        }

        .header-logo {
            max-width: 180px;
            height: auto;
            margin-bottom: 10px;
        }

        .header-title {
            font-size: 15pt;
            font-weight: bold;
            color: #4338ca;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 2px;
        }

        .header-subtitle {
            font-size: 9pt;
            color: #4a5568;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* ========================================
           DOCUMENT BADGE - Identificação
        ======================================== */
        .doc-badge {
            background: #4338ca;
            color: #ffffff;
            text-align: center;
            padding: 14px 20px;
            margin: 20px 0;
        }

        .doc-badge-title {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-bottom: 4px;
        }

        .doc-badge-number {
            font-size: 11pt;
            letter-spacing: 1px;
            opacity: 0.9;
        }

        /* ========================================
           META INFO - Data e Local
        ======================================== */
        .meta-info {
            text-align: right;
            margin-bottom: 20px;
            padding: 8px 0;
        }

        .meta-date {
            font-size: 10pt;
            color: #4a5568;
            font-style: italic;
        }

        /* ========================================
           RECIPIENT BOX - Destinatário
        ======================================== */
        .recipient-box {
            background: #f5f3ff;
            border-left: 4px solid #4338ca;
            padding: 14px 18px;
            margin: 20px 0;
        }

        .recipient-label {
            font-size: 8pt;
            font-weight: bold;
            color: #4338ca;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }

        .recipient-name {
            font-size: 11pt;
            font-weight: bold;
            color: #1a1a2e;
        }

        .recipient-role {
            font-size: 9pt;
            color: #64748b;
            font-style: italic;
            margin-top: 2px;
        }

        .recipient-institution {
            font-size: 9pt;
            color: #4338ca;
            font-weight: bold;
            margin-top: 4px;
        }

        /* ========================================
           SUBJECT BOX - Assunto
        ======================================== */
        .subject-box {
            background: #eef2f7;
            border-left: 4px solid #4338ca;
            padding: 14px 18px;
            margin: 20px 0;
        }

        .subject-label {
            font-size: 8pt;
            font-weight: bold;
            color: #4338ca;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 6px;
        }

        .subject-text {
            font-size: 11pt;
            font-weight: bold;
            color: #1a1a2e;
            line-height: 1.4;
        }

        /* ========================================
           CONTENT - Corpo do Documento
        ======================================== */
        .content {
            margin: 25px 0;
            line-height: 1.7;
        }

        .content-greeting {
            font-size: 10pt;
            color: #1a1a2e;
            margin-bottom: 18px;
        }

        .content-body {
            text-align: justify;
            color: #2d3748;
            font-size: 10pt;
        }

        .content-body p {
            margin-bottom: 14px;
            text-indent: 35px;
        }

        /* ========================================
           SIGNATURE - Assinatura
        ======================================== */
        .signature-area {
            margin-top: 50px;
            page-break-inside: avoid;
        }

        .signature-closing {
            margin-bottom: 45px;
            color: #1a1a2e;
            font-size: 10pt;
        }

        .signature-block {
            text-align: center;
            max-width: 280px;
            margin: 0 auto;
        }

        .signature-line {
            border-top: 1px solid #1a1a2e;
            margin-bottom: 8px;
        }

        .signature-name {
            font-size: 10pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #4338ca;
            letter-spacing: 0.5px;
            margin-bottom: 3px;
        }

        .signature-role {
            font-size: 9pt;
            color: #64748b;
            font-style: italic;
        }

        /* ========================================
           FOOTER
        ======================================== */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            padding-top: 10px;
            border-top: 1px solid #e2e8f0;
        }

        .footer-text {
            font-size: 7.5pt;
            color: #64748b;
        }

        .footer-gen {
            font-size: 7pt;
            color: #94a3b8;
            margin-top: 2px;
        }

        /* ========================================
           UTILITIES
        ======================================== */
        .no-break {
            page-break-inside: avoid;
        }
    </style>
</head>

<body>
    <!-- HEADER -->
    <div class="header">
        @php
            $logoPath = public_path('images/header-logos.png');
            if (file_exists($logoPath)) {
                $logoData = base64_encode(file_get_contents($logoPath));
                $logoSrc = 'data:image/png;base64,' . $logoData;
            } else {
                $logoSrc = null;
            }
        @endphp
        @if($logoSrc)
            <img src="{{ $logoSrc }}" alt="Logos" class="header-logo">
        @endif
        <div class="header-title">Prefeitura Municipal de Lagoa Santa</div>
        <div class="header-subtitle">Estado de Minas Gerais</div>
    </div>

    <!-- DOCUMENT BADGE -->
    <div class="doc-badge">
        <div class="doc-badge-title">Ofício</div>
        <div class="doc-badge-number">
            Nº {{ $oficio->code }}@if($oficio->department) / {{ $oficio->department }}@endif
        </div>
    </div>

    <!-- META INFO -->
    <div class="meta-info">
        <div class="meta-date">Lagoa Santa/MG, {{ $oficio->formatted_date }}</div>
    </div>

    <!-- RECIPIENT BOX -->
    <div class="recipient-box">
        <div class="recipient-label">Destinatário</div>
        <div class="recipient-name">{{ $oficio->recipient_name }}</div>
        @if($oficio->recipient_role)
            <div class="recipient-role">{{ $oficio->recipient_role }}</div>
        @endif
        @if($oficio->recipient_institution)
            <div class="recipient-institution">{{ $oficio->recipient_institution }}</div>
        @endif
    </div>

    <!-- SUBJECT BOX -->
    <div class="subject-box">
        <div class="subject-label">Assunto</div>
        <div class="subject-text">{{ $oficio->subject }}</div>
    </div>

    <!-- CONTENT -->
    <div class="content">
        <div class="content-greeting">Prezado(a) Senhor(a),</div>
        <div class="content-body">
            {!! $oficio->formatted_content !!}
        </div>
    </div>

    <!-- SIGNATURE -->
    <div class="signature-area no-break">
        <div class="signature-closing">Atenciosamente,</div>
        <div class="signature-block">
            <div class="signature-line"></div>
            <div class="signature-name">{{ strtoupper($oficio->sender_name) }}</div>
            @if($oficio->sender_role)
                <div class="signature-role">{{ $oficio->sender_role }}</div>
            @endif
        </div>
    </div>

    <!-- FOOTER -->
    <div class="footer">
        <div class="footer-text">Prefeitura Municipal de Lagoa Santa — Estado de Minas Gerais</div>
        <div class="footer-gen">Documento gerado eletronicamente em {{ now()->format('d/m/Y H:i') }}</div>
    </div>

</body>

</html>