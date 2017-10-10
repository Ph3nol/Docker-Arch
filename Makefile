.PHONY: qa

qa:
	bin/phpqa --analyzedDirs src,tests --output cli

clean-examples:
	@find examples -type d -name ".docker-arch" | xargs rm -rf
	@find examples -type d -name "vendor" | xargs rm -rf
	@find examples -type d -name ".bundle" | xargs rm -rf
	@find examples -type d -name "log" | xargs rm -rf
	@find examples -type d -name "logs" | xargs rm -rf
