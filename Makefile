# LatchPHP - common tasks.
#
#   make setup     one command: install deps, start containers, import schema, seed db
#   make seed      reseed the database to a known state
#   make export    dump the running database to database/dumps/
#   make help      list every target
#
# Requires: docker + docker compose ( PHP 8 + composer run inside the container ).
# vendor/ is bind-mounted, so deps installed in the container also appear on the host.

COMPOSE ?= docker compose
WEB     := $(COMPOSE) exec webserver
WEB_T   := $(COMPOSE) exec -T webserver
DB      := $(COMPOSE) exec -T database

# database credentials - read straight from .env ( compose's source of truth ),
# falling back to the dev defaults when a key, or .env itself, is absent.
# Override on the CLI too:  make seed DB_ROOT_PASS=secret
env-or        = $(or $(shell [ -f .env ] && sed -n 's/^$(1)=//p' .env | tail -n1 | sed -e 's/^"//' -e 's/"$$//'),$(2))
DB_USER      := $(call env-or,DB_USER,starter)
DB_PASS      := $(call env-or,DB_PASS,starter)
DB_NAME      := $(call env-or,DB_NAME,starter)
DB_ROOT_PASS := $(call env-or,DB_ROOT_PASS,root)

# timestamp used for database exports
STAMP := $(shell date +%Y%m%d-%H%M%S)

.DEFAULT_GOAL := help

# optional local overrides; ignored when the file is absent
-include release.mk

help: ## List the available targets
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN{FS=":.*?## "}{printf "  \033[36m%-10s\033[0m %s\n", $$1, $$2}'

## --- setup ---------------------------------------------------------------

setup: up install wait-db seed ## Full first-run: start + install deps (in container) + wait for db + seed
	@echo ""
	@echo "Done. Open https://localhost (accept the self-signed certificate)."
	@echo "Sign in with the seeded super-admin: superadmin@example.com / Password123!"

install: up ## Install PHP dependencies with composer, inside the container
	$(COMPOSE) exec -T -w /var/www webserver composer install

up: ## Build and start the containers (db auto-imports database/database.sql on first run)
	$(COMPOSE) up -d --build

down: ## Stop and remove the containers
	$(COMPOSE) down

destroy: ## Stop containers and wipe all data (removes the db volume)
	$(COMPOSE) down -v

restart: down up ## Restart the containers

wait-db: ## Block until the database accepts connections
	@echo "Waiting for database..."
	@until $(DB) mariadb -u$(DB_USER) -p$(DB_PASS) -e "SELECT 1" $(DB_NAME) >/dev/null 2>&1; do sleep 1; done
	@echo "Database ready."

## --- database ------------------------------------------------------------

seed: ## Reseed: drop+recreate the db, import database/database.sql, run the ACL/demo seeder
	@echo "Reseeding database..."
	@$(DB) mariadb -uroot -p$(DB_ROOT_PASS) -e "DROP DATABASE IF EXISTS $(DB_NAME); CREATE DATABASE $(DB_NAME) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
	@$(DB) mariadb -uroot -p$(DB_ROOT_PASS) $(DB_NAME) < database/database.sql
	@$(WEB_T) php /var/www/database/seed.php
	@echo "Database re-seeded (super-admin: superadmin@example.com / Password123!)."

migrate: ## Apply migrations/*.sql to the running db (idempotent schema upgrades)
	@for f in $(sort $(wildcard migrations/*.sql)); do \
		echo "  applying $$f"; \
		$(DB) mariadb -uroot -p$(DB_ROOT_PASS) $(DB_NAME) < $$f; \
	done
	@echo "Migrations applied."

export: ## Export the running database to database/dumps/database-<timestamp>.sql
	@mkdir -p database/dumps
	@$(DB) mariadb-dump -uroot -p$(DB_ROOT_PASS) --single-transaction $(DB_NAME) > database/dumps/database-$(STAMP).sql
	@echo "Exported -> database/dumps/database-$(STAMP).sql"

reset: destroy up wait-db seed ## Full wipe: recreate the db volume, import schema + seed
	@echo "Database reset."

## --- operations ----------------------------------------------------------

admin: ## Create an additional user/admin account (interactive)
	$(WEB) php /var/www/createUser.php

cron: ## Run any due scheduled jobs once
	$(WEB) php /var/www/cron.php

backup: ## Back up the database + source into ./backup/
	$(COMPOSE) exec -w /var/www webserver php /var/www/backup.php

## --- export --------------------------------------------------------------

EXPORTS_DIR ?= exports

# internal working docs not shipped in a clean copy ( empty this var to include them:
#   make new-project DOC_EXCLUDES= )
DOC_EXCLUDES := --exclude='research.md' --exclude='known-bugs.md' --exclude='plan-*.md' --exclude='names.md'

# optional per-copy overrides
EXTRA_EXCLUDES      ?=
EXPORT_COMMIT_MSG   ?=
EXPORT_AUTHOR_NAME  ?=
EXPORT_AUTHOR_EMAIL ?=
EXPORT_GOALS      := new-project $(EXTRA_EXPORT_GOALS)

# allow a positional project name:  make new-project my-app
# ( treat the extra goal(s) as the name and stub them as no-op targets so make won't try to
#   build them - scoped to the export targets so no other target is affected )
ifneq ($(filter $(EXPORT_GOALS),$(firstword $(MAKECMDGOALS))),)
EXPORT_NAME := $(wordlist 2,$(words $(MAKECMDGOALS)),$(MAKECMDGOALS))
ifneq ($(EXPORT_NAME),)
$(eval $(EXPORT_NAME):;@:)
endif
endif

new-project: ## Clean, timestamped copy of the source into exports/  (usage: make new-project <name>)
	@command -v rsync >/dev/null || { echo "rsync is required."; exit 1; }
	@name="$(EXPORT_NAME)"; \
	if [ -z "$$name" ]; then name="$(NAME)"; fi; \
	if [ -z "$$name" ]; then read -p "Project name: " name; fi; \
	slug=$$(printf '%s' "$$name" | tr '[:upper:]' '[:lower:]' | tr ' ' '-' | tr -cd 'a-z0-9-_'); \
	if [ -z "$$slug" ]; then echo "Aborted: please provide a name."; exit 1; fi; \
	dest="$(EXPORTS_DIR)/$$slug-$(STAMP)"; \
	if [ -e "$$dest" ]; then echo "Aborted: $$dest already exists."; exit 1; fi; \
	echo "Creating clean copy -> $$dest"; \
	mkdir -p "$$dest"; \
	rsync -a \
		--include='/logs/.gitkeep' \
		--filter=':- .gitignore' \
		--exclude='.git' \
		--exclude='.env' \
		--exclude='$(EXPORTS_DIR)' \
		$(DOC_EXCLUDES) \
		$(EXTRA_EXCLUDES) \
		./ "$$dest/"; \
	cp .env.example "$$dest/.env"; \
	msg="$(EXPORT_COMMIT_MSG)"; \
	if [ -z "$$msg" ]; then msg="Fresh export: $$slug ($(STAMP))"; fi; \
	an="$(EXPORT_AUTHOR_NAME)"; ae="$(EXPORT_AUTHOR_EMAIL)"; \
	if [ -n "$$an" ]; then export GIT_AUTHOR_NAME="$$an" GIT_COMMITTER_NAME="$$an"; fi; \
	if [ -n "$$ae" ]; then export GIT_AUTHOR_EMAIL="$$ae" GIT_COMMITTER_EMAIL="$$ae"; fi; \
	if command -v git >/dev/null; then \
		( cd "$$dest" && git init -q && git add -A && git commit -qm "$$msg" ) \
			&& echo "Initialised a fresh git repo in the copy."; \
	else \
		echo "(git not found; skipped git init)"; \
	fi; \
	echo ""; \
	echo "Done. Fresh copy at $$dest"; \
	echo "Next:  cd $$dest && make setup"

## --- dev helpers ---------------------------------------------------------

lint: ## Lint every PHP file (on the host)
	@find app config database helpers routes views public plugins createUser.php cron.php backup.php -name '*.php' -print0 \
		| xargs -0 -n1 php -l >/dev/null && echo "PHP: all files OK"

shell: ## Open a shell in the web container
	$(WEB) bash

db: ## Open a database shell
	$(COMPOSE) exec database mariadb -u$(DB_USER) -p$(DB_PASS) $(DB_NAME)

logs: ## Follow the web server logs
	$(COMPOSE) logs -f webserver

.PHONY: help setup install up down destroy restart wait-db seed migrate export reset admin cron backup new-project lint shell db logs
