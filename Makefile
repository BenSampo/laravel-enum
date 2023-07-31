.PHONY: it
it: fix stan test docs ## Run the commonly used targets

.PHONY: help
help: ## Displays this list of targets with descriptions
	@grep -E '^[a-zA-Z0-9_-]+:.*?## .*$$' $(firstword $(MAKEFILE_LIST)) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}'

.PHONY: fix
fix: vendor ## Apply automatic code fixes
	vendor/bin/php-cs-fixer fix

.PHONY: stan
stan: vendor ## Runs a static analysis with phpstan
	vendor/bin/phpstan

.PHONY: test
test: vendor ## Runs tests with phpunit
	vendor/bin/phpunit --testsuite=Tests
	vendor/bin/phpunit --testsuite=Rector

docs: ## Generate documentation
	vendor/bin/rule-doc-generator generate src/Rector --output-file=rector-rules.md

vendor: composer.json
	composer validate --strict
	composer install
	composer normalize
