
@include('includes.email_header')

<!-- Intro -->
<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center" style="width:100%;">
    <tr>
        <td bgcolor="#f1f1f1">

            <table width="660" border="0" cellspacing="0" cellpadding="0" align="center" class="scale">
                <tr>
                    <td bgcolor="#FFFFFF">

                        <table width="600" border="0" cellspacing="0" cellpadding="0" align="center" style="font-family:Helvetica, Arial, sans-serif; font-size: 14px; color: #000000;" class="scale">
                            <tr><td height="30"></td></tr>
                            <tr>
                                <td class="scale-center">

                                    <font style="font-family:Helvetica, Arial, sans-serif; font-size: 15px; color: #1b3044; font-weight:600;">Hi {{$data['email']}},</font>

                                </td>
                            </tr>
                            <tr>
                                <td height="20"></td>
                            </tr>
                            <tr>
                                <td style="line-height: 30px;" class="scale-center-both">

                                    <font style="font-family:Helvetica, Arial, sans-serif; line-height: 26px; font-size:14px; color:#000000;">You have successfully created a GardenLove account.<br/><br/>Please click on the link below to verify your email address and complete your registration.</font>

                                </td>
                            </tr>
                            <tr>
                                <td height="20"></td>
                            </tr>
                            <tr>
                                <td style="line-height: 30px;" class="scale-center-both">

                                    <a href="{!! url('user/activation', ['code'=>$verification_code]) !!}" style="color: #90cbf5;">
                                        <span style="color: #90cbf5;">Click Here</span>
                                    </a>

                                </td>
                            </tr>
                            <tr>
                                <td height="20"></td>
                            </tr>
                            <tr>
                                <td style="line-height: 30px;" class="scale-center-both">

                                    <font style="font-family:Helvetica, Arial, sans-serif; line-height: 26px; font-size:14px; color:#000000;">Or copy and paste this link into your browser</font>

                                </td>
                            </tr>
                            <tr>
                                <td height="15"></td>
                            </tr>
                            <tr>
                                <td style="line-height: 30px;" class="scale-center-both">

                                    <a href="javascript:void(0);" style="font-family:Helvetica, Arial, sans-serif; line-height: 26px; font-size:16px; color:#90cbf5; word-break: break-all;">{!! url('user/activation', ['code'=>$verification_code]) !!}</a>

                                </td>
                            </tr>
                            <tr>
                                <td height="20"></td>
                            </tr>
                            <tr>
                                <td class="scale-center">

                                    <p style="font-family:Helvetica, Arial, sans-serif; font-size:15px; color:#000000; line-height:2; font-style:italic; font-weight:600;">Thank you,<br/>GardenLove Team..</p>

                                </td>
                            </tr>
                            <tr><td style="border-bottom: 1px solid #d6d6d5;" height="54"></td></tr>
                        </table>

                    </td>
                </tr>
            </table>

        </td>
    </tr>
</table>

<!-- Footer -->
@include('includes.email_footer')

</td>
</tr>
</table>
<!-- End Main Wrapper -->

</body>
</html>