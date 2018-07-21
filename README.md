# Symfony Auth Example

This project is an example of a simple stateless auth system on Symfony 4.

## How it works ?

Here is the pipeline for getting an access to the api resources.

* Register a new user account
```http request
POST /register
    ?username={}
    &firstname={}
    &lastname={}
    &password={}
Content-Type: application/json

HTTP1/1 200 OK
Content-Type: application/json
user: {}
```

* Login with the new account
```http request
POST /login
    ?email={}
    &password={}
Content-Type: application/json

HTTP1/1 200 OK
Content-Type: application/json
token: {}
expiresIn: {}

```

* Use this generate token for accessing resources
```http request
GET /user
X-Auth-Token: {}

HTTP1/1 200 OK
Content-Type: application/json
user: {}
```

# How to do that ?

### Create [User](https://github.com/kerwanp/symfony-auth-example/blob/master/src/Entity/User.php) entity
You have to create a User entity implementing UserInterface.
This interface will make your user understable by the Security Component. 

This entity must at least be able to store the user auth informations.

With this entity, create a [UserRepository](https://github.com/kerwanp/symfony-auth-example/blob/master/src/Repository/UserRepository.php) for storing different methods, in our case a createUser method.

### Create [UserToken](https://github.com/kerwanp/symfony-auth-example/blob/master/src/Entity/UserToken.php) entity
You must create an entity for storing every tokens which will allow your player to access to the resources.
We add an expire field to logout the user if he does not call the API for X time.

Create a [UserTokenRepository](https://github.com/kerwanp/symfony-auth-example/blob/master/src/Repository/UserTokenRepository.php), this class will contain the method for refreshing the expiration date.

### Create the [TokenUserProvider](https://github.com/kerwanp/symfony-auth-example/blob/master/src/Security/TokenUserProvider.php)
This provider will tell to the Security component how to retreive user with a username.
With that we will add a method to help us to retreive a UserToken with a given token.

### Create the [UserTokenAuthenticator](https://github.com/kerwanp/symfony-auth-example/blob/master/src/Security/UserTokenAuthenticator.php)
This class will tell to the Securtiy component how to authenticate, for that we will use the SimplePreAuthenticatorInterface.
In the createToken method we will retreive the X-Auth-Token which surely contains the token so we create an auth token with anonym informations.

With this token we will user our TokenUserProvider for getting the corresponding UserToken if no one exists or the UserToken expired we auth him as an anonymous user without any permission.
If everything passed with succes we auth the user.

### Create the [AuthController](https://github.com/kerwanp/symfony-auth-example/blob/master/src/Controller/AuthController.php)
Create the authentication controller, we need at least 3 routes.
The register route, it creates a new User with the given informations.
The login route, it checks the credentials and returns a new generated UserToken.

### Create the [UserController](https://github.com/kerwanp/symfony-auth-example/blob/master/src/Controller/UserController.php)
Create the user controller to allow users to retreive their own informations.

### Setup the [security](https://github.com/kerwanp/symfony-auth-example/blob/master/config/packages/security.yaml) configuration
In this file we will configurate three things :
* How we crypt passwords
* Register our TokenUserProvider
* Setup the authentication on all our routes (except login and register)