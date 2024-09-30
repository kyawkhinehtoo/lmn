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

    .container{
        width:100%;
        padding:20px;
        margin:20px auto;
    }
    .header{
        width:100%;
        float:left;
    }
    .header .logo{
        width:180px;
        float:left;
    }
    table{
        width:100%;
    }
    .header table.title td{
        vertical-align:top;
    }
    .header  h1,.header h2{
        font-size: 16px;
        padding: 0;
        margin:0;
    }

    .header table.title h2{
        text-decoration: underline;
        font-style: italic;
    }
    .header table td.title{
        font-weight: 900;
    }
    .header table td.content::before{
       content: ": ";
    }
    table.collapse{
        table-layout: fixed;
        border-collapse: collapse;
      
    }
    table.collapse td,table.collapse th{
        border: 1px solid black;
            padding: 5px;
            text-align: center;
    }
    td.right{
        text-align: right !important;
    }
    /* Set column width percentages */
    colgroup col:nth-child(1) { width: 10%; } /* No. */
        colgroup col:nth-child(2) { width: 50%; } /* Description */
        colgroup col:nth-child(3) { width: 10%; } /* Qty */
        colgroup col:nth-child(4) { width: 15%; } /* Price (THB) */
        colgroup col:nth-child(5) { width: 15%; } /* Total Amount (THB) */
   p.thankyou{
    width:100%;
    padding: 50px;
    text-align: center;
   }
</style>
</head>

<body>
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
        <div class="header">

            <table class="title">
                <tbody>
                    <tr>
                        <td>    <img src="{{ asset('storage/images/logo-letter.png') }}" class="logo" id="logo" />
                          
                        </td>
                        <td colspan="2">
                            <h2>
                                LONG MAO NETWORK CO., LTD.
                            </h2>
                            <p>
                                NO.PALA-1/696, BOGYOKE STREET, YAN AUNG MYAY, <br />TACHILEIK TOWNSHIP, EASTERN SHAN STATE, MYANMAR.<br />
Tel: 098-9181-1255, 098-9181-1299 MM.
                            </p>
                        </td>
                        
                        <td>
                            <h2>Invoice</h2>
                            <span>ใบแจ้งค่าบริการ</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        
           <table>
            <tbody>
                <tr>
                    <td class="title">Customer Name</td>
                    <td colspan="2" class="content">{{$bill_to}}</td>
                    <td class="title">Invoice No</td>
                    <td class="content">INV-240700354</td>
                </tr>
                <tr>
                    <td class="title">Customer ID</td>
                    <td colspan="2" class="content"> {{$ftth_id}}</td>
                    <td class="title">Date </td>
                    <td class="content">{{ date("j F Y",strtotime($date_issued)) }}</td>
                </tr>
                <tr>
                    <td class="title">Address</td>
                    <td colspan="2" class="content">{{$attn}}</td>
                    <td class="title">Package: </td>
                    <td class="content">{{$service_description}}</td>
                </tr>
                <tr>
                    <td class="title">Contact No.</td>
                    <td colspan="2" class="content"> {{$phone}}</td>
                    <td class="title">Internet Speed </td>
                    <td class="content"> {{$qty}}</td>
                </tr>
            </tbody>
           </table>
        </div>
    
       
        <div class="center" style="margin-top:5px;">
     
       
             <table class="collapse">
              
                <colgroup>
                    <col>
                    <col>
                    <col>
                    <col>
                    <col>
                </colgroup>
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Description</th>
                        <th>Qty</th>
                        <th>Price (THB)</th>
                        <th>Total Amount (THB)</th>
                    </tr>
                </thead>
                    <tbody>
                 <tr >
                            <td>1</td>
                            <td>{{$service_description}} for <br /> {{$period_covered}} </td>
                            <td >1</td>
                            <td class="right">{{number_format($normal_cost,2,'.')}}</td>
                            <td class="right">{{number_format($sub_total,2,'.')}}</td>
                        </tr>
                        @php
                        if($discount){
                        @endphp 
                        <tr >
                            <td>2</td>
                            <td>Discount </td>
                            <td >1</td>
                         
                            <td class='right'>{{number_format($discount,2,'.')}}</td>
                            <td class='right'>{{number_format($discount,2,'.')}}</td>
                        </tr>
                        @php 
                        }
                        @endphp
                       
                       
                        </tbody>
           
                    <tfoot>
                    <tr>
                          
                            <td class="right" colspan="4">Subtotal</td>
                            <td class="right">{{number_format($sub_total,2,'.')}}</td>
                        </tr>
                        <tr>
                          
                          <td class="right" colspan="4">Discount</td>
                          <td class="right">{{number_format($discount,2,'.')}}</td>
                      </tr>
                    
                      <tr>
                          
                          <td class="right" colspan="4">Grand Total </td>
                          <td class="right">{{number_format($total_payable,2,'.')}}</td>
                      </tr>
                   
                      </tr>
                                                
                    </tfoot>
                       
                   
                </table>
            
        <p class="thankyou">
            Thank you For Choosing Our Services.
        </p>
        
            

        </div>
       
</body>

</html>