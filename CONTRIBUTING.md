# How to contribute to laravel-enum

Hey, thank you for contributing. Here are some tips to make it easy for you.

## Committing code

1. Fork the project
1. `git clone` it and `composer install` the dependencies
1. Create a new branch
1. Think about how the changes you are about to make can be tested, write tests before coding 
1. Run tests, make sure they fail
1. Write the actual code to make the tests pass
1. Run checks with `make`
1. Open a pull request detailing your changes. Make sure to follow the [template](.github/PULL_REQUEST_TEMPLATE.md)

## Testing

We use [PHPUnit](https://phpunit.de) for automated tests.

Have a new feature? You can start off by writing some tests that detail
the behaviour you want to achieve and go from there.

Fixing a bug? The best way to ensure it is fixed for good and never comes
back is to write a failing test for it and then make it pass. If you can
not figure out how to fix it yourself, feel free to submit a PR with a
failing test.

Run the testsuite:

```sh
make test
```

## Codestyle

Formatting is automated through [php-cs-fixer](https://github.com/friendsofphp/php-cs-fixer).

Apply automated fixes:

```sh
make fix
```

## Static Analysis

We use [PHPStan](https://phpstan.org) for static analysis.

Run static analysis:

```sh
make stan
```
