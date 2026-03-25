jQuery(document).ready(function ($) {

    $(document).on('click', '.whatsapp-order-btn', function (e) {
        e.preventDefault();

        var $btn = $(this);
        var phone = $btn.data('phone');
        var $product = $btn.closest('.product');
        var productName = $product.find('.woocommerce-loop-product__title').first().text().trim() || $('h1.product_title').first().text().trim();
        var sku = $product.find('.sku').first().text().trim() || $('span.sku').first().text().trim();
        var productUrl = $product.find('a.woocommerce-LoopProduct-link').attr('href') || window.location.href;

        var qty = 1;
        var $qtyInput = $product.find('input.qty').first();
        if ($qtyInput.length && parseInt($qtyInput.val(), 10) > 0) {
            qty = parseInt($qtyInput.val(), 10);
        }

        var variationData = '';
        var variationSelected = true;

        var $variationForm = $product.find('.variations_form');

        // ONLY fallback on single product page
        if (!$variationForm.length && $('body').hasClass('single-product')) {
            $variationForm = $('.variations_form');
        }

        $variationForm.find('select').each(function () {
            var label = $(this).closest('tr').find('label').text().trim();
            var value = $(this).find('option:selected').text().trim();

            if (!$(this).val()) {
                variationSelected = false;
            }

            if (value && $(this).val()) {
                variationData += label + ': ' + value + '\n';
            }
        });

        var price = '';
        if ($variationForm.length && $variationForm.find('select').length) {
            if (!variationSelected) {
                alert('Please select product variation');
                return;
            }
            price = $product.find('.single_variation .price').text().trim() || $('.single_variation .price').text().trim();
        } else {
            price = $product.find('.price').first().text().trim() || $('.price').first().text().trim();
        }

        if (!productName) {
            productName = 'Product';
        }

        var message = 'Hello\n\n';
        message += 'I want to order this product.\n\n';
        message += 'Product: ' + productName + '\n';
        message += 'Product SKU : ' + sku + '\n';
        message += 'Price: ' + price + '\n';
        message += 'Quantity: ' + qty + '\n\n';

        if (variationData) {
            message += variationData + '\n';
        }

        message += '🔗 Product Link:\n' + productUrl;

        var url = 'https://wa.me/' + phone + '?text=' + encodeURIComponent(message);

        window.open(url, '_blank');
    });

});