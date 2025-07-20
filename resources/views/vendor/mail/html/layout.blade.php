<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body>
<table class="wrapper" width="100%" cellpadding="0" cellspacing="0">
    <tr>
        <td align="center">
            <table class="content" width="100%" cellpadding="0" cellspacing="0">
                <!-- Header -->
                @isset($header)
                    <tr>
                        <td>{{ $header }}</td>
                    </tr>
                @endisset

                <!-- Body -->
                <tr>
                    <td>{{ Illuminate\Mail\Markdown::parse($slot) }}</td>
                </tr>

                <!-- Subcopy -->
                @isset($subcopy)
                    <tr>
                        <td>{{ $subcopy }}</td>
                    </tr>
                @endisset

                <!-- Footer -->
                @isset($footer)
                    <tr>
                        <td>{{ $footer }}</td>
                    </tr>
                @endisset
            </table>
        </td>
    </tr>
</table>
</body>
</html>
