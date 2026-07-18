<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'MIORPA NOTIFY')</title>

    <style>
        :root {
            --primary: #123a63;
            --primary-dark: #0a2948;
            --accent: #13a89e;
            --background: #f4f7fb;
            --surface: #ffffff;
            --text: #182230;
            --muted: #667085;
            --border: #dce3ec;
            --danger: #b42318;
            --success: #067647;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Arial, Helvetica, sans-serif;
            color: var(--text);
            background: var(--background);
        }

        button,
        input {
            font: inherit;
        }

        a {
            color: var(--primary);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 800;
            letter-spacing: .03em;
        }

        .brand-mark {
            display: grid;
            place-items: center;
            width: 42px;
            height: 42px;
            color: white;
            border-radius: 13px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
        }

        .alert {
            padding: 12px 14px;
            margin-bottom: 18px;
            border-radius: 10px;
        }

        .alert-success {
            color: var(--success);
            background: #ecfdf3;
            border: 1px solid #abefc6;
        }

        .alert-danger {
            color: var(--danger);
            background: #fef3f2;
            border: 1px solid #fecdca;
        }

        .field {
            margin-bottom: 18px;
        }

        .field label {
            display: block;
            margin-bottom: 7px;
            font-size: 14px;
            font-weight: 700;
        }

        .field input {
            width: 100%;
            min-height: 46px;
            padding: 11px 13px;
            color: var(--text);
            background: white;
            border: 1px solid var(--border);
            border-radius: 10px;
            outline: none;
        }

        .field input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(19, 168, 158, .15);
        }

        .field-error {
            margin-top: 6px;
            color: var(--danger);
            font-size: 13px;
        }

        .button {
            min-height: 46px;
            padding: 11px 18px;
            border: 0;
            border-radius: 10px;
            font-weight: 700;
            cursor: pointer;
        }

        .button-primary {
            color: white;
            background: var(--primary);
        }

        .button-primary:hover {
            background: var(--primary-dark);
        }

        .button-secondary {
            color: var(--primary);
            background: #eaf0f6;
        }
    </style>

    @stack('styles')
</head>
<body>
    @yield('content')
    @stack('scripts')
</body>
</html>