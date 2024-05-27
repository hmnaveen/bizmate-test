

<style>

  /** Import Monstserrat Font **/
  @import url("https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap"); 

  * {
      -webkit-print-color-adjust: exact;
      font-family: Montserrat, "Open Sans", Helvetica, Arial, sans-serif;
      padding: 0px;
      margin: 0px;
  }

</style>

<table style="position: relative; border-collapse: collapse; width: 100%; height: 100%; z-index: 1; font-family: Montserrat, sans-serif;">
  <tr>
      <td width="2%">&nbsp;</td>
      <td width="7%" style="background: #999; height: 70px;">&nbsp;</td>
      <td width="7%">&nbsp;</td>
      <td width="17%" style="background: #fed678;">&nbsp;</td>
      <td width="57%">&nbsp;</td>
  </tr>
  <tr>
      <td width="2%" style="background: #28292a; height: 70px;">&nbsp;</td>
      <td width="7%">&nbsp;</td>
      <td width="7%" style="background: #fdb917;">&nbsp;</td>
      <td width="17%">&nbsp;</td>
      <td width="57%" style="background: #fdb917;">&nbsp;</td>
  </tr>
  <tr>
      <td width="2%">&nbsp;</td>
      <td width="7%" style="background: #fed678;">&nbsp;</td>
      <td width="84%" colspan="3" style="position: relative; text-align: right;">
        <img src="{{public_path($invoice['image'])}}" style="width: 25%; padding: 4.4% 5% 6.8% 5%;">
      </td>
  </tr>
  <tr>
      <td width="2%" style="border-bottom: 25px solid #999;">&nbsp;</td>
      <td width="7%" style="background: #fed678; height: 100%; border-bottom: 25px solid #fed678;">&nbsp;</td>
      <td width="84%" colspan="3" valign="top" style="padding: 2% 3.4% 2.5% 3.4%; border-bottom: 25px solid #28292a;">

        <div style="position: relative; font-size: 16px; line-height: 160%; margin-bottom: 1%; ">

        {!! nl2br(e($invoice['message'])) !!}

        </div>

        <br>
        <div style="position: relative; line-height: 160%; width: 100%;">
            
            <span style="font-weight: 600; font-size: 20px; letter-spacing: 1px;">{{$invoice['invoice_name']}}</span><br>
            <span style="font-size: 13px;"><a href="mailto:{{$invoice['invoice_email']}}" style="color: #28292a; text-decoration: none;">{{$invoice['invoice_email']}}</a></span><br>
            <span style="font-size: 13px;">{{$invoice['invoice_phone']}}</span><br>
            <span style="font-size: 13px;">{{$invoice['invoice_address']}}</span>
        </div>
      </td>
  </tr>

</table>

