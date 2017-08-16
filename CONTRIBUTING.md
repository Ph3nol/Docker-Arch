# CONTRIBUTING

This part is inspired by [M6Web](https://github.com/M6Web) CONTRIBUTING files.

## Developing

The features available for now are only those I need, but you're welcome to open an issue
or pull-request if you need more, write tests or fix bugs.

### Download Dev dependencies

```bash
composer install
```

### Lint

To ensure good code quality, I use awesome M6Web tool "[coke](https://github.com/M6Web/Coke)" to check there is no coding standards violations. 
We use [Symfony2 coding standards](https://github.com/M6Web/Symfony2-coding-standard).

```bash
make lint
# or run 'vendor/bin/coke' if you don't have 'make'
```

### Tests

This bundle is tested with [atoum](https://github.com/atoum/atoum).

```bash
make test
# or run 'vendor/bin/atoum' if you don't have 'make'
```
