include .env
include .config

.DEFAULT_GOAL := help
.PHONY: help shell docker/up docker/down docker/ps docker/build composer/install composer/update composer/audit install update up down ps

define do_exec
    @docker-compose exec -T --user="${APP_RUN_USER}:${APP_RUN_GROUP}" app ${1}
endef

help: Makefile
	@echo
	@sed -n "s/^##//p" $< | column -t -s ":" | sed -e "s/^/ /"
	@echo

## shell: Запустит терминальную сессию в docker контейнере
shell: docker-compose.yml
	@docker-compose exec --user="${APP_RUN_USER}:${APP_RUN_GROUP}" app-test bash

## docker/up: Запустит docker контейнеры
docker/up: docker-compose.yml
	@docker-compose up --remove-orphans --detach

## docker/down: Остановит docker контейнеры
docker/down: docker-compose.yml
	@docker-compose down --remove-orphans

## docker/build: Соберет docker контейнеры
docker/build: docker-compose.yml
	@docker-compose up --build -d

## docker/ps: Выведет список docker контейнеров
docker/ps: docker-compose.yml
	@docker-compose ps

## composer/install: Установит composer пакеты
composer/install: app/var/www/composer.json
	$(call do_exec, composer install --working-dir=/var/www/ --ignore-platform-req=ext-imap ${options})

## composer/update: Обновит composer пакеты
composer/update: app/var/www/composer.lock
	$(call do_exec, composer update --working-dir=/var/www/ ${options})

## composer/audit: Проверит на уязвимости composer пакеты
composer/audit: app/var/www/composer.json
	$(call do_exec, composer audit --working-dir=/var/www/ ${options})

## crm/migrate/up: Применит общие миграции
yii/migrate/up: app/yii
	$(call do_exec, ${YII_BINARY} migrate/up)

## yii/cache/flush-all: Очистит кэш приложения
yii/cache/flush-all: app/yii
	$(call do_exec, ${YII_BINARY} cache/flush-all)

## yii/install: Установит приложение
yii/install: yii/migrate/up yii/cache/flush-all

## install: Установит composer пакеты и приложение
install: composer/install yii/install

## update: Обновит composer пакеты и приложение
update: composer/update yii/install

## up: Запустит контейнеры
up: docker/build docker/up

## down: Остановит контейнеры
down: docker/down