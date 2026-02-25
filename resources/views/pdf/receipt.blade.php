<!doctype html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="utf-8">
    <style>
        /* ====== Base ====== */
        body {
            font-family: cairo, sans-serif;
            direction: rtl;
            font-size: 12px;
            color: #0f172a;
            line-height: 1.5;
        }

        .page {
            border: 1px solid #e5e7eb;
            padding: 18px;
            background: #fff;
        }

        /* ====== Header ====== */
        .header {
            width: 100%;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 12px;
            margin-bottom: 14px;
        }

        .brand {
            font-size: 18px;
            font-weight: 800;
            letter-spacing: .2px;
        }

        .subtitle {
            color: #64748b;
            font-size: 11px;
            margin-top: 3px;
        }

        .badge {
            display: inline-block;
            padding: 6px 10px;
            border: 1px solid #c7d2fe;
            background: #eef2ff;
            color: #3730a3;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .status-issued {
            border-color: #bbf7d0;
            background: #ecfdf5;
            color: #166534;
        }

        .status-void {
            border-color: #fecaca;
            background: #fef2f2;
            color: #991b1b;
        }

        /* ====== Layout ====== */
        .grid {
            width: 100%;
        }

        .col {
            vertical-align: top;
        }

        .card {
            border: 1px solid #e5e7eb;
            padding: 12px;
            background: #ffffff;
        }

        .cardTitle {
            font-size: 11px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 8px;
        }

        .label {
            color: #64748b;
            font-size: 10px;
            margin-bottom: 3px;
        }

        .value {
            font-size: 12px;
            font-weight: 800;
        }

        .muted {
            color: #64748b;
            font-size: 10px;
        }

        .ltr {
            direction: ltr;
            unicode-bidi: embed;
        }

        /* ====== Amount ====== */
        .amountWrap {
            border: 1px solid #e5e7eb;
            padding: 14px 12px;
            background: #f8fafc;
        }

        .amountLabel {
            color: #334155;
            font-size: 10px;
            font-weight: 800;
        }

        .amount {
            margin-top: 6px;
            font-size: 20px;
            font-weight: 900;
        }

        /* ====== Footer ====== */
        .footer {
            margin-top: 14px;
            padding-top: 12px;
            border-top: 1px solid #e5e7eb;
            color: #64748b;
            font-size: 10px;
            line-height: 1.7;
        }

        /* small separator */
        .sp {
            height: 10px;
        }
    </style>
</head>

<body>
    @php
        $status = $receipt->status ?? 'issued';
        $isVoided = $status === 'void';
        $badgeCls = $isVoided ? 'status-void' : 'status-issued';
        $badgeTxt = $isVoided ? 'VOID' : 'VERIFIED RECEIPT';
        $issuedAt = $receipt->issued_at?->format('Y-m-d H:i');
    @endphp

    <div class="page">

        {{-- ===== Header ===== --}}
        <table class="header" cellpadding="0" cellspacing="0">
            <tr>
                <td class="col" style="width:70%;">
                    <div class="brand">Gaza Sanad</div>
                    <div class="subtitle">Donation Receipt • إيصال تبرع رسمي</div>
                </td>
                <td class="col" style="width:30%; text-align:left;">
                    <span class="badge {{ $badgeCls }}">{{ $badgeTxt }}</span>
                </td>
            </tr>
        </table>

        {{-- ===== Body Grid ===== --}}
        <table class="grid" cellpadding="0" cellspacing="0">
            <tr>
                {{-- Left (Details) --}}
                <td class="col" style="width:62%; padding-left:10px;">
                    <div class="card">
                        <div class="cardTitle">بيانات الإيصال</div>

                        <div class="label">رقم الإيصال / Receipt No.</div>
                        <div class="value ltr">{{ $receipt->receipt_no }}</div>

                        <div class="sp"></div>

                        <div class="label">تاريخ الإصدار / Issued At</div>
                        <div class="value ltr">{{ $issuedAt ?: '—' }}</div>

                        <div class="sp"></div>

                        <div class="label">اسم المتبرع / Donor Name</div>
                        <div class="value">{{ $receipt->donor_name ?: '—' }}</div>

                        <div class="sp"></div>

                        <div class="label">البريد الإلكتروني / Email</div>
                        <div class="value ltr">{{ $receipt->donor_email ?: '—' }}</div>
                    </div>

                    <div class="sp"></div>

                    <div class="amountWrap">
                        <div class="amountLabel">المبلغ المدفوع / Amount Paid</div>
                        <div class="amount ltr">
                            {{ number_format((float) $receipt->amount, 2) }} {{ $receipt->currency }}
                        </div>
                        <div class="muted" style="margin-top:6px;">
                            الحالة / Status: <span class="ltr">{{ $status }}</span>
                        </div>
                    </div>
                </td>

                {{-- Right (QR) --}}
                <td class="col" style="width:38%;">
                    <div class="card" style="text-align:center;">
                        <div class="cardTitle">التحقق / Verification</div>

                        {{-- QR via mPDF --}}
                        <barcode code="{{ $verifyUrl }}" type="QR" size="1.25" error="M" />

                        <div class="sp"></div>

                        <div class="muted">Scan QR أو افتح الرابط:</div>
                        <div class="muted ltr" style="word-break:break-all; margin-top:4px;">
                            {{ $verifyUrl }}
                        </div>
                    </div>

                    <div class="sp"></div>

                    <div class="card">
                        <div class="cardTitle">ملاحظات</div>
                        <div class="muted">
                            هذا الإيصال مولّد إلكترونيًا ولا يحتاج ختم أو توقيع.<br>
                            This receipt is electronically generated and does not require a signature.
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        {{-- ===== Footer ===== --}}
        <div class="footer">
            للدعم: يرجى إرفاق رقم الإيصال عند التواصل. <span class="ltr">Receipt No.</span><br>
            © {{ date('Y') }} Gaza Sanad
        </div>

    </div>
</body>

</html>
