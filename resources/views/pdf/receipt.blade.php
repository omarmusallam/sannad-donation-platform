<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>إيصال تبرع رسمي</title>

    <style>
        body {
            font-family: cairo, sans-serif;
            font-size: 13px;
            color: #1f2937;
        }

        .page {
            padding: 40px;
            border: 1px solid #d1d5db;
        }

        .header {
            border-bottom: 3px solid #16a34a;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }

        .platform {
            font-size: 22px;
            font-weight: bold;
        }

        .platform-sub {
            font-size: 11px;
            color: #6b7280;
        }

        .receipt-title {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #111827;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            background: #dcfce7;
            color: #166534;
            font-size: 11px;
            border-radius: 4px;
        }

        .section-title {
            font-weight: bold;
            font-size: 14px;
            margin-top: 25px;
            margin-bottom: 10px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            padding: 6px 0;
            vertical-align: top;
        }

        .label {
            color: #6b7280;
            font-size: 12px;
        }

        .value {
            font-weight: bold;
            font-size: 14px;
        }

        .amount-box {
            margin-top: 25px;
            padding: 20px;
            border: 2px solid #16a34a;
            text-align: center;
        }

        .amount {
            font-size: 28px;
            font-weight: bold;
            margin-top: 5px;
        }

        .qr-section {
            margin-top: 30px;
            padding: 15px;
            border: 1px solid #e5e7eb;
            text-align: center;
        }

        .qr-section img {
            width: 110px;
        }

        .legal {
            margin-top: 25px;
            font-size: 11px;
            color: #6b7280;
            line-height: 1.7;
        }

        .footer {
            margin-top: 30px;
            border-top: 1px solid #e5e7eb;
            padding-top: 15px;
            text-align: center;
            font-size: 10px;
            color: #9ca3af;
        }
    </style>
</head>

<body>

    <div class="page">

        <div class="header">
            <table>
                <tr>
                    <td align="right">
                        <div class="platform">
                            {{ config('app.name') }}
                        </div>
                        <div class="platform-sub">
                            منصة التبرعات الرقمية
                        </div>
                    </td>

                    <td align="left">
                        <div class="label">رقم الإيصال</div>
                        <div class="value">{{ $receipt->receipt_no }}</div>
                        <div class="label">
                            تاريخ الإصدار: {{ $receipt->issued_at?->format('Y-m-d H:i') }}
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="receipt-title">
            إيصال تبرع رسمي
            <br>
            <span class="status-badge">
                {{ strtoupper($receipt->status) === 'ISSUED' ? 'إيصال صالح' : 'ملغي' }}
            </span>
        </div>

        <div class="section-title">
            بيانات التبرع
        </div>

        <table>
            <tr>
                <td width="50%">
                    <div class="label">اسم الحملة</div>
                    <div class="value">
                        {{ $receipt->donation?->campaign?->title_ar ?? '-' }}
                    </div>
                </td>

                <td width="50%">
                    <div class="label">رقم التبرع</div>
                    <div class="value">{{ $receipt->donation_id }}</div>
                </td>
            </tr>

            <tr>
                <td>
                    <div class="label">اسم المتبرع</div>
                    <div class="value">
                        {{ $receipt->donor_name ?? 'متبرع مجهول' }}
                    </div>
                </td>

                <td>
                    <div class="label">البريد الإلكتروني</div>
                    <div class="value">
                        {{ $receipt->donor_email ?? '-' }}
                    </div>
                </td>
            </tr>

            <tr>
                <td>
                    <div class="label">طريقة الدفع</div>
                    <div class="value">
                        {{ $receipt->donation?->payment_method ?? '-' }}
                    </div>
                </td>

                <td>
                    <div class="label">حالة العملية</div>
                    <div class="value">
                        {{ $receipt->donation?->status ?? '-' }}
                    </div>
                </td>
            </tr>
        </table>

        <div class="amount-box">
            <div class="label">إجمالي المبلغ المدفوع</div>
            <div class="amount">
                {{ number_format($receipt->amount, 2) }} {{ $receipt->currency }}
            </div>
        </div>

        <div class="qr-section">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode($verifyUrl) }}">
            <div style="font-size:11px;margin-top:5px;">
                امسح الرمز للتحقق من صحة الإيصال
            </div>
            <div style="font-size:10px;margin-top:5px;">
                رمز التحقق: {{ $receipt->uuid }}
            </div>
        </div>

        <div class="legal">
            هذا الإيصال صادر إلكترونيًا من النظام ويعتبر مستندًا رسميًا قابلًا للتحقق عبر بوابة التحقق العامة.
            أي تعديل على محتوى هذا الإيصال يؤدي إلى إبطاله.
            رابط التحقق: {{ $verifyUrl }}
        </div>

        <div class="footer">
            © {{ date('Y') }} {{ config('app.name') }}
            جميع الحقوق محفوظة — لا يتطلب هذا المستند توقيعًا يدويًا
        </div>

    </div>

</body>

</html>
