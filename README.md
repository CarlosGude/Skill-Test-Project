# Skill Test Project
[![SymfonyInsight](https://insight.symfony.com/projects/76f2d46a-1480-4fdf-8f40-27a9e6e7e2b7/big.svg)](https://insight.symfony.com/projects/76f2d46a-1480-4fdf-8f40-27a9e6e7e2b7)

This is a skills test code. The idea is created a simple blog.

## Installation

1. Clone the repository.
2. Run `composer install`.
3. Configure the database. The STAGE and TEST databases must be the same.
4. Generate pair key for auth `php bin/console lexik:jwt:generate-keypair`
5. Create the database with the command `php bin/console doctrine:database:create`.
6. Run the migrations with the command `php bin/console doctrine:migrations:migrate -n`.
7. Load the fixtures with the command `php bin/console doctrine:fixtures:load -n`.
8. Configure TEST_URL for testing (in the env.test file).
9. Run the tests.
10. The file `Blog.postman_collection.json` contains all endpoints, with example request for errors.
11. This blog has a admin panel zone, for access got to url `\admin`.
12. Have two user for log in this panel:
    1. `'email' => 'admin@email.test', 'password' => 'password1admin'` This user is admin and can see all articles and users, and active them.
    2. `'email' => 'test@email.test', 'password' => 'password1'` This user only see his articles.
13. Email must activate the user, if the sent don't work (Is a three party email sender), the `admin` user can be activate users in the panel control.
14. For the events send an email and generate of slugs of user and articles, must be use the command `php bin/console messenger:consume events -vv`
