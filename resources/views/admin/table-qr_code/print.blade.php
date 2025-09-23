<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code - Table {{ $tableQrcode->table->table_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: white;
            padding: 20mm;
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
        }

        .print-header {
            text-align: center;
            margin-bottom: 40px;
            border-bottom: 3px solid #1f2937;
            padding-bottom: 20px;
        }

        .print-title {
            font-size: 32px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 10px;
            letter-spacing: 2px;
        }

        .print-subtitle {
            font-size: 18px;
            color: #6b7280;
            font-weight: 500;
        }

        .print-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            align-items: start;
        }

        .print-qr-section {
            text-align: center;
        }

        .print-qr-code {
            width: 220px;
            height: 220px;
            margin: 0 auto 25px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 15px;
            background: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .print-instructions {
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 25px;
            margin-top: 25px;
        }

        .print-instructions h4 {
            margin-bottom: 20px;
            color: #1f2937;
            font-size: 18px;
            text-align: center;
        }

        .print-instructions ol {
            text-align: left;
            padding-left: 25px;
            line-height: 1.8;
            font-size: 14px;
            color: #374151;
        }

        .print-instructions li {
            margin-bottom: 8px;
        }

        .print-info-section h3 {
            font-size: 22px;
            color: #1f2937;
            margin-bottom: 20px;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 8px;
        }

        .print-info-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding: 12px 0;
            border-bottom: 1px dotted #d1d5db;
        }

        .print-info-label {
            font-weight: bold;
            color: #374151;
            font-size: 16px;
        }

        .print-info-value {
            color: #6b7280;
            font-size: 16px;
        }

        .print-notes {
            margin-top: 35px;
            padding: 20px;
            background: #fef3cd;
            border-radius: 12px;
            border: 2px solid #fbbf24;
        }

        .print-notes h4 {
            color: #92400e;
            margin-bottom: 15px;
            font-size: 16px;
        }

        .print-notes ul {
            margin: 0;
            padding-left: 25px;
            color: #92400e;
            line-height: 1.6;
            font-size: 14px;
        }

        .print-notes li {
            margin-bottom: 8px;
        }

        .print-footer {
            position: fixed;
            bottom: 15mm;
            left: 20mm;
            right: 20mm;
            text-align: center;
            font-size: 12px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 15px;
        }

        @media print {
            body {
                padding: 15mm;
                width: 100%;
                margin: 0;
            }
            
            .no-print {
                display: none;
            }
        }

        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #7c3aed;
            color: white;
            border: none;
            padding: 15px 25px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        .print-button:hover {
            background: #6d28d9;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <!-- Print Button (hidden when printing) -->
    <button class="print-button no-print" onclick="window.print()">
        <i class="fas fa-print"></i> Print QR Code
    </button>

    <div class="print-header">
        <div class="print-title">THE STAG</div>
        <div class="print-subtitle">Table QR Code Access</div>
    </div>
    
    <div class="print-content">
        <div class="print-qr-section">
            <img src="{{ route('admin.table-qrcodes.qr-preview', [$tableQrcode->id, 'png']) }}" 
                 alt="QR Code" class="print-qr-code">
            
            <div class="print-instructions">
                <h4>How to Use This QR Code:</h4>
                <ol>
                    <li>Point your smartphone camera at the QR code</li>
                    <li>Tap the notification that appears on your screen</li>
                    <li>Browse our delicious menu items</li>
                    <li>Add your favorite items to cart</li>
                    <li>Place your order and enjoy your meal!</li>
                </ol>
            </div>
        </div>
        
        <div class="print-info-section">
            <h3>Table Information</h3>
            
            <div class="print-info-item">
                <span class="print-info-label">Table Number:</span>
                <span class="print-info-value">{{ $tableQrcode->table->table_number }}</span>
            </div>
            
            <div class="print-info-item">
                <span class="print-info-label">Session Started:</span>
                <span class="print-info-value">{{ $tableQrcode->started_at->format('d/m/Y H:i') }}</span>
            </div>
            
            @if($tableQrcode->guest_name)
            <div class="print-info-item">
                <span class="print-info-label">Guest Name:</span>
                <span class="print-info-value">{{ $tableQrcode->guest_name }}</span>
            </div>
            @endif
            
            @if($tableQrcode->guest_count)
            <div class="print-info-item">
                <span class="print-info-label">Party Size:</span>
                <span class="print-info-value">{{ $tableQrcode->guest_count }} person(s)</span>
            </div>
            @endif
            
            <div class="print-notes">
                <h4>📋 Important Information:</h4>
                <ul>
                    <li>This QR code is unique to your table session</li>
                    <li>Keep this card on your table during your visit</li>
                    <li>Need help? Please ask any of our friendly staff</li>
                    <li>Free WiFi available - Ask staff for password</li>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="print-footer">
        <p>Generated on {{ now()->format('d/m/Y H:i') }} | The Stag Restaurant Management System</p>
        <p>Thank you for dining with us! 🍽️</p>
    </div>

    <!-- FontAwesome for icons -->
    <script src="https://kit.fontawesome.com/your-fontawesome-kit.js" crossorigin="anonymous"></script>
</body>
</html>