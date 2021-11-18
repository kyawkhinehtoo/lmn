<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8" />
  
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="{{ public_path('storage/css/burglish.css')}}">
    <style>
.wininnwa{
    font-family: 'WinInnwa';
    font-size: 1.2rem !important;
}
body{
    font-family:sans-serif;
    font-size: 1rem;
}
.logo-img{
  width:120px;
  height:142px;
}
.container{
    width: 100%;
    margin-top: -50px;
}
.center{
    margin:0 auto;
    width:90%;
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
.collapse{
    border-collapse: collapse;
    padding: 0; 
    border-spacing: 0; 
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
    background:#fed404;
    color:#000000 !important;
}
</style>
   
</head>

<body class="font-sans antialiased" style="border-top:0 !important">
<div class="container">
        <div class="center" >
        <div style="float:right;margin-top:100px;" ><img src="{{ public_path('storage/images/ggh-logo-sm.jpg') }}" class="logo-img"/></div>
        <table class="collapse">
            <tbody>
            <tr>
                <td colspan="2" class="border-bottom bg-marga" style="padding:20px 5px;width:50%"><h1 class="text-semibold font-medium">BILL SUMMARY</h1></td>
                <td style="padding:10px;width:20%;font-size:0.8rem;" class="bottom text-semibold border-bottom">Period Cover:</td>
                <td style="padding:10px 5px;width:30%;font-size:0.8rem;" class="bottom text-semibold border-bottom ">{{ $period_covered }}</td>
            </tr>
            </tbody>
           
        </table>
   
        <table class="collapse" style="margin-top:20px;">
            <tbody>
            <tr>
               
                <td style="padding:10px;width:70%;vertical-align:top;"  rowspan="3" class="border ">
                <table class="collapse ">
                    @php
                        if($bill_to == '0' or (strpos($bill_to,'uspend') !== false)  or  (strpos($bill_to,'09')!== false) or (strpos($bill_to,'959')!== false) ) {
                    @endphp
           
                    @php
                        }else{
                    @endphp
                    <tr><td class="text-semibold" style="width:25%;vertical-align:top;">Bill To  </td><td style="width:75%">: {{$bill_to}}</td></tr>       
                    @php
                        }
                    @endphp
                    <tr ><td class="text-semibold" style="width:25%;vertical-align:top;"><br />Attention </td><td style="width:75%"><br />: {{$attn}}</td></tr>
                </table>
               
                </td>
                <td style="width:30%;text-align:center;" > 
                <div class="border" style="width:calc(100% - 20px);margin-left:20px;padding:10px;">
                <span class="text-semibold">Bill Number:</span> <br />
                {{ $bill_number }}
                </div>
              
                </td>
            </tr>
           
            <tr><td style="width:30%;text-align:center;"> 
            <div class="border" style="width:calc(100% - 20px);margin-left:20px;margin-top:20px;padding:10px;">
            <span class="text-semibold"> Customer ID:</span> <br />
                {{ $ftth_id }}
            </div>
            </td></tr>
            <tr><td style="width:30%;text-align:center;"> 
            <div class="border" style="width:calc(100% - 20px);margin-left:20px;margin-top:20px;padding:10px;">
            <span class="text-semibold">Date Issue:</span> <br />
                {{ $date_issued }}
                </div>
            </td></tr>
            </tbody>
           
        </table>
       
        <table class="space border" style="margin-top:20px;">
            <tr >
                <td style="text-align:center;">PreviousBalance<br/> <span class="wininnwa">vufusefaiG</span><br/> {{ number_format($previous_balance) }}</td>
                <td style="text-align:center;">+</td>
                <td style="text-align:center;">CurrentCharges<br/> <span class="wininnwa">vuf&Sday;acs&rnhfaiG</span> <br/> {{ number_format($current_charge )}}</td>
                <td style="text-align:center;">-</td>
                <td style="text-align:center;">Compensation/Discount<br/> <span class="wininnwa">avsmhay;aiGyrmP</span> <br/> {{number_format($discount)}}</td>
                <td style="text-align:center;">=</td>
                <td style="text-align:center;">Total PayableAmount<br/> <span class="wininnwa">pkpkaygif;</span><br/> {{ number_format($sub_total) }}</td>
            </tr>
        </table>    
        <table class="collapse" style="margin-top:20px;">
            <tr>
                <td class="bg-marga border text-semibold" style="width:30%;padding:20px;">Payment Due Date <br /><span class="wininnwa">aemufqkH;ay;acs&rnhf&ufqGJ</span></td>
                <td class="border text-semibold" style="width:20%;padding:20px;text-align:center">{{$payment_duedate}}</td>
                <td class="bg-marga border text-semibold" style="width:30%;padding:20px;">Total Payable Amount <br /><span class="wininnwa">aemufqkH;ay;acs&rnhfyrmP</span></td>
                <td class="border text-semibold" style="width:20%;padding:20px;text-align:center">{{number_format($sub_total)}}</td>
            </tr>
        </table>
        <table class="collapse" style="margin-top:20px;">
            <tr>
                <td class="bg-marga border text-semibold" style="width:50%;padding:20px;">Account Details <br /><span class="wininnwa">ay;acs&rnhftao;pdwf&Sif;wrf;</span></td>
                <td  style="width:50%"></td>
        </table>
        <table class="collapse border" >
            <tr>
                <td style="width:10%;padding:20px" class="text-bold" >No. </td>
                <td style="width:30% ;padding:20px" class="text-bold">Service Description</td>
                <td style="width:20%;padding:20px" class="text-bold">Qty</td>
                <td style="width:20%;padding:20px" class="text-bold">Type</td>
                <td style="width:20%;padding:20px;text-align:center" class="text-bold">Total (MMK)</td>
            </tr>
            <tr style="border-top:2px solid #000">
                <td style="width:10%;padding:20px"  ><span class="wininnwa">pÚf</span></td>
                <td style="width:30% ;padding:20px" ><span class="wininnwa">0efaqmifrI</span></td>
                <td style="width:20%;padding:20px"><span class="wininnwa">ta&twGuf</span></td>
                <td style="width:20%;padding:20px" ><span class="wininnwa">aiGay;acsrI trsdk;tpm;</span></td>
                <td style="width:20%;padding:20px;text-align:center" ><span class="wininnwa">pkpkaygif; usyf</span></td>
            </tr>
            <tr >
                <td style="width:10%;padding:20px"  >1 </td>
                <td style="width:30% ;padding:20px" >Previous Balance</td>
                <td style="width:20%;padding:20px">-</td>
                <td style="width:20%;padding:20px" ></td>
                <td style="width:20%;padding:20px;text-align:center" >{{number_format($previous_balance)}}</td>
            </tr>
            <tr >
                <td style="width:10%;padding:20px"  >2 </td>
                <td style="width:30% ;padding:20px" >{{$service_description}}</td>
                <td style="width:20%;padding:20px">{{$qty}}</td>
                <td style="width:20%;padding:20px" >{{$type}}</td>
                <td style="width:20%;padding:20px;text-align:center" >{{number_format($sub_total)}}</td>
            </tr>
            @php
            if(!empty($otc))
            {
            @endphp
            <tr style="border-top:2px solid #000;" class="text-bold">
                <td colspan="4" style="width:80%;padding:2px;text-align:right;"  >One Time Charges (OTC)  </td>
                <td style="width:20% ;padding:2px;text-align:center;" >{{number_format($otc)}}</td>
            </tr> 
            @php
            }
            @endphp    
            <tr style="border-top:2px solid #000;" class="text-bold">
                <td colspan="4" style="width:80%;padding:2px;text-align:right;"  >Compensation/Discount  </td>
                <td style="width:20% ;padding:2px;text-align:center;" >{{number_format($discount)}}</td>
            </tr>
            <tr style="border-top:2px solid #000;" class="text-bold">
                <td colspan="4" style="width:80%;padding:2px;text-align:right;"  >Total Payable Amount (MMK) <br /> <span class="wininnwa">pkpkaygif;ay;acs&rnhfyrmP(usyf)</span>  </td>
                <td style="width:20% ;padding:2px;text-align:center;" >{{number_format($total_payable)}}</td>
            </tr>
        </table>
        <div style="margin-top:20px; font-weight:800;font-size: 1rem; text-transform: capitalize;">{{$amount_in_word}}</div>
       
        <div style="margin-top:10px; font-weight:800;font-size: 1rem;">{{$commercial_tax}}</div>
        <div style="margin-top:80px;font-size: 0.8rem;width:100%;text-align:center;" ><i>"This computer generated document has been already authorized electronically - No Signature Required"</i></div>

   
        </div>
</div>

</body>

</html>