# Hubtel notification channel for Laravel 11+

[![Latest Stable Version](https://poser.pugx.org/norris1z/hubtel-laravel-sms-channel/v/stable)](https://packagist.org/packages/norris1z/hubtel-laravel-sms-channel)
[![Total Downloads](https://poser.pugx.org/norris1z/hubtel-laravel-sms-channel/downloads)](https://packagist.org/packages/norris1z/hubtel-laravel-sms-channel)
[![License](https://poser.pugx.org/norris1z/hubtel-laravel-sms-channel/license)](https://packagist.org/packages/norris1z/hubtel-laravel-sms-channel)

This package makes it easy to send notifications using [Hubtel](https://hubtel.com) with Laravel 11+.

## Contents

- [Installation](#installation)
	- [Setting up the Hubtel service](#setting-up-the-hubtel-service)
- [Usage](#usage)
	- [Available Message methods](#available-message-methods)
- [Changelog](#changelog)
- [Testing](#testing)
- [Security](#security)
- [Contributing](#contributing)
- [Credits](#credits)
- [License](#license)


## Installation

To get the latest version of Hubtel Notification channel for Laravel 11+, simply require the project using [Composer](https://getcomposer.org):

Add this to your composer.json
```
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/kwadwokyeremeh/hubtel-laravel-sms.git"
        },
    ],
```
Run from your terminal
```bash
$ composer require kwadwokyeremeh/hubtel-laravel-sms
```


### Setting up the Hubtel service

In your Hubtel account go to [Applications](https://unity.hubtel.com/account/api-accounts) page. Click on the details of the desired application and copy your `apiKey` and `apiSecret`

In your terminal run
```bash
$ php artisan vendor:publish --provider="NotificationChannels\Hubtel\HubtelServiceProvider"
```
This creates a `hubtel-sms.php` file in your `config` directory.

Paste your apiCredentials in the `config/hubtel.php` configuration file. You may copy the example configuration below to get started:
```php
        'api_key' => env('HUBTEL_CLIENT_ID'),
        'api_secret' => env('HUBTEL_CLIENT_SECRET')
```

Or 

Add the `HUBTEL_CLIENT_ID` and `HUBTEL_CLIENT_SECRET` to your `.env` file

## Usage

Now you can use the channel in your `via()` method inside the notification:
``` php
use Illuminate\Notifications\Notification;
use NotificationChannels\Hubtel\HubtelChannel;
use NotificationChannels\Hubtel\HubtelMessage;

class SayHello extends Notification
{
    public function via($notifiable)
    {
        return [HubtelChannel::class];
    }

    public function toSMS($notifiable)
    {
        return (new HubtelMessage)
			->from("JabClari")
			->to("2331234567890")
           	 	->content("Kim Kippo... Sup with you");
    }
}
```

In order to let your Notification know which phone number you are sending to, add the `routeNotificationForSMS` method to your Notifiable model e.g your User Model

```php
public function routeNotificationForSMS()
{
    return $this->phone; // where phone is a cloumn in your users table;
}
```

### Available Message methods

* `from($from)` : set the sender's name or phone number
* `to($to)` : set the recipient's phone number
* `content($content)` : set the message content
* `registeredDelivery()` : set delivery report request
* `clientReference($reference)` : set the client reference number
* `type($type)` : set the message type to be sent
* `udh($udh)` : set the User Data Header of the SMS Message being sent
* `time($time)` : set the time to deliver the message
* `flashMessage()` : sends the message as a flash message

Read more about the avialable methods on the [Hubtel Documentation Page](https://developers.hubtel.com/documentations/sendmessage)
## Changelog

## Latest Notice 
For developers who would want to use this package on VPS hosted applications, if the server location is US for which you have a US IP Address, you may need to seek whitelisting of the US Ip address from hubtel by mailing support@hubtel.com. As i discovered through experience that the package would work fine on local machine because the IP used is a Ghanaian IP address but fails to work on a production server. Note however that this is not a package problem, as the package just organizes components for sending successful SMS messages within Laravel. It is even more challenging to know the cause of the problem when you are using laravel queues, because the response codes are not logged, the queue just logs Processing failed.Hubtels SMS server responds with a 403 Forbidden, when the same SMS is sent directly using Guzzlehttp on the production server (VPS). A 403 HTTP Response according to their website (hubtel) indicates the recipient has not given his/her approval to receive messages which is even more confusing. :) 


Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Security

If you discover any security related issues, please email norisjibril@gmail.com instead of using the issue tracker.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [Norris Oduro Tei](https://github.com/Norris1z)
- [Kwadwo Kyeremeh Tuffour](https://github.com/kwadwokyeremeh)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
