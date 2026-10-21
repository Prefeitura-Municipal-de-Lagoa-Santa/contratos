<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Demanda #{{ $ticket->id }} - {{ $ticket->subject }}</title>
    <style>
        @page {
            margin: 22mm 18mm 20mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            color: #263247;
            font-family: DejaVu Sans, sans-serif;
            font-size: 10pt;
            line-height: 1.55;
        }

        .top-rule {
            height: 5px;
            background: #3449a8;
            margin-bottom: 24px;
        }

        .document-header {
            border-bottom: 1px solid #dce2ed;
            padding-bottom: 18px;
        }

        .eyebrow {
            color: #5264b5;
            font-size: 8pt;
            font-weight: bold;
            letter-spacing: 1.4px;
            text-transform: uppercase;
        }

        h1 {
            color: #17233b;
            font-size: 23pt;
            line-height: 1.25;
            margin: 7px 0 5px;
        }

        .subject {
            color: #596579;
            font-size: 11pt;
            margin: 0;
        }

        .badges {
            margin-top: 14px;
        }

        .badge {
            background: #eef1fb;
            border-radius: 12px;
            color: #3449a8;
            display: inline-block;
            font-size: 8pt;
            font-weight: bold;
            margin-right: 6px;
            padding: 5px 10px;
        }

        .badge-status {
            background: #edf5f1;
            color: #23704e;
        }

        .meta {
            background: #f5f7fb;
            border: 1px solid #e5e9f1;
            border-radius: 8px;
            margin: 18px 0 24px;
            padding: 12px 15px;
            width: 100%;
        }

        .meta td {
            padding: 4px 8px 4px 0;
            vertical-align: top;
            width: 50%;
        }

        .label {
            color: #7a8496;
            display: block;
            font-size: 7.5pt;
            font-weight: bold;
            letter-spacing: .7px;
            margin-bottom: 2px;
            text-transform: uppercase;
        }

        .value {
            color: #27344a;
            font-size: 9pt;
            font-weight: bold;
        }

        .section {
            page-break-inside: avoid;
            margin: 0 0 17px;
        }

        .section-title {
            border-bottom: 1px solid #e3e7ef;
            color: #3449a8;
            font-size: 9pt;
            font-weight: bold;
            letter-spacing: .8px;
            margin: 0 0 8px;
            padding: 0 0 6px;
            text-transform: uppercase;
        }

        .section-content {
            color: #344054;
            margin: 0;
            white-space: pre-wrap;
            overflow-wrap: break-word;
        }

        .answer-row {
            background: #f7f8fc;
            border: 1px solid #e8ebf2;
            border-radius: 6px;
            margin: 5px 0;
            padding: 8px 11px;
        }

        .answer {
            color: #27344a;
            font-weight: bold;
        }

        .ficha-list {
            margin: 0;
            padding-left: 20px;
        }

        .response {
            background: #f1f7f4;
            border-left: 3px solid #398263;
            padding: 11px 14px;
        }

        .footer {
            border-top: 1px solid #e3e7ef;
            color: #8a93a3;
            font-size: 8pt;
            margin-top: 28px;
            padding-top: 9px;
            text-align: right;
        }
    </style>
</head>
<body>
    @php
        $categories = [
            'contrato' => 'Contrato',
            'novo_processo' => 'Novo processo',
            'novo_contrato' => 'Novo processo',
            'aditivo' => 'Aditivo',
            'compra_direta' => 'Compra direta',
            'renovacao' => 'Renovação',
            'documentacao' => 'Documentação',
            'fiscalizacao' => 'Fiscalização',
            'pagamento' => 'Pagamento',
            'outro' => 'Outro assunto',
        ];
        $statuses = [
            'aberto' => 'Aberto',
            'em_atendimento' => 'Em atendimento',
            'resolvido' => 'Resolvido',
            'aprovado' => 'Aprovado',
            'recusado' => 'Recusado',
        ];
    @endphp

    <div class="top-rule"></div>

    <header class="document-header">
        <div class="eyebrow">Gestão de Contratos · Documento de acompanhamento</div>
        <h1>Demanda #{{ $ticket->id }}</h1>
        <p class="subject">{{ $ticket->subject }}</p>
        <div class="badges">
            <span class="badge">{{ $categories[$ticket->category] ?? $ticket->category }}</span>
            <span class="badge badge-status">{{ $statuses[$ticket->status] ?? $ticket->status }}</span>
        </div>
    </header>

    <table class="meta">
        <tr>
            <td>
                <span class="label">Solicitante</span>
                <span class="value">{{ $ticket->user->name }}</span>
            </td>
            <td>
                <span class="label">Responsável</span>
                <span class="value">{{ $ticket->responsible?->name ?? 'Não definido' }}</span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="label">Data de abertura</span>
                <span class="value">{{ $ticket->created_at->format('d/m/Y H:i') }}</span>
            </td>
            <td>
                <span class="label">Tipo de demanda</span>
                <span class="value">{{ $categories[$ticket->category] ?? $ticket->category }}</span>
            </td>
        </tr>
    </table>

    <section class="section">
        <h2 class="section-title">Descrição</h2>
        <p class="section-content">{{ $ticket->description }}</p>
    </section>

    @if ($ticket->specifications)
        <section class="section">
            <h2 class="section-title">Especificações do objeto</h2>
            <p class="section-content">{{ $ticket->specifications }}</p>
        </section>
    @endif

    @if ($ticket->justification)
        <section class="section">
            <h2 class="section-title">Justificativa</h2>
            <p class="section-content">{{ $ticket->justification }}</p>
        </section>
    @endif

    @if ($ticket->ficha || $ticket->in_pca !== null || $ticket->involves_other_secretariats !== null)
        <section class="section">
            <h2 class="section-title">Planejamento e orçamento</h2>
            @if ($ticket->ficha)
                <div class="answer-row">
                    <span class="label">Ficha principal</span>
                    <span class="answer">{{ $ticket->ficha }}</span>
                </div>
            @endif
            @if ($ticket->in_pca !== null)
                <div class="answer-row">
                    <span class="label">Prevista no PCA</span>
                    <span class="answer">{{ $ticket->in_pca ? 'Sim' : 'Não' }}</span>
                </div>
            @endif
            @if ($ticket->involves_other_secretariats !== null)
                <div class="answer-row">
                    <span class="label">Envolve outras secretarias</span>
                    <span class="answer">{{ $ticket->involves_other_secretariats ? 'Sim' : 'Não' }}</span>
                </div>
            @endif
            @if ($ticket->other_fichas)
                <div class="answer-row">
                    <span class="label">Fichas de outras secretarias</span>
                    <ul class="ficha-list">
                        @foreach ($ticket->other_fichas as $ficha)
                            <li>{{ $ficha }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </section>
    @endif

    @if ($ticket->management_response)
        <section class="section">
            <h2 class="section-title">Resposta da Gestão de Contratos</h2>
            <div class="response">
                <p class="section-content">{{ $ticket->management_response }}</p>
            </div>
        </section>
    @endif

    <footer class="footer">
        Documento gerado em {{ now()->format('d/m/Y H:i') }}
    </footer>
</body>
</html>
