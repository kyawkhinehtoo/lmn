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
<link rel="stylesheet" href="{{ asset('storage/css/burglish.css')}}">
    <style>

@media print {
     body{
        width: 29.62cm !important;

        margin: 30mm 45mm 30mm 45mm; 
        page-break-inside: avoid;
       
    }     
   .container-1{
       width: calc(50% - 5px) !important;
       float: left;
   }
   .container-2{
       display: block !important;
   }
   /* .footer{
    position: absolute;
    bottom:0;
    width:50%;
    height:30px;
   }  */
   @page {
  size: A4 landscape;
}

}

body{
    margin:0 auto;
    width:210mm; 
    font-weight: normal;
    font-size: 0.8em;
    /* height:210mm;
    width:297mm;
    width:1344px;
    height:816px;
    */
}


.mm{
    font-family: 'WinInnwa';
    font-size: 1rem;
}

.header-img{
  width:95%;
  height:auto;
}
.footer{
    width:100%;
    height:30px;
}
.container{
    width: 100%;
}
.container-1{
    width: 100%;
}
.container-2{
    width: calc(50% - 5px);
    float:left;   
    border-left: 1px #484848 dotted;
    margin-left:2.5px;
    padding-left: 2.5px;
    display: none;
}

.center{
    margin:0 auto;
    width:calc(100% - 40px);
}
.center table{
    margin:0 auto;
    width:100%;
}
.bottom{
    vertical-align: bottom;
}
.text-bold{
    font-weight: 800;
}
.text-semibold{
    font-weight: 600;
}
.border{
    border:2px solid #000000;
}
.border-top{
    border-top: 2px solid #000000;
}
.border-bottom{
    border-bottom: 2px solid #000000;
}

.font-medium{
    font-size: 1.4rem;
}
.font-small{
    font-size: 0.7rem;
    font-weight:600;
}
.collapse{
    border-collapse: collapse;
    padding: 0; 
    border-spacing: 0; 
    border:1px solid #b6b1b4;

    margin-top:20px;
    float:left;
}


.collapse th, .collapse td{
    border:1px solid #b6b1b4;
    height: 0.5em;
    text-align: center;
    width: auto;
    padding:6px;
    color:#484848;
}
tr td.fix {
    width: 1%;
    padding:0 20px;
    white-space: nowrap;
}
.-left{
   margin-left:-10px;
}
.space{

    border-spacing: 10px; 

}
.underline{
    margin:0 auto;
    width:100%;
    height:2px;
    background: #000000;
}
.bg-marga{
    background:#255978;
    color:#ffffff;
}
.bg-image {
    background-image: url("{{ asset('storage/images/watermark.jpg') }}") !important; /* The image used */
  background-color: transparent;
  /* Center the image */
  background-repeat: no-repeat; /* Do not repeat the image */
  /* Resize the background image to cover the entire container */
  background-size: 50%;

  background-position-y:center ;
  background-position-x:center ;
  float:left;
  margin:0 auto;
}
.round{
    text-align: center;
    vertical-align: middle;
    font-size: 1.2rem;
    font-weight: bolder;
    border-radius: 0.5rem;
    width: 65%;
    padding: 4px;
    margin: 0 auto;
}
.round label{
    white-space: nowrap;
}
.invoice_to{
    background: #fed406;
    color: #06131d;
    display: block;
    width: calc(100% - 40px);
    font-size: 1rem;
    font-weight: bold;
    padding: 10px 20px;
    margin-top:5px;
}

</style>
   
</head>

<body class="font-sans antialiased" style="border-top:0 !important">
@php
                        if (strpos($period_covered, ' to ')) {
                            $p_months = explode(" to ", ($period_covered));
                            $from = (new DateTime($p_months[0]));
                            $to = (new DateTime($p_months[1]));
                            $first_date = $from->format("d-M-Y");
                            $last_date = $to->format("d-M-Y");
                            $period_covered = $first_date.' to '.$last_date;
                        }  
                   
                @endphp
<div class="container">
<div class="container-1">
        <div>
            <table style="background:#000000;border:0;border-collapse: collapse;">
                <tr>
                    <td style="width:40%;text-align:center;" rowspan="2"><img src="{{ asset('storage/images/invoice-header.jpg') }}" class="header-img"/></td>
                    <td style="width:35%;text-align:center;"><label style="display:block;font-size:2.2rem;font-weight:800;color:#fed406;">RECEIPT</label></td>
                </tr>
                <tr>
                <td style="text-align:center;">
                <div style="background:#ffffff;" class="round">
                    <label class="block font-small">Date:  {{ date("j F Y",strtotime($receipt_date)) }}</span></label>
                    <label class="block font-small" >Invoice No:<span style="text-transform:uppercase;">
                    @php
                              
                              $receipt_num = 'R'.substr($bill_number,0, 4).str_pad($receipt_number,5,"0", STR_PAD_LEFT);
                              echo $receipt_num;
                           @endphp        
                </span></label>
                </div>
                </td>
                </tr>
                <tr><td colspan="2"><label class="invoice_to">Invoice to : </label></td></tr>
            </table>
        
        </div>
        <div class="center" style="margin-top:2px;" >
            <div class="bg-image">
            <table class="collapse" style="margin-top:0px; border-top:0; width:100%; ">
                <tr><td style="text-align:left;" colspan="4">Client Name : {{$bill_to}}</td><td rowspan="2"> Package : {{strtoupper($service_type)}} </td></tr>
                <tr><td style="text-align:left;" colspan="4">Client ID : {{$ftth_id}}</td></tr>
                <tr><td style="text-align:left;" colspan="4">Address : {{$attn}}</td><td rowspan="2">Internet Speed: <br />{{$qty}}</td></tr>
                <tr><td style="text-align:left;" colspan="4">Contact No : {{$phone}}</td></tr>
        
                <tr>
                    <th>No</th>
                    <th style="width: 200px;">Description</th>
                    <th style="width: 100px;">Qty</th>
                    <th style="width: 100px;">Price (THB)</th>
                    <th style="width: 150px;">Amount (THB)</th>
                </tr>
                </thead>
            <tbody>
                <tr>
                    <td class="fix">1</td>
                    <td>{{$service_description}}</td>
                    <td class="fix">{{$usage_days}}</td>
                    <td>{{number_format($normal_cost)}}</td>
                    <td>{{number_format($sub_total)}}</td>
                </tr>
                @php
                $count = 1;

                if($otc)
                    {
                    $count++;
                @endphp
                <tr><td>{{$count}}</td><td class="font-small">OTC</td><td>1</td><td>{{$otc}}</td><td>{{$otc}}</td></tr>
                @php
                }
                 if($public_ip)
                {
                    $count++;
                @endphp
                <tr><td>{{$count}}</td><td class="font-small">Public IP Annual Fees</td><td class="font-small">1 Year</td><td>{{number_format($public_ip)}}</td><td>{{number_format($public_ip)}}</td></tr>
                @php
                    }else{
                    @endphp
                <tr><td></td><td></td><td></td><td></td><td></td></tr>
                @php 
                    }
                @endphp


                @php
                    if($compensation)
                    {
                        $count++;
                @endphp
                <tr><td>{{$count}}</td><td>- Compensation</td><td>1</td><td> {{$compensation}}</td><td> {{$compensation}}</td></tr>
                @php
                    }else{
                    @endphp
                <tr><td></td><td></td><td></td><td></td><td></td></tr>
                @php 
                    }
                @endphp
          
                
            
                <tr><td colspan="4">Subtotal</td><td>{{number_format($sub_total)}}</td></tr>
                <tr><td colspan="4">- Discount</td><td> {{number_format($discount)}}</td></tr>
                <tr><td colspan="4">Commercial Tax</td><td>{{number_format($tax)}}</td></tr>
                <tr><td colspan="4">Grand Total</td><td>{{number_format($total_payable)}}</td></tr>
                <tr><td style="text-align:left; padding:10px;" colspan="5">Cover Period : {{$period_covered}}</td></tr>
                <tr><td style="text-align:left; padding:5px 10px;vertical-align:top;" colspan="5">Remark : {{$remark}}</td></tr>
            </tbody>
            </table>
        
            </div>
            <table>
                <tr><td colspan="6">&nbsp;</td></tr>
                <tr><td>Sale Name </td><td>:<span style="text-decoration: underline dotted ;"> {{$collector}}</span></td><td>ID</td><td>: <span style="text-decoration: underline dotted ;"> {{$ftth_id}}</td><td>Paid By</td><td>: ..........................</td></tr>
                <tr><td colspan="6">&nbsp;</td></tr>
         
                <tr><td>Signature </td><td>: ..........................</td><td>&nbsp;</td><td>&nbsp;</td><td>Signature</td><td>: ..........................</td></tr>
            </table>
         
     
        </div>
        <div class="footer">
        <div style="text-align: center;margin-top:10px;">
                <h1 style="font-size:1rem;font-weight:800;">(Sales Office)</h1>
                <p style="text-align: center;font-size: 0.8rem;font-weight: 600;margin-top:5px;">1/91, Bogyoke Street,Sansai (A),Tachileik Township, Eastern Shan State, Myanmar.</p>
                <p><span style="font-size: 1.5rem;">☎ </span><span style="font-weight: 600;font-size:1rem;">09 408534411, 09 408534422, 09 777049644, 09777049645</span></p>
            </div>
        <div style="height: 5px;width:100%;background:#fed406;"></div>
        <div style="height: 5px;width:100%;background:#000000;"></div>
        </div>
</div>
<div class="container-2">
        <div>
            <table style="background:#000000;border:0;border-collapse: collapse;">
                <tr>
                    <td style="width:40%;text-align:center" rowspan="2"><img src="{{ asset('storage/images/invoice-header.jpg') }}" class="header-img"/></td>
                    <td style="width:35%;text-align:center;"><label style="display:block;font-size:2.2rem;font-weight:800;color:#fed406;">RECEIPT</label></td>
                </tr>
                <tr>
                <td style="text-align:center;">
                <div style="background:#ffffff;" class="round">
                    <label class="block font-small">Date:  {{ date("j F Y",strtotime($receipt_date)) }}</span></label>
                    <label class="block font-small" >Invoice No:<span style="text-transform:uppercase;">
                    @php
                              
                              $receipt_num = 'R'.substr($bill_number,0, 4).str_pad($receipt_number,5,"0", STR_PAD_LEFT);
                              echo $receipt_num;
                           @endphp        
                </span></label>
                </div>
                </td>
                </tr>
                <tr><td colspan="2"><label class="invoice_to">Invoice to : </label></td></tr>
            </table>
        
        </div>
        <div class="center" style="margin-top:2px;" >
            <div class="bg-image">
            <table class="collapse" style="margin-top:0px; border-top:0; width:100%; ">
                <tr><td style="text-align:left;" colspan="4">Client Name : {{$bill_to}}</td><td rowspan="2"> Package : {{substr($bill_number,13,18)}} </td></tr>
                <tr><td style="text-align:left;" colspan="4">Client ID : {{$ftth_id}}</td></tr>
                <tr><td style="text-align:left;" colspan="4">Address : {{$attn}}</td><td rowspan="2">Internet Speed: <br />{{$qty}}</td></tr>
                <tr><td style="text-align:left;" colspan="4">Contact No : {{$phone}}</td></tr>
        
                <tr>
                    <th>No</th>
                    <th style="width: 200px;">Description</th>
                    <th style="width: 100px;">Qty</th>
                    <th style="width: 100px;">Price (THB)</th>
                    <th style="width: 150px;">Amount (THB)</th>
                </tr>
                </thead>
            <tbody>
                <tr>
                    <td class="fix">1</td>
                    <td>{{$service_description}}</td>
                    <td class="fix">{{$usage_days}}</td>
                    <td>{{number_format($normal_cost)}}</td>
                    <td>{{number_format($sub_total)}}</td>
                </tr>
                @php
                $count = 1;
                if($otc)
                    {
                    $count++;
                @endphp
                <tr><td>{{$count}}</td><td class="font-small">OTC</td><td>1</td><td>{{$otc}}</td><td>{{$otc}}</td></tr>
                @php
                }
                 if($public_ip)
                {
                    $count++;
                @endphp
                <tr><td>{{$count}}</td><td class="font-small">Public IP Annual Fees</td><td class="font-small">1 Year</td><td>{{number_format($public_ip)}}</td><td>{{number_format($public_ip)}}</td></tr>
                @php
                    }else{
                    @endphp
                <tr><td></td><td></td><td></td><td></td><td></td></tr>
                @php 
                    }
                @endphp
                @php
                    if($compensation)
                    {
                        $count++;
                @endphp
                <tr><td>{{$count}}</td><td>- Compensation</td><td>1</td><td> {{$compensation}}</td><td> {{$compensation}}</td></tr>
                @php
                    }else{
                    @endphp
                <tr><td></td><td></td><td></td><td></td><td></td></tr>
                @php 
                    }
                @endphp
          
         
            
                <tr><td colspan="4">Subtotal</td><td>{{number_format($sub_total)}}</td></tr>
                <tr><td colspan="4">- Discount</td><td> {{number_format($discount)}}</td></tr>
                <tr><td colspan="4">Commercial Tax</td><td>{{number_format($tax)}}</td></tr>
                <tr><td colspan="4">Grand Total</td><td>{{number_format($total_payable)}}</td></tr>
                <tr><td style="text-align:left; padding:10px;" colspan="5">Cover Period : {{$period_covered}}</td></tr>
                <tr><td style="text-align:left; padding:5px 10px;vertical-align:top;" colspan="5">Remark : {{$remark}}</td></tr>
            </tbody>
            </table>
        
            </div>
            <table>
                <tr><td colspan="6">&nbsp;</td></tr>
                <tr><td>Sale Name </td><td>:<span style="text-decoration: underline dotted ;"> {{$collector}}</span></td><td>ID</td><td>: <span style="text-decoration: underline dotted ;"> {{$ftth_id}}</td><td>Paid By</td><td>: ..........................</td></tr>
                <tr><td colspan="6">&nbsp;</td></tr>
                <tr><td>Signature </td><td>: ..........................</td><td>&nbsp;</td><td>&nbsp;</td><td>Signature</td><td>: ..........................</td></tr>
            </table>
         
     
        </div>
        <div class="footer">
        <div style="text-align: center;margin-top:10px;">
                <h1 style="font-size:1rem;font-weight:800;">(Sales Office)</h1>
                <p style="text-align: center;font-size: 0.8rem;font-weight: 600;margin-top:5px;">1/91, Bogyoke Street,Sansai (A),Tachileik Township, Eastern Shan State, Myanmar.</p>
                <p><span style="font-size: 1.5rem;">☎ </span><span style="font-weight: 600;font-size:1rem;">09 408534411, 09 408534422, 09 777049644, 09777049645</span></p>
            </div>
        <div style="height: 5px;width:100%;background:#fed406;"></div>
        <div style="height: 5px;width:100%;background:#000000;"></div>
        </div>
</div>
</div>

</body>

</html>