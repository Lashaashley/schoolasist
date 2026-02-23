<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Invoice Invitation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f7;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 90%;
            max-width: 600px;
            margin: 40px auto;
            background-color: #fff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        h2 {
            color: #2d3748;
            margin-bottom: 20px;
        }
        p {
            line-height: 1.6;
            margin-bottom: 15px;
        }
        ul {
            padding-left: 0;
            list-style: none;
            margin-bottom: 25px;
        }
        li {
            margin-bottom: 10px;
        }
        .button {
            display: inline-block;
            padding: 15px 25px;
            background-color: #1d72b8;
            color: #fff !important;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            text-align: center;
        }
        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #777;
            text-align: center;
        }
        @media (max-width: 480px) {
            .container {
                padding: 20px;
            }
            .button {
                width: 100%;
                padding: 15px 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Hello {{ $invitation->supplier->name }},</h2>

        <p>You have been invited to submit an invoice for the following:</p>

        <ul>
            <li><strong>Company:</strong> {{ $invitation->supplier->company }}</li>
            <li><strong>Invoice Number:</strong> {{ $invitation->invoice->invoice_number ?? 'N/A' }}</li>
            <li><strong>Category:</strong> {{ ucfirst($invitation->category) }}</li>
            @if($invitation->message)
                <li><strong>Message:</strong> {{ $invitation->message }}</li>
            @endif
            @if($invitation->expires_at)
                <li><strong>Expires On:</strong> {{ $invitation->expires_at->format('d M Y') }}</li>
            @endif
        </ul>

        <p>Please click the button below to fill out and submit your invoice:</p>

        <a href="{{ $link }}" target="_blank" class="button">Submit Invoice</a>

        <p class="footer">
            If you did not expect this email, please ignore it.<br>
            &copy; {{ date('Y') }} Your Company. All rights reserved.
        </p>
    </div>
</body>
</html>