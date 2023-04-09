# Blog-IPGlobal

This is a skills test code. The idea is created a simple blog.

## Installation

1. Clone the repository.
2. Run `composer install`.
3. Configure the database. The STAGE and TEST databases must be the same.
4. Create the database with the command `php bin/console doctrine:database:create`.
5. Run the migrations with the command `php bin/console doctrine:migrations:migrate -n`.
6. Load the fixtures with the command `php bin/console doctrine:fixtures:load -n`.
7. Configure TEST_URL for testing (in the env.test file).
8. Run the tests.
9. The file `Blog.postman_collection.json` contains all endpoints, with example request for errors.
10. This blog has a admin panel zone, for access got to url `\admin`.
11. Have two user for log in this panel:
    1. `'email' => 'admin@email.test', 'password' => 'password1admin'` This user is admin and can see all articles and users, and active them.
    2. `'email' => 'test@email.test', 'password' => 'password1'` This user only see his articles.
12. The user must be activated by email, if the sent don't work (Is a three party email sender), the `admin` user can be activated in th panel control.
13. For the events send an email and generate of slugs of user and articles, must be use the command `php bin/console messenger:consume events -vv`
