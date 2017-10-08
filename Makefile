.PHONY: test lint

qa:
	bin/phpqa --analyzedDirs src,tests --output cli

test:
	bin/atoum -v # To do: write tests!

clean-examples:
	@find examples -type d -name ".docker-arch" | xargs rm -rf
	@find examples -type d -name "vendor" | xargs rm -rf
	@find examples -type d -name ".bundle" | xargs rm -rf
	@find examples -type d -name "log" | xargs rm -rf
	@find examples -type d -name "logs" | xargs rm -rf
