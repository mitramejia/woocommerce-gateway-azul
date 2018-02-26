<?php
/* =============================================================
//
// Name: wc-azul-payment-form.php
// -> Description: 
//
// Author: mitramejia 
// Created at: 5/8/17
//
// ============================================================= */

/*
 * form_template ()
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
$form_fields = $template_data;
?>
<p> <?php __('Thank you for your order, please click the button below to pay with Azul.',
    'woocommerce-gateway-azul') ?></p>
<form action="<?php echo $form_fields['FormAction'] ?>" method="post" name="paymentForm"> 
    <?php unset($form_fields['FormAction']) ?>
    <?php foreach ($form_fields as $key => $value): ?>
        <input type="hidden" id="<?php echo $key ?>" name="<?php echo $key ?>" value="<?php echo $value ?>">
    <?php endforeach; ?>
    <input class="checkout-button button alt wc-forward" style="height:50px; font-size:18px; width:240px;" type="submit" value="Pagar en Azul"> 
</form>
<br><br>