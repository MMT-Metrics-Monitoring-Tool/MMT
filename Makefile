.PHONY: run run-test clean test enable-git-hooks

run:
	sudo docker compose --profile local up --build

run-test:
	sudo docker compose --profile testing up --build

clean:
	rm -rf ./tmp/*
	rm -rf ./vendor/*
	rm -rf ./logs/*
	sudo docker compose down -v

test:
	sudo docker compose exec cakephp-testing vendor/bin/phpunit

enable-git-hooks:
	git config --local include.path ../.gitconfig
	$(warning Custom hooks can run arbitary code, check .gitconfig files!)
