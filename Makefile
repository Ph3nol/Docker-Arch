.PHONY: test lint

lint:
	vendor/bin/coke

test:
	vendor/bin/atoum
