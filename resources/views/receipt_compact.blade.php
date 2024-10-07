<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
    <meta charset="UTF-8" />

    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <style>
        body {
            font-family: sans-serif, serif, "pyidaungsu", "notosanthai";
        }

        .container {
            width: 100%;
            margin: 20px auto;

        }

        .thai {
            font-family: "notosanthai" !important;
            font-size: 20px;
        }

        .myanmar {
            font-family: "pyidaungsu";
        }

        img.logo {
            width: 150px;
            height: auto;
            float: left;
            margin-right: 10px;
        }

        table {
            width: 100%;
        }

        .header table.title td {
            vertical-align: top;
        }

        h1,
        h2 {
            font-size: 24px;
            padding: 0;
            margin: 0;
        }

        h2 {
            text-decoration: underline;
            font-style: italic;
            font-size: 26px;
        }

        .header table td.title {
            font-weight: 900;
        }

        /*
        .content::before {
            content: ": ";
        } */

        table.collapse {
            table-layout: fixed;
            border-collapse: collapse;

        }

        td {
            vertical-align: top;
            font-size: 14px;
        }

        table.collapse td,
        table.collapse th {
            border: 1px solid black;
            padding: 5px;

        }

        .right {
            text-align: right !important;
        }

        .center {
            text-align: center;
        }

        .left {
            text-align: left !important;
        }



        /* Total Amount (THB) */
        p.thankyou {
            width: 100%;
            margin-top: 50px;
            text-align: center;
        }
    </style>
</head>

<body>
    @php
        if (strpos($period_covered, ' to ')) {
            $p_months = explode(' to ', $period_covered);
            $from = new DateTime($p_months[0]);
            $to = new DateTime($p_months[1]);
            $first_date = $from->format('d-M-Y');
            $last_date = $to->format('d-M-Y');
            $period_covered = $first_date . ' to ' . $last_date;
        }
        $invoice_no = 'INV' . substr($bill_number, 0, 4) . str_pad($invoice_number, 5, '0', STR_PAD_LEFT);
        $receipt_no = 'REC' . substr($bill_number, 0, 4) . str_pad($receipt_number, 5, '0', STR_PAD_LEFT);
    @endphp


    <div class="container">
        <div class="header">

            <table class="title">
                <tbody>
                    <tr>
                        <td> <img src="{{ public_path('storage/images/logo-letter.png') }}" class="logo"
                                id="logo" />

                        </td>
                        <td colspan="2">
                            <h1>
                                LONG MAO NETWORK CO., LTD.
                            </h1>
                            <p>
                                NO.PALA-1/696, BOGYOKE STREET, YAN AUNG MYAY, <br />TACHILEIK TOWNSHIP, EASTERN SHAN
                                STATE, MYANMAR.<br />
                                Tel: 098-9181-1255, 098-9181-1299 MM.
                            </p>
                        </td>

                        <td>
                            <h2>Receipt</h2>
                            <span class="thai">ใบเสร็จรับเงิน</span>
                        </td>
                    </tr>
                </tbody>
            </table>

            <table>
                <tbody>
                    <tr>
                        <td class="title">Customer Name</td>
                        <td colspan="2" class="myanmar">: {{ $bill_to }}</td>
                        <td class="title">Receipt No</td>
                        <td>: {{ $receipt_no }}</td>
                    </tr>
                    <tr>
                        <td class="title">Customer ID</td>
                        <td colspan="2">: {{ $ftth_id }}</td>
                        <td class="title">Invoice No</td>
                        <td>: {{ $invoice_no }}</td>

                    </tr>
                    <tr>
                        <td class="title" rowspan="2">Address</td>
                        <td colspan="2" rowspan="2">
                            <table>
                                <tr>
                                    <td>:</td>
                                    <td class="myanmar">{{ $attn }}</td>
                                </tr>
                            </table>
                        </td>
                        <td class="title">Date </td>
                        <td>: {{ date('j F Y', strtotime($receipt_date)) }}</td>

                    </tr>
                    <tr>
                        <td class="title">Package: </td>
                        <td>: {{ $service_description }}</td>
                    </tr>
                    <tr>
                        <td class="title">Contact No.</td>
                        <td colspan="2">: {{ $phone }}</td>
                        <td class="title">Internet Speed </td>
                        <td>: {{ $qty }}</td>
                    </tr>
                </tbody>
            </table>
        </div>


        <div style="margin-top:5px;">


            <table class="collapse">


                <thead>
                    <tr>
                        <th style="width: 10%;">No.</th>
                        <th style="width: 35%;">Description</th>
                        <th style="width: 10%;">Qty</th>
                        <th style="width: 20%;">Price (THB)</th>
                        <th style="width: 25%;">Total Amount (THB)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="center">1</td>
                        <td class="center">{{ $service_description }} </td>
                        <td class="center">1</td>
                        <td class="right">{{ number_format($normal_cost, 2, '.') }}</td>
                        <td class="right">{{ number_format($sub_total, 2, '.') }}</td>
                    </tr>




                </tbody>

                <tfoot>

                    <tr>

                        <td class="right" colspan="4">Discount</td>
                        <td class="right">{{ number_format($discount, 2, '.') }}</td>
                    </tr>

                    <tr>

                        <td class="right" colspan="4">Grand Total </td>
                        <td class="right">{{ number_format($total_payable, 2, '.') }}</td>
                    </tr>

                    </tr>

                </tfoot>


            </table>
            <table style="margin-top:10px;">
                <tr>
                    <td style="width:20px;"> Period</td>
                    <td class="left">: {{ $period_covered }}</td>
                </tr>
                <tr>
                    <td> Remark</td>
                    <td class="left">
                        <p class="myanmar">{{ $remark ? ': ' . $remark : '' }}</p>
                    </td>
                </tr>
            </table>
            <table style="margin-top:20px;">
                <tr>
                    <td> Sale Person</td>
                    <td class="myanmar"> : {{ $collector }}</td>
                    <td> Paid By</td>
                    <td class="myanmar"> : {{ $bill_to }} </td>
                </tr>
                <tr>
                    <td> Signature</td>
                    <td> : <br />&nbsp;&nbsp;---------------------------- </td>
                    <td> Signature</td>
                    <td> : <br />&nbsp;&nbsp;---------------------------- </td>
                </tr>
            </table>
        </div>
    </div>


</body>

</html>
