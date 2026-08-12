<style>
    @page { margin: 95px 28px 85px 28px; }

    main { padding-top: 16px; }

    body {
        font-family: DejaVu Sans, sans-serif;
        font-size: 11px;
        color: #111;
    }

    header {
        position: fixed;
        top: -75px;
        left: 0; right: 0;
        height: 70px;
    }

    .title {
        font-size: 18px;
        font-weight: bold;
        text-align: center;
        margin: 0 0 8px 0;
        text-transform: uppercase;
        color: #111;
    }

    .subtitle {
        font-size: 10px;
        text-align: center;
        margin: 0 0 10px 0;
        color: #333;
    }

    table.report-table { margin-top: 8px; }

    table {
        width: 100%;
        table-layout: fixed;
        border-collapse: collapse;
    }

    thead { display: table-header-group; }

    th, td {
        padding: 6px;
        vertical-align: top;
        word-wrap: break-word;
    }

    thead th {
        background: #f2f2f2;
        color: #111;
        font-weight: bold;
        text-align: center;
        font-size: 10px;
        border: 1px solid #d5d5d5;
    }

    tbody td {
        border: 1px solid #cfcfcf;
        font-size: 10px;
    }

    tr, td, th { page-break-inside: avoid; }

    .right { text-align: right; }
    .center { text-align: center; }
</style>