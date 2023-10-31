<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
   
    <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
    <meta charset="UTF-8" />
  
    <title>Test</title>
<style>
      
        .header-img {
            width: 100%;
            margin-bottom: 50px;
            height: auto;
        }

        .footer {
            float: left;
            margin-top: 50px;
            width: 100%;
            color: #ffffff;
            text-align: center;
        }
 
        .container {
            width: 100%;
        }

        .center {
            margin: 0 auto;
            width: 85%;
        }
      
        .center table {
            margin: 0 auto;
            width: 100%;
        }
        table.head{
            border-spacing: 0;
        }
       .text-bold {
            font-weight: 800;
        }

        .text-semibold {
            font-weight: 600;
        }

        .border {
            border: 2px solid #000000;
        }

        .border-top {
            border-top: 2px solid #000000;
        }

        .border-bottom {
            border-bottom: 2px solid #000000;
        }

        .font-medium {
            font-size: 1.4rem;
        }

        .font-small {
            font-size: 1rem;
        }

        .collapse {
            border-collapse: collapse;
            padding: 0;
            border-spacing: 0;
            text-align: center;
            margin-top: 20px;
            float: left;
            display: table;
        }
       
        .collapse > tbody > tr {
            display: table-row;
        }
        .collapse > tbody tr:last-child{
            height: 250px;
        }
        tbody td{
            text-align: center;
            vertical-align: top;
        }
        .collapse th,
        .collapse  td {
            border:1px solid #000000;
            height: 1em;
            width: auto;
            padding: 10px;
            color: #484848;
            font-weight: 600;
        }

        tr td.fix {
            width: 1%;
            padding: 0 20px;
            white-space: nowrap;
        }

        .left {
            text-align: left;
        }

        .space {

            border-spacing: 10px;

        }

        table.head td.text{
            overflow: hidden;
            position: relative;
        }
        table.head td.text { 
         width: 90%;
         text-align:left; padding:10px 0; 
        }
        table.head td.text:after{
            content: " ....................................................................................................................... ";
            position: absolute;
            padding-left: 5px;
        }
        .orange_bg{
            color:#000000;
            background-color: #f27036;
        }
  

        .header h2.title {
            padding: 25px 0 ;
            color: #f27036;
            text-align: center;
            margin:0;
            font-size: xx-large;
            text-transform: uppercase;
            font-family: 'Times New Roman', Times, serif;
        }
        .sign_area{
            overflow: hidden;
        }
        .signature{
            float: left;
            margin-top: 40px;
            width: 100%;
            text-align: left;
            overflow: hidden;
            position: relative;
        }
        .signature .txt:after{
            content: " ....................................................................................................................... ";
            position: absolute;
            padding-left: 5px;
        }
        .label{
            float: left;
            margin-top: 10px;
            width: 100%;
            text-align: left;
        }
        .header_warapper div{
            float:left;
            width: calc(50% - 10px);
            padding:10px 5px;
            font-size: medium;
            font-weight: bold;
        }
       .header_warapper{
            float: left;
            position: relative;
            width: calc(100% - 2px);
            border:1px solid #000000;
            border-bottom: 0;
       }
       td.title{
        text-align: right;
       }
       td.thankyou{
            padding: 25px 0 ;
            color: #f27036 !important;
            text-align: center;
            margin:0;
            font-size: x-large;
            text-transform: capitalize;
            font-family: sans-serif;
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
                $invoice_no = "INV".substr($bill_number,0, 4).str_pad($invoice_number,5,"0", STR_PAD_LEFT);
                @endphp
    <div class="container">
        <div class="header">
            
            <h2 class="title">Invoice</h2>
        </div>
    
       Test
        <div class="center" style="margin-top:5px;">
     
            <div class="header_warapper">
                    <div>Customer Name :  {{$bill_to}}</div>
                    <div>&nbsp;</div>
                    <div>Customer ID :  {{$ftth_id}}</div>
                    <div>Invoice No. :  {{$invoice_no}}</div>
                    <div>Address : {{$attn}}</div>
                    <div>Date :  {{ date("j F Y",strtotime($date_issued)) }}</div>
                   
                    <div>Package : {{$service_description}}</div>
                    <div>Contact No. : {{$phone}}</div>
                    <div>Internet Speed : {{$qty}}</div>
            </div>
               
                
                  
               

               <!-- </tbody>
       </table>
                        <table class="collapse" style="margin-top:0px; width:100%; ">
                        <thead>

                        </thead>
                        <tbody> -->
             <table class="collapse" style="width:100%; ">
               <tbody>
                    <tr>
                        <td>No.</td>
                        <td>Description</td>
                        <td>Qty</td>
                        <td>Price (THB)</td>
                        <td>Total Amount (THB)</td>
                    </tr>

                 <tr >
                            <td>1</td>
                            <td>{{$service_description}} for <br /> {{$period_covered}} </td>
                            <td >1</td>
                            <td>{{number_format($normal_cost,2,'.')}}</td>
                            <td>{{number_format($sub_total,2,'.')}}</td>
                        </tr>
                        @php
                        if($discount){
                        @endphp 
                        <tr >
                            <td>2</td>
                            <td>Discount </td>
                            <td >1</td>
                         
                            <td>{{number_format($discount,2,'.')}}</td>
                            <td>{{number_format($discount,2,'.')}}</td>
                        </tr>
                        @php 
                        }
                        @endphp
                       
                       
                        </tbody>
           
                    <tfoot>
                    <tr>
                          
                            <td class="title" colspan="4">Subtotal</td>
                            <td class="text">{{number_format($sub_total,2,'.')}}</td>
                        </tr>
                        <tr>
                          
                          <td class="title" colspan="4">Discount</td>
                          <td class="text">{{number_format($discount,2,'.')}}</td>
                      </tr>
                      <tr>
                          
                          <td class="title" colspan="4">Commercial Tax</td>
                          <td class="text">{{number_format($tax,2,'.')}}</td>
                      </tr>
                      <tr>
                          
                          <td class="title" colspan="4">Grand Total </td>
                          <td class="text">{{number_format($total_payable,2,'.')}}</td>
                      </tr>
                      <tr>
                        <td class="thankyou" colspan="5">
                            Thank you For Choosing Our Services.
                        </td>
                      </tr>
                                                
                    </tfoot>
                       
                   
                </table>
            
        
        
            

        </div>
        <div class="footer">
       
        </div>
   
</body>

</html>