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