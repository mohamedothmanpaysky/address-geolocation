Clone the project repository to your local machine:

``
git clone <repository_url>
``
Change into the project directory:


cd <project_directory>
Copy the .env.example file and rename it to .env:


cp .env.example .env
Open the .env file and configure the necessary environment variables such as database connection details, mail settings, etc.
Run the Sail installation command:


./vendor/bin/sail up -d
This command will start the Docker containers defined in the docker-compose.yml file and run the Laravel project.
Access the Laravel project in your web browser:


http://localhost
home page wll be download the file


run unit test
sail artisan test

