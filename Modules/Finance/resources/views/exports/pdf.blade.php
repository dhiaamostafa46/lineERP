<!DOCTYPE html>
<html  dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <style>
        @font-face {
            font-family: 'Cairo';
            src: url('{{ public_path('fonts/Cairo-Regular.ttf') }}') format('truetype');
            font-weight: normal;
        }
        @font-face {
            font-family: 'Cairo';
            src: url('{{ public_path('fonts/Cairo-Bold.ttf') }}') format('truetype');
            font-weight: bold;
        }

        body {
            font-family: 'Cairo', sans-serif;
            
            text-align: center;
            font-size: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th {
            background-color: #9ABF80;
            color: #fff;
            border: 1px solid #000;
            padding: 8px;
            font-weight: bold;
        }

        td {
            background-color: #E5E3D4;
            border: 1px solid #000;
            padding: 8px;
        }

        h2 {
            color: #333;
            font-family: 'Cairo', sans-serif;
        }
    </style>
</head>
<body>
    <h2>{{ $name }}</h2>

    <table>
        <thead>
            <tr>
                @foreach($headers as $header)
                    <th>{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
                <tr>
                    @foreach($row as $cell)
                        <td>{{ $cell }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
