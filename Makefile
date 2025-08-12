.PHONY: phpcs lint

lint:
	phplint application/ library/
phpcs:
	phpcs
