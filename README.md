## Woocommerce Payment Gateway for Azul

[Azul](https://www.azul.com.do/Pages/es/default.aspx) is a payment gateway from the dominican bank, Popular. This project is a wordpress plugin to allow payments through this bank's payment page product.

## Requirements

Get your connection credentials from the bank. 

- Test api key
- Production api
- Provide them with your test and production domain


## Contribution

Read [code of conduct](https://www.contributor-covenant.org/version/1/4/code-of-conduct.html). Install [docker](https://docs.docker.com/install/)


Start containers
`docker-compose up -d`

Setup wordpress and activate plugins
`docker-compose exec wordpress bash bin/wp-setup.sh`

See your wordpress installation at `http://localhost:8000`
