.PHONY: it
it: fix stan test ## Run the commonly used targets

.PHONY: help
help: ## Displays this list of targets with descriptions
	@grep -E '^[a-zA-Z0-9_-]+:.*?## .*$$' $(firstword $(MAKEFILE_LIST)) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}'

.PHONY: fix
fix: vendor
	vendor/bin/php-cs-fixer fix

.PHONY: stan
stan: vendor ## Runs a static analysis with phpstan
	vendor/bin/phpstan

.PHONY: test
test: vendor ## Runs tests with phpunit
	vendor/bin/phpunit --testsuite=Tests
	vendor/bin/phpunit --testsuite=Rector

vendor: composer.json
	composer validate --strict
	composer install
	composer normalize
