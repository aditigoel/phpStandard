@include('includes.email_header')

<!-- Intro -->
<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center" style="width:100%;">
    <tr>
        <td bgcolor="#f1f1f1">

            <table width="660" border="0" cellspacing="0" cellpadding="0" align="center" class="scale">

                <tr>
                    <td bgcolor="#FFFFFF">
                        <table width="600" border="0" cellspacing="0" cellpadding="0" align="center" style="font-family:Helvetica, Arial, sans-serif; font-size: 14px; color: #000000;" class="scale">

                            <tr>
                                <td colspan="2" height="30"></td>
                            </tr>
                            <tr>
                                <td bgcolor="#FFFFFF" width="150px">

                                    <font style="font-family:Helvetica, Arial, sans-serif; font-size: 15px; color: #1b3044; font-weight:600;"> Name :</font>


                                </td>
                                <td bgcolor="#FFFFFF">

                                    <font style="font-family:Helvetica, Arial, sans-serif; line-height: 26px; font-size:14px; color:#000000;"><?php echo ucfirst($data['name']); ?></font>


                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" height="15"></td>
                            </tr>
                            <tr>
                                <td bgcolor="#FFFFFF" width="150px">


                                    <font style="font-family:Helvetica, Arial, sans-serif; font-size: 15px; color: #1b3044; font-weight:600;">Subject:</font>

                                </td>
                                <td bgcolor="#FFFFFF">


                                    <font style="font-family:Helvetica, Arial, sans-serif; line-height: 26px; font-size:14px; color:#000000;"><?php echo $data['subject']; ?></font>

                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" height="15"></td>
                            </tr>
                            <tr>
                                <td bgcolor="#FFFFFF" width="150px">


                                    <font style="font-family:Helvetica, Arial, sans-serif; font-size: 15px; color: #1b3044; font-weight:600;">Message:</font>

                                </td>
                                <td bgcolor="#FFFFFF">


                                    <font style="font-family:Helvetica, Arial, sans-serif; line-height: 26px; font-size:14px; color:#000000;">  <?php echo $data['message']; ?></font>

                                </td>
                            </tr>
                          
                            <tr>
                                <td colspan="2" height="30"></td>
                            </tr>
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