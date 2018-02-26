#!/usr/bin/env bash
pwd
wp --path="/var/www/html" core install --url="http://localhost:8000" --title=test --admin_user=test --admin_password=test --admin_email=test@test.com --skip-email
wp --path="/var/www/html" plugin delete hello
wp --path="/var/www/html" plugin delete akismet
wp --path="/var/www/html" plugin activate woocommerce
wp --path="/var/www/html" plugin activate woocommerce-gateway-azul