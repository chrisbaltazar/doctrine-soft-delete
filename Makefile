build:
	@echo "Building the project..."
	docker build -t doctrine-soft-delete .

start:
	@echo "Starting the container..."
	-docker rm -f doctrine-soft-delete
	docker run --rm -d --name doctrine-soft-delete -v "./:/app" doctrine-soft-delete

shell:
	@echo "Accessing the container shell..."
	docker exec -it doctrine-soft-delete bash

stop:
	@echo "Stopping the container..."
	docker stop doctrine-soft-delete

demo-run:
	@echo "Running the demo app..."
	docker compose up -d

demo-run-dev:
	@echo "Running the demo app..."
	docker compose -f docker-compose.yml -f docker-compose.dev.yml up -d

demo-stop:
	@echo "Stopping the demo app..."
	docker compose down -v

test:
	@echo "Running SoftDeleteSmoke tests (Basic functionality, soft deletion)..."
	docker compose exec dsd-app composer test:smoke
	@echo "Running SoftDeleteUnique tests (Apply uniqueness to soft deleted records)..."
	docker compose exec dsd-app composer test:unique
	@echo "Running SoftDeleteNoDiff tests (No diff is generated in the schema as expected)..."
	docker compose exec dsd-app composer test:no-diff
	@echo "Restoring project files to their original state..."
	git restore . -- demo/ && git clean -fd -- demo/


