# ==============================================================================
# Shell Configuration
# ==============================================================================
SHELL = /bin/bash

# ==============================================================================
# Color Definitions
# ==============================================================================
RESET   := \033[0m
BOLD    := \033[1m
BLACK   := \033[0;30m
RED     := \033[0;31m
GREEN   := \033[0;32m
YELLOW  := \033[0;33m
BLUE    := \033[0;34m
MAGENTA := \033[0;35m
CYAN    := \033[0;36m
WHITE   := \033[0;37m

# ==============================================================================
# Color Combinations
# ==============================================================================
TARGET_COLOR      := $(BOLD)$(YELLOW)
INFRA_TARGET_COLOR:= $(BOLD)$(GREEN)
QUEUE_TARGET_COLOR:= $(BOLD)$(CYAN)
QA_TARGET_COLOR   := $(BOLD)$(RED)
DESCRIPTION_COLOR := $(CYAN)
HEADER_COLOR      := $(BOLD)$(BLUE)
USAGE_CMD_COLOR   := $(GREEN)
USAGE_ARG_COLOR   := $(YELLOW)
SECTION_COLOR     := $(BOLD)$(MAGENTA)

# ==============================================================================
# Service Name
# ==============================================================================
SERVICE ?= app

# ==============================================================================
# Phony Targets Declaration
# ==============================================================================
.PHONY: help default docker infra-up infra-stop infra-logs infra-clean infra-shell infra-bash
.PHONY: queue-start queue-stop queue-restart queue-status queue-clear queue-failed

# ==============================================================================
# Default Target
# ==============================================================================
default: help ##@ Show this help message (default target)

# ==============================================================================
# Docker Help
# ==============================================================================
docker: ##@ Show Docker specific help and commands
	@printf "$(HEADER_COLOR)Docker Commands$(RESET):\n"
	@printf "\n"
	@printf "$(TARGET_COLOR)Usage: make <infra-target>$(RESET)\n"
	@printf "\n"
	@printf "$(SECTION_COLOR)Available Docker targets:$(RESET)\n"
	@grep -E '^infra-[a-zA-Z0-9_-]+[[:space:]]*:[[:space:]]*##@' $(MAKEFILE_LIST) | \
    awk -v target_color="$(INFRA_TARGET_COLOR)" \
        -v description_color="$(DESCRIPTION_COLOR)" \
        -v reset_color="$(RESET)" \
        'BEGIN {FS = ":.*?##@"}; \
        {printf "  %s%-25s%s - %s%s%s\n", target_color, $$1, reset_color, description_color, $$2, reset_color}' | \
    sort

# ==============================================================================
# Help Target (Main)
# ==============================================================================
help: ##@ Show this help message
	@printf "$(HEADER_COLOR)Usage: make <target>$(RESET)\n"
	@printf "\n"

	@printf "$(SECTION_COLOR)General Targets:$(RESET)\n"
	@grep -E '^[a-zA-Z0-9_-]+[[:space:]]*:[[:space:]]*##@' $(MAKEFILE_LIST) | \
    grep -vE '^(infra-|queue-)' | \
    awk -v target_color="$(TARGET_COLOR)" \
        -v description_color="$(DESCRIPTION_COLOR)" \
        -v reset_color="$(RESET)" \
        'BEGIN {FS = ":.*?##@"}; \
        {printf "  %s%-25s%s - %s%s%s\n", target_color, $$1, reset_color, description_color, $$2, reset_color}' | \
    sort

	@printf "\n"

	@printf "$(SECTION_COLOR)Infrastructure Targets:$(RESET)\n"
	@grep -E '^infra-[a-zA-Z0-9_-]+[[:space:]]*:[[:space:]]*##@' $(MAKEFILE_LIST) | \
    awk -v target_color="$(INFRA_TARGET_COLOR)" \
        -v description_color="$(DESCRIPTION_COLOR)" \
        -v reset_color="$(RESET)" \
        'BEGIN {FS = ":.*?##@"}; \
        {printf "  %s%-25s%s - %s%s%s\n", target_color, $$1, reset_color, description_color, $$2, reset_color}' | \
    sort

	@printf "\n"

	@printf "$(SECTION_COLOR)Queue Targets:$(RESET)\n"
	@grep -E '^queue-[a-zA-Z0-9_-]+[[:space:]]*:[[:space:]]*##@' $(MAKEFILE_LIST) | \
    awk -v target_color="$(QUEUE_TARGET_COLOR)" \
        -v description_color="$(DESCRIPTION_COLOR)" \
        -v reset_color="$(RESET)" \
        'BEGIN {FS = ":.*?##@"}; \
        {printf "  %s%-25s%s - %s%s%s\n", target_color, $$1, reset_color, description_color, $$2, reset_color}' | \
    sort

# ==============================================================================
# Docker Targets
# ==============================================================================
infra-up: ##@ Start the Docker infrastructure
	@printf "$(HEADER_COLOR)Starting Docker infrastructure...$(RESET)\n"
	@docker compose up -d --build

infra-stop: ##@ Stop the Docker infrastructure
	@printf "$(HEADER_COLOR)Stopping Docker infrastructure...$(RESET)\n"
	@docker compose down

infra-logs: ##@ Show logs from the Docker infrastructure
	@printf "$(HEADER_COLOR)Showing Docker logs...$(RESET)\n"
	@docker-compose logs -f

infra-clean: ##@ Clean up Docker containers and images
	@printf "$(HEADER_COLOR)Cleaning up Docker containers and images...$(RESET)\n"
	@docker-compose down --rmi all

infra-shell: ##@ Open a shell in the Docker container
	@printf "$(HEADER_COLOR)Opening shell in Docker container...$(RESET)\n"
	@docker-compose exec $(SERVICE) sh

infra-bash: ##@ Open a bash shell in the Docker container
	@printf "$(HEADER_COLOR)Opening bash shell in Docker container...$(RESET)\n"
	@docker compose exec $(SERVICE) bash

# ==============================================================================
# Queue Targets
# ==============================================================================
queue-start: ##@ Start all queue workers (1 sitemap + 20 crawl workers)
	@printf "$(HEADER_COLOR)Starting queue workers...$(RESET)\n"
	@printf "$(GREEN)Starting 1 sitemap worker...$(RESET)\n"
	@docker-compose exec -d $(SERVICE) php artisan queue:work --queue=default --tries=2 --timeout=120
	@printf "$(GREEN)Starting 20 crawl workers...$(RESET)\n"
	@for i in {1..20}; do \
		docker-compose exec -d $(SERVICE) php artisan queue:work --queue=crawl --tries=2 --timeout=90 --sleep=0; \
	done
	@printf "$(GREEN)✓ All queue workers started successfully!$(RESET)\n"

queue-stop: ##@ Stop all queue workers
	@printf "$(HEADER_COLOR)Stopping queue workers...$(RESET)\n"
	@docker-compose exec $(SERVICE) php artisan queue:restart
	@docker-compose exec $(SERVICE) pkill -f "artisan queue:work" || true
	@printf "$(GREEN)✓ All queue workers stopped$(RESET)\n"

queue-restart: queue-stop queue-start ##@ Restart all queue workers

queue-status: ##@ Show queue workers status and jobs count
	@printf "$(HEADER_COLOR)Queue Status:$(RESET)\n"
	@printf "\n$(YELLOW)Running Workers:$(RESET)\n"
	@docker-compose exec $(SERVICE) ps aux | grep "artisan queue:work" | grep -v grep || printf "$(RED)No workers running$(RESET)\n"
	@printf "\n$(YELLOW)Pending Jobs:$(RESET)\n"
	@docker-compose exec $(SERVICE) php artisan queue:monitor default,crawl || true
	@printf "\n$(YELLOW)Failed Jobs Count:$(RESET)\n"
	@docker-compose exec $(SERVICE) php artisan queue:failed | wc -l || printf "$(GREEN)0$(RESET)\n"

queue-clear: ##@ Clear all pending jobs from the queue
	@printf "$(HEADER_COLOR)Clearing queue...$(RESET)\n"
	@docker-compose exec $(SERVICE) php artisan queue:clear
	@printf "$(GREEN)✓ Queue cleared$(RESET)\n"

queue-failed: ##@ Show failed jobs
	@printf "$(HEADER_COLOR)Failed Jobs:$(RESET)\n"
	@docker-compose exec $(SERVICE) php artisan queue:failed

queue-retry: ##@ Retry all failed jobs
	@printf "$(HEADER_COLOR)Retrying failed jobs...$(RESET)\n"
	@docker-compose exec $(SERVICE) php artisan queue:retry all
	@printf "$(GREEN)✓ All failed jobs queued for retry$(RESET)\n"

queue-flush: ##@ Delete all failed jobs
	@printf "$(HEADER_COLOR)Flushing failed jobs...$(RESET)\n"
	@docker-compose exec $(SERVICE) php artisan queue:flush
	@printf "$(GREEN)✓ Failed jobs flushed$(RESET)\n"

queue-logs: ##@ Show queue workers logs (live)
	@printf "$(HEADER_COLOR)Showing queue logs...$(RESET)\n"
	@docker-compose exec $(SERVICE) tail -f storage/logs/laravel.log