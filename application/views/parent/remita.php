<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#424242" />
        <title><?php echo $this->customlib->getAppName(); ?></title> 
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/bootstrap/css/bootstrap.min.css"> 
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/font-awesome.min.css"> 
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/style-main.css"> 
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/loader.css">

        <script src="<?php echo base_url(); ?>backend/custom/jquery.min.js"></script> 
        <script src="<?php echo base_url(); ?>backend/dist/js/jquery-ui.min.js"></script>
        <!-- <script src="<?php echo base_url(); ?>backend/js/sstoast.js"></script> -->
    </head>
    <body style="background: #ededed;">
        <div class="container">
            <div class="row">

                <div class="paddtop20">
                    <div class="col-md-8 col-md-offset-2 text-center">
                        <img src="<?php echo base_url('uploads/school_content/logo/' . $setting[0]['image']); ?>">
                    </div>
                    <div class="col-md-6 col-md-offset-3 mt20">
                        <div class="text-center"> 
                            <img src="<?php echo base_url() ?>backend/images/remita-payment-options.png" width="450">  
                        </div>
                        <br>
                        <div class="paymentbg">
                            <div class="invtext" id="fees-payment-title">Fees Payment Details</div>
                            <div class="padd2 paddtzero">
                                <table class="table2" width="100%">
                                    <tr>
                                        <th>Description</th>
                                        <th class="text-right">Amount</th>
                                    </tr>
                                    <tr>
                                        <td> <?php
                                            echo $payment_detail->fee_group_name . "<br/><span>" . $payment_detail->code;
                                            ?></span></td>
                                        <td class="text-right"><?php echo $setting[0]['currency_symbol'] . $total; ?></td>
                                    </tr>

                                    <tr class="bordertoplightgray">
                                        <td colspan="2" bgcolor="#fff" class="text-right">Total: <?php echo $setting[0]['currency_symbol'] . $total; ?></td>
                                    </tr>
                                </table>
                                <div class="divider"></div> 
                                <div class="paddtlrb">
                                    <span id="notification"></span>
                                    <button type="button" onclick="window.history.go(-1); return false;" name="search"  value="" class="btn btn-info"><i class="fa fa fa-chevron-left"></i> Back</button>  
                                    <button type="submit" id="paybtn" class="btn btn-warning pull-right"><i class="fa fa fa-money"></i> Pay Now </button> 
                                </div>
                            </div>
                        </div>
                    </div>
                </div>  
            </div>
        </div>
   
        <script type="text/javascript">
            
            var $loader = '<div class="loader"><div id="loader-1"> <span></span> <span></span> <span></span> </div></div>'; 
            // var paymentForm = document.getElementById('paymentForm');
            var paymentBtn = document.getElementById('paybtn');
            // paymentForm.addEventListener("submit", payWithPaystack, false);
            paymentBtn.addEventListener("click", makePayment); 
            
            function makePayment() {

                $('#fees-payment-title').prepend($loader);
     
                var paymentEngine = RmPaymentEngine.init({
                    key: 'REVNT05UR0lGVHw0MDgyNTIxNHwxZTI1NGNlNTVhMzkyYTgxYjYyNjQ2ZWIwNWU0YWE4ZTNjOTU0ZWFlODllZGEwMTUwMjYyMTk2ZmFmOGMzNWE5ZGVjYmU3Y2JkOGI5ZWI5YzFmZWMwYTI3MGI5MzA0N2FjZWEzZDhiZjUwNDY5YjVjOGY3M2NhYjQzMTg3NzI4Mg==',
                    customerId: '<?php echo $email; ?>',
                    firstName: '<?php echo $firstname; ?>',
                    lastName: '<?php echo $lastname; ?>',
                    email: '<?php echo $email; ?>',
                    amount: '<?php echo $total*100; ?>',
                    narration: 'This is a demo payment',
                    onSuccess: function (response) {
                        console.log('callback Successful Response', response);
                        
                        $('.loader').remove();
                    },
                    onError: function (response) {
                        console.log('callback Error Response', response);
                    },
                    onClose: function () {
                        console.log("closed");

                        $('.loader').remove();
                    }
                });
                paymentEngine.showPaymentWidget();
            }

            // function payWithPaystack() {

            //     $('#fees-payment-title').prepend($loader);

            //     var handler = PaystackPop.setup({
            //         key: '<?php echo $api_publishable_key; ?>', // Replace with your public key
            //         email: '<?php echo $email; ?>',
            //         currency: '<?php echo $setting[0]['currency'] ?>',
            //         amount: '<?php echo $total*100; ?>',
            //         firstname: '<?php echo $firstname; ?>',
            //         lastname: '<?php echo $lastname; ?>',
            //         ref: '<?php echo $reference; ?>lse',  
            //         // label: "Optional string that replaces customer email"
            //         onClose: function(){
            //             $('.loader').remove();
            //         },
            //         callback: function(response){
            //             $.ajax({
            //                 url: '<?php echo base_url("parent/paystack/process")?>?reference=' + response.reference,
            //                 method: 'post',
            //                 dataType: 'json',
            //                 success: function (data) {
            //                     if (data.status === 0 && data.header !== '') {
            //                         window.location.href = data.header+'<?php echo $student_id; ?>';
            //                     } else if (data.status === 1 && data.header !== '') {
            //                         // successMsg(data.response.short_message);
            //                         $('#notification').html('<div class="alert alert-success">'+data.response.message+'</div>');
            //                         window.location.replace(data.header);
            //                     } else {
            //                         $('#notification').html('<div class="alert alert-info">'+data.response+'</div>');
            //                     }
            //                     $('.loader').remove();
            //                 }
            //             });
            //         }
            //     });
                  
            //     handler.openIframe();
            // } 
        </script>
        <script type="text/javascript" src="https://remitademo.net/payment/v1/remita-pay-inline.bundle.js"></script>
    </body>  
</html>
