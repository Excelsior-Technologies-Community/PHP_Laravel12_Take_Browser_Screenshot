<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Screenshot Report</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.6;
        }
        @page {
            margin: 80px 50px 60px 50px;
            @top-center {
                content: "Screenshot History Report";
                font-weight: bold;
                font-size: 14px;
                color: #4f46e5;
                border-bottom: 2px solid #4f46e5;
                padding-bottom: 8px;
                width: 100%;
            }
            @bottom-center {
                content: "Page " counter(page) " of " counter(pages);
                font-size: 10px;
                color: #666;
                border-top: 1px solid #ddd;
                padding-top: 6px;
                width: 100%;
            }
        }
        .report-header {
            text-align: center;
            margin-bottom: 24px;
            padding: 16px;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: white;
            border-radius: 8px;
        }
        .report-header h1 {
            font-size: 22px;
            margin-bottom: 4px;
        }
        .report-header p {
            font-size: 11px;
            opacity: 0.9;
        }
        .meta-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            padding: 10px 14px;
            background: #f8fafc;
            border-left: 4px solid #4f46e5;
            border-radius: 4px;
            font-size: 11px;
        }
        .meta-info span {
            font-weight: bold;
            color: #4f46e5;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }
        th {
            background: #4f46e5;
            color: white;
            padding: 8px 10px;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
        }
        td {
            padding: 7px 10px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 11px;
            vertical-align: top;
        }
        tr:nth-child(even) {
            background: #f9fafb;
        }
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
            background: #e0e7ff;
            color: #4f46e5;
        }
        .badge-full {
            background: #fef3c7;
            color: #d97706;
        }
        .url-cell {
            max-width: 200px;
            word-wrap: break-word;
        }
        .summary {
            margin-top: 20px;
            padding: 12px;
            background: #f0f9ff;
            border-left: 4px solid #0284c7;
            border-radius: 4px;
            font-size: 11px;
        }
        .no-data {
            text-align: center;
            padding: 40px;
            color: #94a3b8;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="report-header">
        <h1>Screenshot History Report</h1>
        <p>Generated on {{ date('F d, Y H:i') }} | Total Screenshots: {{ $screenshots->total() }}</p>
    </div>

    <div class="meta-info">
        <div>Total Records: <span>{{ $screenshots->total() }}</span></div>
        <div>Current Page: <span>{{ $screenshots->currentPage() }} of {{ $screenshots->lastPage() }}</span></div>
        <div>Exported At: <span>{{ date('Y-m-d H:i:s') }}</span></div>
    </div>

    @if($screenshots->count() > 0)
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 30%;">URL</th>
                    <th style="width: 10%;">Viewport</th>
                    <th style="width: 8%;">Format</th>
                    <th style="width: 10%;">Resolution</th>
                    <th style="width: 10%;">Type</th>
                    <th style="width: 12%;">Created At</th>
                    <th style="width: 8%;">Downloads</th>
                </tr>
            </thead>
            <tbody>
                @foreach($screenshots as $index => $shot)
                    <tr>
                        <td>{{ $screenshots->firstItem() + $index }}</td>
                        <td class="url-cell">{{ $shot->url }}</td>
                        <td>{{ ucfirst($shot->viewport) }}</td>
                        <td>{{ strtoupper($shot->format) }}</td>
                        <td>{{ $shot->width }}x{{ $shot->height }}</td>
                        <td>
                            {{ ucfirst($shot->format) }}
                            @if($shot->is_full_page)
                                <span class="badge badge-full">Full Page</span>
                            @endif
                        </td>
                        <td>{{ $shot->created_at->format('M d, Y H:i') }}</td>
                        <td>{{ $shot->download_count }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="summary">
            <strong>Summary:</strong> This report contains {{ $screenshots->total() }} screenshot(s) captured across various viewports and formats. Quality settings ranged from 1-100 depending on format selection.
        </div>
    @else
        <div class="no-data">No screenshots available to export.</div>
    @endif

</body>
</html>
