.PHONY: it
it: fix stan test docs ## Run the commonly used targets

.PHONY: help
help: ## Displays this list of targets with descriptions
	@grep --extended-regexp '^[a-zA-Z0-9_-]+:.*?## .*$$' $(firstword $(MAKEFILE_LIST)) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}'

.PHONY: setup
setup: vendor ## Set up the project

.PHONY: fix
fix: vendor ## Apply automatic code fixes
	# TODO fix PHP Fatal error:  Class PhpCsFixer\Fixer\Operator\AssignNullCoalescingToCoalesceEqualFixer contains 4 abstract methods and must therefore be declared abstract or implement the remaining methods (PhpCsFixer\Fixer\FixerInterface::isRisky, PhpCsFixer\Fixer\FixerInterface::fix, PhpCsFixer\Fixer\FixerInterface::getName, ...) in /home/bfranke/projects/laravel-enum/vendor/friendsofphp/php-cs-fixer/src/Fixer/Operator/AssignNullCoalescingToCoalesceEqualFixer.php on line 24
	#vendor/bin/php-cs-fixer fix

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
	composer update
	composer normalize
