# Emma Backend

<!-- ## Installation

1. Download [Composer](http://getcomposer.org/doc/00-intro.md) or update `composer self-update`.
2. Run `php composer.phar create-project --prefer-dist cakephp/app [app_name]`.
3. Clone this repo to your preffered location by

If Composer is installed globally, run

```bash
composer create-project --prefer-dist cakephp/app
```

In case you want to use a custom app dir name (e.g. `/myapp/`):

```bash
composer create-project --prefer-dist cakephp/app myapp
```

You should now be able to visit the path to where you installed the app and see the default home page.

### Installation of 3.next

In case you want to try the unstable branch:

```bash
composer create-project --prefer-dist cakephp/app=dev-3.next app
```

You may then install specific RC, for example:

```bash
cd app;
composer require cakephp/cakephp:3.4.0-RC3
```

## Update

Since this skeleton is a starting point for your application and various files would have been modified as per your needs, there isn't a way to provide automated upgrades, so you have to do any updates manually.

## Configuration

Read and edit `config/app.php` and setup the `'Datasources'` and any other
configuration relevant for your application.

## Layout
The app skeleton uses a subset of [Foundation](http://foundation.zurb.com/) CSS framework by default. You can, however, replace it with any other library or custom styles. -->

## Introduction

This project contains the backend code for "Emma" and is necessary for the code that is explained in the [Emma](https://github.com/tijnrenders/emma) repo. Emma is created for school purposes and we recommend not to use this for anything else.

## Request points

As documented in the [Emma](https://github.com/tijnrenders/emma) repo there are for now only three request points.

1. /register_user
2. /check_user
3. /get_response

These request points depend on the information that Facebook provide to us and will not work without it. 
