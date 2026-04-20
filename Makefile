build:
	@echo "Building the project..."
	docker build -t doctrine-soft-delete .

start:
	@echo "Starting the container..."
	docker run -d --name doctrine-soft-delete doctrine-soft-delete

shell:
	@echo "Accessing the container shell..."
	docker exec -it doctrine-soft-delete bash

stop:
	@echo "Stopping the container..."
	docker stop doctrine-soft-delete