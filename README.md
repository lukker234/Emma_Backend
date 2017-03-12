
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

## Request point process

The first time a user makes contact with Emma it will send an user check to the backend and it will look like this

```bash
{
  "recipientId": 1464679290223081
}
```

The backend only needs an Facebook id to check if Emma already knows the user. their are 2 ways the backend can respond to this like

```bash
{
  "recipientId": 1464679290223081,
  "registered": false
}
```

This will be send to Emma to let it know if it doesn't knows the user, if it knows the user it will send

```bash
{
  "recipientId": 1464679290223081,
  "registered": true
}
```
The code above is generated by the backend, this will be done so Emma can interact with the user to who she is talking to.
When de user starts talking to Emma it will send the Check user command and receives an True or False. The code below shows how this is being done.

```bash
{
  $value = $this->request;
  $json_encode  = $value->data('recipientId');

  $id = json_encode($json_encode);

  $users = TableRegistry::get('Registred_users');

  $query = $users->find();
  $query->where([
    'Registred_users.recipientid' => $id
  ]);

  $found_user = $query->first();

  if (!is_object($found_user)) {
    $data = Array(
          'recipientId' => $value->data('recipientId'),
          'registered' => false,
      );
      $this->set('data', $data);
  }else{
    $data = Array(
          'recipientId' => $value->data('recipientId'),
          'registered' => true,
      );
      $this->set('data', $data);
  }
}
```

As you can see the backend will check in the database if this user is already known or not and will respond with an True or False, with this response Emma knows how to react so the conversation can begin with this user.

If the user is not known the backend will receive the information from Facebook and will register the user so Emma can recognize this user in an future conversation.

The code below shows how an user is registered, the code is made in an certain way that it can be adjusted to work with Whatsapp or something else.

```bash
  $value = $this->request;
    if($value->data('platform') == "facebook"){
      $connection = ConnectionManager::get('default');
      $connection->insert('Registred_users', [
          'name' => $value->data('first_name') . " " . $value->data('last_name'),
          'gender' => $value->data('gender'),
          'locale' => $value->data('locale'),
          'timezone' => $value->data('timezone'),
          'created' => new \DateTime('now')
      ], ['created' => 'datetime']);

      $users = TableRegistry::get('Registred_users');
      $query = $users->find();
      $query->where([
        'Registred_users.name' => $value->data('first_name') . " " . $value->data('last_name')
      ]);

      $json_encode  = $value->data('recipientId');

      $id = json_encode($json_encode);

      $connection->insert('facebook', [
          'user_id' => $query->first()->id,
          'facebook_id' => $id,
          'profile_pic' => $value->data('profile_pic')
      ]);

      $data = Array(
          "recipientId" => $value->data('recipientId'),
          "registered_completed" => true,
        );

      $this->set('data', $data);
    }

```

After this is all done the real conversation will start between the user and Emma, for now this is limited by asking if Emma knows something to do and Emma will react to it with the question what the user would like to do.
