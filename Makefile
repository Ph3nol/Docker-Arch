.PHONY: test lint

lint:
	bin/coke

test:
	bin/atoum -v # To do: write tests!
